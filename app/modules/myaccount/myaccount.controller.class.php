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
	trigger_error('Controller_Myaccount -> BGP_Controller is missing !');
}

/**
 * My Account Controller
 */

class BGP_Controller_Myaccount extends BGP_Controller {

	function __construct( )	{
	
		// Call parent constructor
		parent::__construct( basename(__DIR__) );
	}

	/**
	 * Update User Configuration
	 *
	 * @param string $username query
	 * @param string $password0 query
	 * @param string $password1 query
	 * @param string $email query
	 * @param string $language query
	 * @param optional string $firstname query
	 * @param optional string $lastname query
	 * @param optional string $template query
	 *
	 * @author Nikita Rousseau
	 */
	public function updateUserConfig( $username, $password0, $password1, $email, $language, $firstname = '', $lastname = '', $template = 'bootstrap.min.css' )
	{
		$form = array (
			'username' 		=> $username,
			'password0' 	=> $password0,
			'password1' 	=> $password1,
			'email' 		=> $email,
			'language' 		=> $language,
			'template'		=> $template
		);

		$errors			= array();  	// array to hold validation errors
		$data 			= array(); 		// array to pass back data

		$dbh = Core_DBH::getDBH();		// Get Database Handle

		// Get languages
		$languages = parse_ini_file( CONF_LANG_INI );
		$languages = array_flip(array_values($languages));

		// Get templates
		$templates = parse_ini_file( CONF_TEMPLATES_INI );
		$templates = array_flip(array_values($templates));

		// validate the variables ======================================================

		$v = new Valitron\Validator( $form );

		$rules = [
				'required' => [
					['username'],
					['password0'],
					['password1'],
					['email'],
					['language']
				],
				'alphaNum' => [
					['username']
				],
				'lengthMin' => [
					['username', 4],
					['password0', 8]
				],
				'equals' => [
					['password0', 'password1']
				],
				'email' => [
					['email']
				],
				'in' => [
					['language', $languages],
					['template', $templates]
				]
			];

		$labels = array(
				'username' 	=> T_('Username'),
				'password0' => T_('Password'),
				'password1' => T_('Confirmation Password'),
				'email'		=> T_('Email'),
				'language' 	=> T_('Language'),
				'template'  => T_('Template')
			);

		$v->rules( $rules );
		$v->labels( $labels );
		$v->validate();

		$errors = $v->errors();

		// Apply =======================================================================

		if (empty($errors))
		{
			// Database update

			$db_data['username']			= $form['username'];
			$db_data['password']			= Core_AuthService::getHash($form['password0']);
			$db_data['email']				= $form['email'];
			$db_data['lang']				= $form['language'];

			if ( !empty($firstname) ) {
				$db_data['firstname'] = $firstname;
			}
			if ( !empty($lastname) ) {
				$db_data['lastname'] = $lastname;
			}
			if ( $template != 'bootstrap.min.css' ) {
				$db_data['template'] = $template;
			}

			$authService = Core_AuthService::getAuthService();
			$uid = Core_AuthService::getSessionInfo('ID');

			foreach ($db_data as $key => $value) {

				try {
					$sth = $dbh->prepare( "	UPDATE " . DB_PREFIX . "user
											SET " . $key . " = :" . $key . "
											WHERE user_id = '" . $uid . "';" );

					$sth->bindParam( ':' . $key, $value );
					$sth->execute();
				}
				catch (PDOException $e) {
					echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
					die();
				}
			}

			// Reload Session
			$authService->rmSession();

			$authService->setSession(
				$uid,
				$db_data['username'],
				$db_data['firstname'],
				$db_data['lastname'],
				$db_data['lang'],
				$db_data['template']
				);

			$authService->setSessionPerms( );

			$this->rmCookie( 'LANG' );
		}

		// return a response and log ===================================================

		$logger = self::getLogger();

		// response if there are errors
		if (!empty($errors)) {

			// if there are items in our errors array, return those errors
			$data['success'] = false;
			$data['errors']  = $errors;

			$data['msgType'] = 'warning';
			$data['msg'] = T_('Bad Settings!');

			$logger->info('Failed to update user configuration.');
		} else {

			$data['success'] = true;

			$logger->info('Updated user configuration.');
		}
		
		// return all our data to an AJAX call
		return $data;
	}

	private function rmCookie( $cookie ) {
		setcookie($cookie, '', time() - 3600, BASE_URL);
	}
}
