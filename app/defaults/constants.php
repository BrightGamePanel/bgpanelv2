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
 * @author		warhawk3407 <warhawk3407@gmail.com>
 * @copyright	Copyleft 2015, Nikita Rousseau
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @link		http://www.bgpanel.net/
 */

// Prevent direct access
if (!defined('LICENSE'))
{
    exit('Access Denied');
}

// DB
if (!defined ('DB_PREFIX')) {
    define('DB_PREFIX', 'bgp_');
}

// GENERAL
if (!defined ('CONF_CRONDELAY')) {

}
if (!defined ('CONF_TIMEZONE')) {
    define('CONF_TIMEZONE', 'Europe/London');
}
if (!defined ('CONF_DEFAULT_LOCALE')) {
    define('CONF_DEFAULT_LOCALE', 'en_EN');
}
if (!defined ('CONF_SEC_LOGIN_ATTEMPTS')) {

}
if (!defined('CONF_SEC_BAN_DURATION')) {

}
if (!defined('CONF_SEC_SESSION_METHOD')) {

}

