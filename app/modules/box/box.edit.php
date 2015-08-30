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

$module = new BGP_Module_Box_Edit( 'edit' );

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
 * Get Resource ID
 */
if (Flight::has('RESOURCE_ID')) {
	$resId = Flight::get('RESOURCE_ID');
	Flight::clear('RESOURCE_ID');
} else {
	// Bad Request
	Flight::redirect( '/400' );
}

// ===========================================================================================================

/**
 * Fetch Resource Model
 */

$dbh = Core_DBH::getDBH(); // Get Database Handle

$sth = $dbh->prepare("
	SELECT
		box.box_id,
		box.os_id,
		box.name,
		box.notes,
		box.steam_lib_path
	FROM " . DB_PREFIX . "box AS box
	JOIN " . DB_PREFIX . "os AS os
		ON box.os_id = os.os_id
	WHERE
		box.box_id = :resId
	;");

$sth->bindParam( ':resId', $resId );

$sth->execute();

$resModel = $sth->fetchAll( PDO::FETCH_ASSOC );
$resModel = $resModel[0];

// ===========================================================================================================

// Os list as json

$sth = $dbh->prepare("
	SELECT *
	FROM " . DB_PREFIX . "os
	;");

$sth->execute();

$os = $sth->fetchAll( PDO::FETCH_ASSOC );

$osMap = '[';
foreach ($os as $key => $value) {
	$osMap .= '{' . 'value: "' . $value['os_id'] . '", name: "' . $value['operating_system'] . '"}' . ',';
}
$osMap = substr($osMap, 0, -1);
$osMap .= ']';


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
									<h3 class="panel-title"><?php echo T_('Edit A Box'); ?></h3>
								</div>

								<div class="panel-body">
									<form name="thisForm" ng-submit="onSubmit(thisForm)">
										<div sf-schema="schema" sf-form="form" sf-model="model"></div>

										<div class="text-center">
											<button class="btn btn-primary" type="submit" ng-disabled="thisForm.$invalid && !thisForm.$submitted"><?php echo T_('Update'); ?></button>
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
		name: {
			title: '" . T_('Remote Machine Name') . "',
			type: 'string'
		},
		os: {
			title: '" . T_('Operating System') . "',
			type: 'string'
		},
		steamcmd: {
			title: '" . T_('SteamCMD Binary Path') . "',
			type: 'string'
		},
		notes: {
			title: '" . T_('Notes') . "',
			type: 'string'
		}
	},
	'required': [
		'name'
	]
}";

// Form Definition
$form = "
[
	{
		key: 'name',
		type: 'text',
		fieldAddonLeft: '<span class=\"glyphicon glyphicon-font\"></span>'
	},
	{
		key: 'os',
		type: 'select',
		titleMap: " . $osMap . "
	},
	{
		key: 'steamcmd',
		type: 'text',
		placeholder: '/home/{user}/steamcmd.sh',
		fieldAddonLeft: 'Optional',
		disableSuccessState: true
	},
	{
		key: 'notes',
		type: 'textarea',
		disableSuccessState: true
	},
]";

$model = json_encode( array(
		'id'		=> htmlspecialchars( $resId, ENT_QUOTES),
		'name'		=> htmlspecialchars( $resModel['name'], ENT_QUOTES),
		'os' 		=> htmlspecialchars( $resModel['os_id'], ENT_QUOTES),
		'steamcmd' 	=> htmlspecialchars( $resModel['steam_lib_path'], ENT_QUOTES),
		'notes' 	=> htmlspecialchars( $resModel['notes'], ENT_QUOTES)
	), JSON_FORCE_OBJECT );

$js->getAngularCode( 'putBox', $schema, $form, $model, './box/view/' . $resId );

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
