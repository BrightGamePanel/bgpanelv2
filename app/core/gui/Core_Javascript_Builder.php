<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 02/01/2018
 * Time: 19:06
 */

class Core_Javascript_Builder
{
    /**
     * @var Core_Page_Interface Page handle
     */
    private $page = null;

    /**
     * @var string
     */
    private $scopeSchema = '{}';

    /**
     * @var string
     */
    private $scopeForm = '[]';

    /**
     * @var string
     */
    private $scopeModel = '{}';

    /**
     * Core_Javascript_Builder constructor.
     * @param $page Core_Page_Interface Page handle
     */
    public function __construct($page)
    {
        $this->page = $page;
    }

    /**
     * Generate An AngularJS Controller
     *
     * Powered by Textalk/angular-schema-form
     *
     * @see https://github.com/Textalk/angular-schema-form/blob/development/docs/index.md
     * @return void
     */
    public function buildNGController() {
?>

                    <!-- ANGULAR JS -->
                    <script>
                        console.clear();

                        angular
                            .module('bgpApp', [
                            'schemaForm',
                            'cgBusy',
                            'ngFileSaver'
                            ])
                            .controller('bgpCtrl', [
                            '$scope',
                            '$http',
                            '$location',
                            'FileSaver',
                            'Blob',
                            function($scope, $http, $location, FileSaver, Blob)
                            {
                                // Schema
                                $scope.schema = <?php echo $this->scopeSchema; ?>;

                                // Form
                                $scope.form = <?php echo $this->scopeForm; ?>;

                                // Model
                                $scope.model = <?php echo $this->scopeModel; ?>;

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
                                        angular.forEach($scope.formErrors, function(value, key) {
                                            angular.forEach(value, function(subValue, subKey) {
                                                if (subKey !== 0) {
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
                                        $scope.bgpPromise = $http({
                                            method  : 'POST',
                                            url     : $location.url(),
                                            data    : $scope.model
                                        })
                                        .success(function(data, status, headers)
                                        {
                                            // Check media-type

                                            // Download resource if it is not JSON

                                            if ( headers()['content-type'].indexOf('application/json') === -1 ) {

                                                var fileContents = new Blob([ data ], { type : headers()['content-type'] });
                                                var fileName = headers()['content-disposition'].replace('attachment; filename=\"', '').slice(0, -1);

                                                FileSaver.saveAs(fileContents, fileName);

                                                return;
                                            }

                                            // JSON Processing

                                            if (!data.success || (data.success === false))
                                            {
                                                // Reset errors repository

                                                $scope.formErrors = {};

                                                // If not successful, bind errors to error variables

                                                angular.forEach(data.errors, function(value, key) {

                                                    // Bind validation messages

                                                    // Multiple errors case

                                                    if (angular.isArray(value)) {

                                                        var tmp   = value;
                                                        var value = '';
                                                        angular.forEach(tmp, function(subValue, subKey) {
                                                            value = value + subValue + '. ';
                                                        });
                                                    }

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

                                            // TODO : implement redirect by reading http headers

                                            if (data.success && (data.success === true))
                                            {
                                                // If successful, we redirect the user to the resource

                                                //window.location = ( "'./'" );
                                            }

                                            // Bind notification message to message

                                            $scope.msgType = data.msgType;
                                            $scope.msg = data.msg;
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
                            }]);
                    </script>
                    <!-- ANGULAR JS -->

<?php
    }
}