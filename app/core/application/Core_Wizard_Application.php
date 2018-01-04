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
 * Install Wizard
 */
final class Core_Wizard_Application extends Core_Abstract_Application
{

    /**
     * BGP_Installer_Application constructor.
     *
     * @param $page
     * @param $http_accept
     */
    public function __construct($page, $http_accept = "text/html")
    {
        // Fake Authentication Service
        $this->authentication_service = new Core_Auth_Service_Anonymous();

        parent::__construct('wizard', $page, 0, $http_accept);
    }

    public function init()
    {
        Core_Defaults::initialize();
    }

    public function execute()
    {
        if ($this->req_method == 'GET' && $this->req_content_type == 'text/html') {
            return $this->module_handle->render($this->page, $this->req_params);
        }
        if ($this->req_method == 'POST') {
            return $this->module_handle->process($this->page, $this->req_params);
        }

        $method =  $this->module_handle->getController()->resolve($this->req_method, $this->req_url);
        echo $this->module_handle->getController()->format(
            $this->module_handle->getController()->invoke($method, $this->req_params), $this->req_content_type
        );
        return 0;
    }
}