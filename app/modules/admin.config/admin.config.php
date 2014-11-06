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
 * @copyright	Copyleft 2014, Nikita Rousseau
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @link		http://www.bgpanel.net/
 */

/**
 * Load Plugin
 */

require( MODS_DIR . '/' . basename(__DIR__) . '/admin.config.class.php' );

$module = new BGP_Module_Admin_Config();

/**
 * Call GUI Builder
 */
$gui = new Core_GUI( $module );

/**
 * Javascript Generator
 */
$js = new Core_JS_GUI();

/**
 * Build Page Header
 */
$gui->getHeader();

/**
 * Build Page Tabs
 */
$gui->getTabs( 'general' );


// Get templates
$templates = parse_ini_file( CONF_TEMPLATES_INI );

// Get current config from database

$dbh = Core_DBH::getDBH(); // Get Database Handle

$sth = $dbh->prepare("
	SELECT setting, value
	FROM " . DB_PREFIX . "config
	;");

$sth->execute();

$current_config = $sth->fetchAll( PDO::FETCH_ASSOC );
$current_config = bgp_get_conf_array( $current_config );


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
									<h3 class="panel-title"><?php echo T_('General Panel Configuration'); ?></h3>
								</div>

								<div class="panel-body">
									<form ng-submit="processForm()">
										<div class="row">
											<div class="col-xs-5">
												<fieldset disabled>
													<div class="form-group">
														<label for="version">Current Core Version</label>
														<input type="text" id="version" class="form-control" placeholder="<?php echo BGP_PANEL_VERSION; ?>">
													</div>
												</fieldset>
											</div>
										</div>

										<div class="row">
											<div class="col-xs-5">
												<div class="form-group" ng-class="{ 'has-error' : errorPanelName }">
													<label for="panelName"><?php echo T_('Panel Name'); ?></label>
													<input class="form-control" type="text" ng-model="formData.panelName" id="panelName" name="panelName" required>
													<span class="help-block" ng-show="errorPanelName" ng-bind="errorPanelName"></span>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-xs-8">
												<div class="form-group" ng-class="{ 'has-error' : errorPanelUrl }">
													<label for="panelUrl"><?php echo T_('Panel URL'); ?></label>
													<input class="form-control" type="text" ng-model="formData.panelUrl" id="panelUrl" name="panelUrl" required>
													<span class="help-block" ng-show="errorPanelUrl" ng-bind="errorPanelUrl"></span>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-xs-12">
												<div class="form-group">
													<label><?php echo T_('Maintenance Mode'); ?></label>
													<div class="radio">
														<label>
															<input type="radio" ng-model="formData.maintenanceMode" name="maintenanceMode" id="maintenanceMode1" ng-value="true" <?php 
															
															if ( $current_config['maintenance_mode'] == '1' ) {

																echo "ng-checked=\"true\"";
															}
															
															?>>
															On
														</label>
													</div>
													<div class="radio">
														<label>
															<input type="radio" ng-model="formData.maintenanceMode" name="maintenanceMode" id="maintenanceMode2" ng-value="false" <?php 
															
															if ( $current_config['maintenance_mode'] == '0' ) {

																echo "ng-checked=\"true\"";
															}
															
															?>>
															Off
														</label>
													</div>
													<span class="help-block">
														<?php echo T_('Switch the panel in maintenance mode.') . "\r\n"; ?>
														<?php echo T_('Only'); ?> <b><?php echo T_('Administrators'); ?></b> <?php echo T_('will be able to log into the panel.'); ?><br />
														<b><?php echo T_('NOTE: CRON JOB IS DISABLED IN THIS MODE!'); ?></b>
													</span>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-xs-5">
												<div class="form-group" ng-class="{ 'has-error' : errorAdminTemplate }">
													<label for="adminTemplate"><?php echo T_('Admin Template'); ?></label>
													<select class="form-control" type="text" ng-model="formData.adminTemplate" id="adminTemplate" name="adminTemplate" required>
<?php
//---------------------------------------------------------+

foreach ($templates as $key => $value)
{
	if ($value == BGP_ADMIN_TEMPLATE) {

//---------------------------------------------------------+
?>
														<option value="<?php echo $value; ?>" ng-selected="true"><?php echo $key; ?></option>
<?php
//---------------------------------------------------------+

	}
	else {

//---------------------------------------------------------+
?>
														<option value="<?php echo $value; ?>"><?php echo $key; ?></option>
<?php
//---------------------------------------------------------+

	}
}

reset($templates);

//---------------------------------------------------------+
?>
													</select>
													<span class="help-block" ng-show="errorAdminTemplate" ng-bind="errorAdminTemplate"></span>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-xs-5">
												<div class="form-group" ng-class="{ 'has-error' : errorUserTemplate }">
													<label for="userTemplate"><?php echo T_('User Template'); ?></label>
													<select class="form-control" type="text" ng-model="formData.userTemplate" id="userTemplate" name="userTemplate" required>
<?php
//---------------------------------------------------------+

foreach ($templates as $key => $value)
{
	if ($value == BGP_USER_TEMPLATE) {

//---------------------------------------------------------+
?>
														<option value="<?php echo $value; ?>" ng-selected="true"><?php echo $key; ?></option>
<?php
//---------------------------------------------------------+

	}
	else {

//---------------------------------------------------------+
?>
														<option value="<?php echo $value; ?>"><?php echo $key; ?></option>
<?php
//---------------------------------------------------------+

	}
}

//---------------------------------------------------------+
?>
													</select>
													<span class="help-block" ng-show="errorUserTemplate" ng-bind="errorUserTemplate"></span>
												</div>
											</div>
										</div>

										<hr>

										<div class="row">
											<div class="text-center">
												<button class="btn btn-primary" type="submit"><?php echo T_('Apply'); ?></button>
												<button class="btn btn-default" type="reset"><?php echo T_('Cancel Changes'); ?></button>
											</div>
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
 */

$fields = array(
		'PanelName' 		=> htmlspecialchars( $current_config['panel_name'], ENT_QUOTES),
		'PanelUrl' 			=> htmlspecialchars( $current_config['system_url'], ENT_QUOTES),
		'MaintenanceMode' 	=> htmlspecialchars( $current_config['maintenance_mode'], ENT_QUOTES),
		'AdminTemplate' 	=> htmlspecialchars( $current_config['admin_template'], ENT_QUOTES),
		'UserTemplate' 		=> htmlspecialchars( $current_config['user_template'], ENT_QUOTES)
	);

$js->getAngularController( 'updateSysConfig', $module::getModuleName( '/' ), $fields, './admin/config' );

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