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

abstract class BGP_Abstract_Module_Controller
{
    /**
     * Invoke the Designated Method on this Controller with its Args
     *
     * @param array $controller_method_prototype The method signature to call, including its name and args
     * @param array $args The args to pass to the method
     * @return mixed
     */
    public function invoke($controller_method_prototype, $args) {

        $controller_method_id = $controller_method_prototype['method'];
        $param_array = array();
        $sorted_param_array = array();

        // Sort arguments in order to match

        if (!empty($args)) {

            foreach ($controller_method_prototype['args'] as $arg_c => $arg_v) {
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
            return call_user_func(array($this, (string)$controller_method_id));
        }

        return call_user_func_array(array($this, (string)$controller_method_id), $param_array);
    }
}
