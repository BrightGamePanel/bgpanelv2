<?php

interface Core_Application_Interface {

    /**
     * Initialize the Application
     *
     * @return void
     * @throws Core_Verbose_Exception
     */
    public function init();

    /**
     * Execute the Application Query and Update User Activity
     *
     * @return int
     * @throws Core_Exception
     */
    public function execute();

    /**
     * Returns the current Authentication Service used by the Application
     *
     * @return Core_Auth_Service_Interface
     */
    public function getAuthenticationService();
}