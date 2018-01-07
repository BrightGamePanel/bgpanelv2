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



final class Core_Database_Service implements Core_Service_Interface {

    /**
     * @var PDO
     */
	private static $service_handle = null;

    /**
     * Core_DBH constructor.
     */
	private function __construct()
    {
    }

    /**
     * @return PDO
     */
	public static function getService() {

	    if (empty(self::$service_handle) ||
            !is_object(self::$service_handle) ||
            (get_class(self::$service_handle) != 'PDO')) {

            // Connect to the SQL server
            if (DB_DRIVER == 'sqlite') {
                self::$service_handle = new PDO( DB_DRIVER.':'.DB_FILE );
            }
            else {
                self::$service_handle = new PDO( DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD );
            }

            // Set ERRORMODE to exceptions
            self::$service_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}

		return self::$service_handle;
	}
}
