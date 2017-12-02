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
 * API Service Autoloader
 * @param $className
 */
function bgp_api_autoloader ($className) {

    if ( $className == 'Core_API' ) {
        require( CORE_DIR	. '/api/api.class.php' );
    } else if ( $className == 'BGP_API_Application' ) {
        require( CORE_DIR	. '/application/api.application.class.php' );
    }
};
spl_autoload_register('bgp_api_autoloader');

/**
 * Module Controllers Autoloader
 * @param $className
 */
function bgp_mod_controllers_autoloader ($className) {

    $module = strtolower(str_replace('BGP_Controller_', '', $className));
    if ( file_exists( MODS_DIR . '/' . $module . '/' . $module . '.controller.class.php' ) ) {
        require( MODS_DIR . '/' . $module . '/' . $module . '.controller.class.php'  );
    }
};
spl_autoload_register('bgp_mod_controllers_autoloader');

/**
 * GUI Parts Autoloader
 * Graphical User Interface Builder
 * @param $className
 */
function bgp_gui_parts_autoloader ($className) {

    if ( $className == 'Core_GUI' ) {
        require( CORE_DIR	. '/gui/gui.class.php' );
    } else if ( $className == 'Core_GUI_JS') {
        require( CORE_DIR	. '/gui/gui.js.class.php' );
    }
};
spl_autoload_register('bgp_gui_parts_autoloader');

/**
 * INSTALL WIZARD LOADER
 */
if (defined('INSTALL_WIZARD')) {

    // BrightGamePanel Functions
    require(APP_DIR . '/core/inc/func.inc.php');

    // PHPSeclib
    require(LIBS_DIR . '/phpseclib/RSA.php');

    // Module Class Definition
    require(APP_DIR . '/core/module.class.php');

    // Controller Class Definition
    require(APP_DIR . '/core/controller.module.class.php');

    // PHP DOC Parser
    require(LIBS_DIR . '/docblockparser/doc_block.php');

    // Module Reflection Class
    require(APP_DIR . '/core/reflection.class.php');

    // Exit
    return;
}

/**
 * APPLICATION
 */
// Main Application Wrapper
require( CORE_DIR	. '/application/application.class.php' );
require( CORE_DIR	. '/application/bootstrap.class.php' );

// PHP 5.5 Functions Implementation
require( LIBS_DIR	. '/php5.5/func.inc.php');

// BrightGamePanel Functions
require( CORE_DIR	. '/inc/func.inc.php');

// Database Handler
require( CORE_DIR	. '/dbh.class.php' );

// Valitron Framework
require( LIBS_DIR	. '/valitron/Validator.php' );

// Apache log4php
require( LIBS_DIR	. '/log4php/Logger.php' );

// Flight Framework
require( LIBS_DIR	. '/flight/Flight.php' );

// PHP-GetText Framework
require( LIBS_DIR	. '/php-gettext/gettext.inc.php' );
require( CORE_DIR 	. '/lang.class.php' );

// PHPSeclib
require( LIBS_DIR	. '/phpseclib/AES.php' );
require( LIBS_DIR	. '/phpseclib/RSA.php' );
require( LIBS_DIR	. '/phpseclib/ANSI.php' );

// PHP-RBAC: Role Based Access Control Library
require( LIBS_DIR	. '/phprbac2.0/autoload.php' );

// Authentication Service
require( CORE_DIR	. '/permissions/auth.class.php' );

// HTTP Status Codes Class
require( CORE_DIR	. '/http.status.class.php' );

// Module Class Definition
require( CORE_DIR	. '/module.class.php' );

// Controller Class Definition
require( CORE_DIR	. '/controller.module.class.php' );

// PHP DOC Parser
require( LIBS_DIR	. '/docblockparser/doc_block.php' );

// Module Reflection Class
require( CORE_DIR	. '/api/reflection.class.php' );
