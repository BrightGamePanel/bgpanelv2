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

final class BGP_Bootstrap
{
    /**
     * BGP_Application main
     *
     * @param $module
     * @param $page
     * @param $id
     * @param $api_version
     * @return int
     */
    public static function start($module, $page, $id, $api_version = null)
    {
        // Check API version
        if (!empty($api_version) && $api_version != self::getFWVersion()['API_VERSION']) {

            // Trigger error when the requested API version
            // is not compatible with the current API version
            // 301 MOVED PERMANENTLY
            return 301;
        }

        // Read HTTP Headers
        $http_headers = array_change_key_case(apache_request_headers(), CASE_UPPER);

        if (!isset($http_headers['CONTENT-TYPE']) ||
            (isset($http_headers['CONTENT-TYPE']) && $http_headers['CONTENT-TYPE'] == "text/html")) {

            if ($module == 'install') {

                // INSTALL WIZARD
                $app = new BGP_Wizard_Application(
                    'install',
                    $page,
                    $id,
                    "text/html"
                );
                return $app->execute();
            } else {

                // GUI
                $app = new BGP_GUI_Application(
                    $module,
                    $page,
                    $id,
                    "text/html"
                );
            }
        } else {

            // RestAPI
            $app = new BGP_API_Application(
                $module,
                $page,
                $id,
                $http_headers['CONTENT-TYPE']
            );
        }

        // Init
        self::init();

        // Execute
        return $app->execute();
    }

    /**
     * Extended Initialization Procedure
     *
     * @return void
     */
    private static function init() {

        // INSTALL WIZARD CHECK

        if ( is_dir( INSTALL_DIR ) ) {
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="utf-8">
            </head>
            <body>
            <h1>Install Directory Detected !</h1><br />
            <h3>FOR SECURITY REASONS PLEASE REMOVE THE `install` DIRECTORY.</h3>
            <p>You will not be able to proceed beyond this point until the installation directory has been removed.</p>
            </body>
            </html>
            <?php
            die();
        }

        // DEFINE BGP CONSTANTS FROM THE DATABASE
        // Syntax: BGP_{$SETTING}

        try {
            $dbh = Core_DBH::getDBH();

            $sth = $dbh->prepare("
            SELECT setting, value
            FROM " . DB_PREFIX . "config
            ;");

            $sth->execute();

            $CONFIG = $sth->fetchAll(PDO::FETCH_ASSOC);

            foreach ($CONFIG as $row) {
                define( strtoupper( 'BGP_' . $row['setting'] ), $row['value'] );
            }
        }
        catch (PDOException $e) {
            echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
            die();
        }

        // VERSION CONTROL
        // Check that core files are compatible with the current BrightGamePanel Database

        if ( !defined('BGP_PANEL_VERSION') || !defined('BGP_API_VERSION')) {
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="utf-8">
            </head>
            <body>
            <h1>Undefined Panel Version</h1><br />
            <h3>&nbsp;</h3>
            <p>Unable to read panel version from the database.</p>
            </body>
            </html>
            <?php
            die();
        }

        $fwVersion = self::getFWVersion();
        if ( (BGP_PANEL_VERSION != $fwVersion['CORE_VERSION']) || (BGP_API_VERSION != $fwVersion['API_VERSION']) ) {
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="utf-8">
            </head>
            <body>
            <h1>Wrong Database Version Detected</h1><br />
            <h3>&nbsp;</h3>
            <p>Make sure you have followed the instructions to install/update the database and check that you are running a compatible MySQL Server</p>
            </body>
            </html>
            <?php
            die();
        }

        // SESSION HANDLER

        require( APP_DIR . '/core/session.class.php' );
        $coreSessionHandler = new Core_SessionHandler();
        session_set_save_handler($coreSessionHandler, TRUE);

        // DISPLAY LANGUAGE

        $lang = CONF_DEFAULT_LOCALE;
        if ( isset($_COOKIE['LANG']) ) {
            $lang = $_COOKIE['LANG'];
        }
        Core_Lang::setLanguage( $lang );

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

    /**
     * Reads framework version from files
     * Loads `version.xml` (app/version/version.xml)
     *
     * @return array
     */
    private static function getFWVersion() {

        $bgpCoreInfo = simplexml_load_file( CORE_VERSION_FILE );

        return array(
            'API_VERSION' => $bgpCoreInfo->{'api_version'},
            'CORE_VERSION' => $bgpCoreInfo->{'version'}
        );
    }
}