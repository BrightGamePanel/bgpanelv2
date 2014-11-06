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

if ( !class_exists('BGP_Controller')) {
	trigger_error('Module_Admin_Config -> BGP_Controller is missing !');
}

/**
 * Admin Configuration Controller
 */

class BGP_Controller_Admin_Config extends BGP_Controller {

	function __construct( )	{
	
		// Call parent constructor
		parent::__construct( basename(__DIR__) );
	}

	public function updateSysConfig( $form )
	{
		$errors			= array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data
		
		$dbh = Core_DBH::getDBH();		// Get Database Handle

		// Get templates
		$templates = parse_ini_file( CONF_TEMPLATES_INI );
		
		// validate the variables ======================================================

		if ( empty($form['panelName']) ) {
			$errors['panelName'] = T_('Panel Name is required.');
		}
		
		if ( empty($form['panelUrl']) ) {
			$errors['panelUrl'] = T_('Panel URL is required.');
		}

		if ( empty($form['adminTemplate']) || !v::in($form['adminTemplate'], $templates) ) {
			$errors['adminTemplate'] = T_('Bad Admin Template.');
		}

		if ( empty($form['userTemplate']) || !v::in($form['userTemplate'], $templates) ) {
			$errors['userTemplate'] = T_('Bad User Template.');
		}

		if ( empty($form['maintenanceMode']) || (boolval($form['maintenanceMode']) != TRUE &&
				boolval($form['maintenanceMode']) != FALSE)	) {
			$errors['maintenanceMode'] = T_('Bad Maintenance Mode.');
		}

		// Apply the form ==============================================================

		$panelName			= $form['panelName'];
		$panelUrl			= $form['panelUrl'];
		$adminTemplate 		= $form['adminTemplate'];
		$userTemplate 		= $form['userTemplate'];
		$maintenanceMode 	= boolval($form['maintenanceMode']);

		if (empty($errors)) {
			
		}

		// return a response ===========================================================
		
		// response if there are errors
		if (!empty($errors)) {
		
			// if there are items in our errors array, return those errors
			$data['success'] = false;
			$data['errors']  = $errors;

			$data['msgType'] = 'warning';
			$data['msg'] = T_('Login Failure!');
		}
		else {
		
			// if there are no errors, return a message
			$data['success'] = true;
		
			// notification
			$data['msgType'] = 'success';
			$data['msg'] = T_('Settings Updated Successfully!');
		}
		
		// return all our data to an AJAX call
		return json_encode($data);
	}
}
