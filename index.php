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

define('LICENSE', 'GNU GENERAL PUBLIC LICENSE - Version 3, 29 June 2007');

/**
 * Bright Game Panel Init
 */
require( 'init.app.php' );

/**
 * Define Display Language
 */
if ( isset($_SESSION['LANG']) ) {
	Core_Lang::setLanguage( $_SESSION['LANG'] );
}
else if ( isset($_COOKIE['LANG']) ) {
	Core_Lang::setLanguage( $_COOKIE['LANG'] );
}
else {
	Core_Lang::setLanguage( CONF_DEFAULT_LOCALE );
}

/**
 * Load System Routing Definitions
 */
require( APP_DIR . '/routing.core.php' );

?>