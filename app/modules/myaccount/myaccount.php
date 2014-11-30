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

require( MODS_DIR . '/' . basename(__DIR__) . '/myaccount.class.php' );

$module = new BGP_Module_Myaccount();

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
$gui->getTabs( 'profile' );


// Get languages
$languages = parse_ini_file( CONF_LANG_INI );

// Get profile settings from database

$dbh = Core_DBH::getDBH(); // Get Database Handle

$role = strtolower(Core_AuthService::getSessionPrivilege()); // Get user role
$uid = Core_AuthService::getSessionInfo('ID'); // Get user id

if ($role == 'admin') {

	$sth = $dbh->prepare("
	SELECT *
	FROM " . DB_PREFIX . "admin
	WHERE admin_id = :uid
	;");
}
else {

	$sth = $dbh->prepare("
	SELECT *
	FROM " . DB_PREFIX . "user
	WHERE user_id = :uid
	;");
}

$sth->bindParam( ':uid', $uid );

$sth->execute();

$profile = $sth->fetchAll( PDO::FETCH_ASSOC );
$profile = $profile[0];


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
									<form ng-submit="processForm()">
										<div class="row">
											<div class="col-xs-5">
												<div class="form-group" ng-class="{ 'has-error' : errorUsername }">
													<label for="username"><?php echo T_('Username'); ?></label>
													<input class="form-control" type="text" ng-model="formData.username" id="username" name="username" required>
													<span class="help-block" ng-show="errorUsername" ng-bind="errorUsername"></span>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-xs-4">
												<div class="form-group" ng-class="{ 'has-error' : errorPassword0 }">
													<label for="password0"><?php echo T_('Password'); ?></label>
													<input class="form-control" type="password" ng-model="formData.password0" id="password0" name="password0" required>
													<span class="help-block" ng-show="errorPassword0" ng-bind="errorPassword0"></span>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-xs-4">
												<div class="form-group" ng-class="{ 'has-error' : errorPassword1 }">
													<label for="password1"><?php echo T_('Confirm Password'); ?></label>
													<input class="form-control" type="password" ng-model="formData.password1" id="password1" name="password1" required>
													<span class="help-block" ng-show="errorPassword1" ng-bind="errorPassword1"></span>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-xs-5">
												<div class="form-group" ng-class="{ 'has-error' : errorFirstname }">
													<label for="firstname"><?php echo T_('First Name'); ?></label>
													<div class="input-group">
														<input class="form-control" type="text" ng-model="formData.firstname" id="firstname" name="firstname">
														<div class="input-group-addon">Optional</div>
													</div>
													<span class="help-block" ng-show="errorFirstname" ng-bind="errorFirstname"></span>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-xs-5">
												<div class="form-group" ng-class="{ 'has-error' : errorLastname }">
													<label for="lastname"><?php echo T_('Last Name'); ?></label>
													<div class="input-group">
														<input class="form-control" type="text" ng-model="formData.lastname" id="lastname" name="lastname">
														<div class="input-group-addon">Optional</div>
													</div>
													<span class="help-block" ng-show="errorLastname" ng-bind="errorLastname"></span>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-xs-5">
												<div class="form-group" ng-class="{ 'has-error' : errorEmail }">
													<label for="email"><?php echo T_('Email'); ?></label>
													<div class="input-group">
														<div class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></div>
														<input class="form-control" type="email" ng-model="formData.email" id="email" name="email" required>
													</div>
													<span class="help-block" ng-show="errorEmail" ng-bind="errorEmail"></span>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-xs-2">
												<div class="form-group" ng-class="{ 'has-error' : errorLanguage }">
													<label for="language"><?php echo T_('Language'); ?></label>
													<select class="form-control" type="text" ng-model="formData.language" id="language" name="language" required>
<?php
//---------------------------------------------------------+

foreach ($languages as $key => $value)
{
	if ($value == Core_AuthService::getSessionInfo('LANG')) {

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
													<span class="help-block" ng-show="errorLanguage" ng-bind="errorLanguage"></span>
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
		'username' 		=> htmlspecialchars( $profile['username'], ENT_QUOTES),
		'password0',
		'password1',
		'firstname' 	=> htmlspecialchars( $profile['firstname'], ENT_QUOTES),
		'lastname' 		=> htmlspecialchars( $profile['lastname'], ENT_QUOTES),
		'email' 		=> htmlspecialchars( $profile['email'], ENT_QUOTES),
		'language'		=> htmlspecialchars( Core_AuthService::getSessionInfo('LANG'), ENT_QUOTES)
	);

$js->getAngularController( 'updateUserConfig', $module::getModuleName( '/' ), $fields, './');

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