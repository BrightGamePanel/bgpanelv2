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
class BGP_GUI_Application extends BGP_Abstract_Application
{

    /**
     * BGP_GUI_Application constructor.
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

        // User Authentication Service
        $this->authService = Core_AuthService_GUI::getService();
    }

    /**
     * Execute the Query
     *
     * @return int
     */
    public function execute()
    {
        // Verify Execution Context

        if (!$this->check() ) {
            return 1;
        }

        // Resolve Request
    }

    /**
     * Render HTML Pages
     */
    private function gui() {
        $authService = Core_AuthService::getAuthService();

        if ($authService->isSignedIn() == FALSE) {

            // The user is not logged in

            Core_AuthService::logout(); // Force logout

            if (!empty($module) && $module != 'login') {

                // Redirect to login form

                if (BASE_URL != '/') {
                    $return = str_replace(BASE_URL, '', REQUEST_URI);
                } else {
                    $return = substr(REQUEST_URI, 1);
                }
                $return = str_replace('index.php', 'dashboard', $return);
                Flight::redirect('/login?page=' . $return);
            }

            // Login

            switch (Flight::request()->method) {
                case 'GET':

                    // Process Task Query Parameter
                    $task = Flight::request()->query['task'];

                    // Forgot passwd? Page
                    if (!empty($page) && $page == 'password') {

                        bgp_safe_require(MODS_DIR . '/login/login.password.php');
                    } // Login Controller
                    else if (!empty($page) && $page == 'process' && !empty($task)) {

                        bgp_safe_require(MODS_DIR . '/login/login.process.php');
                    } // Login View
                    else {

                        bgp_safe_require(MODS_DIR . '/login/login.php');
                    }
                    break;

                case 'POST':

                    // Login Controller
                    bgp_safe_require(MODS_DIR . '/login/login.process.php');

                    break;

                default:
                    Flight::redirect('/400');
            }
        } else {

            // The user is already logged in

            if (empty($module) || $module == 'login' || $module == 'index.php') {

                // Redirect to the Dashboard

                Flight::redirect('/dashboard/');
            } else if (!empty($module)) {

                // NIST Level 2 Standard Role Based Access Control Library

                $rbac = new PhpRbac\Rbac();

                $resource = ucfirst($module) . '/';

                if (!empty($page)) {
                    $resource = ucfirst($module) . '/' . $page . '/';
                }

                $resource = preg_replace('#(\/+)#', '/', $resource);

                // MAINTENANCE CHECK

                if (boolval(BGP_MAINTENANCE_MODE) === TRUE && ($rbac->Users->hasRole('root', $authService->getSessionInfo('ID')) === FALSE)) {
                    Core_AuthService::logout();
                    Flight::redirect('/503');
                }

                // DROP API USERS

                if ($rbac->Users->hasRole('api', $authService->getSessionInfo('ID')) && ($rbac->Users->hasRole('root', $authService->getSessionInfo('ID')) === FALSE)) {
                    Core_AuthService::logout();
                    Flight::redirect('/403');
                }

                // Verify User Authorization On The Requested Resource
                // Root Users Can Bypass

                if ($rbac->Users->hasRole('root', $authService->getSessionInfo('ID')) || $rbac->check($resource, $authService->getSessionInfo('ID'))) {

                    switch (Flight::request()->method) {
                        case 'GET':
                            // Process Task Query Parameter
                            $task = Flight::request()->query['task'];

                            // Page
                            if (!empty($page)) {

                                bgp_safe_require(MODS_DIR . '/' . $module . '/' . $module . '.' . $page . '.php');
                            } // Controller
                            else if (!empty($page) && $page == 'process' && !empty($task)) {

                                // Verify User Authorization On The Called Method

                                $resourcePerm = ucfirst($module) . '.' . $task;

                                if ($rbac->Users->hasRole('root', $authService->getSessionInfo('ID')) || $rbac->check($resourcePerm, $authService->getSessionInfo('ID'))) {

                                    bgp_safe_require(MODS_DIR . '/' . $module . '/' . $module . '.process.php');
                                } else {
                                    Flight::redirect('/401');
                                }
                            } // Module Page
                            else {

                                bgp_safe_require(MODS_DIR . '/' . $module . '/' . $module . '.php');
                            }
                            break;

                        case 'POST':
                        case 'PUT':
                        case 'DELETE':
                            // Controller
                            $task = Flight::request()->data->task;

                            // Verify User Authorization On The Called Method

                            $resourcePerm = ucfirst($module) . '.' . $task;

                            if ($rbac->Users->hasRole('root', $authService->getSessionInfo('ID')) || $rbac->check($resourcePerm, $authService->getSessionInfo('ID'))) {

                                bgp_safe_require(MODS_DIR . '/' . $module . '/' . $module . '.process.php');
                            } else {
                                Flight::redirect('/401');
                            }
                            break;

                        default:
                            Flight::redirect('/400');
                    }
                } else {
                    Flight::redirect('/401');
                }
            }
        }
    }
}

/**
 * Secure require_once alias for the routing component of the system
 * Protect class files and xml files from being called directly
 */
/*
function bgp_safe_require( $BGP_REQUIRED_PATH ) {
    if ( file_exists( $BGP_REQUIRED_PATH ) ) {
        $path_parts = pathinfo( $BGP_REQUIRED_PATH );
        if ( ($path_parts['extension'] == 'php') && (strpos($path_parts['dirname'], '.class') === FALSE) ) {
            unset( $path_parts );
            require_once( $BGP_REQUIRED_PATH );
            return 0;
        }
    }

    Flight::notFound();
}
*/