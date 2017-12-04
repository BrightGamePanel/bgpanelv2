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
 * Generic Authentication Service
 *
 * Relies on JWT tokens
 */
final class Core_AuthService_Generic extends Core_AuthService
{
    // PUBLIC SESSION
    // Logging requirement
    private $logged_user = ''; // Username

    // JWT Token
    private $uid = 0;
    private $firstname = '';
    private $lastname = '';
    private $lang = '';
    private $template = '';

    // Must implement
    //| HS256        | HMAC using SHA-256                | Required       |
    //| HS384        | HMAC using SHA-384                | Optional       |
    //| HS512        | HMAC using SHA-512                | Optional       |

    protected function __construct() {
        parent::__construct();

        session_start();
        $_SESSION['TIMESTAMP'] = time();

        // TODO : verify that the remote client has a valid token
        // TODO : Check both session and request body


        /**
         *
         * Exemple of token
         */
        $token = array (
            'username' => $this->username,
            'token' => session_id(),
            'key' => $this->auth_key,
            'salt' => md5(time())
        );
    }

    public static function getAuthService() {

        if (empty(self::$authService) ||
            !is_object(self::$authService) ||
            !is_a(self::$authService, 'Core_AuthService')) {
            self::$authService = new Core_AuthService_Generic();
        }

        return self::$authService;
    }

    public function logout()
    {
        $_SESSION = array(); // Destroy session variables

        parent::logout();
    }


    /**
     * Appends Information To The Session
     *
     * @param $id
     * @param $username
     * @param $firstname
     * @param $lastname
     * @param $lang
     * @param $template
     */
    protected function login($id, $username, $firstname, $lastname, $lang, $template ) {

        $this->session['ID'] = $id;
        $this->session['USERNAME'] = $username;

        $this->session['INFORMATION'] = array (
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'lang'      => $lang,
            'template'  => $template
        );


        // TODO : clean here

        $credentials = serialize (
            array (
                'username' => $this->username,
                'token' => session_id(),
                'key' => $this->auth_key,
                'salt' => md5(time())
            )
        );

        $perms = Core_AuthService_Perms::getUserPermissions();
        $this->session['PERMISSIONS'] = $perms;

        switch ( CONF_SEC_SESSION_METHOD )
        {
            case 'aes256':
            default:
                $cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
                $cipher->setKeyLength(256);
                $cipher->setKey( $this->session_key );

                $this->session['CREDENTIALS'] = $cipher->encrypt( $credentials );
                break;
        }

        $_SESSION = $this->session;
    }


