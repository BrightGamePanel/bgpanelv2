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
 * @categories	Games/Entertainment, Systems Administration
 * @package		Bright Game Panel V2
 * @author		warhawk3407 <warhawk3407@gmail.com> @NOSPAM
 * @copyleft	2014
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @version		0.1
 * @link		http://www.bgpanel.net/
 */



class Core_AuthService
{
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
	 * @param String $username
	 * @param String $auth_key
	 * @param String $rsa_private_key
	 * @param String $rsa_public_key
	 * @return void
	 * @access public
	 */
	function __construct( $username = '', $auth_key = APP_LOGGED_IN_KEY, $rsa_private_key = RSA_PRIVATE_KEY, $rsa_public_key = RSA_PUBLIC_KEY )
	{
		if ( !empty($username) ) {
			$this->username = $username;
		}
		else {
			trigger_error("Core_AuthService -> Username is missing !", E_USER_ERROR);
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
	 * Check If The Current Session Is Legit
	 *
	 * @param none
	 * @return bool
	 * @access public
	 */
	public function getSessionValidity() {
		if ( !empty($this->session) && array_key_exists('CREDENTIALS', $this->session) ) {
			$rsa = new Crypt_RSA();
			$rsa->loadKey( $this->rsa_private_key ); // private key

			$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
			$credentials = unserialize( $rsa->decrypt( $this->session['CREDENTIALS'] ) );

			if ( $credentials['username'] == $this->username && $credentials['key'] == $this->auth_key ) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Create A New Legit Session
	 *
	 * @param none
	 * @return void
	 * @access public
	 */
	public function setSessionWhitecard() {
		$credentials = serialize (
			array (
			'username' => $this->username,
			'role'	=> NULL,
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

	/**
	 * Remove White Card Of A Session
	 *
	 * @param none
	 * @return void
	 * @access public
	 */
	public function rmSessionWhitecard() {
		if ( array_key_exists('CREDENTIALS', $this->session) ) {
			unset( $this->session['CREDENTIALS'] );
		}

		$_SESSION = $this->session;
	}
}