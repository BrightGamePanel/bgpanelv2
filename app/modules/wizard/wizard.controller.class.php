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
 * Install Wizard Controller
 */

class Wizard_Controller extends Core_Abstract__Controller {

    /**
     * Game Configuration Database
     * Last Update: 30/08/2014 by warhawk3407
     * @var array the game configuration database
     */
    private $game_db;

    /**
     * @var string last known api version
     */
    private $last_api_version = '100';

    /**
     * @var array all known bgp database versions
     */
    private $bgp_db_versions = array(
        '0.1.0',
    );

    /**
     * @var string last known database model version
     */
    private $last_bgp_version;

    /* CHECK REQUIREMENTS KEYS */
    const PHP_VERSION = 'php_version';
    const APACHE2 = 'apache2';
    const SAFE_MODE = 'safe_mode';
    const MODE_RW = 'mode_rewrite';
    const PDO = 'pdo';
    const DBH = 'database_handle';
    CONST FUNC_FSOCK = 'fsockopen';
    const FUNC_MAIL = 'mail';
    const CURL = 'curl';
    const MBSTRING = 'mbstring';
    const BZ2 = 'bz2';
    const ZLIB = 'zlib';
    const GD = 'gd';
    const FREETYPE = 'freetype';
    const SIMPLEXML = 'simplexml';
    const XMLREADER = 'xmlreader';
    const OPENSSL = 'openssl';
    const MCRYPT = 'mcrypt';
    const GMP = 'gmp';
    const HASH = 'hash';
    const API_KEY = 'w_mode_api_key';
    const SSH_KEY = 'w_mode_ssh_key';
    const RSA = 'w_mode_rsa';
    const PHP_SECLIB = 'w_mode_phpseclib';

    function __construct( )	{

        parent::__construct();

        $this->game_db = Array
        (
            // Counter-Strike: Source
            1 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value' => 'cs_assault'
                )
            ),

