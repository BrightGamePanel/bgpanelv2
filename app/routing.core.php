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



// DEFAULT BEHAVIOUR
Flight::route('GET|POST|PUT|DELETE (/@module(/@page(/@element)))', function( $module, $page, $element ) {

	// User Authentication

	$authService = Core_AuthService::getAuthService();

	// Test if the user is allowed to access the system

	if ($authService->getSessionValidity() == FALSE) {

		// The user is not logged in

		switch (Flight::request()->method)
		{
			case 'GET':
				// Forgot passwd? Page
				if ( !empty($page) && $page == 'password' ) {
					$mod_path = MODS_DIR . '/login/login.password.php';
				}
				// Login View
				else {
					$mod_path = MODS_DIR . '/login/login.php';
				}
				break;

			case 'POST':
				// Login Controller
					$mod_path = MODS_DIR . '/login/login.process.php';
				break;

			default:
				break;
		}

		bgp_routing_require_mod( $mod_path );
	}

	// The user is already logged in

	if (empty($module))	{

		// Redirect to the Dashboard

		Flight::redirect('/dashboard/');
	}
	else {

		// Check User Permissions 

		exit('auth');

		// Done

		// element
/*
		// MAINTENANCE CHECKER
		// Logout the user
		if ( BGP_MAINTENANCE_MODE == 1 ) {
			Core_AuthService::logout();
			Flight::redirect('/503'); // If the maintenance mode is ON, we drop the user.
		}

		// Update User Acivity
		bgp_routing_update_user_activity( 'User' );

		if ( !empty($page) ) {
			$mod_path = MODS_DIR . '/' . 'user.' . $module . '/' . 'user.' . $module . '.' . $page . '.php';
		}
		else {
			$mod_path = MODS_DIR . '/' . 'user.' . $module . '/' . 'user.' . $module . '.php';
		}

		bgp_routing_require_mod( $mod_path );

		if ( Core_AuthService::isAdmin() ) {
			// Forbidden
			Flight::redirect('/403');
		}
		else {
			$return = '/' . str_replace( BASE_URL, '', REQUEST_URI );
			Flight::redirect( '/login?page=' . $return );
		}
*/

	}
});



// HTTP status codes VIEW
Flight::route('GET|POST|PUT|DELETE /@http:[0-9]{3}', function( $http ) {
	header( Core_Http_Status_Codes::httpHeaderFor( $http ) );
	echo Core_Http_Status_Codes::getMessageForCode( $http );
	die();
});



// LOGOUT METHOD
Flight::route('/logout/', function() {
	$authService = Core_AuthService::getAuthService();

	if ($authService->getSessionValidity() == TRUE) {
		Core_AuthService::logout();
		Flight::redirect('/login/');
	}

	die();
});



/**
 * Start the FW
 */

Flight::start();
