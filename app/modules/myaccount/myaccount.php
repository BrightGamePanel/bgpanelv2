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

require( MODS_DIR . '/' . basename(__DIR__) . '/myaccount.class.php' );

$module = new Core_Abstract_Module_Myaccount();

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
$gui->getTabs( 'profile' );


// Get languages

$languages = parse_ini_file( CONF_LANG_INI );

// Get templates

$templates = parse_ini_file( CONF_TEMPLATES_INI );

// Get profile settings from database

$dbh = Core_Database_Service::getDBH(); // Get Database Handle

$uid = Core_Abstract_Auth_Service::getSessionInfo('ID'); // Get user id

$sth = $dbh->prepare("
	SELECT *
	FROM user
	WHERE user_id = :uid
;");

$sth->bindParam( ':uid', $uid );

$sth->execute();

$profile = $sth->fetchAll( PDO::FETCH_ASSOC );
$profile = $profile[0];

// Lang list as json

$langMap = '[';
foreach ($languages as $key => $value) {
	$langMap .= '{' . 'value: "' . $value . '", name: "' . $key . '"}' . ',';
}
$langMap = substr($langMap, 0, -1);
$langMap .= ']';

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
									<h3 class="panel-title"><?php echo T_('My Account Configuration'); ?></h3>
								</div>

								<div class="panel-body">
									<form name="thisForm" ng-submit="onSubmit(thisForm)">
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
		username: {
			title: '" . T_('Login') . "',
			type: 'string'
		},
		password0: {
			title: '" . T_('Password') . "',
			type: 'string'
		},
		password1: {
			title: '" . T_('Confirm Password') . "',
			type: 'string'
		},
		firstname: {
			title: '" . T_('First Name') . "',
			type: 'string'
		},
		lastname: {
			title: '" . T_('Last Name') . "',
			type: 'string'
		},
		email: {
			title: '" . T_('Email') . "',
			type: 'string'
		},
		language: {
			title: '" . T_('Language') . "',
			type: 'string'
		},
		template: {
			title: '" . T_('Template') . "',
			type: 'string'
		}
	},
	'required': [
		'username',
		'password0',
		'password1',
		'email',
		'language'
	]
}";

// Form Definition
$form = "
[
	{
		'key': 'username',
		'type': 'text',
		fieldAddonLeft: '<span class=\"glyphicon glyphicon-user\"></span>',
		placeholder: '" . T_('Login') . "'
	},
	{
		'key': 'password0',
		'type': 'password',
		fieldAddonLeft: '<span class=\"glyphicon glyphicon-lock\"></span>',
		placeholder: '" . T_('Password') . "'
	},
	{
		'key': 'password1',
		'type': 'password',
		fieldAddonLeft: '<span class=\"glyphicon glyphicon-lock\"></span>',
		placeholder: '" . T_('Password') . "'
	},
	{
		'key': 'firstname',
		'type': 'text',
		fieldAddonLeft: 'Optional'
	},
	{
		'key': 'lastname',
		'type': 'text',
		fieldAddonLeft: 'Optional'
	},
	{
		'key': 'email',
		'type': 'email',
		fieldAddonLeft: '<span class=\"glyphicon glyphicon-envelope\"></span>',
		placeholder: '" . T_('Email') . "'
	},
	{
		'key': 'language',
		'type': 'select',
		titleMap: " . $langMap . "
	},
	{
		'key': 'template',
		'type': 'select',
		titleMap: " . $templateMap . "
	}
]";

// Model Init
$model = json_encode( array(
		'username' 		=> htmlspecialchars( $profile['username'], ENT_QUOTES),
		'password0',
		'password1',
		'firstname' 	=> htmlspecialchars( $profile['firstname'], ENT_QUOTES),
		'lastname' 		=> htmlspecialchars( $profile['lastname'], ENT_QUOTES),
		'email' 		=> htmlspecialchars( $profile['email'], ENT_QUOTES),
		'language'		=> htmlspecialchars( Core_Abstract_Auth_Service::getSessionInfo('LANG'), ENT_QUOTES),
		'template'		=> htmlspecialchars( $profile['template'], ENT_QUOTES)
	), JSON_FORCE_OBJECT );

$js->getAngularCode( 'updateUserConfig', $schema, $form, $model );

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
