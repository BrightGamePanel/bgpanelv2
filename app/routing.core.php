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

if ( !class_exists('Flight')) {
	trigger_error('Core -> Flight FW is missing !');
}

/**
 * Flight FW Routing Definitions
 */

// By default, we redirect the user to the login page
Flight::route('GET|POST /', function() {
	Flight::redirect('/login');
});

// HTTP status codes
Flight::route('GET|POST /@http:[0-9]{3}', function( $http ) {
	header( 'HTTP/1.0 ' . $http );
	die();
});

// Dynamically load the module VIEW
Flight::route('GET /@module', function( $module ) {
	$mod_path = MODS_DIR . '/' . $module . '/' . $module . '.php';
	if ( file_exists($mod_path) ) {
		require( $mod_path );
	}
	else {
		Flight::notFound();
	}
});

// Dynamically load the module CONTROLLER
Flight::route('POST /@module/process', function( $module ) {
	$mod_path = MODS_DIR . '/' . $module . '/' . $module . '.process.php';
	if ( file_exists($mod_path) ) {
		require( $mod_path );
	}
	else {
		Flight::notFound();
	}
});

/**
 * Start the FW
 */

Flight::start();