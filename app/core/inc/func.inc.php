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


/**
 * Secure require alias for the routing component of the system
 */
function bgp_routing_require_mod( $mod_path ) {
	if ( file_exists($mod_path) ) {
		require( $mod_path );
	}
	else {
		Flight::notFound();
	}
}


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
