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

    /**
     * Module pages handles
     *
     * @var array
     */
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

		// Instantiate Pages
		foreach ($module_definition['module_pages'] as $pageTag => $pages) {

		    if ($pageTag == 'default') {

                $page_class = get_class($this) . '_Page';
                $title = !empty($pages['title']) ? $pages['title'] : get_class($this);
                $description = !empty($pages['description']) ? $pages['description'] : '';

                spl_autoload_call($page_class);
                if (!class_exists($page_class)) {
                    throw new Core_Verbose_Exception(
                        '404 Not Found',
                        'In module : ' . get_class($this),
                        'Default page (' . $page_class . ') not found.'
                    );
                }
		        $this->pages['default'] = new $page_class($this, '', $title, $description);
                continue;
            }

		    // Regular pages
		    foreach ($pages as $key => $value) {

		        if (empty($value['@attributes']['name'])) {
		            // Malformed
                    continue;
                }

                $page_name = $value['@attributes']['name'];
		        $page_class = get_class($this) . '_' . ucfirst(strtolower($page_name)) . '_Page';
                $title = !empty($value['title']) ? $value['title'] : get_class($this);
                $description = !empty($value['description']) ? $value['description'] : '';

                spl_autoload_call($page_class);
                if (!class_exists($page_class)) {
                    throw new Core_Verbose_Exception(
                        '404 Not Found',
                        'In module : ' . get_class($this),
                        'Page `' . $page_name . '` (' . $page_class . ') not found.'
                    );
                }
		        $this->pages[$page_name] = new $page_class($this, $page_name, $title, $description);
            }
        }

        // Set Page Parents
        foreach ($module_definition['module_pages'] as $pageTag => $pages) {

            if ($pageTag == 'default') {
                continue;
            }

            foreach ($pages as $key => $value) {

                if (empty($value['@attributes']['name'])) {
                    // Malformed
                    continue;
                }

                $page_name = $value['@attributes']['name'];
                $parent = !empty($value['parent']) ? $value['parent'] : '';

                if (!empty($parent) && isset($this->pages[$parent])) {
                    $this->pages[$page_name]->setParent($this->pages[$parent]);
                }
            }
        }

        // Is this module enabled ?
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
        if (empty($page)) {
            // Default page
            $page = 'default';
        }

        // Check composition property
        if (!isset($this->pages[$page])) {
            throw new Core_Verbose_Exception(
                '404 Not Found',
                'In module : ' . get_class($this),
                'Page `' . $page . '` not found.'
            );
        }

        // Render page
        $this->pages[$page]->renderPage($query_args);
    }

    public function getModuleTitle() {
        if (empty($this->settings['title'])) {
            return ucfirst(strtolower(get_class($this)));
        }
        return $this->settings['title'];
    }

    public function getStylesheets() {
	    if (!isset($this->resources['stylesheets'])) {
	        return array();
        }
	    return $this->resources['stylesheets'];
    }

    public function getJavascript() {
        if (!isset($this->resources['javascript'])) {
            return array();
        }
        return $this->resources['javascript'];
    }

    public function getOptions() {
        if (empty($this->options)) {
            return array();
        }
        return $this->options;
    }

    public function getHRef()
    {
        if (empty($this->settings['href'])) {
            return './' . strtolower(get_class($this));
        }
        return $this->settings['href'];
    }

    public function getIcon() {
        if (empty($this->settings['icon'])) {
            return 'fa fa-bug';
        }
        return $this->settings['icon'];
    }
}
