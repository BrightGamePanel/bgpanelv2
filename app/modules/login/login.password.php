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

require( MODS_DIR . '/' . basename(__DIR__) . '/login.class.php' );

$module = new Core_Abstract_Module_Login();

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

// Call security component
$authService = Core_AuthService::getAuthService();

if ( $authService->isBanned() ) {
?>
					<!-- BAN MSG -->
					<div id="banmsg" class="alert alert-warning" role="alert">
						<strong><?php echo T_('Too many incorrect login attempts'); ?></strong>
						<?php echo T_('Please wait'); echo ' ' . CONF_SEC_BAN_DURATION . ' '; echo T_('seconds before trying again.'); ?>
					</div>
					<!-- END: BAN MSG -->
<?php
}

?>
					<!-- CONTENTS -->
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="panel panel-default">
								<div class="panel-heading">
									<img src="./gui/img/logo.png" alt="Bright Game Panel Logo" class="img-responsive center-block">
								</div>

								<div class="panel-body">
									<legend><?php echo T_('Lost Password'); ?></legend>

									<form name="thisForm" ng-submit="onSubmit(thisForm)">
										<div sf-schema="schema" sf-form="form" sf-model="model"></div>

										<!-- CAPTCHA -->
										<img class="img-thumbnail" id="captcha" src="./login/process?task=getCaptcha" alt="CAPTCHA Image" />
										<button
											class="btn"
											type="button"
											onclick="document.getElementById('captcha').src = './login/process?task=getCaptcha&amp;' + Math.random(); return false">
											<span class="glyphicon glyphicon-retweet"></span>
										</button>
										<!-- END: CAPTCHA -->

										<div class="form-group" ng-class="{'has-error': formErrors.captcha[0]}">
											<label for="captcha">Captcha</label>
											<div class="input-group">
												<div class="input-group-addon"><span class="glyphicon glyphicon-picture"></span></div>
												<input class="form-control" type="text" ng-model="model['captcha']" id="captcha" name="captcha" placeholder="<?php echo T_('Captcha Code'); ?>" required>
											</div>
											<span class="help-block" ng-show="formErrors.captcha[0]" ng-bind="formErrors.captcha[0]"></span>
											<p class="help-block"><?php echo T_('Refresh the CAPTCHA image each time you submit the form above.'); ?></p>
										</div>

										<button class="btn btn-primary btn-lg btn-block" type="submit" ng-disabled="thisForm.$invalid && !thisForm.$submitted"><?php echo T_('Send Password'); ?></button>
									</form>

									<ul class="pager">
										<li>
											<a href="./login">&larr;&nbsp;<?php echo T_('Back'); ?></a>
										</li>
									</ul>
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
			title: '" . T_('Username') . "',
			type: 'string'
		},
		email: {
			title: '" . T_('Email') . "',
			type: 'string'
		}
	},
	'required': [
		'username',
		'email'
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
		'key': 'email',
		'type': 'email',
		fieldAddonLeft: '<span class=\"glyphicon glyphicon-envelope\"></span>',
		placeholder: '" . T_('Email') . "'
	}
]";

$model = json_encode( array(), JSON_FORCE_OBJECT );

$js->getAngularCode( 'sendNewPassword', $schema, $form, $model, './login' );

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
