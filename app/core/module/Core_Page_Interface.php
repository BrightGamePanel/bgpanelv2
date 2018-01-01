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
    public function renderPage($query_args = array());

    /**
     * Body of this page
     * This method is called by render()
     */
    public function body();

    /**
     * Get page name
     */
    public function getName();

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