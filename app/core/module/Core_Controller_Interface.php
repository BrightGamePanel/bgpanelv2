<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 28/12/2017
 * Time: 20:14
 */

interface Core_Controller_Interface
{
    /**
     * Invoke the Designated Method on this Controller with its Args
     *
     * @param array $controller_method_prototype The method signature to call, including its name and args
     * @param array $args The args to pass to the method
     * @return mixed
     */
    public function invoke($controller_method_prototype, $args);
}