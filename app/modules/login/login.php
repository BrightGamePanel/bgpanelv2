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
 * Load Plugin
 */

require( MODS_DIR . '/' . basename(__DIR__) . '/login.class.php' );

$module = new BGP_Module_Login();

/**
 * Call GUI Builder
 */
$gui = new Core_GUI( $module );

/**
 * Javascript Generator
 */
$js = new Core_GUI_JS( $module );

/**
 * Build Page Header
 */
$gui->getHeader();

/**
 * PAGE BODY
 */
//------------------------------------------------------------------------------------------------------------+

// Call security component
$authService = Core_AuthService::getAuthService();

if ( $authService->isBanned() ) {
?>
					<!-- BAN MSG -->
					<div id="banmsg" class="alert alert-warning" role="alert">
						<strong><?php echo T_('Too many incorrect login attempts'); ?></strong>
						<?php echo T_('Please wait'); echo ' ' . CONF_SEC_BAN_DURATION . ' '; echo T_('seconds before trying again.'); ?>
					</div>
					<!-- END: BAN MSG -->
<?php
}

?>
					<!-- CONTENTS -->
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="panel panel-default">
								<div class="panel-heading">
									<img src="./gui/img/logo.png" alt="Bright Game Panel Logo" class="img-responsive center-block">
								</div>

								<div class="panel-body">
									<legend><?php echo T_('Sign In'); ?></legend>

									<ul class="pager">
										<li>
											<a href="./login/password"><?php echo T_('Forgot Password?'); ?></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- END: CONTENTS -->

					<!-- SCRIPT -->
<?php

/**
 * Generate AngularJS Code
 * @arg $task
 * @arg $inputs
 * @arg $redirect
 */

if ( isset($_COOKIE['USERNAME']) ) {
	$fields = array(
			'username' => htmlspecialchars($_COOKIE['USERNAME'], ENT_QUOTES),
			'password'
		);
}
else {
	$fields = array(
			'username',
			'password'
		);
}

// Redirect
if (!empty($_GET['page'])) {
	$return = '.' . $_GET['page'];
}
else {
	$return = './';
}

$js->getAngularCode( 'authenticateUser', $fields, $return );

?>
					<!-- END: SCRIPT -->

<?php
//------------------------------------------------------------------------------------------------------------+
/**
 * END: PAGE BODY
 */

/**
 * Build Page Footer
 */
$gui->getFooter();

// Clean Up
unset( $module, $gui, $js );

?>