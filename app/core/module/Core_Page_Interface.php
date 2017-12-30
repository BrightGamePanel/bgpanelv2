<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 28/12/2017
 * Time: 20:23
 */

interface Core_Page_Interface
{
    /**
     * Render the given page
     */
    public function render();

    /**
     * Body of this page
     * This method is called by render()
     */
    public function body();

    /**
     * Get page title
     */
    public function getTitle();

    /**
     * Get parent module title
     */
    public function getModuleTitle();

    /**
     * Get page stylesheets dependencies
     */
    public function getStylesheets();

    /**
     * Get page javascript dependencies
     */
    public function getJavascript();
}