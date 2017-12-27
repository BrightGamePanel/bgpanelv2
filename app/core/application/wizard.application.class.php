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
class BGP_Wizard_Application extends BGP_Abstract_Application
{
    /**
     * Game Configuration Database
     * Last Update: 30/08/2014 by warhawk3407
     * @var array the game configuration database
     */
    private $game_db;

    /**
     * @var string last known api version
     */
    private $last_api_version = '100';

    /**
     * @var array all known bgp database versions
     */
    private $bgp_db_versions = array(
        '0.1.0',
    );

    /**
     * @var string last known database model version
     */
    private $last_bgp_version;

    /**
     * BGP_Installer_Application constructor.
     *
     * @param $module
     * @param $page
     * @param $content_type
     * @throws BGP_Launch_Exception
     * @throws BGP_Exception
     */
    public function __construct($module, $page, $content_type = "text/html")
    {
        parent::__construct($module, $page, 0, $content_type);

        if ( !is_dir( INSTALL_DIR ) ) {
            throw new BGP_Launch_Exception(
                'Install Wizard Disabled !',
                'FOR SECURITY REASONS, THE INSTALL WIZARD IS NOT AVAILABLE WITHOUT THE `install` DIRECTORY AT THE ROOT OF THE APPLICATION.',
            'You will not be able to proceed beyond this point until the installation directory is being created.'
            );
        }

        $this->game_db = Array
        (
            // Counter-Strike: Source
            1 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value' => 'cs_assault'
                )
            ),

            // Day of Defeat: Source
            2 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'dod_anzio'
                ),
                1 => Array
                (
                    'name'	=> 'Tickrate',
                    'value'	=> '100'
                )
            ),

            // Half-Life 2: Deathmatch
            3 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'dm_lockdown'
                ),
                1 => Array
                (
                    'name'	=> 'Tickrate',
                    'value'	=> '100'
                )
            ),

            // Team Fortress 2
            4 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'ctf_2fort'
                )
            ),

            // Left 4 Dead
            5 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'l4d_hospital01_apartment'
                )
            ),

            // Left 4 Dead 2
            6 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'c1m1_hotel'
                )
            ),

            // Counter-Strike
            7 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'de_dust2'
                ),
                1 => Array
                (
                    'name'	=> 'Pingboost',
                    'value'	=> '2'
                )
            ),

            // Killing Floor
            8 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'KF-Bedlam.rom'
                ),
                1 => Array
                (
                    'name'	=> 'VACSecure',
                    'value'	=> 'True'
                ),
                2 => Array
                (
                    'name'	=> 'AdminName',
                    'value'	=> 'admin'
                ),
                3 => Array
                (
                    'name'	=> 'AdminPassword',
                    'value'	=> 'passwd'
                ),
                4 => Array
                (
                    'name'	=> 'INI File',
                    'value'	=> 'KillingFloor.ini'
                )
            ),

            // Call of Duty 4: Modern Warfare
            9 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                ),
                1 => Array
                (
                    'name'	=> 'fs_homepath',
                    'value'	=> '/home/user/cod4'
                ),
                2 => Array
                (
                    'name'	=> 'fs_basepath',
                    'value'	=> '/home/user/cod4'
                )
            ),

            // Minecraft
            10 => Array
            (
            ),

            // Call of Duty: Modern Warfare 3
            11 => Array
            (
                0 => Array
                (
                    'name'	=> 'net_queryPort',
                    'value'	=> '27014'
                ),
                1 => Array
                (
                    'name'	=> 'net_authPort',
                    'value'	=> '8766'
                ),
                2 => Array
                (
                    'name'	=> 'net_masterServerPort',
                    'value'	=> '27016'
                ),
                3 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                )
            ),

            // Call of Duty 2
            12 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                ),
                1 => Array
                (
                    'name'	=> 'fs_homepath',
                    'value'	=> '/home/user/cod2'
                ),
                2 => Array
                (
                    'name'	=> 'fs_basepath',
                    'value'	=> '/home/user/cod2'
                )
            ),

            // Call of Duty: World at War
            13 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                ),
                1 => Array
                (
                    'name'	=> 'fs_homepath',
                    'value'	=> '/home/user/codwaw'
                ),
                2 => Array
                (
                    'name'	=> 'fs_basepath',
                    'value'	=> '/home/user/codwaw'
                )
            ),

            // Wolfenstein: Enemy Territory
            14 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                ),
                1 => Array
                (
                    'name'	=> 'fs_homepath',
                    'value'	=> '/home/user/wolfet'
                ),
                2 => Array
                (
                    'name'	=> 'fs_basepath',
                    'value'	=> '/home/user/wolfet'
                )
            ),

            // ArmA: 2
            15 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                )
            ),

            // Garrysmod
            16 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'gm_construct'
                )
            ),

            // Counter-Strike: Global Offensive
            17 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value' => 'cs_italy'
                ),
                1 => Array
                (
                    'name'	=> 'Map Group',
                    'value' => 'mg_hostage'
                ),
                2 => Array
                (
                    'name'	=> 'Game Type',
                    'value' => '0'
                ),
                3 => Array
                (
                    'name'	=> 'Game Mode',
                    'value' => '0'
                ),
                4 => Array
                (
                    'name'	=> 'Tickrate',
                    'value' => '64'
                )
            ),

            // ArmA: Armed Assault
            18 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                )
            ),

            // Battlefield 2
            19 => Array
            (
            ),

            // Battlefield 1942
            20 => Array
            (
            ),

            // Multi Theft Auto
            21 => Array
            (
            ),

            // San Andreas: Multiplayer (SA-MP)
            22 => Array
            (
            ),

            // Urban Terror
            23 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                )
            )
        );

        $this->last_bgp_version = end($this->bgp_db_versions);
    }

    /**
     * Initialize the Application
     * Acts like an extended constructor
     *
     * @return void
     */
    public function init()
    {
        return;
    }

    public function execute()
    {
        exit('install mode');
    }
}