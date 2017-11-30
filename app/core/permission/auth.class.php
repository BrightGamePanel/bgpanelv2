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
 * Authentication Service
 *
 * This class manages the session validity inside the application
 * Loads also the permissions as well as the user information in the current session
 */
class Core_AuthService
{
	// Handle
	public static $authService;

	// Username
	private $username;

	// Session
	private $session = array();

	// Authentication Passphrase
	private $auth_key;

	// Session Passphrase
	private $session_key;

	/**
	 * Default Constructor
	 *
	 * @return void
	 * @access public
	 */
	function __construct()
	{
		if ( !empty($_SESSION['USERNAME']) ) {
			$this->username = $_SESSION['USERNAME'];
		}
		else {
			$this->username = NULL;
		}

		$this->session = $_SESSION;

		// SECRET KEYS
		$CONFIG = parse_ini_file( CONF_SECRET_INI );

		// LOGGED IN KEY
		$this->auth_key = $CONFIG['APP_LOGGED_IN_KEY'];
		if ( empty($this->auth_key) ) {
			trigger_error("Core_AuthService -> Auth key is missing !", E_USER_ERROR);
		}

		// SESSION KEY
		$this->session_key = $CONFIG['APP_SESSION_KEY'];
		if ( empty($this->session_key) ) {
			trigger_error("Core_AuthService -> Session key is missing !", E_USER_ERROR);
		}
	}

	/**
	 * Service Handler
	 *
	 * @return Core_AuthService
	 * @access public
	 */
	public static function getAuthService() {
		if ( empty(self::$authService) || !is_object(self::$authService) || (get_class(self::$authService) != 'Core_AuthService') )
		{
			self::$authService = new Core_AuthService();
		}

		return self::$authService;
	}

	/**
	 * Logout
	 *
	 * Destroy session variables
	 *
	 * @param none
	 * @return void
	 * @access public
	 */
	public static function logout() {

		$_SESSION = array(); // Destroy session variables

		session_regenerate_id(TRUE);

		self::$authService = NULL;
	}

	/**
	 * Security Counter
	 *
	 * Ban a user from being authenticated after unsuccessful attempts
	 *
	 * @param none
	 * @return none
	 * @access public
	 */
	public function incrementSecCount() {

		// Increment security counter
		if ( empty($this->session['SEC_COUNT']) ) {
			$this->session['SEC_COUNT'] = 1;
		}
		else {
			$this->session['SEC_COUNT'] += 1;
		}

		// Ban the user if too many attempts have been done
		// or the user is already banned but keeps trying
		if ( ($this->session['SEC_COUNT'] > CONF_SEC_LOGIN_ATTEMPTS) || !empty($this->session['SEC_BAN']) ) {
			// Time to ban this session

			// Reset counter
			unset($this->session['SEC_COUNT']);

			// Set ban
			$this->session['SEC_BAN'] = time() + CONF_SEC_BAN_DURATION; // Mark the end of the ban

			// Log Event
			Logger::configure( bgp_log4php_def_conf() );
			$logger = Logger::getLogger( 'core.auth' );
			$logger->info('Session banned.');
		}

		// Push to global $_SESSION
		$_SESSION = $this->session;
	}

	/**
	 * Security Counter - Reset Mechanism
	 *
	 * Reset the internal security counter
	 *
	 * @param none
	 * @return none
	 * @access public
	 */
	public function rsSecCount() {

		if ( !empty($this->session['SEC_COUNT']) ) {
			// Reset counter
			unset($this->session['SEC_COUNT']);
		}

		// Push to global $_SESSION
		$_SESSION = $this->session;
	}

	/**
	 * Check If the Current Session Is Banned
	 *
	 * Automatically remove a ban if this one has expired
	 *
	 * @param none
	 * @return bool
	 * @access public
	 */
	public function isBanned() {

		// No ban registred for this session
		if ( empty($this->session['SEC_BAN'] ) ) {
			return FALSE;
		}
		// Reset the ban if this one has expired
		else if ( $this->session['SEC_BAN'] < time() ) {

			unset( $this->session['SEC_BAN'] );

			// Push to global $_SESSION
			$_SESSION = $this->session;

			return FALSE;
		}
		// Ban in effect
		else {
			return TRUE;
		}
	}

