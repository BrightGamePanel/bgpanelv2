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
 * Application Wrapper
 */
final class Core_API_Application extends Core_Abstract_Application
{

    /**
     * Core_API_Application constructor.
     *
     * @param $module
     * @param $page
     * @param $id
     * @param $http_accept
     */
    public function __construct($module, $page, $id, $http_accept)
    {
        // User Authentication Services
        $apiAuthService = Core_Auth_Service_API::getService();
        $jwtAuthService = Core_Auth_Service_JWT::getService();

        // JWT connections are relying on another service
        if ($jwtAuthService->isLoggedIn() === TRUE) {
            // JWT Auth
            $this->authentication_service = $jwtAuthService;
        } else {
            $this->authentication_service = $apiAuthService;
        }

        parent::__construct($module, $page, $id, $http_accept);
    }

    /**
     * Execute the Query
     *
     * @return int
     * @throws Core_Exception
     */
    public function execute()
    {
        // Is enable ?

        if (boolval(APP_API_ENABLE) === FALSE || boolval(BGP_MAINTENANCE_MODE) === TRUE) {
            throw new Core_Exception(503); // Service Unavailable
        }

        // Is over HTTPS enable or explicitly allow unsecured HTTP ?

        if ((Flight::request()->secure === FALSE) AND (boolval(APP_API_ALLOW_UNSECURE) === FALSE)) {
            throw new Core_Exception(418); // Unsecured
        }

        // Resolve Request

        $controller_method_array = Core_API::resolveAPIRequest(
            $this->module_handle,
            $this->req_url,
            $this->req_method
        );

        if (empty($controller_method_array)) {
            throw new Core_Exception(400); // Bad Request
        }

        // Check Authorizations

        if ($this->authentication_service->login() === FALSE) {
            throw new Core_Exception(403); // Forbidden
        }

        // Update User Activity
        parent::execute();

        if ($this->authentication_service->checkMethodAuthorization($this->module_handle, $controller_method_array['method']) === TRUE) {
            return $this->invoke($controller_method_array, $this->req_content_type); // Invoke
        }

        // Forbidden as default response
        throw new Core_Exception(403);
    }
}