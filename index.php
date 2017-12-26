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

define('LICENSE', 'GNU GENERAL PUBLIC LICENSE - Version 3, 29 June 2007');

/**
 * Bright Game Panel Initialization
 */
require('init.php');

/**
 * CLI Mode
 */
if (PHP_SAPI == "cli") {

    // Todo : must be implemented
    // See argv : http://php.net/manual/fr/function.getopt.php

    // $return_code = BGP_Bootstrap::start($module, $page, $id, $api_version);
    // exit($return_code);

    echo 'Todo: Must Be Implemented';
    exit(0);
}

/**
 * HTTP Mode
 * Flight FW Routing Definitions
 */
require( LIBS_DIR	. '/flight/Flight.php' );

// HTTP status codes VIEW
Flight::route('/@http:[0-9]{3}', function( $http ) {
    header(Core_Http_Status_Codes::httpHeaderFor($http));
    echo Core_Http_Status_Codes::getMessageForCode($http);
    exit(0);
});

// Install Wizard Route
Flight::route('GET|POST /wizard(/@page)', function( $page ) {

    ob_start();

    try {
        BGP_Launcher::start('wizard', $page, 0);
    }
    catch (Exception $e) {
        ob_end_clean();
        $code = ($e->getCode() == 1) ? 500 : $e->getCode(); // 500 Internal Server Error

        header(Core_Http_Status_Codes::httpHeaderFor($code));

        if ((int)ini_get('display_errors') === 1) {
            Flight::error($e);
        } else {
            echo Core_Http_Status_Codes::getMessageForCode($code);
        }

        exit($e->getCode());
    }

    header(Core_Http_Status_Codes::httpHeaderFor(200)); // 200 OK
    ob_end_flush();
    exit(0);
});

// RestAPI ENDPOINT Route
Flight::route('GET|POST|PUT|DELETE /api/@api_version(/@module(/@page(/@id)))', function( $api_version, $module, $page, $id ) {

    ob_start();

    try {
        BGP_Launcher::start($module, $page, $id, $api_version);
    }
    catch (Exception $e) {
        ob_end_clean();
        $code = ($e->getCode() == 1) ? 500 : $e->getCode(); // 500 Internal Server Error

        header(Core_Http_Status_Codes::httpHeaderFor($code));

        exit($e->getCode());
    }

    header(Core_Http_Status_Codes::httpHeaderFor(200)); // 200 OK
    ob_end_flush();
    exit(0);
});

// Default Route
Flight::route('GET|POST|PUT|DELETE (/@module(/@page(/@id)))', function( $module, $page, $id ) {

    ob_start();

    try {
        BGP_Launcher::start($module, $page, $id);
    }
    catch (Exception $e) {
        ob_end_clean();
        $code = ($e->getCode() == 1) ? 500 : $e->getCode(); // 500 Internal Server Error

        header(Core_Http_Status_Codes::httpHeaderFor($code));

        if ((int)ini_get('display_errors') === 1) {
            Flight::error($e);
        } else {
            echo Core_Http_Status_Codes::getMessageForCode($code);
        }

        exit($e->getCode());
    }

    header(Core_Http_Status_Codes::httpHeaderFor(200)); // 200 OK
    ob_end_flush();
    exit(0);
});

/**
 * Start Bright Game Panel
 */
Flight::start();