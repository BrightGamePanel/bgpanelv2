<?php

interface Core_Module_Shared_Interface {

    /**
     * Get module stylesheets dependencies
     */
    public function getStylesheets();

    /**
     * Get module javascript dependencies
     */
    public function getJavascript();

    /**
     * Get module options
     */
    public function getOptions();
}