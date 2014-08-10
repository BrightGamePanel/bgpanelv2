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
 * @categories	Games/Entertainment, Systems Administration
 * @package		Bright Game Panel V2
 * @author		warhawk3407 <warhawk3407@gmail.com> @NOSPAM
 * @copyleft	2014
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @version		0.1
 * @link		http://www.bgpanel.net/
 */

define('LICENSE', 'GNU GENERAL PUBLIC LICENSE - Version 3, 29 June 2007');

/**
 * ERROR Handling
 * Sets which PHP errors are reported
 * @link: http://php.net/manual/en/function.error-reporting.php
 *
 * Turn off all error reporting:
 * error_reporting(0);
 *
 * Report all PHP errors:
 * error_reporting(E_ALL);
 */
error_reporting(E_ALL);

// DIRECTORY CONSTANTS
define('BASE_DIR', realpath(dirname(__FILE__)));
define('APP_DIR', BASE_DIR . '/app');
define('CONF_DIR', BASE_DIR . '/conf');
define('GUI_DIR', BASE_DIR . '/gui');
define('LOGS_DIR', BASE_DIR . '/logs');
define('PYDIO_DIR', BASE_DIR . '/pydio');

// CONFIGURATION FILE CONSTANTS
define('CONF_DATA_INI', CONF_DIR . '/data.conf.ini');
define('CONF_GENERAL_INI', CONF_DIR . '/general.conf.ini');
define('CONF_SECRET_INI', CONF_DIR . '/secret.keys.ini');
