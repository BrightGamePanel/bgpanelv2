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

// Init Controller
$controller = new Core__Controller_Box();


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
	case 'postBox':

		$data 			 = array();
		$data['success'] = true;
		$data['errors']  = array();

		// Format input ==========================================================================

		// Optional params

		if (!isset($_POST['password'])) {
			$_POST['password'] = '';
		}
		if (!isset($_POST['privatekey'])) {
			$_POST['privatekey'] = 'bgp_rsa';
		}
		if (!isset($_POST['remoteUserHome'])) {
			$_POST['remoteUserHome'] = '';
		}
		if (!isset($_POST['steamcmd'])) {
			$_POST['steamcmd'] = '';
		}
		if (!isset($_POST['notes'])) {
			$_POST['notes'] = '';
		}

		// Call method ===========================================================================

		$return = $controller->postBox( $_POST['name'], $_POST['os'], $_POST['ip'], $_POST['port'], $_POST['login'], $_POST['password'], $_POST['privatekey'], $_POST['remoteUserHome'], $_POST['steamcmd'], $_POST['notes'] );
		$return = json_decode( $return['data'], TRUE );

		// User notification =====================================================================

		$data['errors'] = $return['errors'];

		if (!empty($data['errors'])) {

			$data['success'] = false;

			// Notification
			$data['msgType'] = 'warning';
			$data['msg']     = T_('Bad Options!');
		} else {

			$data['success'] = true;

			// Notification
			bgp_set_alert( T_('Box Added Successfully!'), NULL, 'success' );
		}

		// Return ================================================================================

		header('Content-Type: application/json');
		echo json_encode($data);

		exit( 0 );

	default:
		Flight::redirect('/400');
}

Flight::redirect('/403');