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

if ( !class_exists('BGP_Controller')) {
	trigger_error('Module_Login -> BGP_Controller is missing !');
}

/**
 * Login Controller
 * by Nikita Rousseau
 */

class BGP_Controller_Login extends BGP_Controller
{
	public function authenticateUser( $form ) {
		$errors         = array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data

		$dbh = Core_DBH::getDBH();		// Get Database Handle

		// validate the variables ======================================================

		if (!v::alphanum($form['username'])) {
			$errors['username'] = T_('Username is required.');
		}

		if (empty($form['password'])) {
			$errors['password'] = T_('Password is required.');
		}

		// Verify the form =============================================================

		$username = $form['username'];
		$password = Core_AuthService::getHash($form['password']);

		try {
			// Parse admin table first
			$sth = $dbh->prepare("
				SELECT admin_id, username, firstname, lastname, lang
				FROM " . DB_PREFIX . "admin
				WHERE
					username = :username AND
					password = :password AND
					status = 'active'
				;");

			$sth->bindParam(':username', $username);
			$sth->bindParam(':password', $password);

			$sth->execute();

			$adminResult = $sth->fetchAll();

			if (empty($adminResult))
			{
				// Parse regular user table
				$sth = $dbh->prepare("
					SELECT user_id, username, firstname, lastname, lang
					FROM " . DB_PREFIX . "user
					WHERE
						username = :username AND
						password = :password AND
						status = 'active'
					;");

				$sth->bindParam(':username', $username);
				$sth->bindParam(':password', $password);

				$sth->execute();

				$userResult = $sth->fetchAll();
			}
		}
		catch (PDOException $e) {
			echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			die();
		}

		if (!empty($adminResult)) {
			// Give Admin Privilege

			$authService = Core_AuthService::getAuthService();

			$authService->setSessionInfo(
				$adminResult[0]['admin_id'],
				$adminResult[0]['username'],
				$adminResult[0]['firstname'],
				$adminResult[0]['lastname'],
				$adminResult[0]['lang'],
				BGP_ADMIN_TEMPLATE
				);

			$authService->setSessionPerms( $role = 'Admin' );

			// Cookies

			// Remember Me
			if ( isset($form['rememberMe']) ) {
				$this->setRememberMeCookie( $adminResult[0]['username'] );
			}
			else if ( isset($_COOKIE['USERNAME']) ) {
				$this->rmCookie( 'USERNAME' );
			}

			// Language
			$this->setLangCookie( $adminResult[0]['lang'] );
		}
		else if (!empty($userResult)) {
			// Give User Privilege

			$authService = Core_AuthService::getAuthService();

			$authService->setSessionInfo(
				$userResult[0]['user_id'],
				$userResult[0]['username'],
				$userResult[0]['firstname'],
				$userResult[0]['lastname'],
				$userResult[0]['lang'],
				BGP_USER_TEMPLATE
				);

			$authService->setSessionPerms( $role = 'User' );

			// Cookies

			// Remember Me
			if ( isset($form['rememberMe']) ) {
				$this->setRememberMeCookie( $userResult[0]['username'] );
			}
			else if ( isset($_COOKIE['USERNAME']) ) {
				$this->rmCookie( 'USERNAME' );
			}

			// Language
			$this->setLangCookie( $userResult[0]['lang'] );
		}
		else {
			// Cookie
			if ( isset($_COOKIE['USERNAME']) ) {
				$this->rmCookie( 'USERNAME' );
			}

			$errors['username'] = T_('Invalid Credentials.');
			$errors['password'] = T_('Invalid Credentials.');
		}

		// return a response ===========================================================

		// response if there are errors
		if (!empty($errors)) {

			// if there are items in our errors array, return those errors
			$data['success'] = false;
			$data['errors']  = $errors;

			// notification
			$data['msgType'] = 'warning';
			$data['msg'] = T_('Login Failure!');
		}
		else {

			// if there are no errors, return a message
			$data['success'] = true;

			// notification
			$data['msgType'] = 'success';
			$data['msg'] = T_('Welcome on BrightGamePanel V2!');
		}

		// return all our data to an AJAX call
		return json_encode($data);
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
}
