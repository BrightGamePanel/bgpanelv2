<?php

/**
 * LICENSE:
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @package		Bright Game Panel V2
 * @version		0.1
 * @category	Systems Administration
 * @author		warhawk3407 <warhawk3407@gmail.com> @NOSPAM
 * @copyright	Copyleft 2015, Nikita Rousseau
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @link		http://www.bgpanel.net/
 */



/**
 * Base CLASS for each BGP controllers
 */

abstract class Core_Abstract__Controller implements Core_Controller_Interface
{
    /**
     * @var array Reflected public API methods
     */
    protected $reflected_public_methods = array();

    /**
     * @var
     */
    protected $validation_errors = array();

    /**
     * @var Logger Controller logger
     */
    protected $logger = null;

    /**
     * Core_Abstract__Controller constructor.
     */
    public function __construct()
    {
        // Logger
        list($module, $suffix) = explode('_', get_class($this), 3);
        $module = strtolower($module);
        $this->logger = Logger::getLogger( $module );

        // Reflection
        $reflector = new ReflectionClass( get_class($this) );
        $methods = $reflector->getMethods( ReflectionMethod::IS_PUBLIC );

        // Filter
        foreach ($methods as $method)
        {
            $name  = $method->name;
            $class = $method->class;
            $doc   = $reflector->getMethod($name)->getDocComment();

            // Parse Doc
            if (!is_string($doc)) {
                continue;
            }

            $doc = new DocBlock($doc);
            $params = $doc->all_params;

            if (empty($params['api']) || empty($params['apiParam'])) {
                continue;
            }

            // Params
            $args = array();
            foreach ($params['apiParam'] as $key => $param) {
                list($type_arg, $arg) = explode(' ', $param, 3);
                $args[] = $arg;
            }

            // Api
            list($http, $resource, $title) = explode(' ', $params['api'][0], 3);

            // Description
            if (!empty($params['apiDescription'])) {
                $desc = $params['apiDescription'][0];
            } else {
                $desc = '';
            }

            $this->reflected_public_methods[$name] = array(
                'id' 			=> trim($class . '::' . $name . '()'),
                'method'		=> trim(strtoupper(substr($http, 1, -1))),
                'path'		    => trim($resource),
                'title'         => trim($title),
                'description'   => trim($desc),
                'params'		=> $args
            );
        }
    }

    /**
     * Sort arguments in order to match method signature
     *
     * @param $method_prototype_name
     * @param array $args
     * @return array
     * @throws Core_Exception
     */
    private function sortArgs($method_prototype_name, $args = array()) {

        if (!isset($this->reflected_public_methods[$method_prototype_name])) {
            throw new Core_Exception(501);
        }

        $method_prototype_args = $this->reflected_public_methods[$method_prototype_name]['params'];
        $param_array = array_fill_keys(array_values($method_prototype_args), null); // Set all values to null

        // Match value on sorted arguments
        foreach ($args as $arg => $value) {
            if (key_exists($arg, $param_array)) {
                $param_array[$arg] = $value;
            }
            // Optional args
            $arg = '[' . $arg . ']';
            if (key_exists($arg, $param_array)) {
                $param_array[$arg] = $value;
            }
        }

        // Delete unmatched arguments (useful for default arguments)
        foreach ($param_array as $arg => $value) {
            if ($arg[0] == '[' && empty($value)) {
                unset($param_array[$arg]);
            }
        }

        return array_values($param_array);
    }

    public function invoke($method_prototype_name, $invocation_args) {

        $method_prototype_name = (string)$method_prototype_name;

        $invocation_args = $this->sortArgs($method_prototype_name, $invocation_args);
        return $this->notifyInvocation(
            $method_prototype_name,
            call_user_func_array(array($this, $method_prototype_name), $invocation_args)
        );
    }

    /**
     * Format the returned array with errors
     * Log to file
     *
     * @param string $method_prototype_name Invoked method
     * @param array $return_array
     * @return array
     */
    private function notifyInvocation($method_prototype_name, $return_array = array()) {

        $uid = 0; // TODO : implement UID
        $info = get_class($this) . '::' . $method_prototype_name . '() "';

        if ($return_array == null) {
            $return_array = array();
        }

        $response = array(
            'data' => $return_array
        );

        if (empty($this->validation_errors)) {

            // No errors
            $response['success'] = TRUE;
            $info .= 'OK';
            $info .= '"';
            $this->logger->info($info);
            return $response;
        }

        // Append errors
        $response['success'] = FALSE;
        $response['errors'] = $this->validation_errors;
        $info .= 'KO';
        $info .= '"';
        $this->logger->info($info);
        return $response;
    }

    public function format($return_array, $content_type = 'application/json') {

        // Send headers
        header('Content-Type: ' . $content_type . '; charset=utf-8');

        switch ($content_type) {
            case 'application/xml':
                // TODO : implement XML Encoder
                return $return_array;
            case 'application/json':
            default:
                return json_encode($return_array);
        }
    }

    public function resolve($http_method, $url)
    {
        $api_schema = array();
        foreach ($this->reflected_public_methods as $public_method_name => $public_method) {
            $api_schema[$public_method['method']][$public_method['path']] = $public_method_name;
        }

        if (!empty($api_schema[$http_method][$url])) {
            return $api_schema[$http_method][$url];
        }

        throw new Core_Exception(501);
    }
}
