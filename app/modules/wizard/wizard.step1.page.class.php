<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 28/12/2017
 * Time: 22:09
 */

class Wizard_Step1_Page extends Core_Abstract_Page
{

    /**
     * Body of this page
     * This method is called by render()
     */
    public function body()
    {
        ?>
        <a href="http://localhost/bgpanelv2/wizard/step2">go to STEP2</a>
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
}