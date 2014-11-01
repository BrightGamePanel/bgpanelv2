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
 * @copyright	Copyleft 2014, Nikita Rousseau
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @link		http://www.bgpanel.net/
 */



class Core_DBH {
	// The Database Handle ($dbh)
	public static $dbh;

	// Creates a PDO instance representing a connection to a database
	public static function getDBH() {
		if ( empty(self::$dbh) || !is_object(self::$dbh) || (get_class(self::$dbh) != 'PDO') ) {
			try {
				// Connect to the SQL server
				if (DB_DRIVER == 'sqlite') {
					self::$dbh = new PDO( DB_DRIVER.':'.DB_FILE );
				}
				else {
					self::$dbh = new PDO( DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD );
				}

				// Set ERRORMODE to exceptions
				self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch (PDOException $e) {
				echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
				die();
			}
		}

		return self::$dbh;
	}
}
