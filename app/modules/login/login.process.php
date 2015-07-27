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
 * Load Plugin Controller
 */

require( MODS_DIR . '/' . basename(__DIR__) . '/login.controller.class.php' );

// Init Controller
$loginController = new BGP_Controller_Login();


// Get the method
if ( isset($_POST['task']) ) {
	$task = $_POST['task'];
	unset($_POST['task']);
}
else if ( isset($_GET['task']) ) {
	$task = $_GET['task'];
	unset($_GET['task']);
}
else {
	$task = 'None';
}


// Call the method
switch ($task)
{
	case 'authenticateUser':
		// Verify that the user is not banned
		$authService = Core_AuthService::getAuthService();

		if ( $authService->isBanned() == FALSE ) {

			if ( isset($_POST['username']) && isset($_POST['password']) ) {
				$json = $loginController->authenticateUser( $_POST['username'], $_POST['password'] );
				Flight::json( $json );
			}
			else {
				Flight::redirect('/400');
			}
		}
		else {
			$authService->incrementSecCount(); // Extend ban duration

			Flight::redirect('/401');
		}
		exit( 0 );

	case 'getCaptcha':
		$img = new Securimage();

		if (!empty($_GET['namespace'])) $img->setNamespace($_GET['namespace']);

		$img->show();  // Outputs the image and content headers to the browser

		exit( 0 );

	case 'sendNewPassword':
		$authService = Core_AuthService::getAuthService();

		if ( $authService->isBanned() == FALSE ) {

			$image = new Securimage();

			if ( $image->check( $_POST['captcha'] ) == TRUE ) {
				// Good captcha

				if ( isset($_POST['username']) && isset($_POST['email']) ) {
					$json = $loginController->sendNewPassword( $_POST['username'], $_POST['email'], TRUE );

					if ($json['success'] === TRUE) {
						// Notification
						bgp_set_alert( T_('Your password has been reset and emailed to you.'), NULL, 'success' );
					}

					Flight::json( $json );
				}
				else {
					Flight::redirect('/400');
				}
			}
			else {
				// Bad captcha

				if ( isset($_POST['username']) && isset($_POST['email']) ) {
					$json = $loginController->sendNewPassword( $_POST['username'], $_POST['email'], FALSE );
					Flight::json( $json );
				}
				else {
					Flight::redirect('/400');
				}
			}
		}
		else {
			$authService->incrementSecCount(); // Extend ban duration

			Flight::redirect('/401');
		}
		exit( 0 );

	default:
		Flight::redirect('/400');
}

Flight::redirect('/403');