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



define('BASE_URL', dirname($_SERVER['PHP_SELF']).'/');
define('REQUEST_URI', $_SERVER["REQUEST_URI"]);

// FILE AND DIRECTORY CONSTANTS
define('BASE_DIR', str_replace('//', '/', realpath(dirname(__FILE__))));

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
define('PYDIO_DIR', BASE_DIR . '/pydio');
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
		<h1>Unable to locate BrightGamePanel V2 configuration directory (conf).</h1><br />
		<h3>&nbsp;</h3>
		<p>Make sure you have renamed the configuration directory from "conf-dist" to "conf".</p>
	</body>
</html>
<?php
	die();
}


// DEFINE INI CONSTANTS
$CONFIG  = parse_ini_file( CONF_DB_INI );
$CONFIG += parse_ini_file( CONF_GENERAL_INI );
$CONFIG += parse_ini_file( CONF_API_INI );

foreach ($CONFIG as $setting => $value) {
	define( $setting, $value );
}

/**
 * LANGUAGE Configuration
 */
if ( isset($_SESSION['LANG']) ) {
	$lang = substr($_SESSION['LANG'], 0, 2);
} else {
	$lang = substr(CONF_DEFAULT_LOCALE, 0, 2);
}

/**
 * DATE Configuration
 * Sets the default timezone used by all date/time functions
 * @link http://php.net/manual/en/timezones.php
 */
date_default_timezone_set( CONF_TIMEZONE ); // Default: "Europe/London"

// DEFINE ENVIRONMENT RUNTIME CONTEXT IF NOT SET
if ( !defined('ENV_RUNTIME') ) {
	$headers = apache_request_headers();

	foreach ($headers as $key => $value) {
		if ($key == 'X-API-KEY' && !empty($value)) {
			define('ENV_RUNTIME', 'M2M');
		}
	}

	if ( !defined('ENV_RUNTIME') ) {
		define('ENV_RUNTIME', 'H2M');
	}
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

// LOAD APPLICATION FILES
require( APP_DIR . '/loader.core.php' );

// DEFINE BGP CONSTANTS FROM THE DATABASE
// Syntax: BGP_CONFIG
try {
	if ( ENV_RUNTIME != 'INSTALL_WIZARD' ) {
		$dbh = Core_DBH::getDBH();

		$sth = $dbh->prepare("
			SELECT setting, value
			FROM " . DB_PREFIX . "config
			;");

		$sth->execute();

		$CONFIG = $sth->fetchAll(PDO::FETCH_ASSOC);

		foreach ($CONFIG as $row) {
			define( strtoupper( 'BGP_' . $row['setting'] ), $row['value'] );
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

if ( ENV_RUNTIME != 'INSTALL_WIZARD' ) {

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

if ( ENV_RUNTIME != 'INSTALL_WIZARD' ) {
	if ( ENV_RUNTIME == 'H2M' ) {
		/**
		 * SESSION HANDLER
		 */
		require( APP_DIR . '/core/session.class.php' );

		// Start new or resume existing session
		$coreSessionHandler = new Core_SessionHandler();
		session_set_save_handler($coreSessionHandler, TRUE);
		session_start();
		$_SESSION['TIMESTAMP'] = time();
	}

	/**
	 * VALITRON Configuration
	 * Valitron is a simple, minimal and elegant stand-alone validation library with NO dependencies
	 *
	 * @link https://github.com/vlucas/valitron#usage
	 */
	Valitron\Validator::langDir( LIBS_DIR . '/valitron/lang' );
	Valitron\Validator::lang( $lang );

	/**
	 * LOGGING Configuration
	 * Apache Log4php configuration
	 *
	 * @link http://logging.apache.org/log4php/docs/configuration.html
	 */
	if ( CONF_LOGS_DIR != 'default' && is_writable( CONF_LOGS_DIR ) ) {

		// Override default configuration
		define( 'REAL_LOGGING_DIR', CONF_LOGS_DIR );
	}
	else {

		// Default configuration
		define( 'REAL_LOGGING_DIR', LOGS_DIR );
	}

	function bgp_get_log4php_conf_array( ) {
		return array(
			'rootLogger' => array(
				'appenders' => array('default')
			),
			'loggers' => array(
				'sys.core' => array(
					'additivity' => false,
					'appenders' => array('coreAppender')
				)
			),
			'appenders' => array(
				'default' => array(
					'class' => 'LoggerAppenderFile',
					'layout' => array(
						'class' => 'LoggerLayoutPattern',
						'params' => array(
							'conversionPattern' => '[%date{Y-m-d H:i:s,u}] %-5level %-10.10logger %-12session{USERNAME} %-3session{ID} %-15.15server{REMOTE_ADDR} %-35server{REQUEST_URI} %-35class %-20method "%msg"%n'
						)
					),
					'params' => array(
						'file' => REAL_LOGGING_DIR . '/' . date('Y-m-d') . '.txt',
						'append' => true
					)
				),
				'coreAppender' => array(
					'class' => 'LoggerAppenderFile',
					'layout' => array(
						'class' => 'LoggerLayoutPattern',
						'params' => array(
							'conversionPattern' => '[%date{Y-m-d H:i:s,u}] %-5level System Core V2 localhost %-35class %-20method "%msg"%n'
						)
					),
					'params' => array(
						'file' => REAL_LOGGING_DIR . '/' . date('Y-m-d') . '.core.txt',
						'append' => true
					)
				)
			)
		);
	}

	/**
	 * ROUTING Configuration
	 * FlightPHP configuration
	 *
	 * flight.base_url - Override the base url of the request. (default: null)
	 * flight.handle_errors - Allow Flight to handle all errors internally. (default: true)
	 * flight.log_errors - Log errors to the web server's error log file. (default: false)
	 * flight.views.path - Directory containing view template files (default: ./views)
	 *
	 * @link http://flightphp.com/learn#configuration
	 */
	Flight::set('flight.handle_errors', TRUE);
	Flight::set('flight.log_errors', FALSE);
}


// Clean Up
unset( $CONFIG, $bgpCoreInfo, $lang, $headers, $key, $value );
