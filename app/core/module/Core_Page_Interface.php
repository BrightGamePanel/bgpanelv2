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
     */
    public function renderPage();

    /**
     * Body of this page
     * This method is called by render()
     */
    public function body();

    /**
     * Get page title
     */
    public function getPageTitle();

    /**
     * Get parent module title
     */
    public function getModuleTitle();
}