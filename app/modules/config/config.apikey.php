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
 * Load Plugin
 */

require( MODS_DIR . '/' . basename(__DIR__) . '/config.class.php' );

$module = new BGP_Module_Config_Apikey( 'apikey' );

/**
 * Call GUI Builder
 */
$gui = new Core_GUI( $module );

/**
 * Javascript Generator
 */
$js = new Core_GUI_JS( $module );

/**
 * Build Page Header
 */
$gui->getHeader();

// API Key

$apiMasterKey = parse_ini_file( CONF_API_KEY_INI );
$apiMasterKey = $apiMasterKey['APP_API_KEY'];

// Local vars

$system_url = BGP_SYSTEM_URL;
$resourcesBaseUrl = ($system_url[strlen($system_url)-1] != '/') ? BGP_SYSTEM_URL . '/api/' : BGP_SYSTEM_URL . 'api/';

/**
 * PAGE BODY
 */
//------------------------------------------------------------------------------------------------------------+
?>
					<!-- CONTENTS -->
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class="panel panel-default">
								<div class="panel-body">

									<div class="alert alert-info" role="alert">
										<strong><?php echo T_('Tip'); ?></strong><br />
										<?php echo T_('Use this key to authenticate your application, in order to remotely access BGPanel components.'); ?><br /><br />
										<strong><?php echo T_('REST API URI'); ?></strong> : <code><?php echo $resourcesBaseUrl; ?></code>
									</div>

<?php
//------------------------------------------------------------------------------------------------------------+

if (boolval(APP_API_ALLOW_BASIC_AUTH) === TRUE) {
?>
									<div class="alert alert-warning" role="alert">
										<strong><?php echo T_('Basic HTTP Authentication Allowed'); ?></strong><br />
										<?php echo T_('This may be a security issue (this authentication method doesn\'t require the API Key and is usually enabled for development purposes).'); ?>
									</div>
<?php
}

//------------------------------------------------------------------------------------------------------------+
?>
									<legend><?php echo T_('API Key'); ?>:</legend>

									<div>
										<pre class="text-center"><?php
//------------------------------------------------------------------------------------------------------------+

										echo $apiMasterKey;

//------------------------------------------------------------------------------------------------------------+
										?></pre>
									</div>

								</div>
							</div>
						</div>
					</div>
					<!-- END: CONTENTS -->

					<!-- SCRIPT -->
<?php

/**
 * Generate AngularJS Code
 *
 * @param 	String 	$task
 * @param 	String 	$schema
 * @param 	String 	$form
 * @param 	String 	$model
 * @param 	String 	$redirect
 */

$js->getAngularCode();

?>
					<!-- END: SCRIPT -->

<?php
//------------------------------------------------------------------------------------------------------------+
/**
 * END: PAGE BODY
 */

/**
 * Build Page Footer
 */
$gui->getFooter();

?>
