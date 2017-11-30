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
 * Little function that will generate a random password
 *
 * Some letters and digits have been removed, as they can be mistaken
 */
function bgp_create_random_password( $length )
{
	$chars = "abcdefghijkmnpqrstuvwxyz23456789-#@*!_?ABCDEFGHJKLMNPQRSTUVWXYZ"; // Available chars for the password
	$string = str_shuffle($chars);
	$pass = substr($string, 0, $length); // Truncate the password to the specified length

	return $pass;
}

/**
 * Set an Alert on the GUI via global $_SESSION
 */
function bgp_set_alert( $strong, $body = '', $type = 'warning' )
{
	if ( !empty($strong) ) {

		switch ($type) {
			case 'success':
			case 'info':
			case 'warning':
			case 'danger':
				$_SESSION['ALERT']['MSG-TYPE'] = $type;
				break;
			
			default:
				$_SESSION['ALERT']['MSG-TYPE'] = 'warning';
				break;
		}

		$_SESSION['ALERT']['MSG-STRONG'] = $strong;
		$_SESSION['ALERT']['MSG-BODY'] = $body;
	}
}

/**
 * bgp_get_net_status
 *
 * Test if the specified socket is Online or Offline.
 *
 * Return string 'Online' || 'Offline'
 */
function bgp_get_net_status($ip, $port)
{
	if($socket = @fsockopen($ip, $port, $errno, $errstr, 1))
	{
		fclose($socket);
		return 'Online';
	}
	else
	{
		###
		//Uncomment the line above for debugging
		//echo "$errstr ($errno)<br />\n";
		###
		return 'Offline';
	}
}

/**
 * Convert bytes to human readable format
 *
 * http://codeaid.net/php/convert-size-in-bytes-to-a-human-readable-format-%28php%29
 *
 * @param integer bytes Size in bytes to convert
 * @return string
 */
function bytesToSize($bytes, $precision = 2)
{
	$kilobyte = 1024;
	$megabyte = $kilobyte * 1024;
	$gigabyte = $megabyte * 1024;
	$terabyte = $gigabyte * 1024;

	if (($bytes >= 0) && ($bytes < $kilobyte)) {
		return $bytes . ' B';

	} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
		return round($bytes / $kilobyte, $precision) . ' KB';

	} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
		return round($bytes / $megabyte, $precision) . ' MB';

	} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
		return round($bytes / $gigabyte, $precision) . ' GB';

	} elseif ($bytes >= $terabyte) {
		return round($bytes / $terabyte, $precision) . ' TB';

	} else {
		return $bytes . ' B';
	}
}

/**
 * Format the mysql timestamp.
 */
function bgp_format_date( $timestamp )
{
	if ($timestamp == '0000-00-00 00:00:00' || $timestamp == 'Never')
	{
		return 'Never';
	}
	else
	{
		$dateTable = date_parse_from_format('Y-m-d H:i:s', $timestamp);
		return date('l | F j, Y | H:i', mktime($dateTable['hour'], $dateTable['minute'], $dateTable['second'], $dateTable['month'], $dateTable['day'], $dateTable['year']));
	}
}

/**
 * Get Full URL
 *
 * http://stackoverflow.com/a/8891890
 */
function url_origin($s, $use_forwarded_host = false)
{
	$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
	$sp = strtolower($s['SERVER_PROTOCOL']);
	$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
	$port = $s['SERVER_PORT'];
	$port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
	$host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
	$host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
	return $protocol . '://' . $host;
}

function get_url($s, $use_forwarded_host = false)
{
	return url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
}

function camelToUnderscore($str) {
	return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $str));
}

/**
 * Normalize a file path string
 *
 * @param $path string
 *     The path to normalise.
 * @param $encoding string
 *     The name of the path iconv() encoding.
 * @return string
 *    The path, normalised.
 */
function normalizePath($path, $separator = DIRECTORY_SEPARATOR, $encoding = "UTF-8") {
  
	$path = iconv($encoding, "$encoding//IGNORE//TRANSLIT", $path);

	$parts = explode('/', $path);
	$safe  = array();  

	foreach ($parts as $idx => $part) {
		$part = str_replace($separator, '', $part);

		if (empty($part) || ('.' == $part)) {
			continue;
		} elseif ('..' == $part) {
			array_pop($safe);
			continue;
		} else {
			$safe[] = $part;
		}
	}

	$path = implode($separator, $safe);

	if (substr($path, -1) == $separator) {
		$path = substr($path, 0, -1);
	}

	return $path;
}