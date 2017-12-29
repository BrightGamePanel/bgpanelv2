<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 28/12/2017
 * Time: 20:13
 */

interface Core_Module_Interface
{
    /**
     * Returns the status of the module : enabled or not
     * @return bool
     */
    public function isEnable();

    /**
     * Render the given page
     * @param string $page The page to render as a string identifier
     * @param array $query_args Query arguments to render the requested page
     */
    public function render($page, $query_args = array());

    /**
     * Get module title
     */
    public function getTitle();
}