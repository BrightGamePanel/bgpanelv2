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
	trigger_error('Controller_Login -> BGP_Controller is missing !');
}

/**
 * Login Controller
 */

class BGP_Controller_Login extends BGP_Controller
{

	function __construct( )	{
	
		// Call parent constructor
		parent::__construct( basename(__DIR__) );
	}

	/**
	 * Authentication
	 *
	 * @param string $username
	 * @param string $password
	 *
	 * @author Nikita Rousseau
	 */
	public function authenticateUser( $username, $password ) {
		$form = array (
			'username' => $username,
			'password' => $password
		);

		$errors			= array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data

		$dbh = Core_DBH::getDBH();		// Get Database Handle

		// validate the variables ======================================================

		$v = new Valitron\Validator( $form );

		$rules = [
				'required' => [
					['username'],
					['password']
				],
				'alphaNum' => [
					['username']
				]
			];

		$v->rules( $rules );
		$v->validate();

		$errors = $v->errors();

		// Verify the form =============================================================

		if (empty($errors))
		{
			$username = $form['username'];
			$password = Core_AuthService::getHash($form['password']);

			try {
				$sth = $dbh->prepare("
					SELECT user_id, username, firstname, lastname, lang
					FROM " . DB_PREFIX . "user
					WHERE
						username = :username AND
						password = :password AND
						status = 'Active'
					;");

				$sth->bindParam(':username', $username);
				$sth->bindParam(':password', $password);

				$sth->execute();

				$result = $sth->fetchAll();
			}
			catch (PDOException $e) {
				echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
				die();
			}

			if (!empty($result)) {

				// Give User Privilege

				$authService = Core_AuthService::getAuthService();

				// Reset Login Attempts
				$authService->rsSecCount();

				$authService->setSessionInfo(
					$result[0]['user_id'],
					$result[0]['username'],
					$result[0]['firstname'],
					$result[0]['lastname'],
					$result[0]['lang'],
					BGP_USER_TEMPLATE
					);

				session_regenerate_id( TRUE );

				$authService->setSessionPerms();

				// Database update

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
				$last_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
				$token = session_id();

				$sth->bindParam(':last_login', $last_login);
				$sth->bindParam(':last_activity', $last_activity);
				$sth->bindParam(':last_ip', $_SERVER['REMOTE_ADDR']);
				$sth->bindParam(':last_host', $last_host);
				$sth->bindParam(':token', $token);
				$sth->bindParam(':user_id', $result[0]['user_id']);

				$sth->execute();

				// Cookies

				// Remember Me
				if ( isset($form['rememberMe']) ) {
					$this->setRememberMeCookie( $result[0]['username'] );
				}
				else if ( isset($_COOKIE['USERNAME']) ) {
					$this->rmCookie( 'USERNAME' );
				}

				// Language
				$this->setLangCookie( $result[0]['lang'] );

				// Log Event
				Logger::configure( bgp_get_log4php_conf_array() );
				$logger = Logger::getLogger( self::getLoggerName() );
				$logger->info('Log in.');
			}
			else {
				// Cookie
				if ( isset($_COOKIE['USERNAME']) ) {
					$this->rmCookie( 'USERNAME' );
				}

				// Call security component
				$authService = Core_AuthService::getAuthService();
				$authService->incrementSecCount();

				// Log Event
				Logger::configure( bgp_get_log4php_conf_array() );
				$logger = Logger::getLogger( self::getLoggerName() );
				$logger->info('Login failure.');

				// Messages
				$errors['username'] = T_('Invalid Credentials.');
				$errors['password'] = T_('Invalid Credentials.');
			}
		}

		// return a response ===========================================================

		// response if there are errors
		if (!empty($errors)) {

			// if there are items in our errors array, return those errors
			$data['success'] = false;
			$data['errors']  = $errors;

			// notification
			$authService = Core_AuthService::getAuthService();

			if ( $authService->isBanned() ) {
				$data['msgType'] = 'warning';
				$data['msg'] = T_('You have been banned') . ' ' . CONF_SEC_BAN_DURATION . ' ' . T_('seconds!');
			}
			else {
				$data['msgType'] = 'warning';
				$data['msg'] = T_('Login Failure!');
			}
		}
		else {

			$data['success'] = true;
		}

		// return all our data to an AJAX call
		return $data;
	}

	private function setRememberMeCookie( $username ) {
		setcookie('USERNAME', htmlentities($username, ENT_QUOTES), time() + (86400 * 7 * 2), BASE_URL); // 86400 = 1 day
	}

	private function setLangCookie( $lang ) {
		setcookie('LANG', htmlentities($lang, ENT_QUOTES), time() + (86400 * 7 * 2), BASE_URL);
	}

	private function rmCookie( $cookie ) {
		setcookie($cookie, '', time() - 3600, BASE_URL);
	}

	/**
	 * User Password Renewal
	 *
	 * @param string $username
	 * @param string $email
	 * @param optional bool $captcha_validation
	 *
	 * @author Nikita Rousseau
	 */
	public function sendNewPassword( $username, $email, $captcha_validation = TRUE ) {
		$form = array (
			'username' => $username,
			'email'    => $email
		);

		$errors			= array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data

		$dbh = Core_DBH::getDBH();		// Get Database Handle

		// validate the variables ======================================================

		$v = new Valitron\Validator( $form );

		$rules = [
				'required' => [
					['username'],
					['email']
				],
				'alphaNum' => [
					['username']
				],
				'email' => [
					['email']
				]
			];

		$v->rules( $rules );
		$v->validate();

		$errors = $v->errors();

		// Verify the form =============================================================

		if (empty($errors))
		{
			$username 	= $form['username'];
			$email 		= $form['email'];

			try {
				$sth = $dbh->prepare("
					SELECT user_id, email
					FROM " . DB_PREFIX . "user
					WHERE
						username = :username AND
						email 	 = :email AND
						status   = 'active'
					;");

				$sth->bindParam(':username', $username);
				$sth->bindParam(':email', $email);

				$sth->execute();

				$result = $sth->fetchAll();
			}
			catch (PDOException $e) {
				echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
				die();
			}

			if ( !empty($result) && ($captcha_validation == TRUE) ) {
				$authService = Core_AuthService::getAuthService();

				// Reset Login Attempts
				$authService->rsSecCount();

				// Reset User Passwd
				$plainTextPasswd = bgp_create_random_password( 13 );
				$digestPasswd = Core_AuthService::getHash($plainTextPasswd);

				// Update User Passwd
				$sth = $dbh->prepare("
					UPDATE " . DB_PREFIX . "user
					SET
						password 	= :password
					WHERE
						user_id		= :user_id
					;");

				$sth->bindParam(':password', $digestPasswd);
				$sth->bindParam(':user_id', $result[0]['user_id']);

				$sth->execute();

				// Send Email
				$to = htmlentities($result[0]['email'], ENT_QUOTES);

				$subject = T_('Reset Password');

				$message = T_('Your password has been reset to:');
				$message .= "<br /><br />" . $plainTextPasswd . "<br /><br />";
				$message .= T_('With IP').': ';
				$message .= $_SERVER['REMOTE_ADDR'];

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: Bright Game Panel System <localhost@'. $_SERVER['SERVER_NAME'] .'>' . "\r\n";
				$headers .= 'X-Mailer: PHP/' . phpversion();

				$mail = mail($to, $subject, $message, $headers);

				// Log Event
				Logger::configure( bgp_get_log4php_conf_array() );
				$logger = Logger::getLogger( self::getLoggerName() );
				$logger->info('Password reset.');
			}
			else {
				// Call security component
				$authService = Core_AuthService::getAuthService();
				$authService->incrementSecCount();

				// Log Event
				Logger::configure( bgp_get_log4php_conf_array() );
				$logger = Logger::getLogger( self::getLoggerName() );
				$logger->info('Bad password reset.');

				// Messages
				if ( empty($result) && empty($adminResult) ) {
					$errors['username'] = T_('Wrong information.');
					$errors['email'] = T_('Wrong information.');
				}

				if ($captcha_validation == FALSE) {
					$errors['captcha'] = T_('Wrong CAPTCHA Code.');
				}
			}
		}

		// return a response ===========================================================

		// response if there are errors
		if (!empty($errors)) {

			// if there are items in our errors array, return those errors
			$data['success'] = false;
			$data['errors']  = $errors;

			// notification
			$authService = Core_AuthService::getAuthService();

			if ( $authService->isBanned() ) {
				$data['msgType'] = 'warning';
				$data['msg'] = T_('You have been banned') . ' ' . CONF_SEC_BAN_DURATION . ' ' . T_('seconds!');
			}
			else {
				$data['msgType'] = 'warning';
				$data['msg'] = T_('Invalid information provided!');
			}
		}
		else if (!$mail) {

			// mail delivery error
			$data['success'] = false;

			// notification
			$data['msgType'] = 'danger';
			$data['msg'] = T_('An error has occured while sending the email. Contact your system administrator.');
		}
		else {

			$data['success'] = true;
		}

		// return all our data to an AJAX call
		return $data;
	}
}
