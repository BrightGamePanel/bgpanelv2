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

abstract class Core_Abstract_Module implements Core_Module_Interface
{
	// Module Attributes
    protected $is_enable = TRUE;
	protected $info = array();
	protected $settings = array();
	protected $options = array();

	// Module Controller
    public $controller = null;

    // Module Pages
    protected $pages = array();

    // Module Dependencies
    protected $resources = array();

    /**
     * BGP_Abstract_Module constructor.
     * @throws Core_Verbose_Exception
     */
    function __construct() {

        $module_name = strtolower(get_class($this));
        $manifest_file = MODS_DIR . '/' . $module_name . '/manifest.xml';

		// Test Manifest File
		if ( !file_exists($manifest_file) ) {
		    throw new Core_Verbose_Exception(
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

		foreach ($module_definition['module_pages'] as $pageTag => $pages) {
		    foreach ($pages as $key => $value) {
		        if (empty($value['name'])) {
		            // Malformed
                    continue;
                }
		        $this->pages[$value['name']] = $value;
            }
        }

		if (isset($this->options['enable'])) {
            $this->is_enable = boolval($this->options['enable']);
		}

		// Load Module PHP Dependencies
        $this->autoload($module_definition['module_dependencies']);

		// Set UI dependencies
        unset($module_definition['module_dependencies']['php_libs']);
        $this->resources = $module_definition['module_dependencies'];

            // Attach controller
        $controller_class = get_class($this) . '_Controller';
        spl_autoload_call($controller_class);
        if (!class_exists($controller_class)) {
            throw new Core_Verbose_Exception(
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
     * @throws Core_Verbose_Exception
     */
	private function autoload($dependencies = array()) {

        if (empty($dependencies) || empty($dependencies['php_libs'])) {
            return;
        }

        foreach ($dependencies['php_libs'] as $depend) {
            spl_autoload_call($depend);
            if (!class_exists($depend)) {
                throw new Core_Verbose_Exception(
                    'Dependency injection failed !',
                    'Module : ' . get_class($this),
                    'Unable to load class: ' . $depend
                );
            }
        }
    }

    public function isEnable()
    {
        return $this->is_enable;
    }

    public function render($page, $query_args = array())
    {
        if (!empty($page)) {

            // Check composition property
            if (!in_array($page, $this->pages, TRUE)) {
                throw new Core_Verbose_Exception(
                    '404 Not Found',
                    'In module : ' . get_class($this),
                    'Page `' . $page . '` not found.'
                );
            }

            // Resolve page
            $page_class = get_class($this) . '_' . $page . '_Page';
            spl_autoload_call($page_class);
        }
        else {

            // Default page
            $page_class = get_class($this) . '_Page';
            spl_autoload_call($page_class);
        }

        if (!class_exists($page_class)) {
            throw new Core_Verbose_Exception(
                '404 Not Found',
                'In module : ' . get_class($this),
                'Page `' . $page . '` (' . $page_class . ') not found.'
            );
        }

        /**
         * Instantiate page
         *
         * @var Core_Page_Interface $page
         */
        $page = new $page_class($this, $query_args);

        // Render page
        $page->renderPage();
    }

    public function getModuleTitle()
    {
        return ucfirst(strtolower(get_class($this)));
    }

    public function getStylesheets() {
	    if (!isset($this->resources['stylesheets'])) {
	        return array();
        }
	    return $this->resources['stylesheets'];
    }

    public function getJavascript()
    {
        if (!isset($this->resources['javascript'])) {
            return array();
        }
        return $this->resources['javascript'];
    }

    public function getOptions()
    {
        if (empty($this->options)) {
            return array();
        }
        return $this->options;
    }
}
