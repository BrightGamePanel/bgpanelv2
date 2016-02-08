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


// HTTP status codes VIEW
Flight::route('/@http:[0-9]{3}', function( $http ) {
	header( Core_Http_Status_Codes::httpHeaderFor( $http ) );

	echo Core_Http_Status_Codes::getMessageForCode( $http );

	die();
});


// LOGOUT METHOD
Flight::route('/logout/', function() {
	$authService = Core_AuthService::getAuthService();

	if ($authService->getSessionValidity() == TRUE) {
		Core_AuthService::logout();
	}

	Flight::redirect('/login/');
});


/**
 * MACHINE 2 MACHINE
 */
Flight::route('GET|POST|PUT|DELETE /api/*', function() {

	if (ENV_RUNTIME != 'M2M') {
		header( Core_Http_Status_Codes::httpHeaderFor( 403 ) );
		exit( 1 );
	}

	// API Process

	if (boolval(APP_API_ENABLE) === TRUE)
	{
		if ( Flight::request()->secure || ( boolval(APP_API_ALLOW_UNSECURE) === TRUE ) )
		{

			$url = Flight::request()->url;
			$http_method = Flight::request()->method;
			$params = explode('&', parse_url($url, PHP_URL_QUERY));

			$headers = apache_request_headers();

			if (!empty($headers['X-API-KEY']) AND !empty($headers['X-API-USER']) AND !empty($headers['X-API-PASS']))
			{
				// Machine Authentication

				if (Core_API::checkRemoteHost( Flight::request()->ip, $headers['X-API-KEY'], $headers['X-API-USER'], $headers['X-API-PASS'] ) === TRUE)
				{
					// Resource Access

					if ($http_method === 'GET' && $url === '/api?WADL') 
					{
						// Web Application Description Language (WADL)

						header('Content-Type: application/xml; charset=utf-8');
						echo Core_API::getWADL( );
					}
					else if (strpos($url, '/api/') !== FALSE)
					{
						// Extract Module Name

						$module = strstr($url, '/api/');

						if (strpos($module, '?') !== FALSE) {
							$module = strstr($module, '?', TRUE);
						}

						$module = str_replace('/api/', '', $module);

						if (!empty($module))
						{
							// Resolve Request

							$method = Core_API::resolveAPIRequest( $module, $url, $http_method );

							if (!empty($method))
							{
								$resourcePerm = ucfirst($module) . '/' . $method['method'];

								// Verify Authorizations

								$rbac = new PhpRbac\Rbac();

								if ( $rbac->Users->hasRole( 'root', $_SERVER['PHP_AUTH_USER'] ) || $rbac->check( $resourcePerm, $_SERVER['PHP_AUTH_USER'] ) ) {

									// Call The Method
									// And Return The Media Response

									$media = Core_API::callAPIControllerMethod( $module, $method, $params );

									header('Content-Type: ' . $media['response'] . '; charset=utf-8');
									echo $media['data'];
								}
								else {
									// Forbidden
									header( Core_Http_Status_Codes::httpHeaderFor( 403 ) );
								}
							}
							else {
								// Bad Request
								header( Core_Http_Status_Codes::httpHeaderFor( 400 ) );
							}
						}
						else {
							// Forbidden
							header( Core_Http_Status_Codes::httpHeaderFor( 403 ) );
						}
					}
					else {
						// Forbidden
						header( Core_Http_Status_Codes::httpHeaderFor( 403 ) );
					}
				}
				else {
					// Unauthorized
					header( Core_Http_Status_Codes::httpHeaderFor( 401 ) );
				}
			}
			else {
				// Unauthorized
				header( Core_Http_Status_Codes::httpHeaderFor( 401 ) );
			}
		}
		else {
			// Unsecure
			header( Core_Http_Status_Codes::httpHeaderFor( 418 ) );
		}
	}
	else {
		// Forbidden
		header( Core_Http_Status_Codes::httpHeaderFor( 403 ) );
	}

	exit( 0 );
});


/**
 * HUMAN 2 MACHINE
 * DEFAULT BEHAVIOUR
 */
