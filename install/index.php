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
 * @copyright	Copyleft 2014, Nikita Rousseau
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @link		http://www.bgpanel.net/
 */

define('LICENSE', 'GNU GENERAL PUBLIC LICENSE - Version 3, 29 June 2007');

/**
 * Install Wizard Version
 */
define('WIZARDVERSION', 'v2.0.1');
define('ENV_RUNTIME', 'INSTALL_WIZARD');

//---------------------------------------------------------+

/**
 * BGP INIT
 */
require( '../init.app.php' );

/**
 * INSTALL WIZARD FUNCTIONS
 */
require( INSTALL_DIR . '/inc/func.inc.php' );

/**
 * BGP VERSION LIST
 */
require( INSTALL_DIR . '/inc/versions.inc.php' );

/**
 * BGP GAME CONFIGURATION DATABASE
 */
require( INSTALL_DIR . '/inc/game.conf.inc.php' );

//---------------------------------------------------------+

if (isset($_POST['task']))
{
	$task = $_POST['task'];
}

switch (@$task)
{
	case 'license':
		if ( isset($_POST['license']) )
		{
			if ($_POST['license'] == 'on')
			{
				header( "Location: index.php?step=one" );
				die();
			}
		}
		exit( "You must accept the terms of the license agreement." );
		break;

	default:
		break;
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Install and Update Script - BrightGamePanel V2</title>
		<!--Powered By Bright Game Panel-->
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- JS -->
			<script src="./bootstrap/js/jquery.js"></script>
			<script src="./bootstrap/js/bootstrap.js"></script>
		<!-- Style -->
			<link href="./bootstrap/css/bootstrap.css" rel="stylesheet">
			<style type="text/css">
			body {
				padding-top: 60px;
				padding-bottom: 40px;
			}
			</style>
			<link href="./bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
			<!--[if lt IE 9]>
			  <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->
		<!-- Favicon -->
			<link rel="shortcut icon" href="./bootstrap/img/favicon.ico">
	</head>

	<body>
			<div class="navbar navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container-fluid">
						<a class="brand" href="#">Bright Game Panel V2</a>
					</div>
				</div>
			</div>
			<div class="container">
				<div class="page-header">
					<h1>Install and Update Script&nbsp;<small>Bright Game Panel V2 - <?php echo LASTBGPVERSION; ?></small></h1>
				</div>
				<ul class="breadcrumb">
<?php

//---------------------------------------------------------+

if (!isset($_GET['step'])) // Step == 'zero'
{
?>
					<li class="active">License</li>
<?php
}
else if ($_GET['step'] == 'one')
{
?>
					<li>
						<a href="index.php">License</a> <span class="divider">/</span>
					</li>
					<li class="active">Check Requirements</li>
<?php
}
else if ($_GET['step'] == 'two')
{
?>
					<li>
						<a href="index.php">License</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="index.php?step=one">Check Requirements</a> <span class="divider">/</span>
					</li>
					<li class="active">Select Database Update</li>
<?php
}
else if ($_GET['step'] == 'three')
{
?>
					<li>
						<a href="index.php">License</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="index.php?step=one">Check Requirements</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="index.php?step=two">Select Database Update</a> <span class="divider">/</span>
					</li>
					<li class="active">Install Database</li>
<?php
}

//---------------------------------------------------------+

?>
				</ul>
<?php



//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+



if (!isset($_GET['step'])) // Step == 'zero'
{
?>
				<div class="well">
					<div style="width:auto;height:480px;overflow:scroll;overflow-y:scroll;overflow-x:hidden;">
<?php
	$license = fopen('../LICENSE', 'r');

	while ($rows = fgets($license))
	{
		echo $rows.'<br />';
	}

	fclose($license);
?>
					</div>
				</div>
				<form method="post" action="index.php">
					<input type="hidden" name="task" value="license" />
					<label class="checkbox">
						<input type="checkbox" name="license">&nbsp;I Accept the Terms of the License Agreement
					</label>
					<div style="text-align: center; margin-top: 19px;">
						<button type="submit" class="btn">Submit</button>
					</div>
				</form>
				<div class="modal fade" id="welcome">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3>BrightGamePanel V2 Install and Update Script</h3>
					</div>
					<div class="modal-body">
						<p class="lead">
							Welcome to BrightGamePanel V2,<br />
							the new version of the easy to use game control panel.
						</p>
						<br /><br />
						<small>Click on the button below to start the installation process.</small>
					</div>
					<div class="modal-footer">
						<a class="btn btn-primary" data-dismiss="modal" href="#">Go !</a>
					</div>
				</div>
				<script>
				$(document).ready(function() {
					$('#welcome').modal('show')
				});
				</script>
<?php
}



//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+



else if ($_GET['step'] == 'one')
{
?>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Action</th>
							<th>Status</th>
							<th>Note</th>
						</tr>
					</thead>
					<tbody>
						<tr class="success">
							<td>Checking for CONFIGURATION files</td>
							<td><span class="label label-success">FOUND</span></td>
							<td></td>
						</tr>
<?php

	$versioncompare = version_compare(PHP_VERSION, '5.3.4');
	if ($versioncompare == -1)
	{
?>
						<tr class="error">
							<td>Checking your version of PHP</td>
							<td><span class="label label-important">FAILED (<?php echo PHP_VERSION; ?>)</span></td>
							<td>Upgrade to PHP 5.3.4 or greater</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking your version of PHP</td>
							<td><span class="label label-success"><?php echo PHP_VERSION; ?></span></td>
							<td></td>
						</tr>
<?php
	}
	unset($versioncompare);

?>
<?php

	if (ini_get('safe_mode'))
	{
?>
						<tr class="error">
							<td>Checking for PHP safe mode</td>
							<td><span class="label label-important">ON</span></td>
							<td>Please, disable safe mode !!!</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for PHP safe mode</td>
							<td><span class="label label-success">OFF</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('pdo'))
	{
?>
						<tr class="error">
							<td>Checking for PDO extension</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>PDO extension could not be found or is not installed. (<a href="http://php.net/manual/en/pdo.installation.php">PDO Installation</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for PDO extension</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php

		try {
			// Connect to the SQL server
			if (DB_DRIVER == 'sqlite') {
				$dbh = new PDO( DB_DRIVER.':'.DB_FILE );
			}
			else {
				$dbh = new PDO( DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD );
			}

			// Set ERRORMODE to exceptions
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e) {
			$pdo_error = $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
		}

		if ( empty($dbh) )
		{
?>
						<tr class="error">
							<td>Checking for SQL server connection</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>Message: "<?php echo $pdo_error; ?>"</td>
						</tr>
<?php

			$error = TRUE;
		}
		else
		{
?>
						<tr class="success">
							<td>Checking for SQL server connection</td>
							<td><span class="label label-success">SUCCESSFUL</span></td>
							<td></td>
						</tr>
<?php
			unset($dbh);
		}
	}

?>
<?php

	if (!function_exists('fsockopen'))
	{
?>
						<tr class="error">
							<td>Checking for FSOCKOPEN function</td>
							<td><span class="label label-important">FAILED</span></td>
							<td></td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for FSOCKOPEN function</td>
							<td><span class="label label-success">SUCCESSFUL</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!function_exists('mail'))
	{
?>
						<tr class="error">
							<td>Checking for MAIL function</td>
							<td><span class="label label-important">FAILED</span></td>
							<td></td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for MAIL function</td>
							<td><span class="label label-success">SUCCESSFUL</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('curl'))
	{
?>
						<tr class="error">
							<td>Checking for Curl extension</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>Curl extension is not installed. (<a href="http://php.net/curl">Curl</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for Curl extension</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('mbstring'))
	{
?>
						<tr class="error">
							<td>Checking for MBSTRING extension (LGSL - Used to show UTF-8 server and player names correctly)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>mbstring extension is not installed. (<a href="http://php.net/mbstring">mbstring</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for MBSTRING extension (LGSL - Used to show UTF-8 server and player names correctly)</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('bz2'))
	{
?>
						<tr class="error">
							<td>Checking for BZIP2 extension (LGSL - Used to show Source server settings over a certain size)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>BZIP2 extension is not installed. (<a href="http://php.net/bzip2">BZIP2</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for BZIP2 extension (LGSL - Used to show Source server settings over a certain size)</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('zlib'))
	{
?>
						<tr class="error">
							<td>Checking for ZLIB extension (LGSL - Required for America's Army 3)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>ZLIB extension is not installed. (<a href="http://php.net/zlib">ZLIB</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for ZLIB extension (LGSL - Required for America's Army 3)</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('gd') && !extension_loaded('gd2'))
	{
?>
						<tr class="error">
							<td>Checking for GD extension (pChart Requirement)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>GD / GD2 extensions are not installed. (<a href="http://php.net/book.image.php">GD</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for GD extension (pChart Requirement)</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!function_exists('imagettftext'))
	{
?>
						<tr class="error">
							<td>Checking for FreeType extension (securimage Requirement)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>FreeType extension is not installed. (<a href="http://php.net/manual/en/image.installation.php">FreeType</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for FreeType extension (securimage Requirement)</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('simplexml'))
	{
?>
						<tr class="error">
							<td>Checking for SimpleXML extension</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>SimpleXML extension is not installed. (<a href="http://php.net/simplexml">SimpleXML</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for SimpleXML extension</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!class_exists('XMLReader'))
	{
?>
						<tr class="error">
							<td>Checking for XMLReader extension</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>XMLReader extension is not installed. (<a href="http://php.net/xmlreader">XMLReader</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for XMLReader extension</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!defined('APP_API_KEY'))
	{
		if (is_writable( CONF_SECRET_INI ))
		{
?>
						<tr class="success">
							<td>Checking for secret keys file write permission</td>
							<td><span class="label label-success">OK</span></td>
							<td></td>
						</tr>
<?php
		}
		else
		{
?>
						<tr class="error">
							<td>Checking for secret keys file write permission</td>
							<td><span class="label label-important">FAILED</span></td>
							<td></td>
						</tr>
<?php
			$error = TRUE;
		}
	}

	if (!defined('RSA_PRIVATE_KEY') || !defined('RSA_PUBLIC_KEY'))
	{
		if (is_writable( RSA_KEYS_DIR ) && is_writable( SSH_KEYS_DIR ))
		{
?>
						<tr class="success">
							<td>Checking for SSH and RSA keys directory write permission</td>
							<td><span class="label label-success">OK</span></td>
							<td></td>
						</tr>
<?php
		}
		else
		{
?>
						<tr class="error">
							<td>Checking for SSH and RSA keys directory write permission</td>
							<td><span class="label label-important">FAILED</span></td>
							<td></td>
						</tr>
<?php
			$error = TRUE;
		}
	}



	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
	// PHPSECLIB

	// TODO
	// PHPSECLIb REQUIREMENTS + OPENSSL TEST
	// http://phpseclib.sourceforge.net/math/intro.html
	// GMP + OPENSSL EXT



	// some versions of XAMPP have mismatched versions of OpenSSL which causes it not to work

	/*
	ob_start();
	@phpinfo();
	$content = ob_get_contents();
	ob_end_clean();

	preg_match_all('#OpenSSL (Header|Library) Version(.*)#im', $content, $matches);

	$versions = array();
	if (!empty($matches[1])) {
		for ($i = 0; $i < count($matches[1]); $i++) {
			$versions[$matches[1][$i]] = trim(str_replace('=>', '', strip_tags($matches[2][$i])));
		}
	}

	// it doesn't appear that OpenSSL versions were reported upon until PHP 5.3+
	switch (true) {
		case !isset($versions['Header']):
		case !isset($versions['Library']):
		case $versions['Header'] == $versions['Library']:
			define('MATH_BIGINTEGER_OPENSSL_ENABLED', true);
			break;
		default:
			define('MATH_BIGINTEGER_OPENSSL_DISABLE', true);
	}
	*/



	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
	// AJXP

?>
						<tr>
							<td class="colspan3"><b>AjaXplorer Tests</b></td>
						</tr>
<?php

	$v = @extension_loaded('apc');
	if (isset($v) && (is_numeric($v) || strtolower($v) == "on"))
	{
?>
						<tr class="warning">
							<td>Checking for PHP APC extension</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>AjaXplorer framework loads a lot of PHP files at each query, and using a PHP accelerator is greatly recommended. (<a href="http://php.net/manual/en/book.apc.php">APC</a>).</td>
						</tr>
<?php
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for PHP APC extension</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

	$v = @ini_get("output_buffering");
	if ( isset($v) && (is_numeric($v) || strtolower($v) == "on") )
	{
?>
						<tr class="warning">
							<td>PHP Output Buffer disabled</td>
							<td><span class="label label-warning">ENABLED</span></td>
							<td>You should disable php output_buffering parameter for better performances with AjaXplorer.</td>
						</tr>
<?php
	}
	else
	{
?>
						<tr class="success">
							<td>PHP Output Buffer disabled</td>
							<td><span class="label label-success">OK</span></td>
							<td></td>
						</tr>
<?php
	}

	if ( !class_exists("DOMDocument") )
	{
?>
						<tr class="error">
							<td>DOM Xml enabled</td>
							<td><span class="label label-important">DISABLED</span></td>
							<td>Dom XML is required, you may have to install the php-xml extension.</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>DOM Xml enabled</td>
							<td><span class="label label-success">OK</span></td>
							<td></td>
						</tr>
<?php
	}

	if ( !function_exists("mcrypt_create_iv") || !function_exists("mcrypt_encrypt") )
	{
?>
						<tr class="error">
							<td>MCrypt enabled</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>MCrypt is required all security functions.</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>MCrypt enabled</td>
							<td><span class="label label-success">OK</span></td>
							<td></td>
						</tr>
<?php
	}

	// Test Write Perms
	$AJXP_DATA_PATH = substr( realpath(dirname(__FILE__)), 0, -8 ).'/ajxp/data';

	if (!is_writable($AJXP_DATA_PATH))
	{
?>
						<tr class="error">
							<td>Checking for AJXP DATA directory is writeable (ajxp/data)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>Make the '/ajxp/data/' folder writeable by the server.<br/>
							EXAMPLE: chown -R www-data /ajxp/data/<br/>
							EXAMPLE: chmod -R 0777 /ajxp/data/</td>
						</tr>
<?php
		$error = TRUE;
	}
	else {
?>
						<tr class="success">
							<td>Checking for AJXP DATA directory is writeable (ajxp/data)</td>
							<td><span class="label label-success">OK</span></td>
							<td></td>
						</tr>
<?php
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

?>
					</tbody>
				</table>
<?php

	if (isset($error))
	{
?>
				<div style="text-align: center;">
					<h3><b>Fatal Error(s) Found.</b></h3><br />
					<button class="btn" onclick="window.location.reload();">Check Again</button>
				</div>
<?php
	}
	else
	{
?>
				<div style="text-align: center;">
					<ul class="pager">
						<li>
							<a href="index.php?step=two">Next Step &rarr;</a>
						</li>
					</ul>
				</div>
<?php
	}

}



//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+



else if ($_GET['step'] == 'two')
{
?>
				<div class="well">
				<h2>Checking for existing tables . . . . .</h2>
<?php

	try {
		// Connect to the SQL server
		if (DB_DRIVER == 'sqlite') {
			$dbh = new PDO( DB_DRIVER.':'.DB_FILE );
		}
		else {
			$dbh = new PDO( DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD );
		}

		// Set ERRORMODE to exceptions
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch (PDOException $e) {
		echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
		die();
	}

	$tables = array();
	$result = $dbh->query( "SHOW TABLES" );
	while ($row = $result->fetch(PDO::FETCH_NUM)) {
		$tables[] = $row[0];
	}

	if (!empty($tables))
	{
		foreach ($tables as $table)
		{
			if ($table == DB_PREFIX.'config')
			{
				$sth = $dbh->query( "SELECT value FROM ".DB_PREFIX."config WHERE setting = 'panel_version'" );
				$currentVersion = $sth->fetch(PDO::FETCH_ASSOC);
				break;
			}
		}
	}

	if (isset($currentVersion))
	{
?>
				<div class="alert alert-block">
					<strong>FOUND !</strong> Tables exist in the database.<br />
					You can update your previous version of BrightGamePanel V2 or perform a clean install <u>which will overwrite all data (BGP tables with the same prefix) in the database.</u><br />
					It is recommend you back up your database first.<br />
				</div>
				<h4>Current Version:</h4>&nbsp;<span class="label label-info"><?php echo $currentVersion['value']; ?></span><br /><br />
				<h4>Select Action :</h4><br />
				<form action="index.php" method="get">
					<input type="hidden" name="step" value="three" />
					<input name="version" type="radio" value="update" checked="checked" /><b>&nbsp;Update to the Last Version (<?php echo LASTBGPVERSION; ?>)</b><br /><br /><br />
					<input name="version" type="radio" value="full" /><b>&nbsp;<span class="label label-warning">Perform Clean Install</span>&nbsp;- Version <?php echo LASTBGPVERSION; ?></b><br /><br />
					<button type="submit" class="btn btn-primary">Install SQL Database</button>
				</form>
				</div>
<?php
	}
	else
	{
?>
				<span class="label label-success">No tables found in the database</span><br /><br />
				<form action="index.php" method="get">
					<input type="hidden" name="step" value="three" />
					<input name="version" type="radio" value="full" checked="checked" /><b>&nbsp;Install BGP Version <?php echo LASTBGPVERSION; ?></b><br /><br />
					<button type="submit" class="btn btn-primary">Install SQL Database</button>
				</form>
				</div>
<?php
	}

?>
				<div style="text-align: center;">
					<ul class="pager">
						<li>
							<a href="index.php?step=one">&larr; Previous Step</a>
						</li>
					</ul>
				</div>
<?php
}



//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+



else if ($_GET['step'] == 'three')
{

	switch (@$_GET['version'])
	{
		case 'full':

			//---------------------------------------------------------+
			// Generating Secret Keys

			$APP_API_KEY 		= hash('sha512', md5(str_shuffle(time()))); usleep( rand(1, 1000) );
			$APP_SSH_KEY 		= hash('sha512', md5(str_shuffle(time()))); usleep( rand(1, 1000) );
			$APP_STEAM_KEY		= hash('sha512', md5(str_shuffle(time()))); usleep( rand(1, 1000) );
			$APP_AUTH_SALT		= hash('sha512', md5(str_shuffle(time()))); usleep( rand(1, 1000) );
			$APP_LOGGED_IN_KEY 	= hash('sha512', md5(str_shuffle(time())));

			if (is_writable( CONF_SECRET_INI )) {
				$handle = fopen( CONF_SECRET_INI, 'w');
				$data = "; SECURITY KEYS FILE
APP_API_KEY 		= \"".$APP_API_KEY."\"
APP_SSH_KEY 		= \"".$APP_SSH_KEY."\"
APP_STEAM_KEY		= \"".$APP_STEAM_KEY."\"
APP_AUTH_SALT		= \"".$APP_AUTH_SALT."\"
APP_LOGGED_IN_KEY 	= \"".$APP_LOGGED_IN_KEY."\"
";
				fwrite($handle, $data);
				fclose($handle);
				unset($handle);
			}
			else {
				exit('Critical error while installing ! Unable to write to /conf/secret.keys.ini !');
			}

			//---------------------------------------------------------+
			// Generating RSA Keys

			if (is_writable( RSA_KEYS_DIR ))
			{
				$rsa = new Crypt_RSA();

				$keypair = $rsa->createKey(2048);

				$handle = fopen( RSA_PRIVATE_KEY_FILE, 'w');
				$data = $keypair['privatekey'];
				fwrite($handle, $data);
				fclose($handle);
				unset($handle);

				$handle = fopen( RSA_PUBLIC_KEY_FILE, 'w');
				$data = $keypair['publickey'];
				fwrite($handle, $data);
				fclose($handle);
				unset($handle);
			}
			else {
				exit('Critical error while installing ! Unable to write to /app/crypto/ !');
			}

			//---------------------------------------------------------+
			// AJXP

			if (is_writable( "../ajxp/data/plugins/boot.conf/bootstrap.json" ))
			{
				$bootstrap = file_get_contents( "../ajxp/data/plugins/boot.conf/bootstrap.json" );
				$bootstrap = str_replace( "\"SECRET\":\"void\"", "\"SECRET\":\"".$APP_API_KEY."\"", $bootstrap );

				$handle = fopen( "../ajxp/data/plugins/boot.conf/bootstrap.json" , 'w' );
				fwrite($handle, $bootstrap);
				fclose($handle);
				unset($handle);
			}
			else {
				exit('Critical error while installing ! Unable to write to /ajxp/data/plugins/boot.conf/bootstrap.json !');
			}

			//---------------------------------------------------------+

			require("./sql/full.php");

			break;


		case 'update':

			try {
				// Connect to the SQL server
				if (DB_DRIVER == 'sqlite') {
					$dbh = new PDO( DB_DRIVER.':'.DB_FILE );
				}
				else {
					$dbh = new PDO( DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD );
				}

				// Set ERRORMODE to exceptions
				$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$sth = $dbh->query( "SELECT value FROM ".DB_PREFIX."config WHERE setting = 'panel_version'" );
				$currentVersion = $sth->fetch(PDO::FETCH_ASSOC);
			}
			catch (PDOException $e) {
				echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
				die();
			}

			//---------------------------------------------------------+

			foreach($bgpVersions as $key => $value)
			{
				if ($value == $currentVersion['value']) // Base version for the update
				{
					if ($key == end($bgpVersions))
					{
						break; // Already up-to-date
					}
					else
					{
						$i = $key; // Starting point for the update

						for ($i; $i < key($bgpVersions); $i++) // Loop in order to reach the last version
						{
							// Apply the update
							$sqlFile = './sql/';
							$sqlFile .= 'update_'.str_replace('.', '', $bgpVersions[$i]).'_to_'.str_replace('.', '', $bgpVersions[$i + 1]).'.php';

							require($sqlFile);
						}

						break; // Update finished
					}
				}
			}

			//---------------------------------------------------------+

			try {
				$sth = $dbh->query( "SELECT value FROM ".DB_PREFIX."config WHERE setting = 'panel_version'" );
				$currentVersion = $sth->fetch(PDO::FETCH_ASSOC);
			}
			catch (PDOException $e) {
				echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
				die();
			}

			if ($currentVersion['value'] != LASTBGPVERSION)
			{
				exit( "Update Error." );
			}

			//---------------------------------------------------------+

			break;


		default:
			exit('<h1><b>Error</b></h1>');
	}

	//---------------------------------------------------------+

?>
				<div class="well">
				<div class="alert alert-block">
					<strong>DELETE THE INSTALL FOLDER</strong><br />
					<?php echo getcwd(); ?>

				</div>
<?php
	if (@$_GET['version'] == 'full') // Full install case
	{
?>
				<h2>Install Complete!</h2>
				<legend>Login Information :</legend>
				Admin Username: <b>admin</b><br />
				Admin Password: <b>password</b><br />
				<hr>
				<i class="icon-share-alt"></i>&nbsp;<a href="../login">@Admin Login</a>
				<hr>
				<div class="alert alert-error">
					<strong>Wait!</strong>
					Remember to change the admin username and password.
				</div>
<?php
	}
	else // Update Case
	{
?>
				<h2>Your system is now up-to-date.</h2>
				<legend>Changelog:</legend>
				<div style="width:auto;height:480px;overflow:scroll;overflow-y:scroll;overflow-x:hidden;">
<?php
		$log = fopen('CHANGELOG', 'r');

		while ($rows = fgets($log))
		{
			echo $rows.'<br />';
		}

		fclose($log);
?>
				</div>
				<hr>
				<i class="icon-share-alt"></i>&nbsp;<a href="../admin">@Admin Login</a>
<?php
	}
?>
				<hr>
				<h1>Thanks for using BrightGamePanel V2 :-)</h1>
				</div>
<?php
}



//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+



?>
				<hr>
				<footer>
					<div class="pull-left">
						Copyleft - 2014. Released Under <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPLv3</a>.<br />
						All Images Are Copyrighted By Their Respective Owners.
					</div>
					<div class="pull-right" style="text-align: right;">
						<a href="http://www.bgpanel.net/" target="_blank">Bright Game Panel</a><br />
						Install Script: <?php echo WIZARDVERSION; ?> - BGPV2: <?php echo LASTBGPVERSION; ?><br />
						Built with <a href="http://getbootstrap.com/" target="_blank">Bootstrap</a>.
					</div>
				</footer>
			</div><!--/container-->

			<!--Powered By Bright Game Panel-->

	</body>
</html>
