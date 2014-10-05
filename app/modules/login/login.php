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
 * @categories	Games/Entertainment, Systems Administration
 * @package		Bright Game Panel V2
 * @author		warhawk3407 <warhawk3407@gmail.com> @NOSPAM
 * @copyleft	2014
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @version		0.1
 * @link		http://www.bgpanel.net/
 */

/**
 * Load Plugin
 */

require( MODS_DIR . '/login/login.class.php' );

$loginModule = new BGP_Module_Login();

/**
 * Call GUI Builder
 */
$gui = new Core_GUI( $loginModule );

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
						<div class="col-md-6 col-md-offset-3">
							<div class="well well-lg">
								<div>
									<img src="./gui/img/logo.png" alt="Bright Game Panel Logo" class="img-responsive center-block">
								</div>
								<br>

								<legend><?php echo T_('Sign In'); ?></legend>

								<form ng-submit="processForm()">
									<div class="form-group" ng-class="{ 'has-error' : errorUsername }">
										<label for="username"><?php echo T_('Username'); ?></label>
										<div class="input-group">
											<div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>
											<input class="form-control" type="text" ng-model="formData.username" id="username" name="username" placeholder="<?php echo T_('Login'); ?>" required>
										</div>
										<span class="help-block" ng-show="errorUsername">{{ errorUsername }}</span>
									</div>

									<div class="form-group" ng-class="{ 'has-error' : errorPassword }">
										<label for="password"><?php echo T_('Password'); ?></label>
										<div class="input-group">
											<div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
											<input class="form-control" type="password" ng-model="formData.password" id="password" name="password" placeholder="<?php echo T_('Password'); ?>" required>
										</div>
										<span class="help-block" ng-show="errorPassword">{{ errorPassword }}</span>
									</div>

									<div class="checkbox">
										<label>
											<input type="checkbox" ng-model="formData.rememberMe" name="rememberMe" checked="checked"><?php echo T_('Remember Me'); ?>&nbsp;
										</label>
									</div>

									<button class="btn btn-default btn-lg btn-block" type="submit"><?php echo T_('Login'); ?></button>
								</form>

								<ul class="pager">
									<li>
										<a href="#password"><?php echo T_('Forgot Password?'); ?></a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- END: CONTENTS -->

					<!-- SCRIPT -->
					<script>
						// define angular module/app
						var bgpApp = angular.module('bgpApp', []);

						// create angular controller and pass in $scope and $http
						function bgpController($scope, $http) {
							// create a JSON object to hold our form information
							// $scope will allow this to pass between controller and view
							// we specify the controller method
							$scope.formData = {"task":"authenticateUser"};

							// Process the form
							$scope.processForm = function() {
								$http({
								method  : 'POST',
								url     : './login/process',
								data    : $.param($scope.formData),
								headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
								})
									.success(function(data) {
										// console.log(data); // Debug

										if (!data.success) {
											// If not successful, bind errors to error variables
											$scope.errorUsername = data.errors.username;
											$scope.errorPassword = data.errors.password;
										}

										// Bind notification message to message
										$scope.msgType = data.msgType;
										$scope.msg = data.msg;

										if (data.success) {
											// If successful, we redirect the user to the dashboard
											// MISSING CODE
										}
									});
							};
						}
					</script>
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
unset( $loginModule );

?>