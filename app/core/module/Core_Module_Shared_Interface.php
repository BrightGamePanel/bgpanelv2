<?php

interface Core_Module_Shared_Interface {

    /**
     * Get stylesheets dependencies
     */
    public function getStylesheets();

    /**
     * Get javascript dependencies
     */
    public function getJavascript();

    /**
     * Get options
     */
    public function getOptions();

    /**
     * Get icon
     */
    public function getIcon();
}