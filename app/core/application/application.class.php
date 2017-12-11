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

    // HTTP Request Attributes
    protected $req_url = '';
    protected $req_method = '';
    protected $req_content_type = 'application/json';
    protected $req_params = array();

    // Core Services
    protected $authService = null;

    /**
     * BGP_Application constructor.
     *
     * @param $module
     * @param $page
     * @param $id
     * @param $content_type
     */
    public function __construct($module, $page, $id, $content_type)
    {
        // Initialization

        if (isset($module) && preg_match("#\w#", $module)) {
            $this->module = strtolower($module);
        }
        if (isset($page) && preg_match("#\w#", $page)) {
            $this->page = strtolower($page);
        }
        if (isset($id) && is_numeric($id)) {
            $this->id = $id;
        }

        // Sanitize Requested Content Type
        $this->req_content_type = (!empty($content_type)) ?
            filter_var($content_type,
                FILTER_SANITIZE_STRING,
                FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) :
            'application/json';

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
     * Execute the Query and Update User Activity
     *
     * @return int
     */
    public abstract function execute();


    /**
     * Update User Activity by User-Id
     * Must be added to any execute() stub of child classes
     *
     * @return void
     */
    protected function updateUserActivity() {

        if ($this->authService == null || $this->authService->isLoggedIn() === FALSE) {
            return;
        }

        $dbh = Core_DBH::getDBH();

        try {
            $sth = $dbh->prepare("
                        UPDATE " . DB_PREFIX . "user
                        SET
                            last_activity	= :last_activity
                        WHERE
                            user_id			= :user_id
                        ;");

            $sth->bindParam(':last_activity', date('Y-m-d H:i:s'));
            $sth->bindParam(':user_id', $this->authService->getUid());

            $sth->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
            die();
        }
    }
}