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

if ( !class_exists('BGP_Controller')) {
	trigger_error('Controller_Config -> BGP_Controller is missing !');
}

/**
 * Configuration Controller
 */

class BGP_Controller_Config extends BGP_Controller {

	function __construct( )	{
	
		// Call parent constructor
		parent::__construct( basename(__DIR__) );
	}

	/**
	 * Get Configuration Collection
	 *
	 * @http_method GET
	 * @resource config/
	 *
	 * @return application/json
	 *
	 * @author Nikita Rousseau
	 */
	public function getSysConfigCollection( )
	{
		$errors			= array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data
		
		$dbh = Core_DBH::getDBH();		// Get Database Handle

		// Apply =======================================================================

		try {
			$sth = $dbh->prepare("
				SELECT setting, value
				FROM config
				;");

			$sth->execute();

			$result = $sth->fetchAll( PDO::FETCH_ASSOC );
		}
		catch (PDOException $e) {
			echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			die();
		}

		if (!empty($result)) {

			// return collection
			$data['collection']['config'] = $result;
		}
		else {

			$data['collection']['config'] = array();
		}

		// return a response and log ===================================================

		$logger = self::getLogger();

		$data['errors'] = $errors;

		if (!empty($data['errors'])) {

			$data['success'] = false;

			$logger->info('Failed to get system configuration collection.');
		} else {

			$data['success'] = true;

			$logger->info('Got system configuration collection.');
		}

		return array(
			'response' => 'application/json',
			'data' => json_encode($data)
		);
	}

	/**
	 * Update Configuration Collection
	 *
	 * @http_method PUT
	 * @resource config/
	 *
	 * @param string $settings query
	 *
	 * @return application/json
	 *
	 * @author Nikita Rousseau
	 */
	public function updateSysConfigCollection( $settings )
	{
		$settings = json_decode($settings, TRUE);

		$errors			= array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data

		// Apply =======================================================================

		foreach ($settings as $setting => $value) {

			$r = $this->updateSysConfigSetting( $setting, $value );
			$r = json_decode( $r['data'], TRUE );

			if ($r['success'] == false) {

				$errors[ $setting ]  = $r['errors'][ $setting ];
			}
		}

		// return a response and log ===================================================

		$logger = self::getLogger();

		$data['errors'] = $errors;

		if (!empty($data['errors'])) {

			$data['success'] = false;

			$logger->info('Failed to update system configuration collection.');
		} else {

			$data['success'] = true;

			$logger->info('Updated system configuration collection.');
		}

		return array(
			'response' => 'application/json',
			'data' => json_encode($data)
		);
	}


	/**
	 * Get Configuration Setting By Element
	 *
	 * @http_method GET
	 * @resource config/setting
	 *
	 * @param string $setting query
	 *
	 * @return application/json
	 *
	 * @author Nikita Rousseau
	 */
	public function getSysConfigSetting( $setting )
	{
		$errors			= array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data
		
		$dbh = Core_DBH::getDBH();		// Get Database Handle
		
		// validate the variables ======================================================

		$v = new Valitron\Validator( array( 'setting' => $setting ) );

		$v->rule('required', 'setting');
		$v->rule('slug', 'setting');

		$v->labels( array( 'setting' => 'Configuration Setting') );

		$v->validate();

		$errors = $v->errors();

		// Apply =======================================================================

		if (empty($errors))
		{
			// Verify that the setting exists

			try {
				$sth = $dbh->prepare("
					SELECT setting, value
					FROM config
					WHERE
						setting = :setting
					;");

				$sth->bindParam(':setting', $setting);

				$sth->execute();

				$result = $sth->fetchAll( PDO::FETCH_ASSOC );
			}
			catch (PDOException $e) {
				echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
				die();
			}

			if (!empty($result)) {

				// return element
				$data['collection']['config'] = $result;
			}
			else {

				$data['collection']['config'][0] = array();
			}
		}

		// return a response and log ===================================================

		$logger = self::getLogger();

		$data['errors'] = $errors;

		if (!empty($data['errors'])) {

			$data['success'] = false;

			$logger->info('Failed to get system configuration setting.');
		} else {

			$data['success'] = true;

			$logger->info('Got system configuration setting.');
		}

		return array(
			'response' => 'application/json',
			'data' => json_encode($data)
		);
	}

	/**
	 * Update A System Configuration By Element
	 *
	 * @http_method PUT
	 * @resource config/setting
	 *
	 * @param string $setting query
	 * @param string $value query
	 *
	 * @return application/json
	 *
	 * @author Nikita Rousseau
	 */
	public function updateSysConfigSetting( $setting, $value )
	{
		$errors			= array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data
		
		$dbh = Core_DBH::getDBH();		// Get Database Handle

		// Get templates
		$templates = parse_ini_file( CONF_TEMPLATES_INI );
		$templates = array_flip(array_values($templates));
		
		// validate the variables ======================================================

		$v = new Valitron\Validator( array( $setting => $value ) );

		switch ( $setting ) {
			case 'panel_name':
				$v->rule('regex', 'panel_name', "/^([-a-z0-9_ -])+$/i");
				$v->labels( array('panel_name' => 'Panel Name') );
				break;

			case 'system_url':
				$v->rule('url', 'system_url');
				$v->labels( array('system_url' => 'Panel URL') );
				break;

			case 'user_template':
				$v->rule('in', 'user_template', $templates);
				$v->labels( array('user_template' => 'User Template') );
				break;

			case 'maintenance_mode':
				// No validation
				break;

			default:
				$errors[$setting] = T_('Unknown Setting!');
				break;
		}

		$v->validate();

		if (empty($errors)) {
			$errors = $v->errors();
		}

		// Apply =======================================================================

		if (empty($errors))
		{
			// Database update

			$db_data[ $setting ] = $value;

			if ( !empty($db_data['maintenance_mode']) ) {
				if ( $db_data['maintenance_mode'] == 'true' ) {
					$db_data['maintenance_mode'] = '1';
				} else {
					$db_data['maintenance_mode'] = '0';
				}
			}

			foreach ($db_data as $key => $value) {

				try {
					$sth = $dbh->prepare( "UPDATE config SET value = :" . $key . " WHERE setting = '" . $key . "';" );
					$sth->bindParam( ':' . $key, $value );
					$sth->execute();
				}
				catch (PDOException $e) {
					echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
					die();
				}
			}
		}

		// return a response and log ===================================================

		$logger = self::getLogger();

		$data['errors'] = $errors;

		if (!empty($data['errors'])) {

			$data['success'] = false;

			$logger->info('Failed to update system configuration setting.');
		} else {

			$data['success'] = true;

			$logger->info('Updated system configuration setting.');
		}

		return array(
			'response' => 'application/json',
			'data' => json_encode($data)
		);
	}
}
