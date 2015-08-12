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



class Core_GUI_JS
{
	private $module_name;

	/**
	 * Default Constructor
	 *
	 * @return void
	 * @access public
	 */
	function __construct( $bgp_module )
	{
		if ( !empty($bgp_module) && is_object($bgp_module) && is_subclass_of($bgp_module, 'BGP_Module') ) {
			$this->module_name = $bgp_module::getModuleName( );
		}
	}

	/**
	 * Dynamically Generate An AngularJS Controller
	 *
	 * @param String $task
	 * @param Array $variables
	 * @param String $redirect
	 * @return String
	 * @access public
	 */
	public function getAngularCode( $task = '', $variables = array(), $redirect = './' )
	{
		$module = $this->module_name;
//------------------------------------------------------------------------------------------------------------+
?>
					<script>
						console.clear();

						angular.module('bgpApp', [])
							.controller('bgpCtrl', function($scope, $http) {

						});

					</script>
<?php
//------------------------------------------------------------------------------------------------------------+
	}

}
