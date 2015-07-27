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
$js = new Core_JS_GUI( $module );

/**
 * Build Page Header
 */
$gui->getHeader();

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
										<?php echo T_('Use this key to authenticate your application, in order to remotely access BGPanel components.'); ?><br />
									</div>
									<legend><?php echo T_('API Key'); ?>:</legend>
									<div>
										<pre class="text-center"><?php
//------------------------------------------------------------------------------------------------------------+

										echo APP_API_KEY;

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
 * @arg $task
 * @arg $inputs
 * @arg $redirect
 */

$js->getAngularController();

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

// Clean Up
unset( $module, $gui, $js );

?>