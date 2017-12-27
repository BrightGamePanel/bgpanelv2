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



class Core_API_WADL_Builder
{
    function displayWADL()
    {
        // Display API WADL

        if (empty($this->module) && $this->req_method == "GET") {

            // Web Application Description Language (WADL)

            header('Content-Type: application/xml; charset=utf-8');
            echo Core_API::getWADL();
            return;
        }
    }

    public static function getWADL()
    {
        $user = Core_AuthService::getSessionInfo('USERNAME');

        $applicationDoc = "BrightGamePanel REST API @" . $user . " [build: " . BGP_API_VERSION . "] [date: " . date('r') . "]";

        $system_url = BGP_SYSTEM_URL;
        $resourcesBaseUrl = ($system_url[strlen($system_url) - 1] != '/') ? BGP_SYSTEM_URL . '/api/' : BGP_SYSTEM_URL . 'api/';

        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
        $header .= "  <application xmlns=\"http://wadl.dev.java.net/2009/02\">\n";
        $header .= "  <doc xml:lang=\"en\" title=\"BGPanel API\">" . $applicationDoc . "</doc>\n";
        $header .= "  <resources base=\"" . $resourcesBaseUrl . "\">\n";

        $body = self::getWADLResources();

        $footer = "   </resources>\n";
        $footer .= "</application>\n";

        return $header . $body . $footer;
    }

    public static function getWADLResources()
    {

        $rbac = new PhpRbac\Rbac();

        $authorizations = Core_AuthService::getSessionInfo('PERMISSIONS');

        $body = '';

        foreach ($authorizations as $module => $methods) {
            $body .= "      <resource path=\"" . $module . "\">\n";

            $subResource = ''; // Tag closure helper for sub resources

            foreach ($methods as $method) {
                $reflectedMethod = Core_Reflection_Helper::getControllerMethod($module, $method);

                $method = self::buildAPIMethodXML($reflectedMethod);

                $path = $reflectedMethod['resource'];
                $pathParts = explode('/', $path);

                // Sub-resource case (Element)
                if (!empty($pathParts[1])) {

                    $path = str_replace($pathParts[0] . '/', '', $path); // Remove parent resource

                    $body .= "         <resource path=\"" . $path . "\">\n";

                    $methodLines = explode("\n", $method);

                    foreach ($methodLines as $line) {
                        if (!empty($line)) {
                            $body .= '   ' . $line . "\n"; // Pad
                        }
                    }

                    $body .= "         </resource>\n";
                } // Resource case (Collection)
                else {

                    $body .= $method;
                }
            }

            $body .= "      </resource>\n";
        }

        return $body;
    }

    public static function buildAPIMethodXML($reflectedMethod)
    {

        $body = "         <method name=\"" . $reflectedMethod['name'] . "\" id=\"" . $reflectedMethod['id'] . "\">\n";
        $body .= "            <doc xml:lang=\"en\" title=\"" . $reflectedMethod['description'] . "\"/>\n";

        if (!empty($reflectedMethod['params'])) {
            $body .= "            <request>\n";
        } else {
            $body .= "            <request/>\n";
        }

        foreach ($reflectedMethod['params'] as $param) {

            if (strpos($param, 'optional') === FALSE) {
                $required = 'true';
            } else {
                $required = 'false';
                $param = trim(str_replace('optional', '', $param));
            }

            $paramParts = explode(' ', $param); // Get type and name
            list($type, $name) = $paramParts; // Assign

            $doc = trim(str_replace($type . ' ' . $name, '', $param)); // Remove from original string type and name to get doc part
            $name = substr($name, 1); // Remove $

            $docParts = explode(' ', $doc); // Get style
            $style = $docParts[0];

            $doc = trim(str_replace($style, '', $doc)); // Get real description

            if (!empty($doc)) {
                $body .= "               <param name=\"" . $name . "\" type=\"xs:" . $type . "\" required=\"" . $required . "\" style=\"" . $style . "\" xmlns:xs=\"http://www.w3.org/2001/XMLSchema\">\n";
                $body .= "                  <doc>" . $doc . "</doc>\n";
                $body .= "               </param>\n";
            } else {
                $body .= "               <param name=\"" . $name . "\" type=\"xs:" . $type . "\" required=\"" . $required . "\" style=\"" . $style . "\" xmlns:xs=\"http://www.w3.org/2001/XMLSchema\"/>\n";
            }
        }

        if (!empty($reflectedMethod['params'])) {
            $body .= "            </request>\n";
        }

        $body .= "            <response>\n";
        $body .= "               <representation mediaType=\"" . $reflectedMethod['response'] . "\"/>\n";
        $body .= "            </response>\n";
        $body .= "         </method>\n";

        return $body;
    }
}