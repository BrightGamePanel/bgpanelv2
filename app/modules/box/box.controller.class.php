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

if ( !class_exists('BGP_Controller')) {
	trigger_error('Controller_Box -> BGP_Controller is missing !');
}

/**
 * Admin Box Controller
 */

class BGP_Controller_Box extends BGP_Controller {

	function __construct( )	{
	
		// Call parent constructor
		parent::__construct( basename(__DIR__) );
	}

	/**
	 * Add a New Box To The Collection
	 *
	 * @http_method POST
	 * @resource box/
	 *
	 * @param string $name query
	 * @param string $os query
	 * @param string $ip query
	 * @param string $port query
	 * @param string $login query
	 * @param string $password query
	 * @param optional string $userPath
	 * @param optional string $steamPath
	 * @param optional string $notes
	 *
	 * @return application/json
	 *
	 * @author Nikita Rousseau
	 */
	function postBox( $name, $os, $ip, $port, $login, $password, $userPath = '', $steamPath = '', $notes = '' )
	{
		$args = array (
			'name' 			=> $name,
			'os' 			=> $os,
			'ip' 			=> $ip,
			'port' 			=> $port,
			'login' 		=> $login,
			'password' 		=> $password,
			'userPath' 		=> $userPath,
			'steamPath' 	=> $steamPath,
			'notes' 		=> $notes
		);

		$errors			= array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data
		
		$dbh = Core_DBH::getDBH();		// Get Database Handle
		
		// validate the variables ======================================================

		$v = new Valitron\Validator( $args );

		$rules = [
				'required' => [
					['name'],
					['os'],
					['ip'],
					['port'],
					['login'],
					['password']
				],
				'regex' => [
					['name', "/^([-a-z0-9_ -])+$/i"]
				],
				'integer' => [
					['os'],
					['port']
				],
				'ip' => [
					['ip']
				],
				'alphaNum' => [
					['login']
				]
			];

		$labels = array(
				'name' 		=> T_('Remote Machine Name'),
				'os' 		=> T_('Operating System'),
				'ip' 		=> T_('IP Address'),
				'port'		=> T_('Port'),
				'login' 	=> T_('Login'),
				'password'  => T_('Password')
			);

		$v->rules( $rules );
		$v->labels( $labels );

		$v->validate();

		$errors = $v->errors();

		// validate the variables phase 2 ==============================================

		if (empty($errors))
		{
			// Verify OS ID

			try {
				$sth = $dbh->prepare("
					SELECT operating_system
					FROM " . DB_PREFIX . "os
					WHERE
						os_id = :os_id
					;");

				$sth->bindParam( ':os_id', $args['os'] );

				$sth->execute();

				$result = $sth->fetchAll( PDO::FETCH_ASSOC );
			}
			catch (PDOException $e) {
				echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
				die();
			}

			if (empty($result[0])) {
				$errors['os'] = 'Bad Identifier';
			}

			// Verify Communication

			$socket = @fsockopen( $args['ip'], $args['port'], $errno, $errstr, 3 );

			if ( $socket === FALSE ) {

				$errors['com'] = "Unable to connect to " . $args['ip'] . " on port " . $args['port'] . ". " . utf8_encode($errstr) . " ( $errno )";
				unset($socket);
			}
			else {
				unset($socket);

				$ssh = new Net_SSH2( $args['ip'], $args['port'] );

				if ( !$ssh->login( $args['login'], $args['password'] ) )
				{

					$errors['com'] = 'Login failed';
				}
				else {

					// Verify Remote Paths		

					if (!empty($args['userPath'])) {

						if ( boolval(trim( $ssh->exec('test -d '. escapeshellcmd($args['userPath']) . " && echo '1' || echo '0'") )) === FALSE ) {

							$errors['remoteUserHome'] = 'Invalid path. Must be an absolute or full path';
						}
					}

					if (!empty($args['steamPath'])) {

						if ( boolval(trim( $ssh->exec('test -f '. escapeshellcmd($args['steamPath']) . " && echo '1' || echo '0'") )) === FALSE ) {

							$errors['steamcmd'] = 'SteamCMD not found. Must be an absolute or full path';
						}
					}
				}

				$ssh->disconnect();
			}
		}

		// Apply =======================================================================

		if (empty($errors))
		{
			//
			// Database update
			//

			// Vars Init

			if (empty($args['userPath'])) {
				$home = "~";
				$args['userPath'] = $home;
			} else {
				$home = escapeshellcmd(normalizePath($args['userPath']));
				$args['userPath'] = $home;
			}

			$config = parse_ini_file( CONF_SECRET_INI );

			// BOX

			try {
				$sth = $dbh->prepare("
					INSERT INTO " . DB_PREFIX . "box
					SET
						os_id 			= :os,
						name 			= :name,
						steam_lib_path 	= :steamcmd,
						notes 			= :notes
					;");

				$sth->bindParam( ':os', $args['os'] );
				$sth->bindParam( ':name', $args['name'] );
				$sth->bindParam( ':steamcmd', $args['steamPath'] );
				$sth->bindParam( ':notes', $args['notes'] );

				$sth->execute();

				$box_id = $dbh->lastInsertId();
			}
			catch (PDOException $e) {
				echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
				die();
			}

			// IP

			try {
				$sth = $dbh->prepare("
					INSERT INTO " . DB_PREFIX . "box_ip
					SET
						box_id = :box_id,
						ip = :ip,
						is_default = 1
					;");

				$sth->bindParam( ':box_id', $box_id );
				$sth->bindParam( ':ip', $args['ip'] );

				$sth->execute();
			}
			catch (PDOException $e) {
				echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
				die();
			}

			// CREDENTIALS

			// Phase 1
			// Connect to the remote host
			// Try to append our public key to authorized_keys

			$ssh = new Net_SSH2( $args['ip'], $args['port'] );
			$ssh->login( $args['login'], $args['password'] );

			$remote_keys = $ssh->exec( 'cat ' . $home . '/.ssh/authorized_keys' );

			// Check if the public key already exists

			if (strpos($remote_keys, file_get_contents( RSA_PUBLIC_KEY_FILE )) === FALSE) {

				// Otherwise, append it

				$ssh->exec( "echo '" . file_get_contents( RSA_PUBLIC_KEY_FILE ) . "' >> " . $home . "/.ssh/authorized_keys" );
			}

			// Phase 2
			// Verify that the public key is allowed on the remote host

			$isUsingSSHPubKey = TRUE; // By default, we use the SSH authentication keys method

			$remote_keys = $ssh->exec( 'cat ' . $home . '/.ssh/authorized_keys' );

			$ssh->disconnect();

			if (strpos($remote_keys, file_get_contents( RSA_PUBLIC_KEY_FILE )) === FALSE)
			{
				// authorized_keys is not writable
				// Use compatibility mode
				// Store the password in DB

				$isUsingSSHPubKey = FALSE;
			}
			else
			{
				// Phase 3
				// Try to connect with our private key on the remote host

				$ssh = new Net_SSH2( $args['ip'], $args['port'] );

				$key = new Crypt_RSA();
				$key->loadKey( file_get_contents( RSA_PRIVATE_KEY_FILE ) );

				if (!$ssh->login( $args['login'], $key )) {

					// Authentication failed
					// Use compatibility mode
					// Store the password in DB

					$isUsingSSHPubKey = FALSE;
				}

				$ssh->disconnect();
			}

			// SSH CREDENTIALS

			$cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
			$cipher->setKeyLength(256);
			$cipher->setKey( $config['APP_SSH_KEY'] );

			if ($isUsingSSHPubKey)
			{
				try {
					$sth = $dbh->prepare("
						INSERT INTO " . DB_PREFIX . "box_credential
						SET
							login = :login,
							remote_user_home = :home,
							com_protocol = 'ssh2',
							com_port = :com_port
						;");

					$login = $cipher->encrypt($args['login']);

					$sth->bindParam( ':login',  $login );
					$sth->bindParam( ':home', $args['userPath'] );
					$sth->bindParam( ':com_port', $args['port'] );

					$sth->execute();

					$credential_id = $dbh->lastInsertId();
				}
				catch (PDOException $e) {
					echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
					die();
				}
			}
			else
			{
				try {
					$sth = $dbh->prepare("
						INSERT INTO " . DB_PREFIX . "box_credential
						SET
							login = :login,
							password = :password,
							remote_user_home = :home,
							com_protocol = 'ssh2',
							com_port = :port
						;");

					$login = $cipher->encrypt($args['login']);
					$password = $cipher->encrypt($args['password']);

					$sth->bindParam( ':login', $login );
					$sth->bindParam( ':password', $password );
					$sth->bindParam( ':home', $args['userPath'] );
					$sth->bindParam( ':com_port', $args['port'] );

					$sth->execute();

					$credential_id = $dbh->lastInsertId();
				}
				catch (PDOException $e) {
					echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
					die();
				}
			}

			// UPDATE BOX

			try {
				$sth = $dbh->prepare( "
					UPDATE " . DB_PREFIX . "box
					SET
						box_credential_id = :box_credential_id
					WHERE box_id = :box_id
					;" );

				$sth->bindParam( ':box_credential_id', $credential_id );
				$sth->bindParam( ':box_id', $box_id );

				$sth->execute();
			}
			catch (PDOException $e) {
				echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
				die();
			}
		}

		// return a response and log ===================================================

		$logger = self::getLogger();

		$data['errors'] = $errors;

		if (!empty($data['errors'])) {

			$data['success'] = false;

			$logger->info('Failed to add box.');
		} else {

			$data['success'] = true;

			$logger->info('Box added.');
		}

		return array(
			'response' => 'application/json',
			'data' => json_encode($data)
		);
	}
}
