<?php

/**
 * LICENSE:
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @package		Bright Game Panel V2
 * @version		0.1
 * @category	Systems Administration
 * @author		warhawk3407 <warhawk3407@gmail.com> @NOSPAM
 * @copyright	Copyleft 2015, Nikita Rousseau
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @link		http://www.bgpanel.net/
 */



/**
 * API Stateless Authentication Service
 *
 * Relies on X-API-HEADERS, or PHP_AUTH_USER otherwise
 * Uses also an IP whitelisting mechanism to prevent unknown hosts
 */
final class Core_AuthService_API extends Core_AuthService
{
    // USER
    private $uid = 0;

    // AUTHENTICATION METHODS
    const AUTH_X_HTTP = 0;
    const AUTH_BASIC = 1;

    // HTTP Request Credentials
    private $x_api_key = null;
    private $x_api_user = null;
    private $x_api_pass = null;

    // BASIC AUTHORIZATION Credentials
    private $php_auth_user = null;
    private $php_auth_pw = null;

    /**
     * Core_AuthService_API constructor.
     */
    protected function __construct() {
        parent::__construct();

        // Credentials

        $headers = array_change_key_case(apache_request_headers(), CASE_UPPER);

        $this->x_api_key = (isset($headers['X-API-KEY'])) ? filter_var($headers['X-API-KEY'],
            FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : NULL;
        $this->x_api_user = (isset($headers['X-API-USER'])) ? filter_var($headers['X-API-USER'],
            FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : NULL;
        $this->x_api_pass = (isset($headers['X-API-PASS'])) ? filter_var($headers['X-API-PASS'],
            FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : NULL;

        // Servers with Server API set to CGI/FCGI will not populate PHP_AUTH vars

        $this->php_auth_user = (isset($_SERVER['PHP_AUTH_USER'])) ? filter_var($_SERVER['PHP_AUTH_USER'],
            FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : NULL;
        $this->php_auth_pw = (isset($_SERVER['PHP_AUTH_PW'])) ? filter_var($_SERVER['PHP_AUTH_PW'],
            FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : NULL;
    }

    public static function getService() {

        if (empty(self::$authService) ||
            !is_object(self::$authService) ||
            !is_a(self::$authService, 'Core_AuthService')) {
            self::$authService = new Core_AuthService_API();
        }

        return self::$authService;
    }

    /**
     * Login Method
     *
     * Fetches authentication information
     * Checks that those information are valid or not
     *
     * Returns TRUE on SUCCESS, FALSE otherwise
     *
     * @return boolean
     */
    public function login() {

        if ($this->isLoggedIn() === TRUE) {
            return TRUE; // Already signed in
        }

        if (empty($this->x_api_key) AND
            empty($this->x_api_user) AND
            empty($this->x_api_pass) AND
            empty($this->php_auth_user) AND
            empty($this->php_auth_pw)) {

            return FALSE; // No credentials
        }

        // Authentication

        // AUTH-BASIC (if allowed)
        if ((boolval(APP_API_ALLOW_BASIC_AUTH) === TRUE)) {
            if (!empty($this->php_auth_user) AND
                !empty($this->php_auth_pw)) {
                return (
                    Core_AuthService_API::checkRemoteHost(
                        Flight::request()->ip
                    ) AND
                    $this->checkRemoteAPIUser(
                        Flight::request()->ip,
                        $this->php_auth_user,
                        $this->php_auth_pw,
                        '',
                        self::AUTH_BASIC
                    )
                );
            }
        }

        // X-HTTP-HEADERS (default)
        return (
            Core_AuthService_API::checkRemoteHost(
                Flight::request()->ip
            ) AND
            $this->checkRemoteAPIUser(
                Flight::request()->ip,
                $this->x_api_user,
                $this->x_api_pass,
                $this->x_api_key,
                self::AUTH_X_HTTP
            )
        );
    }

    /**
     * Checks that the remote entity can call the API service
     *
     * @param $remote_ip
     * @return bool
     */
    private static function checkRemoteHost( $remote_ip )
    {
        // Get IPs Whitelist
        $trustedIps = parse_ini_file( CONF_API_WHITELIST_INI, TRUE );

        if (!empty($trustedIps) && isset($trustedIps['IPv4'])) {

            $trustedIps = array_values( $trustedIps['IPv4'] );

            // Verify IP
            if (in_array($remote_ip, $trustedIps)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Checks the remote user
     *
     * @param $remote_ip
     * @param $api_user
     * @param $api_user_pass
     * @param string $api_key
     * @param $auth_method
     * @return bool
     */
    private function checkRemoteAPIUser( $remote_ip, $api_user, $api_user_pass, $api_key = '', $auth_method )
    {
        // Get API Key
        $apiMasterKey = parse_ini_file( CONF_API_KEY_INI );

        if (!empty($apiMasterKey) && isset($apiMasterKey['APP_API_KEY'])) {

            $apiMasterKey = $apiMasterKey['APP_API_KEY'];

            if ($auth_method === self::AUTH_X_HTTP) {

                if ($api_key != $apiMasterKey) {

                    return FALSE;
                }
            }
        }

        $username = $api_user;
        $password = self::getHash($api_user_pass);

        $dbh = Core_DBH::getDBH();

        try {
            $sth = $dbh->prepare("
				SELECT user_id, username
				FROM " . DB_PREFIX . "user
				WHERE
					username = :username AND
					password = :password AND
					status = 'Active'
				;");

            $sth->bindParam(':username', $username);
            $sth->bindParam(':password', $password);

            $sth->execute();

            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
            die();
        }

        if (empty($result)) {
            return FALSE;
        }

        // Verify API Role

        $user_id = $result[0]['user_id'];

        if ( self::$rbac->Users->hasRole( 'api', $user_id ) ) {

            // Update User Activity

            try {
                $sth = $dbh->prepare("
                    UPDATE " . DB_PREFIX . "user
                    SET
                        last_login		= :last_login,
                        last_activity	= :last_activity,
                        last_ip 		= :last_ip,
                        last_host		= :last_host,
                        token 			= :token
                    WHERE
                        user_id			= :user_id
                    ;");

                $last_login = date('Y-m-d H:i:s');
                $last_activity = date('Y-m-d H:i:s');
                $last_host = gethostbyaddr($remote_ip);
                $token = session_id();

                $sth->bindParam(':last_login', $last_login);
                $sth->bindParam(':last_activity', $last_activity);
                $sth->bindParam(':last_ip', $remote_ip);
                $sth->bindParam(':last_host', $last_host);
                $sth->bindParam(':token', $token);
                $sth->bindParam(':user_id', $user_id);

                $sth->execute();
            }
            catch (PDOException $e) {
                echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
                die();
            }

            // Set Session Information

            session_regenerate_id( TRUE );
            $this->uid = $result[0]['user_id'];

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Checks the Validity Of the Current Session
     *
     * TRUE if the online user is authorized, FALSE otherwise
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return ($this->uid === 0) ? FALSE : TRUE;
    }

    /**
     * Check Authorization dedicated to Module Methods
     *
     * TRUE if the access is granted, FALSE otherwise
     *
     * @param string $module
     * @param string $method
     *
     * @return bool
     */
    function checkMethodAuthorization($module = '', $method = '')
    {
        return parent::_checkMethodAuthorization($module, $method, $this->uid);
    }

    /**
     * Check Authorization dedicated to Module Pages
     *
     * TRUE if the access is granted, FALSE otherwise
     *
     * @param string $module
     * @param string $page
     *
     * @return bool
     */
    function checkPageAuthorization($module = '', $page = '')
    {
        return FALSE;
    }
}