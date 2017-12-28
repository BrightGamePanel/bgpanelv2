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
 * JWT Authentication Service
 *
 * Relies on JWT tokens
 * Inspired by :
 * @link https://php-download.com/package/firebase/php-jwt
 *
 * Note: The OAuth 2.0 Authorization Framework: Bearer Token Usage
 * @link https://tools.ietf.org/html/rfc6750
 * @link https://stackoverflow.com/questions/33265812/best-http-authorization-header-type-for-jwt
 *
 * Note : JSON Web Token (JWT)
 * @link https://tools.ietf.org/html/rfc7519
 *
 * Note : JSON Web Algorithms (JWA): draft-ietf-jose-json-web-algorithms-40
 * @link https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 */
final class Core_AuthService_JWT extends Core_AuthService
{
    // JWT Algorithms
    const HMAC_SHA_256 = 'HS256';
    const HMAC_SHA_384 = 'HS384';
    const HMAC_SHA_512 = 'HS512';

    /**
     * "iss" (Issuer) Claim
     *
     * The "iss" (issuer) claim identifies the principal that issued the
     * JWT.  The processing of this claim is generally application specific.
     * The "iss" value is a case-sensitive string containing a StringOrURI
     * value.  Use of this claim is OPTIONAL.
     */
    private $iss            = '';

    /**
     * "iat" (Issued At) Claim
     *
     * The "iat" (issued at) claim identifies the time at which the JWT was
     * issued.  This claim can be used to determine the age of the JWT.  Its
     * value MUST be a number containing a NumericDate value.  Use of this
     * claim is OPTIONAL.
     */
    private $iat            = 0;

    /**
     * "exp" (Expiration Time) Claim
     *
     * The "exp" (expiration time) claim identifies the expiration time on
     * or after which the JWT MUST NOT be accepted for processing.  The
     * processing of the "exp" claim requires that the current date/time
     * MUST be before the expiration date/time listed in the "exp" claim.
     */
    private $exp            = 0;

    /**
     * Decoded JWT Token of the Request
     */
    private $req_token      = '';

    /**
     * Logged entity attributes
     */
    private $logged_user    = ''; // Username
    private $ip             = '';
    private $uid            = 0;
    private $firstname      = '';
    private $lastname       = '';
    private $lang           = '';
    private $template       = '';

