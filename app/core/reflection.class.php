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

			if (is_subclass_of($bgp_controller_name, 'BGP_Controller'))
			{
				// Reflection
				$reflector = new ReflectionClass( $bgp_controller_name );
				$methods = $reflector->getMethods( ReflectionMethod::IS_PUBLIC );

				// Filter
				foreach ($methods as $method) {
					$name  = $method->name;
					$class = $method->class;
					$doc   = $reflector->getMethod( $name )->getDocComment();

					// Parse Doc
					if (is_string($doc)) {
						$doc = new DocBlock($doc);
						$desc = $doc->description;
					}
					else {
						$desc = '';
					}

					if ($class == $bgp_controller_name && $name[0] != '_') {
						$method = array(
							'method' 		=> trim(ucfirst( strtolower( $bgp_module_name ) ) . '.' . $name),
							'description'   => trim($desc)
						);

						$public_methods[] = $method;
					}
				}
			}
		}

		return $public_methods;
	}

	public static function getControllerMethod( $bgp_module_name, $public_method )
	{
		$method_definition = array();

		if (!empty($bgp_module_name) && !empty($public_method))
		{
			$bgp_controller_name = 'BGP_Controller_' . ucfirst( strtolower( $bgp_module_name ) );

			if (is_subclass_of($bgp_controller_name, 'BGP_Controller'))
			{
				// Reflection
				$reflector = new ReflectionClass( $bgp_controller_name );
				$method = $reflector->getMethod( $public_method );

				$name  = $method->name;
				$class = $method->class;
				$doc   = $reflector->getMethod( $name )->getDocComment();

				// Parse Doc
				if (is_string($doc)) {
					$doc = new DocBlock($doc);

					$desc = $doc->description;
					$params = $doc->all_params;

					// Params
					if (!empty($params['param'])) {
						$args = $params['param'];
					}
					else {
						$args = array();
					}

					$http = $params['http_method'][0];
					$resource = $params['resource'][0];
					$response = $params['return'][0];

					$method_definition = array(
						'resource'		=> trim($resource),
						'id' 			=> trim($public_method),
						'name'			=> trim($http),
						'description'   => trim($desc),
						'params'		=> $args,
						'response'		=> trim($response)
					);
				}
			}
		}

		return $method_definition;
	}

	public static function getModulePublicPages( $bgp_module_name )
	{
		$public_pages[0]['page'] 		= ucfirst( strtolower( $bgp_module_name ) ) . '/';
		$public_pages[0]['description'] = ucfirst( strtolower( $bgp_module_name ) ) . ' Module';

		if (!empty($bgp_module_name))
		{
			// Test Manifest File
			if ( !file_exists( MODS_DIR . '/' . strtolower( $bgp_module_name ) . '/manifest.xml' ) ) {
				return array();
			}

			$xml = simplexml_load_string( file_get_contents( MODS_DIR . '/' . strtolower( $bgp_module_name ) . '/manifest.xml' ) );
			$json = json_encode($xml);
			$module_definition = json_decode($json, TRUE);

			BGP_Module::$module_definition = $module_definition;
			$module_pages = BGP_Module::getModulePages();

			if (!empty($module_pages)) {
				if (!empty($module_pages['page'])) {
					$module_pages = $module_pages['page'];

					if (isset($module_pages[0])) {
						foreach ($module_pages as $key => $value) {

							$page = array(
								'page'			=> $public_pages[0]['page'] . strtolower( $value['name'] ) . '/',
								'description'   => trim( $value['description'] )
							);

							$public_pages[] = $page;
						}
					}
					else {
						$page = array(
							'page'			=> $public_pages[0]['page'] . strtolower( $module_pages['name'] ) . '/',
							'description'   => trim( $module_pages['description'] )
						);

						$public_pages[] = $page;
					}
				}
			}
		}

		return $public_pages;
	}
}
