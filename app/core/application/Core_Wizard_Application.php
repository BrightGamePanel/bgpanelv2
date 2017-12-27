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
 * Install Wizard
 */
class Core_Wizard_Application extends Core_Abstract_Application
{

    /**
     * BGP_Installer_Application constructor.
     *
     * @param $module
     * @param $page
     * @param $content_type
     * @throws BGP_Exception
     */
    public function __construct($module, $page, $content_type = "text/html")
    {
        parent::__construct($module, $page, 0, $content_type);

        if ( !is_dir( INSTALL_DIR ) ) {
            throw new Core_Exception(
                'Install Wizard Disabled !',
                'FOR SECURITY REASONS, THE INSTALL WIZARD IS NOT AVAILABLE WITHOUT THE `install` DIRECTORY AT THE ROOT OF THE APPLICATION.',
            'You will not be able to proceed beyond this point until the installation directory is being created.'
            );
        }
    }

    public function init()
    {
        return;
    }

    public function execute()
    {
        $wizard = new Wizard();

        exit(var_dump($wizard));
    }
}