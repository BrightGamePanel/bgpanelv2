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



// TODO : implement lol

/**
 * Security Service
 *
 * This class manages the security inside the application
 */
class Core_Security_Service {

    public function isBanned() {

        // No ban registered for this session
        if ( empty($this->session['SEC_BAN']) ) {
            return FALSE;
        }

        // Reset the ban if this one has expired
        if ( $this->session['SEC_BAN'] < time() ) {
            $this->resetBanCounter();
            return FALSE;
        }

        return TRUE;
    }

    public function notifyFailedAuthenticationAttempt() {

        // Increment security counter
        if ( empty($this->session['SEC_COUNT']) ) {
            $this->session['SEC_COUNT'] = 1;
        }
        else {
            $this->session['SEC_COUNT'] += 1;
        }

        // Push to global $_SESSION
        $_SESSION = $this->session;
    }

    public function resetFailedAuthenticationAttempts() {

        if ( !empty($this->session['SEC_COUNT']) ) {
            // Reset counter
            unset($this->session['SEC_COUNT']);
        }

        // Push to global $_SESSION
        $_SESSION = $this->session;
    }

    private function ban() {

        if ($this->session['SEC_COUNT'] <= CONF_SEC_LOGIN_ATTEMPTS) {
            return;
        }

        // Ban the user if too many attempts have been done
        // or the user is already banned but keeps trying

        // Set ban
        $this->session['SEC_BAN'] = time() + CONF_SEC_BAN_DURATION; // Mark the end of the ban

        // Push to global $_SESSION
        $_SESSION = $this->session;

        // Log Event
        Logger::configure( bgp_log4php_def_conf() );
        $logger = Logger::getLogger( 'core.auth' );
        $logger->info('Session banned.');
    }



    /*
public function getUserPermissions($uid) {

    $authorizations = array();

    if (empty($uid)) {
        return $authorizations;
    }

    // Notice:
    // root users access all methods and resources

    if ($this->rbac->Users->hasRole( 'root', $uid )) {

        // Parse all modules

        $handle = opendir( MODS_DIR );

        if ($handle) {

            // Foreach modules
            while (false !== ($entry = readdir($handle))) {

                // Dump specific directories
                if ($entry == "." || $entry == "..") {

                    continue;
                }

                $module = $entry;

                // Get Public Methods
                $methods = Core_Reflection::getControllerPublicMethods( $module );

                if (empty($methods)) {
                    continue;
                }

                foreach ($methods as $key => $value) {
                    list($module, $method) = explode(".", $value['method']);
                    $module = strtolower($module);

                    $authorizations[$module][] = $method;
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
*/
}