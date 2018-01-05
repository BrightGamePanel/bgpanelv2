<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 28/12/2017
 * Time: 22:09
 */

class Wizard_Step2_Page extends Core_Abstract_Page
{

    /**
     * Body of this page
     * This method is called by render()
     */
    public function body()
    {
        ?>
        <a href="http://localhost/bgpanelv2/wizard/step3">go to STEP3</a>
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
        // TODO: Implement process() method.
    }

    /**
     * Schema of the form as a JSON String
     *
     * @return string
     */
    public function schema()
    {
        // TODO: Implement schema() method.
    }

    /**
     * Form body as a JSON String
     *
     * @return string
     */
    public function form()
    {
        // TODO: Implement form() method.
    }

    /**
     * Model of the forms a JSON String
     *
     * @return string
     */
    public function model()
    {
        // TODO: Implement model() method.
    }

    /**
     * Appends to the response the redirection on a successful query
     *
     * @param $response
     * @return void
     */
    public function redirectionOnSuccess(&$response)
    {
        // TODO: Implement redirectionOnSuccess() method.
    }
}