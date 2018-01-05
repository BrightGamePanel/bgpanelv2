<?php

interface Core_Module_Shared_Interface {

    /**
     * Get module interface name
     */
    public function getName();

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
     * Get module base address
     */
    public function getHRef();

    /**
     * Get module icon
     */
    public function getIcon();
}