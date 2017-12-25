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
 * Bright Game Panel Init
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
Flight::route('GET|POST /wizard(/@page(/@id))', function( $page, $id ) {
    $return_code = BGP_Bootstrap::start('install', $page, $id);
    if ($return_code === 0 || $return_code === 200) {
        header(Core_Http_Status_Codes::httpHeaderFor(200)); // 200 OK
    } else if ($return_code === 1 || $return_code === 500) {
        header(Core_Http_Status_Codes::httpHeaderFor(500)); // 500 Internal Server Error
        echo Core_Http_Status_Codes::getMessageForCode(500);
    } else {
        header(Core_Http_Status_Codes::httpHeaderFor($return_code));
        echo Core_Http_Status_Codes::getMessageForCode($return_code);
    }
    exit($return_code);
});

// RestAPI ENDPOINT Route
Flight::route('GET|POST|PUT|DELETE /api/@api_version(/@module(/@page(/@id)))', function( $api_version, $module, $page, $id ) {
    ob_start();
    $return_code = BGP_Bootstrap::start($module, $page, $id, $api_version);
    if ($return_code === 0 || $return_code === 200) {
        header(Core_Http_Status_Codes::httpHeaderFor(200)); // 200 OK
        ob_end_flush();
    } else if ($return_code === 1 || $return_code === 500) {
        ob_end_clean();
        header(Core_Http_Status_Codes::httpHeaderFor(500)); // 500 Internal Server Error
    } else {
        ob_end_clean();
        header(Core_Http_Status_Codes::httpHeaderFor($return_code));
    }
    exit($return_code);
});

// Default Route
Flight::route('GET|POST|PUT|DELETE (/@module(/@page(/@id)))', function( $module, $page, $id ) {
    ob_start();
    $return_code = BGP_Bootstrap::start($module, $page, $id);
    if ($return_code === 0 || $return_code === 200) {
        header(Core_Http_Status_Codes::httpHeaderFor(200)); // 200 OK
        ob_end_flush();
    } else if ($return_code === 1 || $return_code === 500) {
        ob_end_clean();
        header(Core_Http_Status_Codes::httpHeaderFor(500)); // 500 Internal Server Error
        echo Core_Http_Status_Codes::getMessageForCode(500);
    } else {
        ob_end_clean();
        header(Core_Http_Status_Codes::httpHeaderFor($return_code));
        echo Core_Http_Status_Codes::getMessageForCode($return_code);
    }
    exit($return_code);
});

/**
 * Start Bright Game Panel
 */
Flight::start();