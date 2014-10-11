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

// Prevent direct access
if (!defined('LICENSE'))
{
	exit('Access Denied');
}

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


// Start new or resume existing session
session_start();


// FILE AND DIRECTORY CONSTANTS
define('BASE_DIR', realpath(dirname(__FILE__)));

define('APP_DIR', BASE_DIR . '/app');
	define('CRYPTO_DIR', APP_DIR . '/crypto');
		define('RSA_KEYS_DIR', CRYPTO_DIR . '/rsa_keys');
			define('RSA_PRIVATE_KEY_FILE', RSA_KEYS_DIR . '/bgp_rsa');
			define('RSA_PUBLIC_KEY_FILE', RSA_KEYS_DIR . '/bgp_rsa.pub');
		define('SSH_KEYS_DIR', CRYPTO_DIR . '/ssh_keys');
	define('LIBS_DIR', APP_DIR . '/libs');
	define('LOCALE_DIR', APP_DIR . '/locale');
	define('MODS_DIR', APP_DIR . '/modules');
	define('CORE_VERSION_FILE', APP_DIR . '/version/version.xml');

define('CONF_DIR', BASE_DIR . '/conf');
	define('CONF_DB_INI', CONF_DIR . '/db.conf.ini');
	define('CONF_G_BIN_INI', CONF_DIR . '/game-binaries.ini');
	define('CONF_GENERAL_INI', CONF_DIR . '/general.conf.ini');
	define('CONF_LANG_INI', CONF_DIR . '/languages.ini');
	define('CONF_SECRET_INI', CONF_DIR . '/secret.keys.ini');
	define('CONF_TEMPLATES_INI', CONF_DIR . '/templates.ini');

define('GUI_DIR', BASE_DIR . '/gui');
define('LOGS_DIR', BASE_DIR . '/logs');
define('PYDIO_DIR', BASE_DIR . '/pydio');
define('INSTALL_DIR', BASE_DIR . '/install');


// DEFINE INI CONSTANTS
$CONFIG  = parse_ini_file( CONF_DB_INI, TRUE );
$CONFIG += parse_ini_file( CONF_GENERAL_INI, TRUE );
$CONFIG += parse_ini_file( CONF_SECRET_INI, TRUE );

foreach ($CONFIG as $setting => $value) {
	define( $setting, $value );
}

/**
 * DATE Configuration
 * Sets the default timezone used by all date/time functions
 * @link: http://php.net/manual/en/timezones.php
 */
date_default_timezone_set( CONF_TIMEZONE ); // Default: "Europe/London"

// DEFINE RSA KEYS
if ( file_exists(RSA_PRIVATE_KEY_FILE) && file_exists(RSA_PUBLIC_KEY_FILE) ) {
	define( 'RSA_PRIVATE_KEY', file_get_contents( RSA_PRIVATE_KEY_FILE ) );
	define( 'RSA_PUBLIC_KEY', file_get_contents( RSA_PUBLIC_KEY_FILE ) );
}

// DEFINE ENVIRONMENT RUNTIME IF NOT SET
if ( !defined('ENV_RUNTIME') ) {
	define('ENV_RUNTIME', 'DEFAULT');
}

// INSTALL WIZARD CHECK
if ( is_dir( INSTALL_DIR ) ) {
	if ( ENV_RUNTIME != 'INSTALL_WIZARD' ) {
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h1>Install Directory Detected</h1><br />
		<h3>REMOVE THE INSTALLATION DIRECTORY.</h3>
		<p>You will not be able to proceed beyond this point until the installation directory has been removed. This is a security feature of BrightGamePanel V2.</p>
	</body>
</html>
<?php
		die();
	}
}

// CORE SYSTEM
require( APP_DIR . '/app.core.php' );

// DEFINE BGP CONSTANTS FROM THE DATABASE
// Syntax: BGP_CONFIG
try {
	if ( ENV_RUNTIME == 'DEFAULT' ) {
		$dbh = Core_DBH::getDBH();

		$sth = $dbh->prepare("
			SELECT setting, value
			FROM " . DB_PREFIX . "config
			;");

		$sth->execute();

		while ($CONFIG = $sth->fetch(PDO::FETCH_ASSOC)) {
			define( strtoupper( 'BGP_' . $CONFIG['setting'] ), $CONFIG['value'] );
		}

		unset($dbh, $sth);
	}
}
catch (PDOException $e) {
	echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
	die();
}

/**
 * GET BGP CORE FILES INFORMATION
 * Load version.xml (app/version/version.xml)
 */
$bgpCoreInfo = simplexml_load_file( CORE_VERSION_FILE );

if ( ENV_RUNTIME == 'DEFAULT' ) {
	/**
	 * VERSION CONTROL
	 * Check that core files are compatible with the current BrightGamePanel Database
	 */
	if ( BGP_PANEL_VERSION != $bgpCoreInfo->{'version'} ) {
	?>
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
		</head>
		<body>
			<h1>Wrong Database Version Detected</h1><br />
			<h3>&nbsp;</h3>
			<p>Make sure you have followed the instructions to install/update the database.</p>
		</body>
	</html>
	<?php
		die();
	}
}

// Clean Up
unset( $CONFIG, $bgpCoreInfo );
