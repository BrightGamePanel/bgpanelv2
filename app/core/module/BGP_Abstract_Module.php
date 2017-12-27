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

abstract class BGP_Abstract_Module
{
	// Module Definition
	protected $info = array();
	protected $settings = array();
	protected $options = array();
	protected $pages = array();

	// Module Controller
    public $controller = null;

    /**
     * BGP_Abstract_Module constructor.
     * @throws Core_Exception
     */
    function __construct() {

        $module_name = strtolower(get_class($this));
        $manifest_file = MODS_DIR . '/' . $module_name . '/manifest.xml';

		// Test Manifest File
		if ( !file_exists($manifest_file) ) {
		    throw new Core_Exception(
		        'Missing manifest file !',
                'Module : ' . $module_name,
                'Unable to load : ' . $manifest_file
            );
		}

		// Load Plugin Manifest
		$xml = simplexml_load_string(file_get_contents($manifest_file));
		$json = json_encode($xml);
		$module_definition = json_decode($json, TRUE);

		// Populate attributes
		$this->info = $module_definition['module_info'];
		$this->settings = $module_definition['module_settings'];
		$this->options = $module_definition['module_options'];
		$this->pages = $module_definition['module_pages'];

		// Load Module Dependencies
        $this->autoload($module_definition['module_dependencies']);

        // Attach controller
        $controller_class = get_class($this) . '_Controller';
        spl_autoload_call($controller_class);
        if (!class_exists($controller_class)) {
            throw new Core_Exception(
                'Missing controller class !',
                'Module : ' . $module_name,
                'Unable to load controller class: ' . $controller_class
            );
        }
        $this->controller = new $controller_class();
	}

    /**
     * Call autoloader for module required libraries
     * @param array $dependencies
     * @throws Core_Exception
     */
	private function autoload($dependencies = array()) {

        if (empty($dependencies) || empty($dependencies['php_libs'])) {
            return;
        }

        foreach ($dependencies['php_libs'] as $depend) {
            spl_autoload_call($depend);
            if (!class_exists($depend)) {
                throw new Core_Exception(
                    'Dependency injection failed !',
                    'Module : ' . get_class($this),
                    'Unable to load class: ' . $depend
                );
            }
        }
    }
}
