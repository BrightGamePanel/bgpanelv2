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



class Core_AuthService
{
	// Handle
	public static $authService;

	// Username
	private $username;

	// Encrypted Session
	private $session = array();

	// Hash Salt
	public $auth_salt;

	// Authentication Passphrase
	private $auth_key;

	// Session Passphrase
	private $session_key;

	// RSA Keys
	private $rsa_private_key;
	private $rsa_public_key;

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


		// AUTH SALT
		$this->auth_salt = $CONFIG['APP_AUTH_SALT'];
		if ( empty($this->auth_salt) ) {
			trigger_error("Core_AuthService -> Auth salt is missing !", E_USER_ERROR);
		}

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

		// RSA KEYS
		if ( file_exists(RSA_PRIVATE_KEY_FILE) && file_exists(RSA_PUBLIC_KEY_FILE) ) {
			$this->rsa_private_key = file_get_contents( RSA_PRIVATE_KEY_FILE );
			$this->rsa_public_key  = file_get_contents( RSA_PUBLIC_KEY_FILE );
		}
		if ( empty($this->rsa_private_key) || empty($this->rsa_public_key) ) {
			trigger_error("Core_AuthService -> RSA keys are missing !", E_USER_ERROR);
		}
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

		// Log Event
		Logger::configure( bgp_get_log4php_conf_array() );
		$logger = Logger::getLogger( 'auth.core' );
		$logger->info('Log out.');

		$_SESSION = array(); // Destroy session variables
		session_destroy();

		self::$authService = NULL;
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
	 * Retrieves From The Session Credentials The User Type
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public static function getSessionType() {
		$authService = Core_AuthService::getAuthService();

		$credentials = $authService->decryptSessionCredentials();

		if ( !empty($credentials['type']) ) {
			return $credentials['type'];
		}
		return 'Guest';
	}

	/**
	 * Test If The Session Has Full Admin Privilege
	 *
	 * @param none
	 * @return bool
	 * @access public
	 */
	public static function isAdmin() {
		if (self::getSessionType() == 'Admin') {

			$authService = Core_AuthService::getAuthService();

			if ($authService->getSessionValidity() == TRUE) {

				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Test If The Session Has Full User Privilege
	 *
	 * @param none
	 * @return bool
	 * @access public
	 */
	public static function isUser() {
		if (self::getSessionType() == 'User') {

			$authService = Core_AuthService::getAuthService();

			if ($authService->getSessionValidity() == TRUE) {

				return TRUE;
			}
		}

		return FALSE;
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
			Logger::configure( bgp_get_log4php_conf_array() );
			$logger = Logger::getLogger( 'auth.core' );
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
		$authService = Core_AuthService::getAuthService();

		return hash( 'sha512', $authService->auth_salt . $data );
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
			if ( empty($credentials['type']) ) {
				$credentials['type'] = 'Guest';
			}

			// Level 1
			if ( $credentials['username'] == $this->username && $credentials['key'] == $this->auth_key && $credentials['token'] == session_id() ) {

				// Level 2
				$dbh = Core_DBH::getDBH();

				switch ( $credentials['type'] )
				{
					case 'Admin':

						// Fetch information from the database
						$sth = $dbh->prepare("
							SELECT username, last_ip, token
							FROM " . DB_PREFIX . "admin
							WHERE
								admin_id = :admin_id
							;");

						$sth->bindParam( ':admin_id', $this->session['INFORMATION']['id'] );

						$sth->execute();

						$adminResult = $sth->fetchAll(PDO::FETCH_ASSOC);

						// Verify
						if ( $adminResult[0]['username'] == $this->username && $adminResult[0]['last_ip'] == $_SERVER['REMOTE_ADDR'] && $adminResult[0]['token'] == session_id() ) {
							return TRUE;
						}
						else {
							return FALSE;
						}

					case 'User':

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
							return TRUE;
						}
						else {
							return FALSE;
						}

					default:
						// Guest case
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
	 * @param String $COM
	 * @return void
	 * @access public
	 */
	public function setSessionInfo( $id, $username, $firstname, $lastname, $lang, $template, $COM ) {
		$info = array (
				'id' => $id,
				'firstname' => $firstname,
				'lastname' => $lastname,
				);

		$this->session['INFORMATION'] = $info;
		$this->session['LANG'] = $lang;
		$this->session['TEMPLATE'] = $template;

		$this->session['ID'] = $id; // Logging (user-id)
		$this->session['COM'] = $COM; // Logging (Component Object Model)
		$this->session['USERNAME'] = $username;	// Multi-purpose
												// Logging (user-identifier)

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
	 *
	 * Note: should be called after Core_AuthService->setSessionInfo()
	 *
	 * @param String $type
	 * @return void
	 * @access public
	 */
	public function setSessionPerms( $type ) {
		if ( !empty($this->username) ) {
			if ( $type == 'Admin' || $type == 'User' ) {

				$credentials = serialize (
					array (
					'username' => $this->username,
					'type'	=> $type,
					'token' => session_id(),
					'key' => $this->auth_key,
					'salt' => md5(time())
					)
				);

				switch ( CONF_SEC_SESSION_METHOD )
				{
					case 'aes256':
						$cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
						$cipher->setKeyLength(256);
						$cipher->setKey( $this->session_key );

						$this->session['CREDENTIALS'] = $cipher->encrypt( $credentials );
						break;

					case 'rsa':
					default:
						$rsa = new Crypt_RSA();
						$rsa->loadKey( $this->rsa_public_key ); // public key

						$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
						$this->session['CREDENTIALS'] = $rsa->encrypt( $credentials );
						break;
				}

				$_SESSION = $this->session;
			}
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
					$cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
					$cipher->setKeyLength(256);
					$cipher->setKey( $this->session_key );

					$credentials = unserialize( $cipher->decrypt( $this->session['CREDENTIALS'] ) );
					break;

				case 'rsa':
				default:
					$rsa = new Crypt_RSA();
					$rsa->loadKey( $this->rsa_private_key ); // private key

					$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
					$credentials = unserialize( $rsa->decrypt( $this->session['CREDENTIALS'] ) );
					break;
			}

			return $credentials;
		}
		return array();
	}
}