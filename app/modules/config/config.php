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

$module = new BGP_Module_Config();

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
$gui->getTabs( 'general' );


// Get templates
$templates = parse_ini_file( CONF_TEMPLATES_INI );

// Get current config from database

$dbh = Core_DBH::getDBH(); // Get Database Handle

$sth = $dbh->prepare("
	SELECT setting, value
	FROM config
	;");

$sth->execute();

$current_config = $sth->fetchAll( PDO::FETCH_ASSOC );

foreach ($current_config as $key => $config) {
	$current_config[$config['setting']] = $config['value'];

	unset($current_config[$key]);
}

// Template list as json

$templateMap = '[';
foreach ($templates as $key => $value) {
	$templateMap .= '{' . 'value: "' . $value . '", name: "' . $key . '"}' . ',';
}
$templateMap = substr($templateMap, 0, -1);
$templateMap .= ']';


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

									<form name="thisForm" ng-submit="onSubmit(thisForm)">
										<div class="row">
											<div class="col-xs-5">
												<label>Current Core Version</label><br />
												<span class="label label-info"><?php echo BGP_PANEL_VERSION; ?></span><br /><br />
											</div>
										</div>

										<div sf-schema="schema" sf-form="form" sf-model="model"></div>

										<div class="text-center">
											<button class="btn btn-primary" type="submit" ng-disabled="thisForm.$invalid && !thisForm.$submitted"><?php echo T_('Apply'); ?></button>
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

// Schema Definition
$schema = "
{
	type: 'object',
	properties: {
		panelName: {
			title: '" . T_('Panel Name') . "',
			type: 'string'
		},
		systemUrl: {
			title: '" . T_('Panel URL') . "',
			type: 'string'
		},
		maintenanceMode: {
			title: '" . T_('Enable Maintenance Mode') . "',
			type: 'boolean'
		},
		userTemplate: {
			title: '" . T_('Default User Template') . "',
			type: 'string'
		}
	},
	'required': [
		'panelName',
		'systemUrl',
		'userTemplate'
	]
}";

// Form Definition
$form = "
[
	{
		key: 'panelName',
		type: 'text',
		fieldAddonLeft: '<span class=\"glyphicon glyphicon-font\"></span>'
	},
	{
		key: 'systemUrl',
		type: 'text',
		fieldAddonLeft: '<span class=\"glyphicon glyphicon-globe\"></span>'
	},
	{
		type: 'help',
		helpvalue: \"<label>Maintenance Mode</label>\"
	},
	{
		key: 'maintenanceMode',
		type: 'checkbox',
		disableSuccessState: true,
	},
	{
		type: 'help',
		helpvalue: \"Switch the panel in maintenance mode. Only <b>Administrators</b> will be able to log into the panel. <br> <b>NOTE: CRON JOB IS DISABLED IN THIS MODE!</b> <br><br>\"
	},
	{
		key: 'userTemplate',
		type: 'select',
		titleMap: " . $templateMap . "
	}
]";

// Model Init
$model = json_encode( array(
		'panelName' 		=> htmlspecialchars( $current_config['panel_name'], ENT_QUOTES ),
		'systemUrl'			=> htmlspecialchars( $current_config['system_url'], ENT_QUOTES ),
		'maintenanceMode'	=> boolval( $current_config['maintenance_mode'] ),
		'userTemplate' 		=> htmlspecialchars( $current_config['user_template'], ENT_QUOTES )
	), JSON_FORCE_OBJECT );

$js->getAngularCode( 'updateSysConfig', $schema, $form, $model, './config' );

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
