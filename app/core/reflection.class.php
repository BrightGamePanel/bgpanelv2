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


class Core_Reflection
{
	public static function getControllerPublicMethods( $bgp_module_name )
	{
		$public_methods = array();

		if (!empty($bgp_module_name))
		{
			$bgp_controller_name = 'BGP_Controller_' . ucfirst( strtolower( $bgp_module_name ) );

			if (!class_exists( $bgp_controller_name ) && file_exists( MODS_DIR . '/' . strtolower( $bgp_module_name ) . '/' . strtolower( $bgp_module_name ) . '.controller.class.php' ))
			{
				// Try to load controller
				require_once( MODS_DIR . '/' . strtolower( $bgp_module_name ) . '/' . strtolower( $bgp_module_name ) . '.controller.class.php' );

				if (is_subclass_of($bgp_controller_name, 'BGP_Controller'))
				{
					// Reflection
					$class_definition = new ReflectionClass( $bgp_controller_name );
					$methods = $class_definition->getMethods( ReflectionMethod::IS_PUBLIC );

					// Filter
					foreach ($methods as $method) {
						$name  = $method->name;
						$class = $method->class;

						if ($class == $bgp_controller_name && $name[0] != '_') {
							$public_methods[] = ucfirst( strtolower( $bgp_module_name ) ) . '.' . $name;
						}
					}
				}
			}
		}

		return $public_methods;
	}

	public static function getModulePublicPages( $bgp_module_name )
	{
		$public_pages[0] = ucfirst( strtolower( $bgp_module_name ) ) . '/';

		if (!empty($bgp_module_name))
		{
			// Test Manifest File
			if ( !file_exists(MODS_DIR . '/' . strtolower( $bgp_module_name ) . '/manifest.xml' ) ) {
				return array();
			}

			$xml = simplexml_load_string( file_get_contents( MODS_DIR . '/' . strtolower( $bgp_module_name ) . '/manifest.xml' ) );
			$json = json_encode($xml);
			$module_definition = json_decode($json, TRUE);

			BGP_Module::$module_definition = $module_definition;
			$module_pages = BGP_Module::getModulePages();

			if (!empty($module_pages)) {
				$module_pages = $module_pages['page'];

				if (is_array($module_pages)) {
					$module_pages = array_unique($module_pages);

					foreach ($module_pages as $module_page) {
						$public_pages[] = $public_pages[0] . strtolower( $module_page ) . '/';
					}
				}
				else {
					$public_pages[1] = $public_pages[0] . strtolower( $module_pages ) . '/';
				}	
			}
		}

		return $public_pages;
	}
}
