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
 * Abstract Application Wrapper
 */
abstract class BGP_Abstract_Application
{
    // Request Attributes
    protected $module = '';
    protected $page = '';
    protected $id = 0;
    protected $api_version = '';
    protected $http_headers = array();

    // HTPP Request Attributes
    protected $req_url = '';
    protected $req_method = '';
    protected $req_params = array();

    // Core Services
    protected $authService = null;
    protected $api = null;

    /**
     * BGP_Application constructor.
     */
    protected function __construct()
    {
        // User Authentication Service
        $this->authService = Core_AuthService::getAuthService();

        // Request Information
        $this->req_url = Flight::request()->url;
        $this->req_method = Flight::request()->method;

        // Request Parameters
        $plain_body = Flight::request()->getBody();
        if (!empty($plain_body)) {
            // JSON parameters
            $this->req_params = json_decode($plain_body, TRUE);
        } else {
            // Query parameters
            $this->req_params = Flight::request()->query;
        }
    }

    /**
     * Execute Operation
     *
     * @param $module
     * @param $page
     * @param $id
     * @param $api_version
     * @param $http_headers
     */
    public function execute($module, $page, $id, $api_version, $http_headers) {
        if (isset($module) && preg_match("#\w#", $module)) {
            $this->module = strtolower($module);
        }
        if (isset($page) && preg_match("#\w#", $page)) {
            $this->page = strtolower($page);
        }
        if (isset($id) && is_numeric($id)) {
            $this->id = $id;
        }
        if (isset($api_version) && preg_match("#\w#", $api_version)) {
            $this->api_version = strtolower($api_version);
        }

        $content_type = (isset($http_headers['CONTENT-TYPE'])) ? filter_var($http_headers['CONTENT-TYPE'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : 'application/json';
        $this->http_headers['CONTENT-TYPE'] = $content_type;
    }
}