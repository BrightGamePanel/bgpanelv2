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

// Prevent direct access
if (!defined('LICENSE'))
{
	exit('Access Denied');
}

/**
 * ERROR Handling
 * Sets which PHP errors are reported
 * @link http://php.net/manual/en/function.error-reporting.php
 *
 * Turn off all error reporting:
 * error_reporting(0);
 * ini_set('display_errors', 0);
 *
 * Report all PHP errors:
 * error_reporting(E_ALL);
 * ini_set('display_errors', 1);
 *
 * !IMPORTANT: More options below
 * !IMPORTANT: See [FlightPHP configuration]
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * URI CONSTANTS
 */

define('BASE_URL', str_replace('//', '/', str_replace("\\", "/", dirname($_SERVER['PHP_SELF'])) . '/'));
define('REQUEST_URI', $_SERVER["REQUEST_URI"]);

/**
 * PROJECT FILE AND DIRECTORY CONSTANTS
 */

define('BASE_DIR', str_replace('//', '/', realpath(dirname(__FILE__))));

define('APP_DIR', BASE_DIR . '/app');
    define('CORE_DIR', APP_DIR . '/core');
	define('LIBS_DIR', APP_DIR . '/libs');
	define('LOCALE_DIR', APP_DIR . '/locale');
	define('MODS_DIR', APP_DIR . '/modules');
	define('DEFAULTS_CONSTANTS_DIR', APP_DIR . '/defaults');
	define('CORE_VERSION_FILE', APP_DIR . '/version/version.xml');

define('CONF_DIR', BASE_DIR . '/conf');
	define('CRYPTO_DIR', CONF_DIR . '/crypto');
		// CRYPTO SSH2
		define('RSA_KEYS_DIR', CRYPTO_DIR . '/ssh2');
			define('RSA_PRIVATE_KEY_FILE', RSA_KEYS_DIR . '/bgp_rsa');
			define('RSA_PUBLIC_KEY_FILE', RSA_KEYS_DIR . '/bgp_rsa.pub');
	define('CONF_LIBS_DIR', CONF_DIR . '/libs');
		// LIBS SPECIFIC CONSTANTS
		define('CONF_PHPSECLIB_INI', CONF_LIBS_DIR . '/phpseclib.ini');
	define('CONF_API_INI', CONF_DIR . '/api.conf.ini');
	define('CONF_API_KEY_INI', CONF_DIR . '/api.key.ini');
	define('CONF_API_WHITELIST_INI', CONF_DIR . '/api.whitelist.ini');
	define('CONF_DB_INI', CONF_DIR . '/db.conf.ini');
	define('CONF_GENERAL_INI', CONF_DIR . '/general.conf.ini');
	define('CONF_LANG_INI', CONF_DIR . '/languages.ini');
	define('CONF_SECRET_INI', CONF_DIR . '/secret.keys.ini');
	define('CONF_TEMPLATES_INI', CONF_DIR . '/templates.ini');

define('GUI_DIR', BASE_DIR . '/gui');
define('LOGS_DIR', BASE_DIR . '/logs');
define('INSTALL_DIR', BASE_DIR . '/install');


// VERIFY CONFIGURATION DIRECTORY
if ( !is_dir( CONF_DIR ) ) {
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h1>Unable to find configuration directory (conf).</h1><br />
		<h3>&nbsp;</h3>
		<p>Make sure you have renamed the configuration directory from "conf-dist" to "conf" then retry.</p>
	</body>
</html>
<?php
	die();
}

// VERIFY HTACCESS
if ( !is_file( BASE_DIR . '/.htaccess' ) ) {
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h1>Unable to find .htaccess.</h1><br />
		<h3>&nbsp;</h3>
		<p>Make sure you have uploaded the ".htaccess" file at the root of the application directory then retry.</p>
	</body>
</html>
<?php
	die();
}

/**
 * DEFINE INI CONSTANTS
 */

$CONFIG  = parse_ini_file( CONF_DB_INI );
$CONFIG += parse_ini_file( CONF_GENERAL_INI );
$CONFIG += parse_ini_file( CONF_API_INI );
// LIBS SPECIFIC CONSTANTS
$CONFIG += parse_ini_file( CONF_PHPSECLIB_INI );

// DEFINE
foreach ($CONFIG as $setting => $value) {
	define( $setting, $value );
}
unset($CONFIG, $setting, $value);

// FIX IF MISSING CONSTANTS
require ( DEFAULTS_CONSTANTS_DIR . '/constants.php');

/**
 * DATE Configuration
 * Sets the default timezone used by all date/time functions
 * @link http://php.net/manual/en/timezones.php
 */

date_default_timezone_set( CONF_TIMEZONE );

/**
 * LOAD CORE FILES
 */

require( CORE_DIR . '/loader.php' );

/**
 * LOGGING Configuration
 */

if ( defined('CONF_LOGS_DIR') && is_writable( CONF_LOGS_DIR ) ) {
    // Override default configuration
    define( 'REAL_LOGGING_DIR', CONF_LOGS_DIR );
}
else {
    // Default configuration
    define( 'REAL_LOGGING_DIR', LOGS_DIR );
}
