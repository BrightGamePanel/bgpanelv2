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

	$authService = Core_AuthService::getAuthService();

	// Test if the user has a whitecard to access the system

	if ($authService->getSessionValidity() == FALSE) {

		// The user is not logged in
		// Redirect him to the login system

		Flight::redirect('/login');
	}

	// Redirect to the Dashboard
	switch (Core_AuthService::getSessionPrivilege()) {
		case 'Admin':
			Flight::redirect('/admin/dashboard');

		case 'User':
			Flight::redirect('/user/dashboard');
		
		default:
			// Invalid Privilege
			Core_AuthService::logout();
			Flight::redirect('/login');
	}
});



// HTTP status codes VIEW
Flight::route('GET|POST /@http:[0-9]{3}', function( $http ) {
	header( Core_Http_Status_Codes::httpHeaderFor( $http ) );
	echo Core_Http_Status_Codes::getMessageForCode( $http );
	die();
});



// [COMMON] LOGOUT METHOD
Flight::route('GET /logout', function() {
	$authService = Core_AuthService::getAuthService();

	if ($authService->getSessionValidity() == TRUE) {
		Core_AuthService::logout();
		Flight::redirect('/login');
	}

	die();
});



// [LOGIN] VIEW
Flight::route('GET /login', function() {
	$mod_path = MODS_DIR . '/login/login.php';
	bgp_routing_require_mod( $mod_path );
});

// [LOGIN] CONTROLLER
Flight::route('POST /login/process', function() {
	$mod_path = MODS_DIR . '/login/login.process.php';
	bgp_routing_require_mod( $mod_path );
});



// [ADMIN] Dynamically load the module VIEW
Flight::route('GET /admin/@module', function( $module ) {

	// Test Access Perms
	if ( Core_AuthService::isAdmin() ) {
		$mod_path = MODS_DIR . '/' . 'admin.' . $module . '/' . 'admin.' . $module . '.php';
		bgp_routing_require_mod( $mod_path );
	}
	else{
		Flight::redirect('/403');
	}
});

// [ADMIN] CONTROLLER
Flight::route('POST /admin/@module/process', function( $module ) {

	// Test Access Perms
	if ( Core_AuthService::isAdmin() ) {
		$mod_path = MODS_DIR . '/' . 'admin.' . $module . '/' . 'admin.' . $module . '.process.php';
		bgp_routing_require_mod( $mod_path );
	}
	else{
		Flight::redirect('/403');
	}
});



// [USER] VIEW
Flight::route('GET /user/@module', function( $module ) {

	// Test Access Perms
	if ( Core_AuthService::isUser() ) {
		$mod_path = MODS_DIR . '/' . 'user.' . $module . '/' . 'user.' . $module . '.php';
		bgp_routing_require_mod( $mod_path );
	}
	else{
		Flight::redirect('/403');
	}
});

// [USER] CONTROLLER
Flight::route('POST /user/@module/process', function( $module ) {

	// Test Access Perms
	if ( Core_AuthService::isUser() ) {
		$mod_path = MODS_DIR . '/' . 'user.' . $module . '/' . 'user.' . $module . '.process.php';
		bgp_routing_require_mod( $mod_path );
	}
	else{
		Flight::redirect('/403');
	}
});



// [COMMON] VIEW (with authentication)
Flight::route('GET /@module', function( $module ) {
	$authService = Core_AuthService::getAuthService();

	// Test Access Perms
	if ($authService->getSessionValidity() == TRUE) {
		$mod_path = MODS_DIR . '/' . $module . '/' . $module . '.php';
		bgp_routing_require_mod( $mod_path );
	}
	else{
		Flight::redirect('/403');
	}
});

// [COMMON] CONTROLLER (with authentication)
Flight::route('POST /@module/process', function( $module ) {
	$authService = Core_AuthService::getAuthService();

	// Test Access Perms
	if ($authService->getSessionValidity() == TRUE) {
		$mod_path = MODS_DIR . '/' . $module . '/' . $module . '.process.php';
		bgp_routing_require_mod( $mod_path );
	}
	else{
		Flight::redirect('/403');
	}
});



/**
 * Start the FW
 */

Flight::start();
