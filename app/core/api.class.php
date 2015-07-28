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

				// Update $_SERVER
				$_SERVER['PHP_AUTH_USER'] = $user_id;

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

	public static function getWADL( )
	{
		$resourcesScheme = 'http://';
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$resourcesScheme = 'https://';
		}

		$applicationDoc = "BrightGamePanel REST API (build " . BGP_API_VERSION . ")";

		$resourcesBaseUrl = get_url($_SERVER);
		$resourcesBaseUrl = str_replace('?WADL', '/', $resourcesBaseUrl);

		$header = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
   <application xmlns=\"http://wadl.dev.java.net/2009/02\">
   <doc xml:lang=\"en\" title=\"BGPanel API\">" . $applicationDoc . "</doc>
   <resources base=\"" . $resourcesBaseUrl . "\">
";

		$body = self::getWADLResources();

		$footer = "
   </resources>
</application>
";

		return $header . $body . $footer;
	}

	public static function getWADLResources( ) {

		$authorizations = self::getAPIUserPermissions();

		exit(var_dump($authorizations));

		return "";
	}

	public static function getAPIUserPermissions( ) {

		// NIST Level 2 Standard Role Based Access Control Library

		$rbac = new PhpRbac\Rbac();

		// root api users access all methods and resources

		// if ( $rbac->Users->hasRole( 'root', $_SERVER['PHP_AUTH_USER'] ) ) {

		// }

		// fetch all allowed resources and methods

		$roles = $rbac->Users->allRoles( $_SERVER['PHP_AUTH_USER'] );
		$perms = array();
		$authorizations = array();

		foreach ($roles as $role) {
			$perms[] = $rbac->Roles->permissions( $role['ID'], false );
		}

		foreach ($perms as $perm) {

			foreach ($perm as $p) {

				// filter pages and get only modules and methods
				if (substr_count($p['Title'], '/') === intval(1)) {
					$module = $p['Title'];
					$module = substr(strtolower($module), 0, -1);

					if (!isset($authorizations[$module])) {
						$authorizations[$module] = array();
					}
				}
				else if (preg_match("#(^[A-Z])*(\.)#", $p['Title'])) {
					list($module, $method) = explode(".", $p['Title']);
					$module = strtolower($module);

					// append method only if the module was allowed
					if (isset($authorizations[$module])) {
						$authorizations[$module][] = $method;
					}
				}
			}
		}

		return $authorizations;
	}
}
