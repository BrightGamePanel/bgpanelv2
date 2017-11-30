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



class Core_API
{
    /**
     * Given a Module, URL and an HTTP Method
     * Returns the Method with its associated Controller
     *
     * @param $module
     * @param $url
     * @param $http_method
     * @param $api_version
     * @return array
     */
    public static function resolveAPIRequest( $module, $url, $http_method, $api_version ) {

        $request_method = array();
        $api_schema = array();

        // Get Public Methods
        $methods = Core_Reflection::getControllerPublicMethods( $module );

        if (!empty($methods)) {

            foreach ($methods as $key => $value) {

                list($module, $method) = explode(".", $value['method']);
                $module = strtolower($module);

                $reflectedMethod = Core_Reflection::getControllerMethod($module, $method);

                // The ending slash of a collection is always omitted
                // when the resource is called.
                // We delete the ending slash if any in order to avoid bad resolution
                // in the next step (#Resolve).

                if (substr($reflectedMethod['resource'], -1) == '/') {
                    $reflectedMethod['resource'] = substr($reflectedMethod['resource'], 0, -1);
                }

                $api_schema[$reflectedMethod['resource']][$reflectedMethod['name']] = array(
                    $reflectedMethod['id'] => $reflectedMethod['params']
                );
            }
        }

        // Get Resource
        $path = parse_url($url, PHP_URL_PATH);
        $resource = str_replace('/api/' . $api_version . '/', '', $path);

        // #Resolve
        if (!empty($api_schema[$resource][$http_method])) {

            $resource = $api_schema[$resource][$http_method];

            foreach ($resource as $key => $value) {

                $request_method['method'] = $key;
                $request_method['args'] = $value;
            }
        }

        return $request_method;
    }

    /**
     * Invoke the Designated Method on the Controller with its Args
     *
     * @param $module_name
     * @param $controller_method
     * @param $args
     * @return mixed
     */
	public static function callAPIControllerMethod( $module_name, $controller_method, $args ) {

        $controller_name = 'Controller_' . ucfirst(strtolower($module_name));
        $controller_method_id = $controller_method['method'];
        $param_array = array();
        $sorted_param_array = array();

        $controller = new $controller_name();

        // Sort arguments in order to match

        if (!empty($args)) {

            foreach ($controller_method['args'] as $arg_c => $arg_v) {
                list($t, $arg_v, $mode) = explode(' ', $arg_v);
                $sorted_param_array[substr($arg_v, 1)] = '';
            }

            // Match value
            foreach (array_keys($args) as $key) {
                $sorted_param_array[$key] = $args[$key];
            }

            // Push to array numeric
            foreach ($sorted_param_array as $arg => $value) {
                $param_array[] = $value;
            }
        }

        if (empty($param_array)) {
            return call_user_func(array($controller, (string)$controller_method_id));
        }

        return call_user_func_array(array($controller, (string)$controller_method_id), $param_array);
	}
}
