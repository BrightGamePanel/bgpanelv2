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

if ( !class_exists('Core_Abstract_Module')) {
	trigger_error('Module_Tools -> BGP_Abstract_Module is missing !');
}

/**
 * Tools Module
 */

class Core_Abstract_Module_Tools extends Core_Abstract_Module {

	function __construct( )	{

		// Call parent constructor
		parent::__construct( basename(__DIR__) );
	}

}

/**
 * Tools Module
 * Page: WADL
 * Title: Web Application Description Language
 */

class BGP_Module_Tools_Wadl extends Core_Abstract_Module_Tools {

	function __construct( $page = '' )	{

		// Call parent constructor
		parent::__construct( );

		self::setModulePageTitle( $page );
	}
}


/**
 * Tools Module
 * Page: Phpinfo
 * Title: Php Info
 */

class BGP_Module_Tools_Phpinfo extends Core_Abstract_Module_Tools {

	function __construct( $page = '' )	{

		// Call parent constructor
		parent::__construct( );

		self::setModulePageTitle( $page );
	}
}

/**
 * Tools Module
 * Page: Opdb
 * Title: Optimize Database
 */

class BGP_Module_Tools_Opdb extends Core_Abstract_Module_Tools {

	function __construct( $page = '' )	{

		// Call parent constructor
		parent::__construct( );

		self::setModulePageTitle( $page );
	}
}
