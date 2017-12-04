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

		// LOGGED IN KEY
		self::$auth_key = $CONFIG['APP_LOGGED_IN_KEY'];
		if ( empty($this->auth_key) ) {
			trigger_error("Core_AuthService -> Auth key is missing !", E_USER_ERROR);
		}

		// SESSION KEY
		self::$session_key = $CONFIG['APP_SESSION_KEY'];
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
    public static function getService() { return null; }

    /**
     * Login Method
     *
     * Initiates or Resumes a valid session
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

        session_destroy();

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
     * Check Authorization Method
     * dedicated to Module Methods
     *
     * TRUE if the access is granted, FALSE otherwise
     *
     * @param string $module
     * @param string $method
     *
     * @return boolean
     */
    public abstract function checkMethodAuthorization($module = '', $method = '');

    /**
     * Check Authorization Method
     * dedicated to Module Pages
     *
     * TRUE if the access is granted, FALSE otherwise
     *
     * @param string $module
     * @param string $page
     * @return boolean
     */
    public abstract function checkPageAuthorization($module = '', $page = '');
}
