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
class BGP_Installer_Application extends BGP_Abstract_Application
{

    /**
     * BGP_Installer_Application constructor.
     *
     * @param $module
     * @param $page
     * @param $id
     * @param $content_type
     */
    public function __construct($module, $page, $id, $content_type)
    {
        parent::__construct($module, $page, $id, $content_type);

        if ( !is_dir( INSTALL_DIR ) ) {
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="utf-8">
            </head>
            <body>
            <h1>Install Wizard Disabled !</h1><br />
            <h3>FOR SECURITY REASONS, THE INSTALL WIZARD IS NOT AVAILABLE WITHOUT THE `install` DIRECTORY AT THE ROOT OF THE APPLICATION.</h3>
            <p>You will not be able to proceed beyond this point until the installation directory has been created.</p>
            </body>
            </html>
            <?php
            die();
        }
    }

    /**
     * Execute the Query and Update User Activity
     *
     * @return int
     */
    public function execute()
    {
        exit('install mode');
    }
}