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

require( MODS_DIR . '/' . basename(__DIR__) . '/box.class.php' );

$module = new BGP_Module_Box_View( 'view' );

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
 * Get Resource ID
 */
if (Flight::has('REQUEST_RESOURCE_ID')) {
	$resId = Flight::get('REQUEST_RESOURCE_ID');
	Flight::clear('REQUEST_RESOURCE_ID');
} else {
	// Bad Request
	Flight::redirect( '/400' );
}

// ===========================================================================================================

/**
 * Fetch Resource Model
 */

$dbh = Core_DBH::getDBH(); // Get Database Handle

$sth = $dbh->prepare("
	SELECT
		box.box_id,
		os.operating_system,
		box.name,
		box.notes,
		box.steam_lib_path
	FROM box AS box
	JOIN os AS os
		ON box.os_id = os.os_id
	WHERE
		box.box_id = :resId
	;");

$sth->bindParam( ':resId', $resId );

$sth->execute();

$resModel = $sth->fetchAll( PDO::FETCH_ASSOC );
$resModel = $resModel[0];


/**
 * PAGE BODY
 */
//------------------------------------------------------------------------------------------------------------+
?>
					<!-- CONTENTS -->
					<div class="row">
						<div class="col-md-8 col-md-offset-2">

						</div>
					</div>
					<!-- END: CONTENTS -->

					<!-- SCRIPT -->
<?php

/**
 * Generate AngularJS Code
 *
 * @param 	String 	$task
 * @param 	String 	$schema
 * @param 	String 	$form
 * @param 	String 	$model
 * @param 	String 	$redirect
 */

$js->getAngularCode();

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

?>