    /**
     * Core_AuthService_JWT constructor.
     * @throws Core_Application_Exception
     */
    protected function __construct() {
        parent::__construct();

        // Check Config

        if ( !defined('CONF_SEC_SESSION_METHOD') ) {
            throw new Core_Application_Exception($this, 'Session security policy is missing !');
        }

        if ( !defined('APP_TOKEN_KEY') || empty(APP_TOKEN_KEY)) {
            throw new Core_Application_Exception($this, 'Token key is missing or empty !');
        }

        // Fetch Token

        if (session_status() == PHP_SESSION_ACTIVE) {

            // Stateful
            // Read $_SESSION

            $this->req_token = (!empty($_SESSION['AUTHORIZATION'])) ? filter_var($_SESSION['AUTHORIZATION'],
                FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : '';
        } else if (session_status() == PHP_SESSION_NONE) {

            // Stateless
            // Read HTTP_HEADERS

            $headers = array_change_key_case(apache_request_headers(), CASE_UPPER);

            $this->req_token = (isset($headers['AUTHORIZATION'])) ? filter_var($headers['AUTHORIZATION'],
                FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : '';
        }
    }

    public function logout()
    {
        // TODO ban the token

        parent::logout();
    }

    public static function getService() {

        if (empty(self::$authService) ||
            !is_object(self::$authService) ||
            !is_a(self::$authService, 'Core_AuthService')) {
            self::$authService = new Core_AuthService_JWT();
        }

        return self::$authService;
    }

    /**
     * Forge a new JWT Token from the given information
     *
     * Returns a new valid JWT Token
     *
     * @param string $logged_user
     * @param string $password
     * @return string
     * @throws Core_Application_Exception
     */
    public static function forgeToken($logged_user = '', $password = '') {

        $password = self::getHash($password);
        $dbh = Core_DBH::getDBH();

        try {
            $sth = $dbh->prepare("
					SELECT user_id, username, firstname, lastname, lang, template
					FROM user
					WHERE
						username = :username AND
						password = :password AND
						status = 'Active'
					;");
            $sth->bindParam(':username', $logged_user);
            $sth->bindParam(':password', $password);
            $sth->execute();
            $result = $sth->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
            die();
        }

        if (empty($result)) {
            return '';
        }

        return 'Bearer ' . \Firebase\JWT\JWT::encode(array(
            'iss'               => BGP_SYSTEM_URL,
            'iat'               => time(),
            'exp'               => time() + (int)ini_get("session.gc_maxlifetime"),
            'uid'               => $result[0]['user_id'],
            'logged_user'       => $result[0]['username'],
            'ip'                => $_SERVER['REMOTE_ADDR'],
            'firstname'         => $result[0]['firstname'],
            'lastname'          => $result[0]['lastname'],
            'lang'              => $result[0]['lang'],
            'template'          => $result[0]['template']
        ), APP_TOKEN_KEY);
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
    public function login()
    {
        if ($this->isLoggedIn() === TRUE) {
            return TRUE; // Already signed in
        }

        if (empty($this->req_token) || strpos($this->req_token, 'Bearer ') !== 0) {
            return FALSE; // No credentials
        }

        // Decode JWT
        str_replace('Bearer ', '', $this->req_token);

        /**
         * You can add a leeway to account for when there is a clock skew times between
         * the signing and verifying servers. It is recommended that this leeway should
         * not be bigger than a few minutes.
         *
         * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
         */

        \Firebase\JWT\JWT::$leeway = 60; // $leeway in seconds

        try {
            switch (CONF_SEC_SESSION_METHOD) {
                case 'HMAC_SHA_384':
                    $decoded_token = (array)\Firebase\JWT\JWT::decode(
                        $this->req_token,
                        APP_TOKEN_KEY,
                        array(self::HMAC_SHA_384)
                    );
                    break;
                case 'HMAC_SHA_512':
                    $decoded_token = (array)\Firebase\JWT\JWT::decode(
                        $this->req_token,
                        APP_TOKEN_KEY,
                        array(self::HMAC_SHA_512)
                    );
                    break;
                case 'HMAC_SHA_256':
                default:
                    $decoded_token = (array)\Firebase\JWT\JWT::decode(
                        $this->req_token,
                        APP_TOKEN_KEY,
                        array(self::HMAC_SHA_256)
                    );
                    break;
            }
        } catch (UnexpectedValueException $uve) {
            // Provided JWT was invalid
            return FALSE;
        }

        // Verify

        $dbh = Core_DBH::getDBH();

        try {
            // Fetch information from the database

            $sth = $dbh->prepare("
                SELECT username, last_ip
                FROM user
                WHERE
                    user_id = :user_id AND
                    status = 'Active'
                ;");

            $sth->bindParam( ':user_id', $decoded_token['uid'] );

            $sth->execute();

            $userResult = $sth->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
            die();
        }

        if ($userResult[0]['username'] == $decoded_token['logged_user'] &&
            $userResult[0]['last_ip'] == $decoded_token['ip'] &&
            $userResult[0]['last_ip'] == $_SERVER['REMOTE_ADDR']) {
        } else {
            return FALSE;
        }

        // Check (Issuer) Claim

        if ($decoded_token['iss'] != BGP_SYSTEM_URL) {
            return FALSE;
        }

        // OK

        $this->iat              = $decoded_token['iat'];
        $this->iss              = $decoded_token['iss'];
        $this->exp              = $decoded_token['exp'];

        $this->logged_user      = $decoded_token['logged_user'];
        $this->ip               = $decoded_token['ip'];
        $this->uid              = $decoded_token['uid'];
        $this->firstname        = $decoded_token['firstname'];
        $this->lastname         = $decoded_token['lastname'];
        $this->lang             = $decoded_token['lang'];
        $this->template         = $decoded_token['template'];

        return TRUE;
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
        return parent::_checkPageAuthorization($module, $page, $this->uid);
    }

    /**
     * @return mixed
     */
    public function getLoggedUser()
    {
        return $this->logged_user;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
