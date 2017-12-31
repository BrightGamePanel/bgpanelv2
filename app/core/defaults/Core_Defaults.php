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
 * Class Core_Defaults
 * Load default constants value (if missing)
 */
final class Core_Defaults {

    public static function initialize() {

        /* GENERAL */

        if (!defined('CONF_SEC_SESSION_METHOD')) {
            define('CONF_SEC_SESSION_METHOD', 'HMAC_SHA_256');
        }

        if (!defined('BGP_PANEL_NAME')) {
            define('BGP_PANEL_NAME', 'BrightGamePanel V2');
        }

        /* SECRETS */

        if (!defined('Core\Authentication\APP_TOKEN_KEY')) {
            define('Core\Authentication\APP_TOKEN_KEY', hash('sha256', $_SERVER['SERVER_ADDR']));
        }

        if (!defined('Core\Authentication\APP_AUTH_SALT')) {
            define('Core\Authentication\APP_AUTH_SALT', hash('sha256', $_SERVER['SERVER_ADDR']));
        }
    }
}