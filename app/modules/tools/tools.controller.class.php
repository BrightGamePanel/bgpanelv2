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

if ( !class_exists('Core_Abstract_Module_Controller')) {
	trigger_error('Controller_Tools -> BGP_Controller is missing !');
}

/**
 * Tools Controller
 */

class Core_Module_Controller_Tools extends Core_Abstract_Module_Controller {

	function __construct( )	{
	
		// Call parent constructor
		parent::__construct( basename(__DIR__) );
	}

	/**
	 * Generate WADL
	 *
	 * @http_method GET
	 * @resource tools/wadlGenerator
	 *
	 * @return application/xml
	 *
	 * @author Nikita Rousseau
	 */
	public function getWADL( )
	{
		// Apply =======================================================================

		$wadl =  Core_API::getWADL( );

		// return a response and log ===================================================

		$logger = self::getLogger();
	
		$logger->info('Downloaded WADL File.');

		return array(
			'response' => 'application/xml',
			'data' => $wadl
		);
	}

	/**
	 * Refresh Modules Permissions Table
	 *
	 * @http_method GET
	 * @resource tools/refreshModsPerms
	 *
	 * @return application/json
	 *
	 * @author Nikita Rousseau
	 */
	public function refreshModsPerms( )
	{
		$permissions 		= array();
		$parsedPermissions	= array();

		$errors				= array();  	// array to hold validation errors
		$data 				= array(); 		// array to pass back data
		$data['add']		= array();
		$data['remove']		= array();

		$dbh = Core_DBH::getDBH(); // Get Database Handle

		// Apply =======================================================================

		// Fetch existing permissions

		try {
			$sth = $dbh->prepare("
				SELECT Title
				FROM permissions
				;");

			$sth->execute();

			$tmp = $sth->fetchAll( PDO::FETCH_ASSOC );

			foreach ($tmp as $key => $value) {
				$permissions[] = $value['Title'];
			}

			unset($tmp);
		}
		catch (PDOException $e) {
			echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			die();
		}

		// Add new permissions

		$rbac = new PhpRbac\Rbac();
		
		$handle = opendir( MODS_DIR );

		if ($handle) {
		
			// Foreach modules
			while (false !== ($entry = readdir($handle))) {
		
				// Dump specific directories
				if ($entry == "." || $entry == "..") {
					continue;
				}

				// Exceptions
				if ($entry == 'login') {
					continue;
				}

				$module = $entry;

				// Get Module Pages

				$pages = Core_Reflection_Helper::getModulePublicPages( $module );

				if (empty($pages)) {
					continue;
				}

				// Create Page Access Permission

				foreach ($pages as $value) {

					$parsedPermissions[] = $value['page'];

					if (in_array($value['page'], $permissions)) {
						continue;
					}

					$id = $rbac->Permissions->add($value['page'], $value['description']);

					$data['add'][ $id ] = $value['page'];
				}
				
				// Get Public Methods

				$methods = Core_Reflection_Helper::getControllerPublicMethods( $module );

				if (empty($methods)) {
					continue;
				}

				// Create Method Permission

				foreach ($methods as $key => $value) {

					$parsedPermissions[] = $value['method'];

					if (in_array($value['method'], $permissions)) {
						continue;
					}

					$id = $rbac->Permissions->add($value['method'], $value['description']);

					$data['add'][ $id ] = $value['method'];
				}
			}

			closedir($handle);
		}

		// Remove obsolete permissions

		$removal = array_diff($permissions, $parsedPermissions);

		foreach ($removal as $value) {

			// Exeptions
			if ($value == 'root') {
				continue; 
			}

			$id = $rbac->Permissions->returnId($value);
			$rbac->Permissions->remove($id, TRUE);

			$data['remove'][ $id ] = $value;
		}

		// return a response and log ===================================================

		$logger = self::getLogger();

		$data['success'] = true;
		$data['errors'] = null;

		$logger->info('Reloaded Modules Rights.');

		return array(
			'response' => 'application/json',
			'data' => json_encode($data)
		);
	}

	/**
	 * Optimize Database
	 *
	 * @http_method GET
	 * @resource tools/databaseOptimizer
	 *
	 * @return application/json
	 *
	 * @author Nikita Rousseau
	 */
	public function optimizeDB( )
	{
		$tables = array();

		$errors			= array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data

		$dbh = Core_DBH::getDBH(); // Get Database Handle

		// Apply =======================================================================

		try {
			$result = $dbh->query( "SHOW TABLES" );
			$tables[] = $result->fetchAll( PDO::FETCH_NUM );
			$tables = $tables[0];
	
			if (!empty($tables)) {
				foreach ($tables as $table)
				{
					$table = $table[0];
	
					if (strstr($table, DB_PREFIX)) {
						$dbh->query("OPTIMIZE TABLE " . $table . ";");
					}
				}
			}
		}
		catch (PDOException $e) {
			echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			die();
		}

		// return a response and log ===================================================

		$logger = self::getLogger();
		
		$data['success'] = true;
		$data['errors'] = null;
	
		$logger->info('Optimized database tables.');

		return array(
			'response' => 'application/json',
			'data' => json_encode($data)
		);
	}
}
