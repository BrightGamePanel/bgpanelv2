<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 04/01/2018
 * Time: 15:15
 */

interface Core_Service_Interface
{
    /**
     * Returns an handle on the Service
     *
     * @return Core_Service_Interface
     */
    public static function getService();
}