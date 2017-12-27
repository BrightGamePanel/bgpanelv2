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

final class Core_Launcher
{
    /**
     * BGP_Launcher main
     *
     * @param string $module
     * @param string $page
     * @param int $id
     * @param string $api_version
     * @return int exit code
     * @throws BGP_Exception
     */
    public static function start($module, $page, $id = 0, $api_version = null)
    {
        // Check API version
        if (!empty($api_version) && $api_version != Core_Abstract_Application::getFilesVersion()['API_VERSION']) {

            // Trigger error when the requested API version
            // is not compatible with the current API version
            // 301 MOVED PERMANENTLY
            throw new BGP_Exception(301);
        }

        // Read HTTP Headers
        $http_headers = array_change_key_case(apache_request_headers(), CASE_UPPER);

        if (!isset($http_headers['CONTENT-TYPE']) ||
            (isset($http_headers['CONTENT-TYPE']) && $http_headers['CONTENT-TYPE'] == "text/html")) {

            if ($module == 'wizard') {

                // INSTALL WIZARD
                $app = new Core_Wizard_Application(
                    'wizard',
                    $page
                );
            }
            else {

                // CHECK INSTALL
                if (!self::testDBConfig()) {
                    throw new Core_Exception(
                        'System not configured',
                        '',
                        "Please configure and install the application"
                    );
                }

                // GUI
                $app = new Core_GUI_Application(
                    $module,
                    $page,
                    $id
                );
            }
        }
        else {

            if (!self::testDBConfig()) {
                throw new Core_Exception(
                    'System not configured',
                    '',
                    "Please configure and install the application"
                );
            }

            // RestAPI
            $app = new Core_API_Application(
                $module,
                $page,
                $id,
                $http_headers['CONTENT-TYPE']
            );
        }

        // Execute
        return $app->execute(); // Runtime
    }

    /**
     * Reads framework version from the database
     * and determines if the application is either installed or not
     *
     * @return true if installed, false otherwise
     */
    private static function testDBConfig() {

        $dbh = Core_DBH::getDBH();

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