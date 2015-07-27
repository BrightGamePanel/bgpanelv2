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



class Core_API
{
	public static function checkRemoteAPIUser( $remote_ip, $api_user, $api_user_pass )
	{
		$username = $api_user;
		$password = Core_AuthService::getHash($api_user_pass);

		$dbh = Core_DBH::getDBH();

		try {
			$sth = $dbh->prepare("
				SELECT user_id
				FROM " . DB_PREFIX . "user
				WHERE
					username = :username AND
					password = :password AND
					status = 'Active'
				;");

			$sth->bindParam(':username', $username);
			$sth->bindParam(':password', $password);

			$sth->execute();

			$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e) {
			echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			die();
		}

		if (!empty($result)) {
			$user_id = $result[0]['user_id'];

			// NIST Level 2 Standard Role Based Access Control Library

			$rbac = new PhpRbac\Rbac();

			// Verify API Role

			if ( $rbac->Users->hasRole( 'api', $user_id ) ) {

				// Update User Activity

				try {
					$sth = $dbh->prepare("
						UPDATE " . DB_PREFIX . "user
						SET
							last_login		= :last_login,
							last_activity	= :last_activity,
							last_ip 		= :last_ip,
							last_host		= :last_host
						WHERE
							user_id			= :user_id
						;");

					$last_login = date('Y-m-d H:i:s');
					$last_activity = date('Y-m-d H:i:s');
					$last_host = gethostbyaddr($remote_ip);

					$sth->bindParam(':last_login', $last_login);
					$sth->bindParam(':last_activity', $last_activity);
					$sth->bindParam(':last_ip', $remote_ip);
					$sth->bindParam(':last_host', $last_host);
					$sth->bindParam(':user_id', $user_id);

					$sth->execute();
				}
				catch (PDOException $e) {
					echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
					die();
				}

				return TRUE;
			}
		}

		return FALSE;
	}

	public static function checkRemoteHost( $remote_ip, $api_key, $api_user, $api_user_pass )
	{
		// Get IPs Whitelist

		$trustedIps = parse_ini_file( CONF_API_WHITELIST_INI, TRUE );

		if (!empty($trustedIps) && isset($trustedIps['IPv4'])) {
			$trustedIps = array_values( $trustedIps['IPv4'] );

			// Verify IP

			if (in_array($remote_ip, $trustedIps)) {

				// Get API Key

				$apiMasterKey = parse_ini_file( CONF_API_KEY_INI );

				if (!empty($apiMasterKey) && isset($apiMasterKey['APP_API_KEY'])) {
					$apiMasterKey = $apiMasterKey['APP_API_KEY'];

					// Verify Master Key

					if ($api_key == $apiMasterKey) {

						// Verify API User

						return self::checkRemoteAPIUser( $remote_ip, $api_user, $api_user_pass );
					}
				}
			}
		}

		return FALSE;
	}
}
