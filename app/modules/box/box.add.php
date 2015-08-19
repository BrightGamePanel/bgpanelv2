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

require( MODS_DIR . '/' . basename(__DIR__) . '/box.class.php' );

$module = new BGP_Module_Box();

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
 * Build Page Tabs
 */
$gui->getTabs( 'add' );


/**
 * PAGE BODY
 */
//------------------------------------------------------------------------------------------------------------+
?>
					<!-- CONTENTS -->
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title"><?php echo T_('Add A New Box'); ?></h3>
								</div>

								<div class="panel-body">
									<form name="thisForm" ng-submit="onSubmit(thisForm)">
										<div sf-schema="schema" sf-form="form" sf-model="model"></div>

										<div class="text-center">
											<button class="btn btn-primary" type="submit" ng-disabled="thisForm.$invalid && !thisForm.$submitted"><?php echo T_('Submit'); ?></button>
											<button class="btn btn-default" type="reset"><?php echo T_('Cancel Changes'); ?></button>
										</div>
									</form>
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
