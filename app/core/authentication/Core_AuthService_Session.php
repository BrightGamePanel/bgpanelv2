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
 * Session Authentication Service
 *
 * Wrap JWT Token Authentication Service over $_SESSION
 */
final class Core_AuthService_Session extends Core_AuthService {

    private $wrapped_jwt_service = null;

    /**
     * Core_AuthService_Session constructor.
     * @throws Core_Application_Exception
     */
    protected function __construct() {
        parent::__construct();

        // Start Session
        session_start();

        // JWT
        $this->wrapped_jwt_service = Core_AuthService_JWT::getService();
    }

    public static function getService() {

        if (empty(self::$authService) ||
            !is_object(self::$authService) ||
            !is_a(self::$authService, 'Core_AuthService')) {
            self::$authService = new Core_AuthService_Session();
        }

        return self::$authService;
    }

    public function logout()
    {
        $_SESSION = array(); // Destroy session variables

        session_destroy();

        if ($this->wrapped_jwt_service != null) {
            $this->wrapped_jwt_service->logout();
        }

        parent::logout();
    }

    /**
     * Login Method
     *
     * Fetches authentication information
     * Checks that those information are valid or not
     *
     * Returns TRUE on SUCCESS, FALSE otherwise
     *
     * @param string $logged_user
     * @param string $password
     * @return bool
     * @throws Core_Application_Exception
     */
    public function login($logged_user = '', $password = '') {

        // Rely on the wrapped service

        if ($this->wrapped_jwt_service == null) {
            $this->wrapped_jwt_service = Core_AuthService_JWT::getService();
        }

        if ($this->wrapped_jwt_service->isLoggedIn()) {
            return TRUE;
        }

        $this->wrapped_jwt_service->logout();

        // Not logged in

        // Try to forge TOKEN

        $_SESSION['AUTHORIZATION'] = Core_AuthService_JWT::forgeToken(
            $logged_user,
            $password
        );

        $this->wrapped_jwt_service = Core_AuthService_JWT::getService();

        $ret = $this->wrapped_jwt_service->login();
        if ($ret === TRUE) {
            session_regenerate_id( TRUE );
        }

        return $ret;
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
        if ($this->wrapped_jwt_service != null) {
            return $this->wrapped_jwt_service->isLoggedIn();
        }

        return FALSE;
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
        if ($this->wrapped_jwt_service != null) {
            return $this->wrapped_jwt_service->checkMethodAuthorization($module, $method);
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
    function checkPageAuthorization($module = '', $page = '')
    {
        if ($this->wrapped_jwt_service != null) {
            return $this->wrapped_jwt_service->checkPageAuthorization($module, $page);
        }

        return FALSE;
    }

    /**
     * @return mixed
     */
    public function getLoggedUser()
    {
        return $this->wrapped_jwt_service->getLoggedUser();
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->wrapped_jwt_service->getIp();
    }

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->wrapped_jwt_service->getUid();
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->wrapped_jwt_service->getFirstname();
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->wrapped_jwt_service->getLastname();
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->wrapped_jwt_service->getLang();
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->wrapped_jwt_service->getTemplate();
    }
}