Flight::route('GET|POST|PUT|DELETE (/@module(/@page)(/@id))', function( $module, $page, $id ) {

	if (ENV_RUNTIME != 'H2M') {
		Flight::redirect('/403');
		exit( 1 );
	}

	// Vars Init

	if (isset($module) && preg_match("#\w#", $module)) {
		$module = strtolower($module);
	} else {
		$module = '';
	}
	if (isset($page) && preg_match("#\w#", $page)) {
		$page = strtolower($page);
	} else {
		$page = '';
	}
	if (isset($id) && is_numeric($id)) {
		Flight::set('RESOURCE_ID', $id);
	}

	// User Authentication

	$authService = Core_AuthService::getAuthService();

	// Test if the user is allowed to access the system

	if ($authService->getSessionValidity() == FALSE) {

		// The user is not logged in

		if (!empty($module) && $module != 'login') {

			// Redirect to login form

			$return = '/' . str_replace( BASE_URL, '', REQUEST_URI );
			$return = str_replace( 'index.php', 'dashboard', $return );
			Flight::redirect( '/login?page=' . $return );
		}

		// Login

		switch (Flight::request()->method)
		{
			case 'GET':

				// Process Task Query Parameter
				$task = Flight::request()->query['task'];

				// Forgot passwd? Page
				if ( !empty($page) && $page == 'password' ) {

					bgp_safe_require( MODS_DIR . '/login/login.password.php' );
				}
				// Login Controller
				else if ( !empty($page) && $page == 'process' && !empty($task) ) {

					bgp_safe_require( MODS_DIR . '/login/login.process.php' );
				}
				// Login View
				else {

					bgp_safe_require( MODS_DIR . '/login/login.php' );
				}
				break;

			case 'POST':

				// Login Controller
				bgp_safe_require( MODS_DIR . '/login/login.process.php' );

				break;

			default:
				Flight::redirect('/400');
		}
	}
	else {

		// The user is already logged in

		if (empty($module) || $module == 'login' || $module == 'index.php')	{

			// Redirect to the Dashboard

			Flight::redirect('/dashboard/');
		}
		else if (!empty($module)) {

			// NIST Level 2 Standard Role Based Access Control Library

			$rbac = new PhpRbac\Rbac();

			$resource = ucfirst($module) . '/';

			if (!empty($page)) {
				$resource = ucfirst($module) . '/' . $page . '/';
			}

			$resource = preg_replace('#(\/+)#', '/', $resource);

			// MAINTENANCE CHECK

			if ( BGP_MAINTENANCE_MODE == 1 && ($rbac->Users->hasRole( 'root', $authService->getSessionInfo('ID') ) === FALSE) ) {
				Core_AuthService::logout();
				Flight::redirect('/503');
			}

			// DROP API USERS

			if ( $rbac->Users->hasRole( 'api', $authService->getSessionInfo('ID') ) && ($rbac->Users->hasRole( 'root', $authService->getSessionInfo('ID') ) === FALSE) ) {
				Core_AuthService::logout();
				Flight::redirect('/403');
			}

			// Verify User Authorization On The Requested Resource
			// Root Users Can Bypass

			if ( $rbac->Users->hasRole( 'root', $authService->getSessionInfo('ID') ) || $rbac->check( $resource, $authService->getSessionInfo('ID') ) ) {

				switch (Flight::request()->method)
				{
					case 'GET':
						// Process Task Query Parameter
						$task = Flight::request()->query['task'];

						// Page
						if ( !empty($page) ) {

							bgp_safe_require( MODS_DIR . '/' . $module . '/' . $module . '.' . $page . '.php' );
						}
						// Controller
						else if ( !empty($page) && $page == 'process' && !empty($task) ) {

							// Verify User Authorization On The Called Method

							$resourcePerm = ucfirst($module). '.' . $task;

							if ( $rbac->Users->hasRole( 'root', $authService->getSessionInfo('ID') ) || $rbac->check( $resourcePerm, $authService->getSessionInfo('ID') ) ) {

								bgp_safe_require( MODS_DIR . '/' . $module . '/' . $module . '.process.php' );
							}
							else {
								Flight::redirect('/401');
							}
						}
						// Module Page
						else {

							bgp_safe_require( MODS_DIR . '/' . $module . '/' . $module . '.php' );
						}
						break;

					case 'POST':
					case 'PUT':
					case 'DELETE':
						// Controller
						$task = Flight::request()->data->task;

						// Verify User Authorization On The Called Method

						$resourcePerm = ucfirst($module). '.' . $task;

						if ( $rbac->Users->hasRole( 'root', $authService->getSessionInfo('ID') ) || $rbac->check( $resourcePerm, $authService->getSessionInfo('ID') ) ) {

							bgp_safe_require( MODS_DIR . '/' . $module . '/' . $module . '.process.php' );
						}
						else {
							Flight::redirect('/401');
						}
						break;

					default:
						Flight::redirect('/400');
				}
			}
			else {
				Flight::redirect('/401');
			}
		}
	}
});


/**
 * Start the FW
 */


Flight::start();
