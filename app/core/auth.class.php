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
 * @copyright	Copyleft 2014, Nikita Rousseau
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

	// Authentication Passphrase
	private $auth_key;

	// RSA Keys
	private $rsa_private_key;
	private $rsa_public_key;

	/**
	 * Default Constructor
	 *
	 * @param String $auth_key
	 * @param String $rsa_private_key
	 * @param String $rsa_public_key
	 * @return void
	 * @access public
	 */
	function __construct( $auth_key = APP_LOGGED_IN_KEY, $rsa_private_key = RSA_PRIVATE_KEY, $rsa_public_key = RSA_PUBLIC_KEY )
	{

		if ( !empty($_SESSION['USERNAME']) ) {
			$this->username = $_SESSION['USERNAME'];
		}
		else {
			$this->username = NULL;
		}

		$this->session = $_SESSION;

		if ( !empty($auth_key) && !empty($rsa_private_key) && !empty($rsa_public_key) ) {
			$this->auth_key = $auth_key;
			$this->rsa_private_key = $rsa_private_key;
			$this->rsa_public_key =  $rsa_public_key;
		}
		else {
			trigger_error("Core_AuthService -> Auth keys are missing !", E_USER_ERROR);
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
	 * @param String $auth_key
	 * @param String $rsa_private_key
	 * @param String $rsa_public_key
	 * @return Core_AuthService
	 * @access public
	 */
	public static function getAuthService( $auth_key = APP_LOGGED_IN_KEY, $rsa_private_key = RSA_PRIVATE_KEY, $rsa_public_key = RSA_PUBLIC_KEY ) {
		if ( empty(self::$authService) || !is_object(self::$authService) || (get_class(self::$authService) != 'Core_AuthService') ) {

			self::$authService = new Core_AuthService( $auth_key, $rsa_private_key, $rsa_public_key );
		}

		return self::$authService;
	}

	/**
	 * Retrieves From The Session Credentials The Role
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public static function getSessionPrivilege() {
		$authService = Core_AuthService::getAuthService();

		$credentials = $authService->decryptSessionCredentials();

		if ( !empty($credentials['role']) ) {
			return $credentials['role'];
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
		if (self::getSessionPrivilege() == 'Admin') {

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
		if (self::getSessionPrivilege() == 'User') {

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
	 * May ban a user from being authenticated after unsuccessful attempts
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
	 * @param String $salt
	 * @return String
	 * @access public
	 */
	public static function getHash( $data, $salt = APP_AUTH_SALT ) {
		return hash( 'sha512', $salt . $data );
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
			if ( empty($credentials['role']) ) {
				$credentials['role'] = 'Guest';
			}

			// Level 1
			if ( $credentials['username'] == $this->username && $credentials['key'] == $this->auth_key && $credentials['token'] == session_id() ) {

				// Level 2
				$dbh = Core_DBH::getDBH();

				switch ( $credentials['role'] )
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
	 * @param String $role
	 * @return void
	 * @access public
	 */
	public function setSessionPerms( $role ) {
		if ( !empty($this->username) ) {
			if ( $role === 'Admin' || $role === 'User' || $role === 'Guest' ) {

				$credentials = serialize (
					array (
					'username' => $this->username,
					'role'	=> $role,
					'token' => session_id(),
					'key' => $this->auth_key,
					'salt' => md5(time())
					)
				);

				$rsa = new Crypt_RSA();
				$rsa->loadKey( $this->rsa_public_key ); // public key

				$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
				$this->session['CREDENTIALS'] = $rsa->encrypt( $credentials );

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
			$rsa = new Crypt_RSA();
			$rsa->loadKey( $this->rsa_private_key ); // private key

			$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
			$credentials = unserialize( $rsa->decrypt( $this->session['CREDENTIALS'] ) );

			return $credentials;
		}
		return array();
	}
}