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

$module = new BGP_Module_Config_Cron( 'cron' );

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
										<?php echo T_('To enable server monitoring, set up the cron job to run every'); ?>&nbsp;<?php echo (CONF_CRONDELAY / 60); ?>&nbsp;<?php echo T_('minutes.'); ?><br />
										<?php echo T_('More information at'); ?>:&nbsp;<a target="_blank" href="http://wiki.bgpanel.net/doku.php?id=wiki:setting_up_cron_job"><b><u><?php echo T_('Setting Up Cron Job'); ?></u></b></a>
									</div>
									<legend><?php echo T_('Create the following Cron Job using PHP'); ?>:</legend>
									<div>
										<pre class="text-center"><?php
//------------------------------------------------------------------------------------------------------------+

										echo '*/' . (CONF_CRONDELAY / 60) . ' * * * * php -q ' .
										BASE_DIR .
										'/cron.php > /dev/null 2>&1';

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