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
 * Authentication Service
 *
 * This class manages the session validity inside the application and checks the permissions
 */
abstract class Core_AuthService
{
	// Service Handle
	protected static $authService = null;

	// RBAC Framework Handle
    protected static $rbac = null;

    // Authentication Passphrase
    protected static $auth_key = '';

    // Session Passphrase
    protected static $session_key = '';

	/**
	 * Default Constructor
	 */
	protected function __construct()
	{
		// RBAC
        if (empty(self::$rbac) ||
            !is_object(self::$rbac) ||
            !is_a(self::$rbac, 'Rbac')) {
            self::$rbac = new PhpRbac\Rbac();
        }

		// SECRET KEYS
		$CONFIG = parse_ini_file( CONF_SECRET_INI );

		// SESSION KEY
		self::$session_key = $CONFIG['APP_TOKEN_KEY'];
		if ( empty($this->session_key) ) {
			trigger_error("Core_AuthService -> Session key is missing !", E_USER_ERROR);
		}
	}

    /**
     * SHA512 With Salt Function
     * Generate a hash value (message digest)
     *
     * @param String $data
     * @return String
     * @access public
     */
    public static function getHash( $data ) {

        // SECRET KEYS
        $CONFIG = parse_ini_file( CONF_SECRET_INI );

        // AUTH SALT
        $auth_salt = $CONFIG['APP_AUTH_SALT'];
        if ( empty($auth_salt) ) {
            trigger_error("Core_AuthService -> Auth salt is missing !", E_USER_ERROR);
        }

        return hash( 'sha512', $auth_salt . $data );
    }

    /**
     * Build permission path from a given module name and a controller method
     *
     * @param $module
     * @param $method
     * @return string
     */
    protected static function buildMethodPermissionPath($module, $method) {
        return ucfirst(strtolower($module)) . '::' . $method;
    }

    /**
     * Build permission path from a given module name and a page
     *
     * @param $module
     * @param $page
     * @return string
     */
    protected static function buildPagePermissionPath($module, $page) {
        return ucfirst(strtolower($module)) . '/' . strtolower($page);
    }

    /**
     * Service Handler
     */
    public static function getService(){}

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
    public abstract function login();

    /**
     * Logout Method
     *
     * Destroys the session
     */
    public function logout() {
        self::$authService = null;
    }

    /**
     * Checks the Validity Of the Current Session
     *
     * TRUE if the online user is authorized, FALSE otherwise
     *
     * @return boolean
     */
    public abstract function isLoggedIn();

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
    abstract function checkMethodAuthorization($module = '', $method = '');

    /**
     * Default implementation of checkMethodAuthorization()
     *
     * @param string $module
     * @param string $method
     * @param int $uid
     *
     * @return bool
     */
    protected function _checkMethodAuthorization($module = '', $method = '', $uid = 0) {

        if (empty($module) || empty($method) || !is_numeric($uid)) {
            return FALSE;
        }

        // Are you root or do you have explicitly rights on this resource ?

        $permissionPath = self::buildMethodPermissionPath($module, $method);

        if ($uid === 0) {
            return self::$rbac->check($permissionPath, 'guest');
        }

        if (self::$rbac->Users->hasRole('root', $uid) || self::$rbac->check($permissionPath, $uid)) {
            return TRUE;
        }

        return FALSE;
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
    abstract function checkPageAuthorization($module = '', $page = '');

    /**
     * Default implementation of checkPageAuthorization()
     *
     * @param string $module
     * @param string $page
     * @param int $uid
     *
     * @return bool
     */
    protected function _checkPageAuthorization($module = '', $page = '', $uid = 0) {

        if (empty($module) || empty($page) || !is_numeric($uid)) {
            return FALSE;
        }

        // Are you root or do you have explicitly rights on this resource ?

        $permissionPath = self::buildPagePermissionPath($module, $page);

        if ($uid === 0) {
            return self::$rbac->check($permissionPath, 'guest');
        }

        if (self::$rbac->Users->hasRole('root', $uid) || self::$rbac->check($permissionPath, $uid)) {
            return TRUE;
        }

        return FALSE;
    }

    // Todo : clean that shit

    /*
    public function getUserPermissions($uid) {

        $authorizations = array();

        if (empty($uid)) {
            return $authorizations;
        }

        // Notice:
        // root users access all methods and resources

        if ($this->rbac->Users->hasRole( 'root', $uid )) {

            // Parse all modules

            $handle = opendir( MODS_DIR );

            if ($handle) {

                // Foreach modules
                while (false !== ($entry = readdir($handle))) {

                    // Dump specific directories
                    if ($entry == "." || $entry == "..") {

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
    */

    /*
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
    */



    /*
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

    protected function resetBanCounter() {

        if ( !empty($this->session['SEC_COUNT']) ) {
            // Reset counter
            unset($this->session['SEC_COUNT']);
        }

        // Push to global $_SESSION
        $_SESSION = $this->session;
    }

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
    */

}
