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
 * @categories	Games/Entertainment, Systems Administration
 * @package		Bright Game Panel V2
 * @author		warhawk3407 <warhawk3407@gmail.com> @NOSPAM
 * @copyleft	2014
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @version		0.1
 * @link		http://www.bgpanel.net/
 */

// Prevent direct access
if (!defined('LICENSE'))
{
	exit('Access Denied');
}

if ( !class_exists('Flight')) {
	trigger_error('Core -> Flight FW is missing !');
}

/**
 * Flight FW Routing Definitions
 */

// DEFAULT
Flight::route('GET|POST /', function() {
	// User Authentication

	$authService = new Core_AuthService();

	// Test if the user has a whitecard to access the system

	if ($authService->getSessionValidity() == FALSE) {

		// The user is not logged in
		// Redirect him to the login system

		Flight::redirect('/login');
	}

	Flight::redirect('/dashboard');
});

// HTTP status codes VIEW
Flight::route('GET|POST /@http:[0-9]{3}', function( $http ) {
	echo Core_Http_Status_Codes::httpHeaderFor( $http );
	echo Core_Http_Status_Codes::getMessageForCode( $http );
	die();
});

// Dynamically load the module VIEW
Flight::route('GET /@module', function( $module ) {
	$mod_path = MODS_DIR . '/' . $module . '/' . $module . '.php';
	if ( file_exists($mod_path) ) {
		require( $mod_path );
	}
	else {
		Flight::notFound();
	}
});

// Dynamically load the module CONTROLLER
Flight::route('POST /@module/process', function( $module ) {
	$mod_path = MODS_DIR . '/' . $module . '/' . $module . '.process.php';
	if ( file_exists($mod_path) ) {
		require( $mod_path );
	}
	else {
		Flight::notFound();
	}
});

/**
 * Start the FW
 */

Flight::start();