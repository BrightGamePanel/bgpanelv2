<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 30/11/2017
 * Time: 14:46
 */

/**
 * Application Wrapper
 */
class BGP_API_Application extends BGP_Abstract_Application
{

    /**
     * BGP_API_Application constructor.
     *
     * @param $module
     * @param $page
     * @param $id
     * @param $api_version
     * @param $content_type
     */
    public function __construct($module, $page, $id, $api_version, $content_type)
    {
        parent::__construct($module, $page, $id, $api_version, $content_type);

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
     * Execute the Query
     *
     * @return int
     */
    public function execute()
    {
        // Is enable ?

        if (boolval(APP_API_ENABLE) === FALSE || boolval(BGP_MAINTENANCE_MODE) === TRUE) {
            return 503; // Service Unavailable
        }

        // Is over HTTPS enable or explicitly allow unsecured HTTP ?

        if ((Flight::request()->secure === FALSE) AND (boolval(APP_API_ALLOW_UNSECURE) === FALSE)) {
            return 418; // Unsecured
        }

        // Resolve Request

        $controller_method_array = Core_API::resolveAPIRequest($this->module,
            $this->req_url,
            $this->req_method);

        if (empty($controller_method_array)) {
            return 400; // Bad Request
        }

        // Check Authorizations

        if ($this->authService->login() === FALSE) {
            return 403; // Forbidden
        }

        if ($this->authService->checkMethodAuthorization($this->module, $controller_method_array['method']) === TRUE) {
            return $this->invoke($controller_method_array, $this->req_content_type); // Invoke
        }

        return 403;  // Forbidden as default response
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

        $media = Core_API::callAPIControllerMethod($this->module, $controller_method_array, $this->req_params);

        header('Content-Type: ' . $media['content-type'] . '; charset=utf-8');
        echo $media['data'];
        return 0;
    }
}