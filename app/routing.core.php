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
 * @copyright	Copyleft 2014, Nikita Rousseau
 * @license		GNU General Public License version 3.0 (GPLv3)
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

	// The user is already logged in
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



// [LOGIN] MODULE
Flight::route('GET|POST /login(/@page)', function( $page ) {

	$authService = Core_AuthService::getAuthService();

	if ($authService->getSessionValidity() == TRUE) {
		// The user is already logged in

		Flight::redirect('/');
	}
	else {

		// Forgot passwd? Page
		if ( !empty($page) && $page == 'password' ) {
			$mod_path = MODS_DIR . '/login/login.password.php';
		}

		// Login Controller
		else if ( !empty($page) && $page == 'process' ) {
			$mod_path = MODS_DIR . '/login/login.process.php';
		}

		// Login View
		else {
			$mod_path = MODS_DIR . '/login/login.php';
		}

		bgp_routing_require_mod( $mod_path );
	}
});



// Dynamically load the module VIEW | CONTROLLER
// Note that the page "process" is the module controller
Flight::route('GET|POST /@role(/@module(/@page))', function( $role, $module, $page ) {

	switch ($role)
	{
		// [ADMIN]
		case 'admin':

			// Test Access Perms
			if ( Core_AuthService::isAdmin() && !empty($module) )
			{
				// Switch the view depending the task
				if ( !empty($page) ) {
					// Admin Controller OR subPage Invoked
					$mod_path = MODS_DIR . '/' . 'admin.' . $module . '/' . 'admin.' . $module . '.' . $page . '.php';
				}
				else {
					// Admin View Invoked
					$mod_path = MODS_DIR . '/' . 'admin.' . $module . '/' . 'admin.' . $module . '.php';
				}

				// Call the module
				bgp_routing_require_mod( $mod_path );
			}
			else if ( Core_AuthService::isUser() ) {
				// A regular user has tried to access admin components
				// Forbidden
				Flight::redirect('/403');
			}
			else {
				$return = '/' . str_replace( BASE_URL, '', REQUEST_URI );
				Flight::redirect( '/login?page=' . $return );
			}
			break;

		// [USER]
		case 'user':

			if ( Core_AuthService::isUser() && !empty($module) )
			{

				// MAINTENANCE CHECKER
				// Logout the user
				if ( BGP_MAINTENANCE_MODE == 1 ) {
					Core_AuthService::logout();
					Flight::redirect('/503'); // If the maintenance mode is ON, we drop the user.
				}

				if ( !empty($page) ) {
					$mod_path = MODS_DIR . '/' . 'user.' . $module . '/' . 'user.' . $module . '.' . $page . '.php';
				}
				else {
					$mod_path = MODS_DIR . '/' . 'user.' . $module . '/' . 'user.' . $module . '.php';
				}

				bgp_routing_require_mod( $mod_path );
			}
			else if ( Core_AuthService::isAdmin() ) {
				// Forbidden
				Flight::redirect('/403');
			}
			else {
				$return = '/' . str_replace( BASE_URL, '', REQUEST_URI );
				Flight::redirect( '/login?page=' . $return );
			}
			break;

		// [COMMON]
		default:

			// Switch the vars
			if (!empty($module)) {
				$page = $module;
			}
			$module = $role;
			unset($role);

			// MAINTENANCE CHECKER
			if ( Core_AuthService::getSessionPrivilege() != 'Admin' ) {
				if ( BGP_MAINTENANCE_MODE == 1 ) {
					Core_AuthService::logout();
					Flight::redirect('/503');
				}	
			}

			$authService = Core_AuthService::getAuthService();

			if ( $authService->getSessionValidity() == TRUE && !empty($module) )
			{
				if ( !empty($page) ) {
					$mod_path = MODS_DIR . '/' . $module . '/' . $module . '.' . $page . '.php';
				}
				else {
					$mod_path = MODS_DIR . '/' . $module . '/' . $module . '.php';
				}

				bgp_routing_require_mod( $mod_path );
			}
			else {
				$return = '/' . str_replace( BASE_URL, '', REQUEST_URI );
				Flight::redirect( '/login?page=' . $return );
			}
			break;
	}
});



/**
 * Start the FW
 */

Flight::start();
