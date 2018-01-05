<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 28/12/2017
 * Time: 22:09
 */

class Wizard_Page extends Core_Abstract_Page
{

    /**
     * Body of this page
     * This method is called by render()
     */
    public function body()
    {
?>
                    <!-- License -->
                    <pre class="pre-scrollable">
<?php

$license = fopen(LICENSE_FILE, 'r');
while ($rows = fgets($license)) {
    echo $rows;
}
fclose($license);

?>
                    </pre>
                    <form name="thisForm" ng-submit="onSubmit(thisForm)">

                        <div sf-schema="schema" sf-form="form" sf-model="model"></div>

                        <div class="text-center">
                            <button class="btn btn-primary" type="submit" ng-disabled="thisForm.$invalid && !thisForm.$submitted"><?php echo T_('Submit'); ?></button>
                            <button class="btn btn-default" type="reset"><?php echo T_('Cancel Changes'); ?></button>
                        </div>
                    </form>
<?php
    }

    /**
     * Process the page
     * This method is executed by default when a POST request is submitted with the page as the target
     *
     * @param array $query_args Request parameters
     * @return int
     */
    public function process($query_args = array())
    {
        $response = $this->parent_module->getController()->invoke('acceptLicense', $query_args);
        if ($response['success'] === TRUE) {
            $this->redirectionOnSuccess($response);
        }
        echo $this->parent_module->getController()->format($response);
        return 0;
    }

    /**
     * Schema of the form as a JSON String
     *
     * @return string
     */
    public function schema()
    {
        return "
{
	type: 'object',
	properties: {
		agreement: {
			title: '" . T_('I Accept the Terms of the License Agreement') . "',
			type: 'boolean'
		}
	},
	'required': [
		'agreement'
	]
}";
    }

    /**
     * Form body as a JSON String
     *
     * @return string
     */
    public function form()
    {
        return "
[
	{
		type: 'help',
		helpvalue: \"<label>License Agreement</label>\"
	},
	{
		key: 'agreement',
		type: 'checkbox',
		disableSuccessState: true,
	}
]";
    }

    /**
     * Model of the forms a JSON String
     *
     * @return string
     */
    public function model()
    {
        return json_encode( array(), JSON_FORCE_OBJECT );
    }

    /**
     * Appends to the response the redirection on a successful query
     *
     * @param $response
     * @return void
     */
    public function redirectionOnSuccess(&$response)
    {
        $response['location'] = './' . $this->parent_module->getName() . '/step1';
    }
}