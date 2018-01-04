<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 04/01/2018
 * Time: 16:04
 */

class Core_Auth_Service_Anonymous implements Core_Auth_Service_Interface
{
    // Service Handle
    protected static $service_handle = null;

    public function login()
    {
        return false;
    }

    public function logout()
    {
    }

    public function isLoggedIn()
    {
        return false;
    }

    public function checkMethodAuthorization($module = '', $method = '', $uid = 0)
    {
        false;
    }

    public function checkPageAuthorization($module = '', $page = '', $uid = 0)
    {
        false;
    }

    public function getUid()
    {
        return 0;
    }

    public static function getService()
    {
        if (empty(self::$service_handle) ||
            !is_object(self::$service_handle) ||
            !is_a(self::$service_handle, 'Core_Auth_Service_Anonymous')) {
            self::$service_handle = new Core_Auth_Service_Anonymous();
        }

        return self::$service_handle;
    }
}