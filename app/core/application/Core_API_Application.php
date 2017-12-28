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
class Core_API_Application extends Core_Abstract_Application
{

    /**
     * Core_API_Application constructor.
     *
     * @param $module
     * @param $page
     * @param $id
     * @param $content_type
     */
    public function __construct($module, $page, $id, $content_type)
    {
        parent::__construct($module, $page, $id, $content_type);

        // User Authentication Services
        $apiAuthService = Core_AuthService_API::getService();
        $jwtAuthService = Core_AuthService_JWT::getService();

        // JWT connections are relying on another service
        if ($jwtAuthService->isLoggedIn() === TRUE) {
            // JWT Auth
            $this->authService = $jwtAuthService;
        } else {
            $this->authService = $apiAuthService;
        }
    }

    /**
     * @throws Core_Verbose_Exception
     */
    public function init()
    {
        parent::_init();
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
            $this->module,
            $this->req_url,
            $this->req_method
        );

        if (empty($controller_method_array)) {
            throw new Core_Exception(400); // Bad Request
        }

        // Check Authorizations

        if ($this->authService->login() === FALSE) {
            throw new Core_Exception(403); // Forbidden
        }

        // Update User Activity
        $this->updateUserActivity();

        if ($this->authService->checkMethodAuthorization($this->module, $controller_method_array['method']) === TRUE) {
            return $this->invoke($controller_method_array, $this->req_content_type); // Invoke
        }

        // Forbidden as default response
        throw new Core_Exception(403);
    }

    /**
     * Answer the RestAPI call with a response formatted to the given content type
     *
     * @param $controller_method_array
     * @param string $content_type
     * @return int
     */
    private function invoke($controller_method_array, $content_type = "application/json")
    {
        // Call The Method
        // And Return The Media Response

        // TODO : format given a content type

        $media = Core_API::callAPIControllerMethod($this->module, $controller_method_array, $this->req_params);

        header('Content-Type: ' . $media['content-type'] . '; charset=utf-8');
        echo $media['data'];
        return 0;
    }

    /**
     * Given a Module, URL and an HTTP Method
     * Returns the Method with its associated Controller
     *
     * @param $module
     * @param $url
     * @param $http_method
     * @return array
     */
    public static function resolveAPIRequest( $module, $url, $http_method ) {

        $request_method = array();
        $api_schema = array();

        // Get Public Methods
        $methods = Core_Reflection_Helper::getControllerPublicMethods( $module );

        if (!empty($methods)) {

            foreach ($methods as $key => $value) {

                list($module, $method) = explode(".", $value['method']);
                $module = strtolower($module);

                $reflectedMethod = Core_Reflection_Helper::getControllerMethod($module, $method);

                // The ending slash of a collection is always omitted
                // when the resource is called.
                // We delete the ending slash if any in order to avoid bad resolution
                // in the next step (#Resolve).

                if (substr($reflectedMethod['resource'], -1) == '/') {
                    $reflectedMethod['resource'] = substr($reflectedMethod['resource'], 0, -1);
                }

                $api_schema[$reflectedMethod['resource']][$reflectedMethod['name']] = array(
                    $reflectedMethod['id'] => $reflectedMethod['params']
                );
            }
        }

        // Get Resource
        $path = parse_url($url, PHP_URL_PATH);
        $resource = str_replace('/api/' . $api_version . '/', '', $path);

        // #Resolve
        if (!empty($api_schema[$resource][$http_method])) {

            $resource = $api_schema[$resource][$http_method];

            foreach ($resource as $key => $value) {

                $request_method['method'] = $key;
                $request_method['args'] = $value;
            }
        }

        return $request_method;
    }
}