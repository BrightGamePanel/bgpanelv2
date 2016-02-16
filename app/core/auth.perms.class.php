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



class Core_AuthService_Perms
{
	// Modules that are not callable by the API service
	public static $restricted_modules = array(
		'login',
		'myaccount'
	);

	public static function getUserPermissions( ) {

		$rbac = new PhpRbac\Rbac();

		$authorizations = array();

		// Get Session UID

		$uid = Core_AuthService::getSessionInfo('ID');
		if (empty($uid)) {
			return $authorizations;
		}

		// Notice:
		// root users access all methods and resources

		if ($rbac->Users->hasRole( 'root', $uid )) {

			// Parse all modules

			$handle = opendir( MODS_DIR );

			if ($handle) {
			
				// Foreach modules
				while (false !== ($entry = readdir($handle))) {
			
					// Dump specific directories
					if ($entry != "." && $entry != "..")
					{
						$module = $entry;

						// Exceptions
						if (!in_array($module, self::$restricted_modules))
						{
							// Get Public Methods
							$methods = Core_Reflection::getControllerPublicMethods( $module );

							if (!empty($methods)) {

								foreach ($methods as $key => $value) {
									list($module, $method) = explode(".", $value['method']);
									$module = strtolower($module);

									$authorizations[$module][] = $method;
								}
							}
						}
					}
				}
			
				closedir($handle);
			}

			return $authorizations;
		}

		// fetch all allowed resources and methods

		$roles = $rbac->Users->allRoles( $uid );
		$perms = array();

		foreach ($roles as $role) {
			$perms[] = $rbac->Roles->permissions( $role['ID'], false );
		}

		foreach ($perms as $perm) {

			if (empty($perm)) {
				continue;
			}

			foreach ($perm as $p) {

				// filter pages and get only modules and methods
				if (substr_count($p['Title'], '/') === intval(1)) {
					$module = $p['Title'];
					$module = substr(strtolower($module), 0, -1);

					if (!isset($authorizations[$module]) && !in_array($module, self::$restricted_modules)) {
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
