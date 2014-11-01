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

// Prevent direct access
if (!defined('LICENSE'))
{
	exit('Access Denied');
}

switch (ENV_RUNTIME)
{
	case 'INSTALL_WIZARD':
		require( LIBS_DIR	. '/phpseclib/RSA.php' );
		break;

	default:
		// PHP 5.5 Functions Implementation
		require( LIBS_DIR	. '/php5.5/func.inc.php');

		// BrightGamePanel Functions
		require( APP_DIR	. '/core/inc/func.inc.php');

		// Database Handle Manager
		require( APP_DIR	. '/core/dbh.class.php' );

		// Kirby 2 Framework
		require( LIBS_DIR	. '/kirby/bootstrap.php');

		// Apache log4php
		require( LIBS_DIR	. '/log4php/Logger.php');

		// Flight Framework
		require( LIBS_DIR	. '/flight/Flight.php');

		// PHP-GetText Framework
		require( LIBS_DIR	. '/php-gettext/gettext.inc.php' );
		require( APP_DIR 	. '/core/lang.class.php' );

		// PHPSeclib
		require( LIBS_DIR	. '/phpseclib/AES.php' );
		require( LIBS_DIR	. '/phpseclib/RSA.php' );
		require( LIBS_DIR	. '/phpseclib/ANSI.php' );

		//require( LIBS_DIR	. '/phpseclib/SSH2.php' );
		//require( LIBS_DIR	. '/phpseclib/SFTP.php' );

		// Authentication Service
		require( APP_DIR	. '/core/auth.class.php' );

		// Graphical User Interface Builder
		require( APP_DIR	. '/core/gui.class.php' );
		require( APP_DIR	. '/core/js.gui.class.php' );

		// HTTP Status Codes Class
		require( APP_DIR	. '/core/http.status.class.php' );

		// Module Class Definition
		require( APP_DIR	. '/core/module.class.php' );

		// Controller Class Definition
		require( APP_DIR	. '/core/controller.class.php' );
}
