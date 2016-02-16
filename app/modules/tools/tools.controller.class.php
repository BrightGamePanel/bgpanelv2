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
	trigger_error('Controller_Tools -> BGP_Controller is missing !');
}

/**
 * Tools Controller
 */

class BGP_Controller_Tools extends BGP_Controller {

	function __construct( )	{
	
		// Call parent constructor
		parent::__construct( basename(__DIR__) );
	}

	/**
	 * Web Application Description Language
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
