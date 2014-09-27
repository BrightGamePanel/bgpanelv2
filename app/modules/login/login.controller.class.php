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
	public function authenticateUser( $_POST ) {



	// validate the variables ======================================================
	if (empty($_POST['name']))
		$errors['name'] = 'Name is required.';

	if (empty($_POST['superheroAlias']))
		$errors['superheroAlias'] = 'Superhero alias is required.';

	// return a response ===========================================================

	// response if there are errors
	if ( ! empty($errors)) {

		// if there are items in our errors array, return those errors
		$data['success'] = false;
		$data['errors']  = $errors;
	} else {

		// if there are no errors, return a message
		$data['success'] = true;
		$data['message'] = 'Success!';
	}

	// return all our data to an AJAX call
	return json_encode($data);

	}
}