            // Day of Defeat: Source
            2 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'dod_anzio'
                ),
                1 => Array
                (
                    'name'	=> 'Tickrate',
                    'value'	=> '100'
                )
            ),

            // Half-Life 2: Deathmatch
            3 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'dm_lockdown'
                ),
                1 => Array
                (
                    'name'	=> 'Tickrate',
                    'value'	=> '100'
                )
            ),

            // Team Fortress 2
            4 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'ctf_2fort'
                )
            ),

            // Left 4 Dead
            5 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'l4d_hospital01_apartment'
                )
            ),

            // Left 4 Dead 2
            6 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'c1m1_hotel'
                )
            ),

            // Counter-Strike
            7 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'de_dust2'
                ),
                1 => Array
                (
                    'name'	=> 'Pingboost',
                    'value'	=> '2'
                )
            ),

            // Killing Floor
            8 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'KF-Bedlam.rom'
                ),
                1 => Array
                (
                    'name'	=> 'VACSecure',
                    'value'	=> 'True'
                ),
                2 => Array
                (
                    'name'	=> 'AdminName',
                    'value'	=> 'admin'
                ),
                3 => Array
                (
                    'name'	=> 'AdminPassword',
                    'value'	=> 'passwd'
                ),
                4 => Array
                (
                    'name'	=> 'INI File',
                    'value'	=> 'KillingFloor.ini'
                )
            ),

            // Call of Duty 4: Modern Warfare
            9 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                ),
                1 => Array
                (
                    'name'	=> 'fs_homepath',
                    'value'	=> '/home/user/cod4'
                ),
                2 => Array
                (
                    'name'	=> 'fs_basepath',
                    'value'	=> '/home/user/cod4'
                )
            ),

            // Minecraft
            10 => Array
            (
            ),

            // Call of Duty: Modern Warfare 3
            11 => Array
            (
                0 => Array
                (
                    'name'	=> 'net_queryPort',
                    'value'	=> '27014'
                ),
                1 => Array
                (
                    'name'	=> 'net_authPort',
                    'value'	=> '8766'
                ),
                2 => Array
                (
                    'name'	=> 'net_masterServerPort',
                    'value'	=> '27016'
                ),
                3 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                )
            ),

            // Call of Duty 2
            12 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                ),
                1 => Array
                (
                    'name'	=> 'fs_homepath',
                    'value'	=> '/home/user/cod2'
                ),
                2 => Array
                (
                    'name'	=> 'fs_basepath',
                    'value'	=> '/home/user/cod2'
                )
            ),

            // Call of Duty: World at War
            13 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                ),
                1 => Array
                (
                    'name'	=> 'fs_homepath',
                    'value'	=> '/home/user/codwaw'
                ),
                2 => Array
                (
                    'name'	=> 'fs_basepath',
                    'value'	=> '/home/user/codwaw'
                )
            ),

            // Wolfenstein: Enemy Territory
            14 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                ),
                1 => Array
                (
                    'name'	=> 'fs_homepath',
                    'value'	=> '/home/user/wolfet'
                ),
                2 => Array
                (
                    'name'	=> 'fs_basepath',
                    'value'	=> '/home/user/wolfet'
                )
            ),

            // ArmA: 2
            15 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                )
            ),

            // Garrysmod
            16 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value'	=> 'gm_construct'
                )
            ),

            // Counter-Strike: Global Offensive
            17 => Array
            (
                0 => Array
                (
                    'name'	=> 'Default Map',
                    'value' => 'cs_italy'
                ),
                1 => Array
                (
                    'name'	=> 'Map Group',
                    'value' => 'mg_hostage'
                ),
                2 => Array
                (
                    'name'	=> 'Game Type',
                    'value' => '0'
                ),
                3 => Array
                (
                    'name'	=> 'Game Mode',
                    'value' => '0'
                ),
                4 => Array
                (
                    'name'	=> 'Tickrate',
                    'value' => '64'
                )
            ),

            // ArmA: Armed Assault
            18 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                )
            ),

            // Battlefield 2
            19 => Array
            (
            ),

            // Battlefield 1942
            20 => Array
            (
            ),

            // Multi Theft Auto
            21 => Array
            (
            ),

            // San Andreas: Multiplayer (SA-MP)
            22 => Array
            (
            ),

            // Urban Terror
            23 => Array
            (
                0 => Array
                (
                    'name'	=> 'Server CFG File',
                    'value'	=> 'server.cfg'
                )
            )
        );

        $this->last_bgp_version = end($this->bgp_db_versions);
	}

    /**
     * @api {post} /wizard Accepts software license.
     * @author Nikita Rousseau
     * @apiVersion v1
     * @apiName AcceptLicense
     * @apiGroup Wizard
     *
     * @apiDescription Accepts software license of Bright Game Panel.
     *
     * @apiParam {String} agreement
     *
     * @param string $agreement
     * @return void
     */
	public function acceptLicense($agreement)
    {
        if (empty($agreement)) {
            $this->validation_errors['agreement'] = 'You must accept the terms of the license agreement in order to use this software';
        }

        return; // void
    }

	function checkRequirements() {

	    $ret = array();

        $version_cmp = version_compare(PHP_VERSION, '5.6.0');
        if ($version_cmp == -1) {
            $ret[self::PHP_VERSION] = FALSE;
        } else {
            $ret[self::PHP_VERSION] = TRUE;
        }

        $apache2Check = strpos($_SERVER['SERVER_SOFTWARE'], 'Apache/2');
        if ($apache2Check === FALSE) {
            $ret[self::APACHE2] = FALSE;
        } else {
            $ret[self::APACHE2] = TRUE;
        }

        if (ini_get('safe_mode')) {
            $ret[self::SAFE_MODE] = FALSE;
        } else {
            $ret[self::SAFE_MODE] = TRUE;
        }

        // HTACCESS + MOD_REWRITE

        $pageURL = get_url($_SERVER);
        $pageURL = str_replace('install/', '', $pageURL) . 'root/';

        $htaccessCheck = get_headers($pageURL);
        $htaccessCheck = strpos($htaccessCheck[0], '200 OK');

        if ($htaccessCheck === FALSE) {
            $ret[self::MODE_RW] = FALSE;
        } else {
            $ret[self::MODE_RW] = TRUE;
        }

        if (!extension_loaded('pdo')) {
            $ret[self::PDO] = FALSE;
        } else {
            $ret[self::PDO] = TRUE;

            try {
                // Connect to the SQL server
                if (DB_DRIVER == 'sqlite') {
                    $dbh = new PDO( DB_DRIVER.':'.DB_FILE );
                }
                else {
                    $dbh = new PDO( DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD );
                }

                // Set ERRORMODE to exceptions
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e) {
                exit($e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
            }

            if (empty($dbh)) {
                $ret[self::DBH] = FALSE;
            } else {
                $ret[self::DBH] = TRUE;
            }
        }

        $ret[self::FUNC_FSOCK] = function_exists('fsockopen');
        $ret[self::FUNC_MAIL] = function_exists('mail');
        $ret[self::CURL] = extension_loaded('curl');
        $ret[self::MBSTRING] = extension_loaded('mbstring');
        $ret[self::BZ2] = extension_loaded('bz2');
        $ret[self::ZLIB] = extension_loaded('zlib');
        $ret[self::GD] = extension_loaded('gd2');
        $ret[self::FREETYPE] = function_exists('imagettftext');
        $ret[self::SIMPLEXML] = extension_loaded('simplexml');
        $ret[self::XMLREADER] = class_exists('XMLReader');

        //
        // PHPSECLIB REQUIREMENTS
        //

        $ret[self::OPENSSL] = extension_loaded('openssl');
        $ret[self::MCRYPT] = extension_loaded('mcrypt');
        $ret[self::GMP] = extension_loaded('gmp');
        $ret[self::HASH] = function_exists('hash');

        //
        // CONFIGURATION WRITE MODE CHECK
        //

        if (!defined('APP_API_KEY') && !is_writable( CONF_API_KEY_INI )) {
            $ret[self::API_KEY] = FALSE;
        } else {
            $ret[self::API_KEY] = TRUE;
        }

        if (!defined('APP_SSH_KEY') && !is_writable( CONF_SECRET_INI )) {
            $ret[self::SSH_KEY] = FALSE;
        } else {
            $ret[self::SSH_KEY] = TRUE;
        }

        if (!defined('RSA_PRIVATE_KEY') && !is_writable( RSA_KEYS_DIR )) {
            $ret[self::RSA] = FALSE;
        } else {
            $ret[self::RSA] = TRUE;
        }

        if (!is_writable( CONF_PHPSECLIB_INI )) {
            $ret[self::PHP_SECLIB] = FALSE;
        } else {
            $ret[self::PHP_SECLIB] = TRUE;
        }

        return $ret;
    }



    function checkExistingTables()
    {
        $currentVersion = '';

        try {
            // Connect to the SQL server
            if (DB_DRIVER == 'sqlite') {
                $dbh = new PDO(DB_DRIVER . ':' . DB_FILE);
            } else {
                $dbh = new PDO(DB_DRIVER . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
            }

            // Set ERRORMODE to exceptions
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
            die();
        }

        $tables = array();
        $result = $dbh->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        if (!empty($tables)) {
            foreach ($tables as $table) {
                if ($table == 'config') {
                    $sth = $dbh->query("SELECT value FROM config WHERE setting = 'panel_version'");
                    $currentVersion = $sth->fetch(PDO::FETCH_ASSOC);
                    break;
                }
            }
        }

        return $currentVersion;
    }


    function installVersion($version = 'full')
    {

        switch ($version) {
            case 'full':

                //---------------------------------------------------------+
                // PHPSECLIB Configuration

                ob_start();
                @phpinfo();
                $content = ob_get_contents();
                ob_end_clean();

                preg_match_all('#OpenSSL (Header|Library) Version(.*)#im', $content, $matches);

                $versions = array();
                if (!empty($matches[1])) {
                    for ($i = 0; $i < count($matches[1]); $i++) {
                        $fullVersion = trim(str_replace('=>', '', strip_tags($matches[2][$i])));
                        if (!preg_match('/(\d+\.\d+\.\d+)/i', $fullVersion, $m)) {
                            $versions[$matches[1][$i]] = $fullVersion;
                        } else {
                            $versions[$matches[1][$i]] = $m[0];
                        }
                    }
                }

                switch (true) {
                    case !isset($versions['Header']):
                    case !isset($versions['Library']):
                    case $versions['Header'] == $versions['Library']:
                        $CRYPT_RSA_MODE = CRYPT_RSA_MODE_OPENSSL;
                        break;
                    default:
                        $CRYPT_RSA_MODE = CRYPT_RSA_MODE_INTERNAL;
                }

                if (is_writable(CONF_PHPSECLIB_INI)) {
                    $handle = fopen(CONF_PHPSECLIB_INI, 'w');

                    if ($CRYPT_RSA_MODE === CRYPT_RSA_MODE_OPENSSL) {
                        $data = "; BIGINTEGER CONFIGURATION FILE

; INTERNAL 	= 1
; BCMATH 	= 2
; GMP 		= 3
MATH_BIGINTEGER_MODE				= 3

MATH_BIGINTEGER_OPENSSL_ENABLED		= 1

; RSA CONFIGURATION FILE

; INTERNAL 	= 1
; OPENSSL 	= 2
CRYPT_RSA_MODE						= " . $CRYPT_RSA_MODE . "
";
                    } else {
                        $data = "; BIGINTEGER CONFIGURATION FILE

; INTERNAL 	= 1
; BCMATH 	= 2
; GMP 		= 3
MATH_BIGINTEGER_MODE				= 3

MATH_BIGINTEGER_OPENSSL_DISABLE		= 1

; RSA CONFIGURATION FILE

; INTERNAL 	= 1
; OPENSSL 	= 2
CRYPT_RSA_MODE						= " . $CRYPT_RSA_MODE . "
";
                    }

                    fwrite($handle, $data);
                    fclose($handle);
                    unset($handle);
                } else {
                    return ('Critical error while installing ! Unable to write to ' . CONF_PHPSECLIB_INI . ' !');
                }

                //---------------------------------------------------------+
                // Generating Secret Keys

                $APP_API_KEY = hash('sha512', md5(str_shuffle(time())));
                usleep(rand(1, 1000));
                $APP_SSH_KEY = hash('sha512', md5(str_shuffle(time())));
                usleep(rand(1, 1000));
                $APP_STEAM_KEY = hash('sha512', md5(str_shuffle(time())));
                usleep(rand(1, 1000));
                $APP_AUTH_SALT = hash('sha512', md5(str_shuffle(time())));
                usleep(rand(1, 1000));
                $APP_SESSION_KEY = hash('sha512', md5(str_shuffle(time())));

                if (is_writable(CONF_SECRET_INI)) {
                    $handle = fopen(CONF_SECRET_INI, 'w');
                    $data = "; SECURITY KEYS FILE
APP_SSH_KEY 		= \"" . $APP_SSH_KEY . "\"
APP_STEAM_KEY		= \"" . $APP_STEAM_KEY . "\"
APP_AUTH_SALT		= \"" . $APP_AUTH_SALT . "\"
APP_TOKEN_KEY 	= \"" . $APP_SESSION_KEY . "\"
";
                    fwrite($handle, $data);
                    fclose($handle);
                    unset($handle);
                } else {
                    return ('Critical error while installing ! Unable to write to ' . CONF_SECRET_INI . ' !');
                }

                if (is_writable(CONF_API_KEY_INI)) {
                    $handle = fopen(CONF_API_KEY_INI, 'w');
                    $data = "; API KEY FILE
APP_API_KEY 		= \"" . $APP_API_KEY . "\"
";
                    fwrite($handle, $data);
                    fclose($handle);
                    unset($handle);
                } else {
                    return ('Critical error while installing ! Unable to write to ' . CONF_API_KEY_INI . ' !');
                }

                //---------------------------------------------------------+
                // Generating RSA Keys

                if (is_writable(RSA_KEYS_DIR)) {
                    $rsa = new Crypt_RSA();

                    $rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_OPENSSH);

                    $keypair = $rsa->createKey(2048);

                    $handle = fopen(RSA_PRIVATE_KEY_FILE, 'w');
                    $data = $keypair['privatekey'];
                    fwrite($handle, $data);
                    fclose($handle);
                    unset($handle);

                    $handle = fopen(RSA_PUBLIC_KEY_FILE, 'w');
                    $data = $keypair['publickey'];
                    fwrite($handle, $data);
                    fclose($handle);
                    unset($handle);
                } else {
                    return ('Critical error while installing ! Unable to write to ' . RSA_KEYS_DIR . ' !');
                }

                //---------------------------------------------------------+
                // DEFINE SYSTEM URL

                define('SYSTEMURL', str_replace('install/index.php?step=three&version=full', '', filter_var(get_url($_SERVER), FILTER_SANITIZE_URL)));

                //---------------------------------------------------------+
                // Creating Database Schema

                require("./sql/full.php");

                //---------------------------------------------------------+
                // Creating System Permissions

                $rbac = new PhpRbac\Rbac();

                $perms = array();

                $handle = opendir(MODS_DIR);

                if ($handle) {

                    // Foreach modules
                    while (false !== ($entry = readdir($handle))) {

                        // Dump specific directories
                        if ($entry == "." || $entry == "..") {

                            continue;
                        }

                        $module = $entry;

                        // Get Module Pages
                        $pages = Core_Reflection_Helper::getModulePublicPages($module);

                        if (!empty($pages)) {

                            // Create Page Access Permission

                            foreach ($pages as $value) {
                                $id = $rbac->Permissions->add($value['page'], $value['description']);
                                $perms[$module][] = $id;
                            }
                        }

                        // Get Public Methods
                        $methods = Core_Reflection_Helper::getControllerPublicMethods($module);

                        if (!empty($methods)) {

                            // Create Method Permission

                            foreach ($methods as $key => $value) {
                                $id = $rbac->Permissions->add($value['method'], $value['description']);
                                $perms[$module][] = $id;
                            }
                        }
                    }

                    closedir($handle);
                }

                // Create Default Roles

                $apiRoleId = $rbac->Roles->add('api', 'API User');
                $adminRoleId = $rbac->Roles->add('admin', 'System Administrator');
                $userRoleId = $rbac->Roles->add('user', 'Regular User');

                // Bind Perms To Roles

                foreach ($perms as $module => $ids) {
                    switch ($module) {
                        case 'box':
                        case 'user':
                        case 'config':
                        case 'tools':

                            // Admin Only

                            foreach ($ids as $id) {
                                $rbac->Roles->assign($adminRoleId, $id);
                            }

                            break 1;

                        default:

                            foreach ($ids as $id) {
                                $rbac->Roles->assign($adminRoleId, $id);
                                $rbac->Roles->assign($userRoleId, $id);
                            }

                            break 1;
                    }
                }

                // Assign API Role
                $rbac->Users->assign($apiRoleId, 2);

                break;


            case 'update':

                try {
                    // Connect to the SQL server
                    if (DB_DRIVER == 'sqlite') {
                        $dbh = new PDO(DB_DRIVER . ':' . DB_FILE);
                    } else {
                        $dbh = new PDO(DB_DRIVER . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
                    }

                    // Set ERRORMODE to exceptions
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $sth = $dbh->query("SELECT value FROM config WHERE setting = 'panel_version'");
                    $currentVersion = $sth->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
                    die();
                }

                //---------------------------------------------------------+

                foreach ($this->bgp_db_versions as $key => $value) {
                    if ($value == $currentVersion['value']) // Base version for the update
                    {
                        if ($key == end($bgpVersions)) {
                            break; // Already up-to-date
                        } else {
                            $i = $key; // Starting point for the update

                            for ($i; $i < key($bgpVersions); $i++) // Loop in order to reach the last version
                            {
                                // Apply the update
                                $sqlFile = './sql/';
                                $sqlFile .= 'update_' . str_replace('.', '', $bgpVersions[$i]) . '_to_' . str_replace('.', '', $bgpVersions[$i + 1]) . '.php';

                                require($sqlFile);
                            }

                            break; // Update finished
                        }
                    }
                }

                //---------------------------------------------------------+

                try {
                    $sth = $dbh->query("SELECT value FROM config WHERE setting = 'panel_version'");
                    $currentVersion = $sth->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
                    die();
                }

                if ($currentVersion['value'] != LASTBGPVERSION) {
                    return ("Update Error.");
                }

                //---------------------------------------------------------+

                break;


            default:
                return ('Error');
        }
    }
}

/*

function full() {

    $dbh = null;

    try {
        // Connect to MySQL
        if (DB_DRIVER == 'sqlite') {
            $dbh = new PDO( DB_DRIVER.':'.DB_FILE );
        }
        else {
            $dbh = new PDO( DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD );
        }

        // Set ERRORMODE to exceptions
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
        echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
        die();
    }

    try {

        //---------------------------------------------------------+

        // BrightGamePanel V2 Database
        // Version 1.0.0
        // 20/12/2017

        //---------------------------------------------------------+

        // Table structure for table "box"

        $dbh->exec( "DROP TABLE IF EXISTS box  ; " );
        $dbh->exec( "
	CREATE TABLE box (
	  box_id 			INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  box_credential_id	INTEGER UNSIGNED,
	  os_id				INTEGER UNSIGNED,
	  name				TEXT NOT NULL,
	  steam_lib_path	TEXT,
	  notes				TEXT,
	  PRIMARY KEY  (box_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        //---------------------------------------------------------+

        // Table structure for table "box_cache"

        $dbh->exec( "DROP TABLE IF EXISTS box_cache  ; " );
        $dbh->exec( "
	CREATE TABLE box_cache (
	  box_cache_id	INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  box_id		INTEGER UNSIGNED NOT NULL,
	  timestamp		TIMESTAMP NOT NULL,
	  cache			BLOB NOT NULL,
	  PRIMARY KEY  (box_cache_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        //---------------------------------------------------------+

        // Table structure for table "box_credential"

        $dbh->exec( "DROP TABLE IF EXISTS box_credential  ; " );
        $dbh->exec( "
	CREATE TABLE box_credential (
	  box_credential_id	INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  login				BLOB NOT NULL,
	  password			BLOB NULL,
	  privatekey 		TEXT NULL,
	  remote_user_home	TEXT NOT NULL,
	  com_protocol		TEXT NOT NULL,
	  com_port			TEXT NOT NULL,
	  PRIMARY KEY (box_credential_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        //---------------------------------------------------------+

        // Table structure for table "box_ip"

        $dbh->exec( "DROP TABLE IF EXISTS box_ip  ; " );
        $dbh->exec( "
	CREATE TABLE box_ip (
	  box_ip_id		INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  box_id		INTEGER UNSIGNED NOT NULL,
	  ip			TEXT NOT NULL,
	  is_default	INTEGER UNSIGNED NOT NULL,
	  PRIMARY KEY (box_ip_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        //---------------------------------------------------------+

        // Table structure for table "config"

        $dbh->exec( "DROP TABLE IF EXISTS config  ; " );
        $dbh->exec( "
	CREATE TABLE config (
	  config_id		INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  setting		TEXT NOT NULL,
	  value			TEXT NOT NULL,
	  PRIMARY KEY (config_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        // Data for table "config"

        $dbh->exec( "
	INSERT INTO config (setting, value)
	VALUES
	  ('panel_name',		'BrightGamePanel V2'),
	  ('system_url',		'".SYSTEMURL."'),
	  ('panel_version',		'".LASTBGPVERSION."'),
	  ('api_version',		'".LASTAPIVERSION."'),
	  ('maintenance_mode',	'0'),
	  ('last_cron_run',		'Never'),
	  ('user_template',		'bootstrap.min.css')  ; " );

        //---------------------------------------------------------+

        // Table structure for table "game"

        $dbh->exec( "DROP TABLE IF EXISTS game  ; " );
        $dbh->exec( "
	CREATE TABLE game (
	  game_id		INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  name			TEXT NOT NULL,
	  status		TEXT NOT NULL,
	  max_slots		INTEGER UNSIGNED NOT NULL,
	  default_port	INTEGER UNSIGNED NOT NULL,
	  query_port	INTEGER UNSIGNED NOT NULL,
	  query_type	TEXT NOT NULL,
	  config		BLOB,
	  exe			TEXT NOT NULL,
	  launcher		TEXT NOT NULL,
	  cache_dir		TEXT NULL,
	  PRIMARY KEY  (game_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        // Data for table "game"

        $dbh->exec( "
	INSERT INTO game (
	  game_id, name,								status,		max_slots, default_port, query_port, query_type, config,						exe,			launcher,
	  			cache_dir
	)
	VALUES
	  ('1', 'Counter-Strike: Source',				'Active',	'16',  '27015', '27015',	'source',			'".serialize($GAME_DB[1])."',	'srcds_run',	'./srcds_run -game cstrike -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate',
	  			'~/game-repositories/css/'),
	  ('2', 'Day of Defeat: Source',				'Active',	'16',  '27015', '27015',	'source',			'".serialize($GAME_DB[2])."',	'srcds_run',	'./srcds_run -game dod -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -tickrate {cfg2} -nohltv -autoupdate',
	  			'~/game-repositories/dods/'),
	  ('3', 'Half-Life 2: Deathmatch',				'Active',	'16',  '27015', '27015',	'source',			'".serialize($GAME_DB[3])."',	'srcds_run',	'./srcds_run -game hl2mp -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -tickrate {cfg2} -nohltv -autoupdate',
	  			'~/game-repositories/hl2dm/'),
	  ('4', 'Team Fortress 2',						'Active',	'24',  '27015', '27015',	'source',			'".serialize($GAME_DB[4])."',	'srcds_run',	'./srcds_run -game tf -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate',
	  			'~/game-repositories/tf2/'),
	  ('5', 'Left 4 Dead',							'Active',	'8',   '27015', '27015',	'source',			'".serialize($GAME_DB[5])."',	'srcds_run',	'./srcds_run -game left4dead -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate',
	  			'~/game-repositories/left4dead/'),
	  ('6', 'Left 4 Dead 2',						'Active',	'8',   '27015', '27015',	'source',			'".serialize($GAME_DB[6])."',	'srcds_run',	'./srcds_run -game left4dead2 -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate',
	  			'~/game-repositories/left4dead2/'),
	  ('7', 'Counter-Strike',						'Active',	'16',  '27015', '27015',	'halflife', 		'".serialize($GAME_DB[7])."',	'hlds_run',		'./hlds_run -game cstrike +ip {ip} +port {port} +maxplayers {slots} +map {cfg1} -pingboost {cfg2} -autoupdate',
	  			'~/game-repositories/cstrike/'),
	  ('8', 'Killing Floor',						'Inactive', '6',   '7707', 	'7708',		'killingfloor',		'".serialize($GAME_DB[8])."',	'ucc_bin',		'./ucc-bin server {cfg1}?game=KFmod.KFGameType?VACSecure={cfg2}?MaxPlayers={slots}?AdminName={cfg3}?AdminPassword={cfg4} -nohomedir ini={cfg5}',
	  			'~/game-repositories/kfserver/'),
	  ('9', 'Call of Duty 4: Modern Warfare',		'Inactive', '18',  '28960', '28960',	'callofduty4', 		'".serialize($GAME_DB[9])."',	'cod4_lnxded',	'./cod4_lnxded +exec {cfg1} +set sv_maxclients {slots} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2',
	  			'~/game-repositories/cod4/'),
	  ('10', 'Minecraft',							'Active',	'24',  '25565', '25565',	'minecraft',		'".serialize($GAME_DB[10])."',	'minecraft_server',	'java -Xms1024M -Xmx1024M -jar minecraft_server.jar nogui',
	  			'~/game-repositories/minecraft/'),
	  ('11', 'Call of Duty: Modern Warfare 3',		'Active',	'18',  '27015', '27016',	'callofdutymw3',	'".serialize($GAME_DB[11])."',	'iw5mp_server.exe',	'xvfb-run -a wine iw5mp_server.exe +set sv_config {cfg4} +set sv_maxclients {slots} +start_map_rotate +set net_ip {ip} +set net_port {port} +set net_queryPort {cfg1} +set net_authPort {cfg2} +set net_masterServerPort {cfg3} +set dedicated 2',
	  			'~/game-repositories/codmw3/'),
	  ('12', 'Call of Duty 2',						'Inactive', '32',  '28960', '28960',	'callofduty2',		'".serialize($GAME_DB[12])."',	'cod2_lnxded',	'./cod2_lnxded +exec {cfg1} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2',
	  			'~/game-repositories/cod2/'),
	  ('13', 'Call of Duty: World at War',			'Inactive', '32',  '28960', '28960',	'callofdutywaw', 	'".serialize($GAME_DB[13])."',	'codwaw_lnxded', './codwaw_lnxded +exec {cfg1} +set sv_maxclients {slots} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2',
	  			'~/game-repositories/codwaw/'),
	  ('14', 'Wolfenstein: Enemy Territory',		'Inactive', '32',  '27960', '27960',	'wolfet', 			'".serialize($GAME_DB[14])."',	'etded',		'./etded +exec {cfg1} +sv_maxclients {slots} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set net_port {port}',
	  			'~/game-repositories/wolfet/'),
	  ('15', 'ArmA: 2',								'Active',	'64',  '2302', 	'2302',		'arma2',			'".serialize($GAME_DB[15])."',	'server',		'./server -config={cfg1} -netlog -port={port}',
	  			'~/game-repositories/arma2/'),
	  ('16', 'Garrysmod',							'Active',	'16',  '27015', '27015',	'source',			'".serialize($GAME_DB[16])."',	'srcds_run',	'./srcds_run -game garrysmod -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate',
	  			'~/game-repositories/garrysmod/'),
	  ('17', 'Counter-Strike: Global Offensive',	'Active',	'24',  '27015', '27015',	'source',			'".serialize($GAME_DB[17])."',	'srcds_run',	'./srcds_run -game csgo -console -usercon -secure -nohltv -tickrate {cfg5} +ip {ip} +hostport {port} -maxplayers_override {slots} +map {cfg1} +mapgroup {cfg2} +game_type {cfg3} +game_mode {cfg4}',
	  			'~/game-repositories/csgo/'),
	  ('18', 'ArmA: Armed Assault',					'Active',	'64',  '2302',  '2302',		'arma',				'".serialize($GAME_DB[18])."',	'server',		'./server -config={cfg1} -netlog -port={port}',
	  			'~/game-repositories/arma/'),
	  ('19', 'Battlefield 2',						'Active',	'64',  '16567', '29900',	'bf2',				'".serialize($GAME_DB[19])."',	'start.sh',		'./start.sh',
	  			'~/game-repositories/bf2/'),
	  ('20', 'Battlefield 1942',					'Active',	'64',  '14567', '23000',	'bf1942', 			'".serialize($GAME_DB[20])."',	'start.sh',		'./start.sh +statusMonitor 1',
	  			'~/game-repositories/bf1942/'),
	  ('21', 'Multi Theft Auto',					'Active',	'128', '22003', '22126',	'mta',				'".serialize($GAME_DB[21])."',	'mta-server',	'./mta-server -t --ip {ip} --port {port} --httpport {port} --maxplayers {slots}',
	  			'~/game-repositories/mta/'),
	  ('22', 'San Andreas: Multiplayer (SA-MP)',	'Active',	'128', '7777',  '7777',		'samp',				'".serialize($GAME_DB[22])."',	'samp03svr',	'./samp03svr',
	  			'~/game-repositories/samp/'),
	  ('23', 'Urban Terror',						'Active',	'32',  '27960', '27960',	'urbanterror',		'".serialize($GAME_DB[23])."',	'ioUrTded.i386', './ioUrTded.i386 +set fs_game q3ut4 +set net_port {port} +set com_hunkmegs 128 +exec {cfg1} +set dedicated 2',
	  			'~/game-repositories/urbanterror/') 
	; " );

        //---------------------------------------------------------+

        // Table structure for table "lgsl"

        $dbh->exec( "DROP TABLE IF EXISTS lgsl  ; " );
        $dbh->exec( "
	CREATE TABLE lgsl (
	  id 		 INTEGER UNSIGNED 	NOT NULL AUTO_INCREMENT,
	  type       VARCHAR(255)     	NOT NULL DEFAULT '',
	  ip         VARCHAR(255)    	NOT NULL DEFAULT '',
	  c_port     VARCHAR(255)      	NOT NULL DEFAULT '0',
	  q_port     VARCHAR(255)      	NOT NULL DEFAULT '0',
	  s_port     VARCHAR(255)      	NOT NULL DEFAULT '0',
	  zone       VARCHAR(255)    	NOT NULL DEFAULT '',
	  disabled   INTEGER UNSIGNED  	NOT NULL DEFAULT '0',
	  comment    VARCHAR(255) 		NOT NULL DEFAULT '',
	  status     INTEGER UNSIGNED  	NOT NULL DEFAULT '0',
	  cache      TEXT             	NOT NULL,
	  cache_time TEXT             	NOT NULL,
	  PRIMARY KEY  (id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        //---------------------------------------------------------+

        // Table structure for table "os"

        $dbh->exec( "DROP TABLE IF EXISTS os  ; " );
        $dbh->exec( "
	CREATE TABLE os (
	  os_id				INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  operating_system	TEXT NOT NULL,
	  PRIMARY KEY (os_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        // Data for table "os"

        $dbh->exec( "
	INSERT INTO os (operating_system)
	VALUES
		('Generic Linux 3.x Kernel'),
		('Debian'),
		('Ubuntu'),
		('Linux Mint'),
		('Fedora'),
		('Red Hat Enterprise Linux'),
		('SUSE Linux Enterprise Server'),
		('CentOS'),
		('Oracle Linux'),
		('Mandriva'),
		('Arch Linux')
	; " );

        //---------------------------------------------------------+

        // Table structure for table "permissions"

        $dbh->exec( "DROP TABLE IF EXISTS permissions  ; " );
        $dbh->exec( "
	CREATE TABLE permissions (
	  ID 			INTEGER UNSIGNED NOT NULL auto_increment,
	  Lft 			INTEGER UNSIGNED NOT NULL,
	  Rght 			INTEGER UNSIGNED NOT NULL,
	  Title 		TEXT NOT NULL,
	  Description 	TEXT NOT NULL,
	  PRIMARY KEY  (ID)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        // Data for table "permissions"

        $dbh->exec( "
	INSERT INTO permissions (ID, Lft, Rght, Title, Description)
	VALUES (1, 0, 1, 'root', 'root');
		" );

        //---------------------------------------------------------+

        // Table structure for table "rolepermissions"

        $dbh->exec( "DROP TABLE IF EXISTS rolepermissions  ; " );
        $dbh->exec( "
	CREATE TABLE rolepermissions (
	  RoleID 			INTEGER UNSIGNED NOT NULL,
	  PermissionID 		INTEGER UNSIGNED NOT NULL,
	  AssignmentDate 	INTEGER UNSIGNED NOT NULL,
	  PRIMARY KEY  (RoleID, PermissionID)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        // Data for table "rolepermissions"

        $dbh->exec( "
	INSERT INTO rolepermissions (RoleID, PermissionID, AssignmentDate)
	VALUES (1, 1, " . time() . ");
		" );

        //---------------------------------------------------------+

        // Table structure for table "roles"

        $dbh->exec( "DROP TABLE IF EXISTS roles  ; " );
        $dbh->exec( "
	CREATE TABLE roles (
	  ID 			INTEGER UNSIGNED NOT NULL auto_increment,
	  Lft 			INTEGER UNSIGNED NOT NULL,
	  Rght 			INTEGER UNSIGNED NOT NULL,
	  Title 		TEXT NOT NULL,
	  Description 	TEXT NOT NULL,
	  PRIMARY KEY  (ID)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        // Data for table "roles"

        $dbh->exec( "
	INSERT INTO roles (ID, Lft, Rght, Title, Description)
	VALUES (1, 0, 1, 'root', 'root');
		" );

        //---------------------------------------------------------+

        // Table structure for table "script"

        $dbh->exec( "DROP TABLE IF EXISTS script  ; " );
        $dbh->exec( "
	CREATE TABLE script (
	  script_id				INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  script_category_id	INTEGER UNSIGNED NOT NULL,
	  box_id				INTEGER UNSIGNED NOT NULL,
	  owner_id				INTEGER UNSIGNED NOT NULL,
	  owner_type			TEXT NOT NULL,
	  name					TEXT NOT NULL,
	  description			TEXT,
	  status				TEXT NOT NULL,
	  process_state			TEXT,
	  path 					TEXT NOT NULL,
	  launcher				TEXT NOT NULL,
	  screen_name			TEXT,
	  type					TEXT NOT NULL,
	  PRIMARY KEY  (script_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        //---------------------------------------------------------+

        // Table structure for table "script_category"

        $dbh->exec( "DROP TABLE IF EXISTS script_category  ; " );
        $dbh->exec( "
	CREATE TABLE script_category (
	  script_category_id	INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  name					TEXT NOT NULL,
	  description			TEXT,
	  PRIMARY KEY  (script_category_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        //---------------------------------------------------------+

        // Table structure for table "server"

        $dbh->exec( "DROP TABLE IF EXISTS server  ; " );
        $dbh->exec( "
	CREATE TABLE server (
	  server_id			INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  box_id			INTEGER UNSIGNED NOT NULL,
	  ip_id				INTEGER UNSIGNED NOT NULL,
	  game_id			INTEGER UNSIGNED NOT NULL,
	  owner_id			INTEGER UNSIGNED NOT NULL,
	  owner_type		TEXT NOT NULL,
	  name				TEXT NOT NULL,
	  description 		TEXT,
	  status			TEXT NOT NULL,
	  process_state		TEXT NOT NULL,
	  slots				INTEGER UNSIGNED NOT NULL,
	  port				INTEGER UNSIGNED NOT NULL,
	  query_port		INTEGER UNSIGNED NOT NULL,
	  config 			BLOB,
	  path				TEXT NOT NULL,
	  launcher			TEXT NOT NULL,
	  screen_name		TEXT NOT NULL,
	  PRIMARY KEY  (server_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        //---------------------------------------------------------+

        // Table structure for table "session"

        $dbh->exec( "DROP TABLE IF EXISTS session  ; " );
        $dbh->exec( "
	CREATE TABLE session (
	  session_id		VARCHAR(255) NOT NULL,
	  session_data		BLOB NOT NULL,
	  expires			INTEGER UNSIGNED NOT NULL,
	  PRIMARY KEY  (session_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        //---------------------------------------------------------+

        // Table structure for table "user"

        $dbh->exec( "DROP TABLE IF EXISTS user  ; " );
        $dbh->exec( "
	CREATE TABLE user (
	  user_id		INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  username		TEXT NOT NULL,
	  password		TEXT NOT NULL,
	  firstname		TEXT,
	  lastname		TEXT,
	  email			TEXT NOT NULL,
	  notes			TEXT,
	  status		TEXT NOT NULL,
	  lang			TEXT NOT NULL,
	  template 		TEXT NOT NULL,
	  last_login	TIMESTAMP,
	  last_activity	TIMESTAMP,
	  last_ip		TEXT,
	  last_host		TEXT,
	  token			TEXT,
	  PRIMARY KEY  (user_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        // Data for table "user"

        $dbh->exec( "
	INSERT INTO user (
	  user_id,
	  username,
	  password,
	  firstname,
	  lastname,
	  email,
	  notes,
	  status,
	  lang,
	  template,
	  last_login,
	  last_activity,
	  last_ip,
	  last_host,
	  token
	)
	VALUES (
	  1,
	  'root',
	  '".getHash('password', $APP_AUTH_SALT)."',
	  'root',
	  'root',
	  'root@toor.com',
	  '',
	  'Active',
	  '".CONF_DEFAULT_LOCALE."',
	  'bootstrap.min.css',
	  '".date('Y-m-d H:i:s', time())."',
	  '".date('Y-m-d H:i:s', time())."',
	  '',
	  '',
	  NULL
	)  ; " );

        $dbh->exec( "
	INSERT INTO user (
	  user_id,
	  username,
	  password,
	  firstname,
	  lastname,
	  email,
	  notes,
	  status,
	  lang,
	  template,
	  last_login,
	  last_activity,
	  last_ip,
	  last_host,
	  token
	)
	VALUES (
	  2,
	  'api',
	  '".getHash(str_shuffle( 'abcdefghijkmnpqrstuvwxyz23456789-#@*!_?ABCDEFGHJKLMNPQRSTUVWXYZ' ), $APP_AUTH_SALT)."',
	  '',
	  '',
	  'root@toor.com',
	  '',
	  'Inactive',
	  '".CONF_DEFAULT_LOCALE."',
	  'bootstrap.min.css',
	  '".date('Y-m-d H:i:s', time())."',
	  '".date('Y-m-d H:i:s', time())."',
	  '',
	  '',
	  NULL
	)  ; " );

        //---------------------------------------------------------+

        // Table structure for table "userroles"

        $dbh->exec( "DROP TABLE IF EXISTS userroles  ; " );
        $dbh->exec( "
	CREATE TABLE userroles (
	  UserID 			INTEGER UNSIGNED NOT NULL,
	  RoleID 			INTEGER UNSIGNED NOT NULL,
	  AssignmentDate 	INTEGER UNSIGNED NOT NULL,
	  PRIMARY KEY  (UserID, RoleID)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

        // Data for table "userroles"

        $dbh->exec( "
	INSERT INTO userroles (UserID, RoleID, AssignmentDate)
	VALUES (1, 1, " . time() . ");
		" );
   }
    catch (PDOException $e) {
        echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
        die();
    }
}

*/