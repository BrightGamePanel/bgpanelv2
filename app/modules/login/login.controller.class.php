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

if ( !class_exists('BGP_Controller')) {
	trigger_error('Module_Login -> BGP_Controller is missing !');
}

/**
 * Login Controller
 * by Nikita Rousseau
 */

class BGP_Controller_Login extends BGP_Controller
{
	public function authenticateUser( $form ) {
		$errors         = array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data

		// validate the variables ======================================================

		if (!v::alphanum($form['username'])) {
			$errors['username'] = 'Username is required.';
		}

		if (empty($form['password'])) {
			$errors['password'] = 'Password is required.';
		}

		// Verify the form =============================================================

		if ($form['username'] != 'admin') {
			$errors['username'] = 'Unknown username.';
		}
		if ($form['password'] != 'password') {
			$errors['password'] = 'Wrong password.';
		}

		// return a response ===========================================================

		// response if there are errors
		if (!empty($errors)) {

			// if there are items in our errors array, return those errors
			$data['success'] = false;
			$data['errors']  = $errors;

			// notification
			$data['msgType'] = 'warning';
			$data['msg'] = 'Login Failure!';
		} else {

			// if there are no errors, return a message
			$data['success'] = true;

			// notification
			$data['msgType'] = 'success';
			$data['msg'] = 'Welcome on BrightGamePanel V2!';
		}

		// return all our data to an AJAX call
		return json_encode($data);
	}
}
