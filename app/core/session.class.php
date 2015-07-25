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
 * SessionHandlerInterface is an interface which defines a prototype for creating a custom session handler. In order to pass a custom session handler to session_set_save_handler() using its OOP invocation, 
 * the class must implement this interface.
 * Please note the callback methods of this class are designed to be called internally by PHP and are not meant to be called from user-space code. 
 *
 * See: http://www.codedodle.com/2014/04/storing-sessions-in-database-in-php.html
 */

class Core_SessionHandler implements SessionHandlerInterface
{
	private $dbh;

	public function open($save_path = "", $name = "PHPSESSID")
	{
		$this->dbh = Core_DBH::getDBH();

		return TRUE;
	}

	// Close the session
	public function close()
	{
		return TRUE;
	}

	// Write session data
	public function write($session_id = "", $session_data = "")
	{
		$sth = $this->dbh->prepare("
			REPLACE INTO ".DB_PREFIX."session
				(session_id, session_data, expires)
			VALUES
				(:session_id, :session_data, " . (time() + session_cache_expire() * 60) . ")
			;");

		$sth->bindParam(':session_id', $session_id);
		$sth->bindParam(':session_data', $session_data);

		$sth->execute();

		return TRUE;
	}

	// Read session data
	public function read($session_id = "")
	{
		$sth = $this->dbh->prepare("
			SELECT session_data
			FROM " . DB_PREFIX . "session
			WHERE
				session_id = :session_id
			;");

		$sth->bindParam(':session_id', $session_id);

		$sth->execute();

		$data = $sth->fetchAll(PDO::FETCH_ASSOC);

		if (!isset($data[0])) {
			return (string)'';
		}

		return (string)$data[0]['session_data'];
	}

	// Destroy a session
	public function destroy($session_id = "")
	{
		$sth = $this->dbh->prepare("
			DELETE FROM " . DB_PREFIX . "session
			WHERE
				session_id = :session_id
			;");

		$sth->bindParam(':session_id', $session_id);

		$sth->execute();

		return TRUE;
	}

	// Cleanup old sessions
	public function gc($maxlifetime = "")
	{
		$sth = $this->dbh->prepare("
			DELETE FROM " . DB_PREFIX . "session
			WHERE
				expires	<= " . time() . "
			;");

		$sth->execute();

		 return TRUE;
	}
}
