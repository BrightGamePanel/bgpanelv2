<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 28/12/2017
 * Time: 20:23
 */

interface Core_Page_Interface extends Core_Module_Shared_Interface
{
    /**
     * Render the page
     *
     * @param array $query_args Request parameters
     * @return void
     */
    public function render($query_args = array());

    /**
     * Process the page
     * This method is executed by default when a POST request is submitted with the page as the target
     *
     * @param array $query_args Request parameters
     * @return int
     */
    public function process($query_args = array());

    /**
     * Body of this page
     * This method is called by render()
     */
    public function body();

    /**
     * Schema of the form as a JSON String
     *
     * @return string
     */
    public function schema();

    /**
     * Form body as a JSON String
     *
     * @return string
     */
    public function form();

    /**
     * Model of the forms a JSON String
     *
     * @return string
     */
    public function model();

    /**
     * Appends to the response the redirection on a successful query
     *
     * @param $response
     * @return void
     */
    public function redirectionOnSuccess(&$response);

    /**
     * Get page title
     */
    public function getPageTitle();

    /**
     * Get page description
     */
    public function getPageDescription();

    /**
     * Get parent module title
     */
    public function getModuleTitle();

    /**
     * Get parent module base address
     */
    public function getModuleHRef();

    /**
     * Get parent page
     */
    public function getParent();

    /**
     * Set parent page
     *
     * @param Core_Page_Interface $parent
     * @return void
     */
    public function setParent($parent = null);
}