    /**
     * Check If the Current Session Is Banned
     *
     * Automatically remove a ban if this one has expired
     */
    protected function isBanned() {

        // No ban registered for this session
        if ( empty($this->session['SEC_BAN']) ) {
            return FALSE;
        }

        // Reset the ban if this one has expired
        if ( $this->session['SEC_BAN'] < time() ) {
            $this->resetBanCounter();
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Check If The Current Session Is Legit
     */
    protected function isSignedIn() {

        if (empty($this->username) || empty($this->uid) || empty($this->session['CREDENTIALS'])) {
            return FALSE;
        }

        // Decipher session
        $credentials = array();
        switch (CONF_SEC_SESSION_METHOD)
        {
            case 'aes256':
            default:
                $cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
                $cipher->setKeyLength(256);
                $cipher->setKey($this->session_key);

                $credentials = unserialize($cipher->decrypt($this->session['CREDENTIALS']));
                break;
        }

        // Level 1
        if ($credentials['username'] == $this->username &&
            $credentials['key'] == $this->auth_key &&
            $credentials['token'] == session_id()) {

            // Level 2
            $dbh = Core_DBH::getDBH();

            // Fetch information from the database
            $sth = $dbh->prepare("
                SELECT username, last_ip, token
                FROM " . DB_PREFIX . "user
                WHERE
                    user_id = :user_id
                ;");

            $sth->bindParam( ':user_id', $this->session['INFORMATION']['id'] );

            $sth->execute();

            $userResult = $sth->fetchAll(PDO::FETCH_ASSOC);

            // Verify
            if ($userResult[0]['username'] == $this->username &&
                $userResult[0]['last_ip'] == $_SERVER['REMOTE_ADDR'] &&
                $userResult[0]['token'] == session_id()) {

                $this->updateUserActivity();

                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Update Logged In User Information
     */
    private function updateUserActivity() {

        $dbh = Core_DBH::getDBH();

        // Update User Activity on page request

        $last_activity = date('Y-m-d H:i:s');

        $sth = $dbh->prepare("
                    UPDATE " . DB_PREFIX . "user
                    SET
                        last_activity	= :last_activity
                    WHERE
                        user_id			= :user_id
                    ;");

        $uid = Core_AuthService::getSessionInfo('ID');
        $sth->bindParam(':last_activity', $last_activity);
        $sth->bindParam(':user_id', $uid);

        $sth->execute();
    }

    /**
     * Security Counter
     *
     * SEC_COUNT
     *
     * Increment the ban counter
     */
    protected function incrementBanCounter() {

        // Increment security counter
        if ( empty($this->session['SEC_COUNT']) ) {
            $this->session['SEC_COUNT'] = 1;
        }
        else {
            $this->session['SEC_COUNT'] += 1;
        }

        // Push to global $_SESSION
        $_SESSION = $this->session;
    }

    /**
     * Security Counter - Reset Mechanism
     *
     * Reset the internal BAN counter
     *
     * @param none
     * @return none
     * @access public
     */
    protected function resetBanCounter() {

        if ( !empty($this->session['SEC_COUNT']) ) {
            // Reset counter
            unset($this->session['SEC_COUNT']);
        }

        // Push to global $_SESSION
        $_SESSION = $this->session;
    }

    /**
     * Verify Security Counter
     *
     * SEC_BAN
     *
     * BAN a user from being authenticated after multiple unsuccessful attempts, if the counter as exceeded the max
     */
    protected function ban() {

        if ($this->session['SEC_COUNT'] <= CONF_SEC_LOGIN_ATTEMPTS) {
            return;
        }

        // Ban the user if too many attempts have been done
        // or the user is already banned but keeps trying

        // Set ban
        $this->session['SEC_BAN'] = time() + CONF_SEC_BAN_DURATION; // Mark the end of the ban

        // Push to global $_SESSION
        $_SESSION = $this->session;

        // Log Event
        Logger::configure( bgp_log4php_def_conf() );
        $logger = Logger::getLogger( 'core.auth' );
        $logger->info('Session banned.');
    }

}


class Core_AuthService_Perms
{


    public static function getUserPermissions( ) {

        $rbac = new PhpRbac\Rbac();

        $authorizations = array();

        // Get Session UID

        $uid = Core_AuthService::getSessionInfo('ID');
        if (empty($uid)) {
            return $authorizations;
        }

        // Notice:
        // root users access all methods and resources

        if ($rbac->Users->hasRole( 'root', $uid )) {

            // Parse all modules

            $handle = opendir( MODS_DIR );

            if ($handle) {

                // Foreach modules
                while (false !== ($entry = readdir($handle))) {

                    // Dump specific directories and exceptions
                    if ($entry == "." || $entry == ".." || in_array($entry, self::$restricted_modules)) {

                        continue;
                    }

                    $module = $entry;

                    // Get Public Methods
                    $methods = Core_Reflection::getControllerPublicMethods( $module );

                    if (empty($methods)) {
                        continue;
                    }

                    foreach ($methods as $key => $value) {
                        list($module, $method) = explode(".", $value['method']);
                        $module = strtolower($module);

                        $authorizations[$module][] = $method;
                    }
                }

                closedir($handle);
            }

            return $authorizations;
        }

        // fetch all allowed resources and methods

        $roles = $rbac->Users->allRoles( $uid );
        $perms = array();

        foreach ($roles as $role) {

            $perms[] = $rbac->Roles->permissions( $role['ID'], false );
        }

        foreach ($perms as $perm) {

            if (empty($perm)) {

                continue;
            }

            foreach ($perm as $p) {

                // filter pages and get only modules and methods
                if (substr_count($p['Title'], '/') === intval(1)) {
                    $module = $p['Title'];
                    $module = substr(strtolower($module), 0, -1);

                    if (!isset($authorizations[$module]) && !in_array($module, self::$restricted_modules)) {
                        $authorizations[$module] = array();
                    }
                }
                else if (preg_match("#(^[A-Z])*(\.)#", $p['Title'])) {
                    list($module, $method) = explode(".", $p['Title']);
                    $module = strtolower($module);

                    // append method only if the module was allowed
                    if (isset($authorizations[$module])) {
                        $authorizations[$module][] = $method;
                    }
                }
            }
        }

        return $authorizations;
    }
}
