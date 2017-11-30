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
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the Query
     *
     * @param $module
     * @param $page
     * @param $id
     * @param $api_version
     * @param array $http_headers
     * @return int
     */
    public function execute($module, $page, $id, $api_version, $http_headers = array())
    {

        // Initialize

        parent::execute($module, $page, $id, $api_version, $http_headers);
        unset($module, $page, $id, $api_version, $http_headers);

        // Verify Execution Context

        if (!$this->check() ) {
            return 1;
        }

        // Resolve Request

        $controller_method_array = Core_API::resolveAPIRequest($this->module, $this->req_url, $this->req_method, $this->api_version);
        if (empty($controller_method_array)) {

            // Bad Request

            header(Core_Http_Status_Codes::httpHeaderFor(400));
            return 1;
        }

        // Check Authorizations

        $rbac = new PhpRbac\Rbac();
        $uid = $this->authService->getSessionInfo('ID');
        $permPath = Core_AuthService_Perms::buildPermissionPath($this->module, $controller_method_array['method']);

        // Are you root or do you have explicitly rights on this resource ?

        if ($rbac->Users->hasRole('root', $uid) || $rbac->check($permPath, $uid)) {

            // Invoke

            return $this->invoke($controller_method_array, $this->http_headers['CONTENT-TYPE']);
        }

        // Forbidden as default response

        header(Core_Http_Status_Codes::httpHeaderFor(403));
        return 1;
    }

    /**
     * Verify Execution Context
     * @return bool
     */
    private function check() {

        // Is enable ?

        if (boolval(APP_API_ENABLE) === FALSE || boolval(BGP_MAINTENANCE_MODE) === TRUE) {

            // Service Unavailable

            header(Core_Http_Status_Codes::httpHeaderFor(503));
            session_destroy();
            return false;
        }

        // Is over HTTPS enable or explicitly allow unsecure HTTP ?

        if ((Flight::request()->secure === FALSE) AND (boolval(APP_API_ALLOW_UNSECURE) === FALSE)) {

            // Unsecure

            header(Core_Http_Status_Codes::httpHeaderFor(418));
            session_destroy();
            return false;
        }

        return true;
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

        header('Content-Type: ' . $content_type . '; charset=utf-8');
        echo $media['data'];
        return 0;
    }
}