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

require( MODS_DIR . '/' . basename(__DIR__) . '/tools.class.php' );

$module = new BGP_Module_Tools_Wadl( 'wadl' );

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


/**
 * PAGE BODY
 */
//------------------------------------------------------------------------------------------------------------+
?>
					<!-- CONTENTS -->
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class="panel panel-default">

								<div class="panel-heading">
									<h3 class="panel-title"><?php echo T_('Download WADL File'); ?></h3>
								</div>

								<div class="panel-body">
									<div class="alert alert-info" role="alert">
										<strong><?php echo T_('Tip'); ?></strong><br />
										<?php echo T_('This file will describe all the methods you can access through the REST API with your account.'); ?><br />
										<?php echo T_('You can use SoapUI by SmartBear to import the WADL file in a REST project.'); ?>
										<a target="_blank" href="https://www.soapui.org/">SoapUI.org</a>
									</div>

									<div class="well" style="max-width: 400px; margin: 0 auto 10px; padding-left: 35px; padding-right: 35px;">
										<form name="thisForm" ng-submit="onSubmit(thisForm)">
											<div sf-schema="schema" sf-form="form" sf-model="model"></div>

											<div class="text-center">
												<button class="btn btn-primary btn-lg btn-block" type="submit" ng-disabled="thisForm.$invalid && !thisForm.$submitted"><?php echo T_('Download WADL File !'); ?></button>
											</div>
										</form>
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

$schema = json_encode( array(), JSON_FORCE_OBJECT );
$form   = json_encode( array(), JSON_FORCE_OBJECT );
$model  = json_encode( array(), JSON_FORCE_OBJECT );

$js->getAngularCode( 'getWADL', $schema, $form, $model, './tools/wadl' );

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
