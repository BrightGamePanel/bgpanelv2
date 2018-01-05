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
        $this->scopeSchema = $page->schema();
        $this->scopeForm = $page->form();
        $this->scopeModel = $page->model();
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
                                            // If successful, we redirect the user to the resource
                                            if (data.success && (data.success === true) && data.location) {
                                                window.location = ( data.location );
                                            }

                                            // Download resource if it is not JSON
                                            // Check media-type
                                            if (headers()['content-type'].indexOf('application/json') === -1) {
                                                var fileContents = new Blob([ data ], { type : headers()['content-type'] });
                                                var fileDisposition = 'text/plain';
                                                if (headers()['content-disposition']) {
                                                    fileDisposition = headers()['content-disposition'];
                                                }
                                                var fileName = fileDisposition.replace('attachment; filename=\"', '');
                                                FileSaver.saveAs(fileContents, fileName);
                                                return;
                                            }

                                            // JSON Processing
                                            if (!data.success || (data.success === false))
                                            {
                                                // Reset errors repository
                                                $scope.formErrors = {};

                                                // If not successful,
                                                // inject errors into form
                                                angular.forEach(data.errors, function(value, key)
                                                {
                                                    // Multiple errors case
                                                    if (angular.isArray(value)) {
                                                        var tmp   = value;
                                                        var value = '';
                                                        angular.forEach(tmp, function(subValue, subKey) {
                                                            value = value + subValue + '. ';
                                                        });
                                                    }

                                                    $scope.$broadcast('schemaForm.error.' + key, value.toCamel(), value);

                                                    // Copy errors
                                                    $scope.formErrors[key] = {};
                                                    $scope.formErrors[key][value.toCamel()] = value;
                                                    $scope.formErrors[key][0] = value;
                                                });
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

                                            // Extract body (if any)
                                            var parser = new DOMParser();
                                            var doc = parser.parseFromString(data, "text/html");
                                            if (doc.getElementsByTagName('h1')[0]) {
                                                var title = doc.getElementsByTagName('h1')[0].innerHTML;
                                                var desc = '';
                                                if (doc.getElementsByTagName('p')[0]) {
                                                    desc = doc.getElementsByTagName('p')[0].innerHTML;
                                                }
                                                $scope.msg = title + ' : ' + desc;
                                            }
                                            else {
                                                $scope.msg = data;
                                            }
                                        });
                                    }
                                }
                            }]);
                    </script>
                    <!-- ANGULAR JS -->

<?php
    }
}