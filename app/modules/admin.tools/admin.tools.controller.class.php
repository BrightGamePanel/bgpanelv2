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
	trigger_error('Module_Admin_Tools -> BGP_Controller is missing !');
}

/**
 * Admin Tools Controller
 */

class BGP_Controller_Admin_Tools extends BGP_Controller {

	function __construct( )	{
	
		// Call parent constructor
		parent::__construct( basename(__DIR__) );
	}

	public function optimizeDB( $form ) {
		$dbh = Core_DBH::getDBH(); // Get Database Handle

		// process =====================================================================

		$tables = array();

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

		// return a response ===========================================================

		$data['success'] = true;

		// notification
		bgp_set_alert(  T_('Optimizing tables... Done!'), T_('Tables are up to date.'), 'success' );
		
		// return all our data to an AJAX call
		return json_encode($data);
	}
}
