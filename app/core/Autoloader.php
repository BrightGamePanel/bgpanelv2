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
 * Class Autoloader
 * Loads components on runtime
 */
class Autoloader
{
    /**
     * Minimal requirements for the framework to start
     *
     * @return void
     */
    public static function load() {

        // BrightGamePanel Functions
        require( CORE_DIR	. '/inc/func.inc.php');

        // Applications
        require( CORE_DIR	. '/application/Core_Abstract_Application.php' );
        require( CORE_DIR	. '/application/Core_Launcher.php' );

        // Authentication
        require( CORE_DIR	. '/authentication/Core_AuthService.php' );
        require( CORE_DIR	. '/authentication/Core_AuthService_JWT.php' );

        // Database Handler
        require( CORE_DIR	. '/database/Core_DBH.php' );

        // Base Module Classes
        require( CORE_DIR   . '/module/Core_Module_Interface.php');
        require( CORE_DIR   . '/module/Core_Controller_Interface.php');
        require( CORE_DIR	. '/module/Core_Abstract_Module.php' );
        require( CORE_DIR	. '/module/Core_Abstract_Module_Controller.php' );
    }

    /**
     * Runtime dependency resolution and injection
     *
     * @param string $class Class name to load
     * @return void
     */
    public static function loader($class)
    {
        // CORE

        if (strpos($class, 'Core_') === 0) {

            switch ($class) {
                // Application Package
                case 'Core_Wizard_Application':
                case 'Core_API_Application':
                case 'Core_GUI_Application':
                    require( CORE_DIR	. '/application/' . $class . '.php' );
                    return;
                // Authentication Package
                case 'Core_AuthService_API':
                case 'Core_AuthService_Session':
                    require( CORE_DIR	. '/authentication/' . $class . '.php' );
                    return;
                // GUI
                case 'Core_Page_Builder':
                    require( CORE_DIR	. '/gui/' . $class . '.php' );
                    return;
                // Lang Package
                case 'Core_Lang':
                    require( CORE_DIR 	. '/lang/' . $class . '.php' );
                    require( LIBS_DIR	. '/php-gettext/gettext.inc.php' );
                    return;
                    // Module
                case 'Core_Page_Interface':
                case 'Core_Abstract_Page':
                    require( CORE_DIR   . '/module/' . $class . '.php');
                    return;
                default:
                    // Unknown injection
                    return;
            }
        }

        // LIBS

        switch ($class) {
            case 'DocBlock':
                require( LIBS_DIR	. '/docblockparser/doc_block.php' );
                return;
            case 'Flight':
                require( LIBS_DIR	. '/flight/Flight.php' );
                return;
            case 'JWT':
            case 'BeforeValidException':
            case 'ExpiredException':
            case 'SignatureInvalidException':
                require( LIBS_DIR   . '/jwt/' . $class . '.php');
                return;
            case 'Logger':
                require( LIBS_DIR	. '/log4php/Logger.php' );
                return;
            case 'Rbac':
                require( LIBS_DIR	. '/phprbac2.0/autoload.php' );
                return;
            case 'Crypt_AES':
                require( LIBS_DIR	. '/phpseclib/AES.php' );
                return;
            case 'Crypt_RSA':
                require( LIBS_DIR	. '/phpseclib/RSA.php' );
                return;
            case 'File_ANSI':
                require( LIBS_DIR	. '/phpseclib/ANSI.php' );
                return;
            case 'Securimage':
                require( LIBS_DIR	. '/securimage/securimage.php' );
                return;
            case 'Validator':
                require( LIBS_DIR	. '/valitron/Validator.php' );
                return;
        }

        // MODULE

        $module = strtolower($class);
        $class_file = MODS_DIR . '/' . $module . '/' . $class . '.class.php';
        $controller_file = MODS_DIR . '/' . $module . '/' . $class . '.controller.class.php';
        $page_file = MODS_DIR . '/' . $module . '/' . $module . '.page.class.php';

        if (file_exists($class_file)) {
            require( $controller_file );
            require( $class_file );
            require( $page_file ); // Default page
            return;
        }

        // Module specific page
        $page = str_replace('_' . $module, '', strtolower($class));
        $page = str_replace('_page', '', $page);
        $page_file = MODS_DIR . '/' . $module . '/' . $module . '.' . $page .  'page.class.php';
        if (file_exists($page_file)) {
            require( $page_file );
            return;
        }
    }
}
