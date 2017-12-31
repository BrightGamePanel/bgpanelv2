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
	}

    /**
     * SHA512 With Salt Function
     * Generate a hash value (message digest)
     *
     * @param String $data
     * @return String
     * @access public
     * @throws Core_Verbose_Exception
     */
    public static function getHash( $data ) {

        if (empty(Core\Authentication\APP_TOKEN_KEY)) {
            throw new Core_Verbose_Exception(
                'Bad security configuration !',
                'Authentication salt is missing !',
                'The random string `salt` used by cryptographic components of the authentication service is missing or empty.'
            );
        }

        return hash( 'sha512', Core\Authentication\APP_TOKEN_KEY . $data );
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

        try {
            if (self::$rbac->Users->hasRole('root', $uid) || self::$rbac->check($permissionPath, $uid)) {
                return TRUE;
            }
        } catch (RbacUserNotProvidedException $e) {
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

        try {
            if (self::$rbac->Users->hasRole('root', $uid) || self::$rbac->check($permissionPath, $uid)) {
                return TRUE;
            }
        } catch (RbacUserNotProvidedException $e) {
        }

        return FALSE;
    }

    /**
     * Gets the User-Id of the current user
     *
     * @return int
     */
    public abstract function getUid();
}
