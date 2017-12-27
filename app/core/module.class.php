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



/**
 * Base CLASS for each BGP modules
 */

class BGP_Module
{
	// Module Definition
	public static $module_definition = array();
	public static $module_name = '';

    /**
     * BGP_Module constructor.
     * @param $module_name
     * @throws BGP_Module_Exception
     */
    function __construct($module_name ) {

		// Test Manifest File
		if ( !file_exists(MODS_DIR . '/' . $module_name . '/manifest.xml' ) ) {
		    throw new BGP_Module_Exception('Missing manifest file !') ;
		}

		// Load Plugin Manifest
		$xml = simplexml_load_string( file_get_contents( MODS_DIR . '/' . $module_name . '/manifest.xml' ) );
		$json = json_encode($xml);
		self::$module_definition = json_decode($json, TRUE);
		self::$module_name = $module_name;

		// Load Module Dependencies
		self::requireDepends( );
	}

	public static function getModuleName( ) {

		return self::$module_name;
	}

	public static function getModuleInfo( $info = '' ) {

		if (isset(self::$module_definition['module_info'][$info])) {
			return self::$module_definition['module_info'][$info];
		}
		else {
			return '';
		}
	}

	public static function getModuleSetting( $setting = '' ) {

		if (isset(self::$module_definition['module_settings'][$setting])) {
			return self::$module_definition['module_settings'][$setting];
		}
		else {
			return '';
		}
	}

	public static function getModuleOption( $option = '' ) {

		if (isset(self::$module_definition['module_options'][$option])) {
			return self::$module_definition['module_options'][$option];
		}
		else {
			return '';
		}
	}

	public static function getModulePages( ) {

		if (isset(self::$module_definition['module_pages'])) {
			return self::$module_definition['module_pages'];
		}
		else {
			return array();
		}
	}

	public static function getModuleDependencies( ) {

		if (isset(self::$module_definition['module_dependencies'])) {
			return self::$module_definition['module_dependencies'];
		}
		else {
			return array();
		}
	}

	public static function requireDepends( ) {
	
		$module_dependencies = self::getModuleDependencies( );
	
		if ( !empty($module_dependencies) && !empty($module_dependencies['php_libs']) ) {
	
			foreach ($module_dependencies['php_libs'] as $depend) {
	
				$requirement = LIBS_DIR	. '/' . $depend['require'];
	
				if ( file_exists( $requirement ) ) {
	
					require_once( $requirement );
				}
			}
		}
	}

	public static function getModuleClassName( ) {

		if (isset(self::$module_definition['class_definition']['@attributes']['classname'])) {
			return self::$module_definition['class_definition']['@attributes']['classname'];
		}
	}

	public static function getModuleControllerClassName( ) {

		if (isset(self::$module_definition['controller_class_definition']['@attributes']['classname'])) {
			return self::$module_definition['controller_class_definition']['@attributes']['classname'];
		}
	}

	public static function setModulePageTitle( $page = '' ) {

		$title = self::$module_name;

		if (!empty($page)) {
			$module_pages = BGP_Module::getModulePages();

			if (!empty($module_pages)) {
				$module_pages = $module_pages['page'];

				if (isset($module_pages[0])) {
					foreach ($module_pages as $key => $value) {
						if ($value['name'] == $page) {
							$title = $value['description'];
							break;
						}
					}
				}
				else {
					$title = $module_pages['description'];
				}
			}
		}

		self::$module_definition['module_settings']['title'] = $title;
	}
}
