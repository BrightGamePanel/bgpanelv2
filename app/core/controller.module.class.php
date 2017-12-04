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
 * Base CLASS for each BGP controllers
 */

class BGP_Controller extends BGP_Module
{
	// Controller Definition

	// None

	function __construct( $module_name )	{

		// Call module constructor
		parent::__construct( $module_name );
	}

    /**
     * LOGGING Configuration
     * Apache Log4php configuration
     *
     * @link http://logging.apache.org/log4php/docs/configuration.html
     */
    public static function getLogger() {

        // TODO : implement UID resolution
        $logged_user = str_pad(666, 8);

	    // Configure logging
        Logger::configure(
            array(
                'rootLogger' => array(
                    'appenders' => array('default')
                ),
                'loggers' => array(
                    'core' => array(
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
                                'conversionPattern' => '[%date{Y-m-d H:i:s,u}] %-5level %-10.10logger ' . $logged_user . ' %-15.15server{REMOTE_ADDR} %-35server{REQUEST_URI} "%msg" %-30class %-30method %request%n'
                            )
                        ),
                        'params' => array(
                            'file' => REAL_LOGGING_DIR . '/' . date('Y-m-d') . '.txt',
                            'append' => true
                        )
                    )
                )
            )
        );
        return Logger::getLogger( self::getModuleName() );
	}
}