	/**
	 * SHA512 With Salt Function
	 * Generate a hash value (message digest)
	 *
	 * @param String $data
	 * @return String
	 * @access public
	 */
	public static function getHash( $data ) {
		// SECRET KEYS
		$CONFIG = parse_ini_file( CONF_SECRET_INI );

		// AUTH SALT
		$auth_salt = $CONFIG['APP_AUTH_SALT'];
		if ( empty($auth_salt) ) {
			trigger_error("Core_AuthService -> Auth salt is missing !", E_USER_ERROR);
		}

		return hash( 'sha512', $auth_salt . $data );
	}

	/**
	 * Check If The Current Session Is Legit
	 *
	 * @param none
	 * @return bool
	 * @access public
	 */
	public function getSessionValidity() {
		if ( !empty($this->username) ) {

			$credentials = $this->decryptSessionCredentials();

			// Level 1
			if ( $credentials['username'] == $this->username && $credentials['key'] == $this->auth_key && $credentials['token'] == session_id() ) {

				// Level 2
				$dbh = Core_DBH::getDBH();

				// Fetch information from the database
				$sth = $dbh->prepare("
					SELECT username, last_ip, token
					FROM " . DB_PREFIX . "user
					WHERE
						user_id = :user_id
					;");

				$sth->bindParam( ':user_id', $this->session['INFORMATION']['id'] );

				$sth->execute();

				$userResult = $sth->fetchAll(PDO::FETCH_ASSOC);

				// Verify
				if ( $userResult[0]['username'] == $this->username && $userResult[0]['last_ip'] == $_SERVER['REMOTE_ADDR'] && $userResult[0]['token'] == session_id() ) {

					// Update User Activity on page request

					$last_activity = date('Y-m-d H:i:s');

					$sth = $dbh->prepare("
						UPDATE " . DB_PREFIX . "user
						SET
							last_activity	= :last_activity
						WHERE
							user_id			= :user_id
						;");

					$uid = Core_AuthService::getSessionInfo('ID');
					$sth->bindParam(':last_activity', $last_activity);
					$sth->bindParam(':user_id', $uid);

					$sth->execute();

					return TRUE;
				}
				else {

					return FALSE;
				}
			}
		}

		return FALSE;
	}

	/**
	 * Appends Information To The Session
	 *
	 * Note: should be called before Core_AuthService->setSessionPerms()
	 *
	 * @param String $id
	 * @param String $username
	 * @param String $firstname
	 * @param String $lastname
	 * @param String $lang
	 * @param String $template
	 * @return void
	 * @access public
	 */
	public function setSessionInfo( $id, $username, $firstname, $lastname, $lang, $template ) {
		$info = array (
				'id' => $id,
				'firstname' => $firstname,
				'lastname' => $lastname,
				);

		$this->session['INFORMATION'] = $info;
		$this->session['LANG'] = $lang;
		$this->session['TEMPLATE'] = $template;

		$this->session['ID'] = $id;
		$this->session['USERNAME'] = $username;

		$this->username = $username; // Update username var as well
		$_SESSION = $this->session;
	}

	/**
	 * Retrieves from the session the specified information by key
	 *
	 * @param String $info
	 * @return String
	 * @access public
	 */
	public static function getSessionInfo( $info ) {
		if (!empty($_SESSION[ $info ])) {
			return $_SESSION[ $info ];
		}
		else {
			return '';
		}
	}

	/**
	 * Remove Information From The Session
	 *
	 * @param none
	 * @return void
	 * @access public
	 */
	public function rmSessionInfo() {
		if ( array_key_exists('INFORMATION', $this->session) ) {
			unset (
			$this->session['INFORMATION'],
			$this->session['LANG'],
			$this->session['TEMPLATE'],

			$this->session['ID'],
			$this->session['COM'],
			$this->session['USERNAME']
			);
		}

		$this->username = NULL;
		$_SESSION = $this->session;
	}

	/**
	 * Create A New Legit Session
	 * With all perms
	 *
	 * Note: should be called after Core_AuthService->setSessionInfo()
	 *
	 * @return void
	 * @access public
	 */
	public function setSessionPerms() {
		if ( !empty($this->username) ) {

			$credentials = serialize (
				array (
				'username' => $this->username,
				'token' => session_id(),
				'key' => $this->auth_key,
				'salt' => md5(time())
				)
			);

			$perms = Core_AuthService_Perms::getUserPermissions();
			$this->session['PERMISSIONS'] = $perms;

			switch ( CONF_SEC_SESSION_METHOD )
			{
				case 'aes256':
				default:
					$cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
					$cipher->setKeyLength(256);
					$cipher->setKey( $this->session_key );

					$this->session['CREDENTIALS'] = $cipher->encrypt( $credentials );
					break;
			}

			$_SESSION = $this->session;
		}
	}

	/**
	 * Remove Permissions Of A Session
	 *
	 * @param none
	 * @return void
	 * @access public
	 */
	public function rmSessionPerms() {
		if ( array_key_exists('CREDENTIALS', $this->session) ) {
			unset ( $this->session['CREDENTIALS'] );
			unset ( $this->session['PERMISSIONS'] );
		}

		$_SESSION = $this->session;
	}

	/**
	 * Decrypt Session Credentials
	 *
	 * @param none
	 * @return array
	 * @access private
	 */
	private function decryptSessionCredentials() {
		if ( !empty($this->session) && array_key_exists('CREDENTIALS', $this->session) ) {
			switch ( CONF_SEC_SESSION_METHOD )
			{
				case 'aes256':
				default:
					$cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
					$cipher->setKeyLength(256);
					$cipher->setKey( $this->session_key );

					$credentials = unserialize( $cipher->decrypt( $this->session['CREDENTIALS'] ) );
					break;
			}

			return $credentials;
		}
		return array();
	}








	function checkApiUser() {
        // Credentials

        $headers = array_change_key_case(apache_request_headers(), CASE_UPPER);

        $headers['X-API-KEY'] = (isset($headers['X-API-KEY'])) ? filter_var($headers['X-API-KEY'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : NULL;
        $headers['X-API-USER'] = (isset($headers['X-API-USER'])) ? filter_var($headers['X-API-USER'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : NULL;
        $headers['X-API-PASS'] = (isset($headers['X-API-PASS'])) ? filter_var($headers['X-API-PASS'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : NULL;

        // Servers with Server API set to CGI/FCGI
        // Will not populate PHP_AUTH vars

        $_SERVER['PHP_AUTH_USER'] = (isset($_SERVER['PHP_AUTH_USER'])) ? filter_var($_SERVER['PHP_AUTH_USER'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : NULL;
        $_SERVER['PHP_AUTH_PW'] = (isset($_SERVER['PHP_AUTH_PW'])) ? filter_var($_SERVER['PHP_AUTH_PW'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW) : NULL;

        if (empty($headers['X-API-KEY']) AND empty($headers['X-API-USER']) AND empty($headers['X-API-PASS']) AND empty($_SERVER['PHP_AUTH_USER']) AND empty($_SERVER['PHP_AUTH_PW'])) {

            // Unauthorized
            // No credentials
            header( Core_Http_Status_Codes::httpHeaderFor( 401 ) );
            session_destroy();
            exit( 0 );
        }

        // Machine Authentication

        // AUTH-BASIC (if allowed)
        // OR
        // X-HTTP-HEADERS AUTH (default)

        if ((boolval(APP_API_ALLOW_BASIC_AUTH) === TRUE) && !empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
            if (Core_AuthService_API::checkRemoteHost( Flight::request()->ip, $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], '', 'auth-basic' ) === FALSE) {

                // Unauthorized
                header( Core_Http_Status_Codes::httpHeaderFor( 401 ) );
                session_destroy();
                exit( 0 );
            }
        }
        else {
            if (Core_AuthService_API::checkRemoteHost( Flight::request()->ip, $headers['X-API-USER'], $headers['X-API-PASS'], $headers['X-API-KEY'], 'x-http-headers' ) === FALSE) {

                // Unauthorized
                header( Core_Http_Status_Codes::httpHeaderFor( 401 ) );
                session_destroy();
                exit( 0 );
            }
        }
    }

// Verify that the remote machine can call the API service
    public static function checkRemoteHost( $remote_ip, $api_user, $api_user_pass, $api_key = '', $auth_method = 'x-http-headers' )
    {
        // Get IPs Whitelist

        $trustedIps = parse_ini_file( CONF_API_WHITELIST_INI, TRUE );

        if (!empty($trustedIps) && isset($trustedIps['IPv4'])) {
            $trustedIps = array_values( $trustedIps['IPv4'] );

            // Verify IP

            if (in_array($remote_ip, $trustedIps)) {

                // Get API Key

                $apiMasterKey = parse_ini_file( CONF_API_KEY_INI );

                if (!empty($apiMasterKey) && isset($apiMasterKey['APP_API_KEY'])) {
                    $apiMasterKey = $apiMasterKey['APP_API_KEY'];

                    switch ($auth_method) {

                        case 'auth-basic' :

                            // Verify API User

                            return self::checkRemoteAPIUser( $remote_ip, $api_user, $api_user_pass );

                            break;

                        case 'x-http-headers' :
                        default :

                            if ($api_key == $apiMasterKey) {

                                return self::checkRemoteAPIUser( $remote_ip, $api_user, $api_user_pass );
                            }

                            break;
                    }
                }
            }
        }

        return FALSE;
    }

// Once the machine has been authenticated, we verify the user
    public static function checkRemoteAPIUser( $remote_ip, $api_user, $api_user_pass )
    {
        $username = $api_user;
        $password = Core_AuthService::getHash($api_user_pass);

        $dbh = Core_DBH::getDBH();

        try {
            $sth = $dbh->prepare("
				SELECT user_id, username, firstname, lastname, lang, template
				FROM " . DB_PREFIX . "user
				WHERE
					username = :username AND
					password = :password AND
					status = 'Active'
				;");

            $sth->bindParam(':username', $username);
            $sth->bindParam(':password', $password);

            $sth->execute();

            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
            die();
        }

        if (!empty($result)) {
            $user_id = $result[0]['user_id'];

            // NIST Level 2 Standard Role Based Access Control Library

            $rbac = new PhpRbac\Rbac();

            // Verify API Role

            if ( $rbac->Users->hasRole( 'api', $user_id ) ) {

                // Update User Activity

                try {
                    $sth = $dbh->prepare("
						UPDATE " . DB_PREFIX . "user
						SET
							last_login		= :last_login,
							last_activity	= :last_activity,
							last_ip 		= :last_ip,
							last_host		= :last_host,
							token 			= :token
						WHERE
							user_id			= :user_id
						;");

                    $last_login = date('Y-m-d H:i:s');
                    $last_activity = date('Y-m-d H:i:s');
                    $last_host = gethostbyaddr($remote_ip);
                    $token = session_id();

                    $sth->bindParam(':last_login', $last_login);
                    $sth->bindParam(':last_activity', $last_activity);
                    $sth->bindParam(':last_ip', $remote_ip);
                    $sth->bindParam(':last_host', $last_host);
                    $sth->bindParam(':token', $token);
                    $sth->bindParam(':user_id', $user_id);

                    $sth->execute();
                }
                catch (PDOException $e) {
                    echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
                    die();
                }

                // Start the authentication service

                $authService = Core_AuthService::getAuthService();

                // Log in the user

                if ($authService->getSessionValidity() == FALSE) {

                    session_regenerate_id( TRUE );

                    $authService->setSessionInfo(
                        $result[0]['user_id'],
                        $result[0]['username'],
                        $result[0]['firstname'],
                        $result[0]['lastname'],
                        $result[0]['lang'],
                        $result[0]['template']
                    );

                    $authService->setSessionPerms();
                }

                return TRUE;
            }
        }

        return FALSE;
    }



}