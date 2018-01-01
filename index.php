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

require('Init.php');
Init::initialize();

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

// RestAPI ENDPOINT Route
Flight::route('GET|POST|PUT|DELETE /api/@api_version(/@module(/@page(/@id)))', function( $api_version, $module, $page, $id ) {

    ob_start();

    try {
        $return_code = Core_Launcher::start($module, $page, $id, $api_version);
    }
    catch (Core_Exception $e) {
        ob_end_clean();
        $e->sendHeader();
        exit($e->getCode());
    }
    catch (Exception $e) {
        ob_end_clean();
        header('500 Internal Server Error');
        exit(1);
    }

    // 200 OK
    header('200 OK');
    ob_end_flush();

    exit($return_code);
});

// Default Route
Flight::route('GET|POST|PUT|DELETE (/@module(/@page(/@id)))', function( $module, $page, $id ) {

    ob_start();

    try {
        $return_code = Core_Launcher::start($module, $page, $id);
    }
    catch (Core_Exception $e) {
        ob_end_clean();

        $e->sendHeader();
        if ((int)ini_get('display_errors') === 1) {
            echo $e;
        }

        exit($e->getCode());
    }
    catch (Exception $e) {
        ob_end_clean();

        if ((int)ini_get('display_errors') === 1) {
            Flight::error($e);
        } else {
            header('500 Internal Server Error');
        }

        exit(1);
    }

    // 200 OK
    header('200 OK');
    ob_end_flush();

    exit($return_code);
});

/**
 * Bright Game Panel Startup
 */

Flight::start();