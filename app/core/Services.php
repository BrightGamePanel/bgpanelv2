<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 04/01/2018
 * Time: 14:53
 */

class Services
{
    /**
     * @var Core_Application_Interface
     */
    private static $application_handle = null;

    /**
     * @param Core_Application_Interface $application
     * @return void
     */
    public static function createServices($application) {

        if (empty($application) ||
            !is_object($application) ||
            !is_a($application, 'Core_Application_Interface')) {
            return;
        }

        self::$application_handle = $application;
    }

    /**
     * @return Core_Auth_Service_Interface
     */
    public static function getAuthenticationService() {
        return self::$application_handle->getAuthenticationService();
    }

    /**
     * @return PDO
     */
    public static function getDatabaseService() {
        return Core_Database_Service::getService();
    }

    /**
     * @return Core_Language_Service
     */
    public static function getLanguageService() {
        return Core_Language_Service::getService();
    }
}