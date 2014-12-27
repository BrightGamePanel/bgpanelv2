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



class Core_JS_GUI
{

	/**
	 * Dynamically Generate An AngularJS Controller
	 *
	 * @param String $task
	 * @param String $module
	 * @param Array $variables
	 * @param String $redirect
	 * @return String
	 * @access public
	 */
	public function getAngularController( $task, $module = '', $variables = array(), $redirect = './', $debug = FALSE )
	{
//------------------------------------------------------------------------------------------------------------+
?>
					<script>
<?php

		// Define angular module/app
		$this->defAngularMod();

?>

						// create angular controller and pass in $scope and $http
						function bgpController($scope, $http) {

<?php

		// Do not define controller if no task has been given
		if (!empty($task))
		{
?>
							// $scope will allow this to pass between controller and view

							// create a JSON object to hold our form information
							// we specify the controller method
							$scope.formData = {
<?php

			// Complete Form Fields
			foreach ($variables as $var => $value)
			{
				if (!is_numeric($var) && !empty($value)) {
?>
								<?php echo "'" . $var . "'"; ?>:<?php echo "'$value'"; ?>,
<?php
				}
			}

?>
								'task':<?php echo "'$task'"; ?>

							};

							// Process the form
							$scope.processForm = function() {
								$http({
								method  : 'POST',
								url     : <?php echo "'./$module/process'"; ?>,
								data    : $.param($scope.formData),
								headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
								})
									.success(function(data) {
<?php

			// Debug param
			if ($debug)
			{
?>
										console.log(data); // Debug
<?php
			}

?>

										if (!data.success)
										{
											// If not successful, bind errors to error variables
<?php

			// Form Fields
			foreach ($variables as $var => $value)
			{

				// bind field errors to error variables
				if (is_numeric($var)) {

					// Hack in case the values are not defined in the array
					$var = lcfirst($value);
				}
?>
											$scope.error<?php echo ucfirst($var); ?> = data.errors.<?php echo $var; ?>;
<?php
			}

			// Display notification only on.error when a redirection has been specified
			if (!empty($redirect))
			{
?>

											// Bind notification message to message
											$scope.msgType = data.msgType;
											$scope.msg = data.msg;
<?php
			}

?>
										}
<?php

			// Redirect on.form.success if required
			if (!empty($redirect))
			{
?>

										if (data.success)
										{
											// If successful, we redirect the user to the resource
											window.location = ( <?php echo "'$redirect'"; ?> );
										}
<?php
			}
			// Display notification when no redirection has been specified
			else
			{
?>

										// Bind notification message to message
										$scope.msgType = data.msgType;
										$scope.msg = data.msg;
<?php
			}

?>
									})
									.error(function(data) {
										// An error has been triggered while submitting the form.

										$scope.msgType = 'danger';
										$scope.msg = data;
									});
							};
<?php
		}

?>
						}
					</script>
<?php
//------------------------------------------------------------------------------------------------------------+
	}

	/**
	 * Define Angular Module/App as JS Code
	 *
	 * @param none
	 * @return String
	 * @access private
	 */
	private function defAngularMod() {
//------------------------------------------------------------------------------------------------------------+
?>
						// define angular module/app
						var bgpApp = angular.module('bgpApp', []);
<?php
//------------------------------------------------------------------------------------------------------------+
	}

}
