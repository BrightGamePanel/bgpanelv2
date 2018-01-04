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
 * Abstract Application Wrapper
 */
abstract class Core_Abstract_Application implements Core_Application_Interface
{
    /**
     * Module Handle
     *
     * @var Core_Module_Interface
     */
    protected $module_handle = null;

    // Request Attributes
    protected $page = '';
    protected $id = 0;

    // HTTP Request Attributes
    protected $req_url = '';
    protected $req_method = '';
    protected $req_content_type = '';
    protected $req_params = array();

    /**
     * Core Authentication Service Handle
     *
     * @var Core_Auth_Service_Interface
     */
    protected $authentication_service = null;

    /**
     * BGP_Application constructor.
     *
     * @param string $module
     * @param string $page
     * @param integer $id
     * @param string $http_accept
     * @throws Core_Exception
     * @throws Core_Verbose_Exception
     */
    public function __construct($module, $page, $id, $http_accept = 'application/json')
    {
        // Create Service
        // for this application
        Services::createServices($this);

        // Application Initialization

        if (preg_match("#\w#", $module)) {

            // Module object

            $module_class = ucfirst(strtolower($module));
            spl_autoload_call($module_class);
            if (!class_exists($module_class)) {
                throw new Core_Verbose_Exception(
                    '404 Not Found',
                    'Module not loaded : ' . $module,
                    'Class `' . $module_class . '` not found.'
                );
            }

            $this->module_handle = new $module_class();
        }

        if (preg_match("#\w#", $page)) {
            $this->page = strtolower($page);
        }

        if (is_numeric($id)) {
            $this->id = $id;
        }

        // Sanitized Requested Content Type
        $this->req_content_type = $http_accept;

        // Request Information
        $this->req_url = Flight::request()->url;
        $this->req_method = Flight::request()->method;

        // Request Parameters

        if ($this->req_method == 'GET') {

            // Query parameters
            $this->req_params = Flight::request()->query->getData();
        }
        else if ($this->req_method == 'POST') {

            // Post parameters
            if ($this->req_content_type == 'application/json') {

                $plain_body = Flight::request()->getBody();
                $this->req_params = json_decode($plain_body, TRUE);
            }
            else {
                $this->req_params = Flight::request()->data->getData();
            }
        }
        else {

            if ($this->req_content_type == 'application/json') {

                $plain_body = Flight::request()->getBody();
                $this->req_params = json_decode($plain_body, TRUE);
            }
            else {
                throw new Core_Exception(415); // Unsupported Media Type
            }
        }
    }

    public function init() {

        // DEFINE BGP CONSTANTS FROM THE DATABASE
        // Syntax: BGP_{$SETTING}

        $CONFIG = self::getDBConfig();
        foreach ($CONFIG as $row) {
            define( strtoupper( 'BGP_' . $row['setting'] ), $row['value'] );
        }
        // Complete missing constants (if any)
        Core_Defaults::initialize();

        // VERSION CONTROL
        // Check that core files are compatible with the current BrightGamePanel Database

        if ( !defined('BGP_PANEL_VERSION') || !defined('BGP_API_VERSION')) {

            throw new Core_Verbose_Exception(
                'Undefined Panel Version',
                '',
                'Unable to read panel version from the database.'
            );
        }

        $fwVersion = self::getFilesVersion();
        if ( (BGP_PANEL_VERSION != $fwVersion['CORE_VERSION']) || (BGP_API_VERSION != $fwVersion['API_VERSION']) ) {

            throw new Core_Verbose_Exception(
                'Wrong Database Version Detected',
                '',
                'Make sure you have followed the instructions to install/update the database and check that you are running a compatible MySQL Server.'
            );
        }

        // SESSION HANDLER

        require( APP_DIR . '/core/session/Core_SessionHandler.php' );
        $coreSessionHandler = new Core_SessionHandler();
        session_set_save_handler($coreSessionHandler, TRUE);

        // DISPLAY LANGUAGE

        $lang = CONF_DEFAULT_LOCALE;
        if ( isset($_COOKIE['LANG']) ) {
            $lang = $_COOKIE['LANG'];
        }
        Services::getLanguageService()->setLanguage($lang);

        // VALITRON Configuration
        // Valitron is a simple, minimal and elegant stand-alone validation library with NO dependencies
        //
        // https://github.com/vlucas/valitron#usage

        $lang = substr($lang, 0, 2);
        Valitron\Validator::langDir( LIBS_DIR . '/valitron/lang' );
        Valitron\Validator::lang( $lang );

        /**
         * ROUTING Configuration
         * FlightPHP configuration
         *
         * flight.base_url - Override the base url of the request. (default: null)
         * flight.handle_errors - Allow Flight to handle all errors internally. (default: true)
         * flight.log_errors - Log errors to the web server's error log file. (default: false)
         * flight.views.path - Directory containing view template files (default: ./views)
         *
         * @link http://flightphp.com/learn#configuration
         */

        Flight::set('flight.handle_errors', TRUE);
        Flight::set('flight.log_errors', FALSE);
    }

    public function execute()
    {
        $this->updateUserActivity();
    }

    public function getAuthenticationService()
    {
        return $this->authentication_service;
    }

    /**
     * Update User Activity
     * Must be added to any execute() method stub of child classes
     *
     * @return void
     */
    private function updateUserActivity() {

        if ($this->authentication_service == null || $this->authentication_service->isLoggedIn() === FALSE) {
            return;
        }

        $dbh = Core_Database_Service::getDBH();

        try {
            $sth = $dbh->prepare("
                        UPDATE user
                        SET
                            last_activity	= :last_activity
                        WHERE
                            user_id			= :user_id
                        ;");

            $sth->bindParam(':last_activity', date('Y-m-d H:i:s'));
            $sth->bindParam(':user_id', $this->authentication_service->getUid());

            $sth->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
            die();
        }
    }

    /**
     * Reads framework version from files
     * Loads `version.xml` (app/version/version.xml)
     *
     * @return array
     */
    public static function getFilesVersion() {

        $bgpCoreInfo = simplexml_load_file( CORE_VERSION_FILE );

        return array(
            'API_VERSION' => $bgpCoreInfo->{'api_version'},
            'CORE_VERSION' => $bgpCoreInfo->{'version'}
        );
    }

    /**
     * Reads framework configuration from the database
     *
     * @return array
     */
    private static function getDBConfig() {

        $dbh = Core_Database_Service::getDBH();
        $ret = array();

        try {
            $sth = $dbh->prepare("
            SELECT setting, value
            FROM config
            ;");

            $sth->execute();

            $ret = $sth->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
            die();
        }

        return $ret;
    }
}