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



class Core_GUI_JS
{
	private $module_name;

	/**
	 * Default Constructor
	 *
	 * @return void
	 * @access public
	 */
	function __construct( $bgp_module )
	{
		if ( !empty($bgp_module) && is_object($bgp_module) && is_subclass_of($bgp_module, 'BGP_Module') ) {
			$this->module_name = $bgp_module::getModuleName( );
		}
	}

	/**
	 * Dynamically Generate An AngularJS Controller
	 * 
	 * Powered by Textalk/angular-schema-form
	 *
	 * @param 	String 	$task
	 * @param 	String 	$scopeSchema
	 * @param 	String 	$scopeForm
	 * @param 	String 	$scopeModel
	 * @param 	String 	$redirect
	 * @return 	String
	 * @see 	https://github.com/Textalk/angular-schema-form/blob/development/docs/index.md
	 * @access public
	 */
	public function getAngularCode(	$task = '',	$scopeSchema = '', $scopeForm = '', $scopeModel = '', $redirect = './' )
	{
		$module = $this->module_name;

		ob_start();

		// Required params
		if (empty($scopeSchema) || empty($scopeForm) || empty($scopeModel))
		{
//------------------------------------------------------------------------------------------------------------+
?>
					<script>
						console.clear();

						angular.module('bgpApp', []).controller('bgpCtrl', function() {});
					</script>
<?php
//------------------------------------------------------------------------------------------------------------+
			ob_end_flush();
			return 1;
		}

//------------------------------------------------------------------------------------------------------------+
?>
					<script>
						console.clear();

						/**
						 * AngularJS
						 */

						angular.module('bgpApp', ['schemaForm']).controller('bgpCtrl', function($scope, $http)
						{
							// Schema
							$scope.schema = <?php echo $scopeSchema; ?>;

							// Form
							$scope.form = <?php echo $scopeForm; ?>;

							// Model
							$scope.task = {'task': <?php echo "'$task'"; ?>};
							$scope.model = <?php echo $scopeModel; ?>;
							angular.extend($scope.model, $scope.task);

							// Errors repository
							$scope.formErrors = {};

							// Submit Function

							$scope.onSubmit = function(form)
							{
								// Reset backend validation (if any) because of its async state
								//
								//  * Fake validation
								//  * Refresh model
								//  * Refresh form validation

								if ($scope.formErrors)
								{
									angular.forEach($scope.formErrors, function(value, key)
									{
										angular.forEach(value, function(subValue, subKey)
										{
											if (subKey != 0) {
												// Reset the previous error

												$scope.$broadcast('schemaForm.error.' + key, subValue.toCamel(), true); 

												// Refresh model

												$scope.model[key] = form[key].$$lastCommittedViewValue; 

												// Validate the new form entry

												form[key].$$parseAndValidate(); 
											}
										});
									});
								}

								// Client side validation

								$scope.$broadcast('schemaFormValidate');

								// Backend side validation

								if (form.$valid) {

									// Post form to process page

									$http({
										method  : 'POST',
										url     : <?php echo "'./$module/process'"; ?>,
										data    : $.param($scope.model),
										headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
									})

									.success(function(data)
									{
										if (!data.success || (data.success == false))
										{
											// Reset errors repository

											$scope.formErrors = {};

											// If not successful, bind errors to error variables

											angular.forEach(data.errors, function(value, key) {

												// Bind validation messages

												$scope.$broadcast('schemaForm.error.' + key, value.toCamel(), value);

												// Copy errors to another repository
												// Useful for hybrid forms

												$scope.formErrors[key] = {};
												$scope.formErrors[key][value.toCamel()] = value;
												$scope.formErrors[key][0] = value;
											});

											// Bind notification message to message

											$scope.msgType = data.msgType;
											$scope.msg = data.msg;
										}

<?php
//------------------------------------------------------------------------------------------------------------+

		// Redirect on.form.success if required
		if (!empty($redirect))
		{
?>
										if (data.success && (data.success == true))
										{
											// If successful, we redirect the user to the resource

											window.location = ( <?php echo "'$redirect'"; ?> );
										}
<?php
		}
		// Display notification message when no redirection is specified
		else
		{
?>
										// Bind notification message to message

										$scope.msgType = data.msgType;
										$scope.msg = data.msg;
<?php
		}

//------------------------------------------------------------------------------------------------------------+
?>
									})

									.error(function(data)
									{
										// An error has been triggered while submitting the form

										// Bind notification message to message

										$scope.msgType = 'danger';
										$scope.msg = data;
									});
								}
							}
						});

					</script>
<?php
//------------------------------------------------------------------------------------------------------------+

		ob_end_flush();
	}
}
