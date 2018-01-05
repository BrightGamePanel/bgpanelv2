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
 * Application Wrapper
 */

final class Launcher
{
    
    /**
     * Launcher
     *
     * @param string $module
     * @param string $page
     * @param int $id
     * @param string $api_version
     * @return int exit code
     * @throws Core_Exception
     */
    public static function start($module, $page = '', $id = 0, $api_version = '')
    {
        // Check API version
        if (!empty($api_version) && $api_version != Core_Abstract_Application::getFilesVersion()['API_VERSION']) {

            // Trigger error when the requested API version
            // is not compatible with the current API version
            // 301 MOVED PERMANENTLY
            throw new Core_Exception(301);
        }

        // CHECK INSTALL
        if (!self::testDBConfig() && $module != 'wizard') {
            throw new Core_Verbose_Exception(
                'System not configured',
                '',
                "Please configure and install the application"
            );
        }

        // Read HTTP Headers
        $http_headers = array_change_key_case(apache_request_headers(), CASE_UPPER);

        // Determine output content type
        $output_content_type = self::filterHTTPHeader(
            $http_headers['ACCEPT'],
            'application/json',
            self::getAcceptHeaderWhitelist()
        );

        // Determine request content type
        $request_content_type = self::filterHTTPHeader(
            (isset($http_headers['CONTENT-TYPE'])) ? $http_headers['CONTENT-TYPE'] : '',
            '',
            self::getRequestContentTypeHeaderWhitelist()
        );

        if ($output_content_type == "text/html" || $output_content_type == 'application/xhtml') {

            if ($module == 'wizard') {

                // INSTALL WIZARD (HTML)
                $app = new Core_Wizard_Application(
                    $page,
                    $request_content_type,
                    'text/html'
                );
            }
            else {

                // TODO : check that the wizard module is not enabled

                // GUI
                $app = new Core_GUI_Application(
                    $module,
                    $page,
                    $id,
                    $request_content_type,
                    'text/html'
                );
            }
        }
        else {

            if ($module == 'wizard') {

                // INSTALL WIZARD (script mode)
                $app = new Core_Wizard_Application(
                    $page,
                    $request_content_type,
                    $output_content_type
                );
            }
            else {

                // TODO : check that the wizard module is not enabled

                // RestAPI
                $app = new Core_API_Application(
                    $module,
                    $page,
                    $id,
                    $request_content_type,
                    $output_content_type
                );
            }
        }

        // Initialize
        $app->init();

        // Execute
        return $app->execute();
    }

    /**
     * Returns allowed content types, expressed as MIME types,
     * that the application is expected to return
     *
     * @return array
     */
    public static function getAcceptHeaderWhitelist() {
        return array(
            'text/html',
            'text/plain',
            'application/json',
            'application/xml',
            'application/xhtml'
        );
    }

    /**
     * Returns allowed content types of requests
     * Required for POST / PUT requests
     *
     * @return array
     */
    public static function getRequestContentTypeHeaderWhitelist() {
        return array(
            'text/plain',
            'application/json',
            'application/x-www-form-urlencoded',
            'application/form-data',
            'application/octet-stream'
        );
    }

    /**
     * Reads the given http header and returns a valid http header value,
     * given a default value and a whitelist of possible values
     *
     * @param string $header
     * @param string $default
     * @param array $whitelist
     * @return string
     */
    private static function filterHTTPHeader($header, $default, $whitelist = array()) {

        if (empty($header)) {
            return $default;
        }

        $header_parts = explode(',', $header);
        foreach ($header_parts as $part) {
            foreach ($whitelist as $known_type) {
                if (strstr($part, $known_type)) {
                    return $known_type;
                }
            }
        }

        return $default;
    }

    /**
     * Reads framework version from the database
     * and determines if the application is either installed or not
     *
     * @return true if installed, false otherwise
     */
    private static function testDBConfig() {

        $dbh = Core_Database_Service::getService();

        try {
            $sth = $dbh->prepare("
            SELECT value
            FROM config
            WHERE setting = 'api_version';");

            $sth->execute();

            if ($sth->rowCount() >= 1) {
                return TRUE;
            }
        }
        catch (PDOException $e) {
        }

        return FALSE;
    }
}