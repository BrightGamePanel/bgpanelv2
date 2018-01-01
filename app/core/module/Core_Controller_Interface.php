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
     * @param string $method_prototype_name The method name to call
     * @param array $invocation_args The request args to pass
     * @return array
     */
    public function invoke($method_prototype_name, $invocation_args);

    /**
     * Format the result of an Invocation
     *
     * @param array $return_array The method returned array to format
     * @param string $content_type The response format
     * @return string
     */
    public function format($return_array, $content_type = 'application/json');

    /**
     * Resolves the Method Signature with its associated Controller
     * Returns the method name
     *
     * @param $http_method
     * @param $url
     * @return string
     */
    public function resolve($http_method, $url);
}