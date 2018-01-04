<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 28/12/2017
 * Time: 20:13
 */

interface Core_Module_Interface extends Core_Module_Shared_Interface
{
    /**
     * Core_Module_Interface constructor.
     */
    public function __construct();

    /**
     * Returns the attached module controller
     *
     * @return Core_Controller_Interface
     */
    public function getController();

    /**
     * Returns the status of the module : enabled or not
     *
     * @return bool
     */
    public function isEnable();

    /**
     * Render the given page
     *
     * @param string $page The page to render as a string identifier
     * @param array $query_args Query arguments to render the requested page
     */
    public function render($page, $query_args = array());

    /**
     * Process the query
     * Implicit call to the controller method by calling the page process() method that implements the call
     *
     * @param $page
     * @param array $query_args
     * @return mixed
     */
    public function process($page, $query_args = array());

    /**
     * Get module title
     */
    public function getModuleTitle();
}