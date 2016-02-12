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
	private $dbh = NULL;

	/**
	 * Re-initialize existing session, or creates a new one. Called when a session starts or when session_start() is invoked.
	 *
	 * @param string $save_path 	The path where to store/retrieve the session.
	 * @param string $name 			The session name.
	 *
	 * @return bool
	 *
	 * @author Nikita Rousseau
	 */
	public function open($save_path = "", $name = "PHPSESSID")
	{
		$this->dbh = Core_DBH::getDBH();

		return TRUE;
	}

	/**
	 * Closes the current session. This function is automatically executed when closing the session, or explicitly via session_write_close().
	 *
	 * @return bool
	 *
	 * @author Nikita Rousseau
	 */
	public function close()
	{
		$this->dbh = NULL;
		return TRUE;
	}

	/**
	 * Reads the session data from the session storage, and returns the results. Called right after the session starts or when session_start() is called.
	 * This method is called SessionHandlerInterface::open() is invoked.
	 *
	 * This method should retrieve the session data from storage by the session ID provided.
	 *
	 * @param string $session_id 	The session id.
	 *
	 * @return string
	 *
	 * @author Nikita Rousseau
	 */
	public function read($session_id = "")
	{
		try {
			$sth = $this->dbh->prepare("
				SELECT session_data
				FROM " . DB_PREFIX . "session
				WHERE
					session_id = :session_id
				;");

			$sth->bindParam(':session_id', $session_id);

			$sth->execute();

			$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e) {
			echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			die();
		}

		if (!isset($data[0])) {
			return (string)'';
		}

		return (string)$data[0]['session_data'];
	}

	/**
	 * Writes the session data to the session storage. Called by session_write_close(), when session_register_shutdown() fails, or during a normal shutdown.
	 *
	 * @param string $session_id 	The session id.
	 * @param string $session_data	The encoded session data. This data is the result of the PHP internally encoding the $_SESSION superglobal to a serialized string and passing it as this parameter.
	 *
	 * @return bool
	 *
	 * @author Nikita Rousseau
	 */
	public function write($session_id = "", $session_data = "")
	{
		try {
			$sth = $this->dbh->prepare("
				REPLACE INTO ".DB_PREFIX."session
					(session_id, session_data, expires)
				VALUES
					(:session_id, :session_data, " . (time() + session_cache_expire() * 60) . ")
				;");

			$sth->bindParam(':session_id', $session_id);
			$sth->bindParam(':session_data', $session_data);

			$sth->execute();
		}
		catch (PDOException $e) {
			echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			die();
		}

		return TRUE;
	}

	/**
	 * Destroys a session. Called by session_regenerate_id() (with $destroy = TRUE), session_destroy() and when session_decode() fails.
	 *
	 * @param string $session_id 	The session id.
	 *
	 * @return bool
	 *
	 * @author Nikita Rousseau
	 */
	public function destroy($session_id = "")
	{
		try {
			$sth = $this->dbh->prepare("
				DELETE FROM " . DB_PREFIX . "session
				WHERE
					session_id = :session_id
				;");

			$sth->bindParam(':session_id', $session_id);

			$sth->execute();
		}
		catch (PDOException $e) {
			echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			die();
		}

		return TRUE;
	}

	/**
	 * Cleans up expired sessions. Called by session_start(), based on session.gc_divisor, session.gc_probability and session.gc_lifetime settings.
	 *
	 * @param int $maxlifetime 	Sessions that have not updated for the last maxlifetime seconds will be removed.
	 *
	 * @return bool
	 *
	 * @author Nikita Rousseau
	 */
	public function gc($maxlifetime = 60)
	{
		try {
			$sth = $this->dbh->prepare("
				DELETE FROM " . DB_PREFIX . "session
				WHERE
					expires	<= " . time() . "
				;");

			$sth->execute();
		}
		catch (PDOException $e) {
			echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			die();
		}

		return TRUE;
	}
}
