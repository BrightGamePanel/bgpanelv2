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

require( MODS_DIR . '/' . basename(__DIR__) . '/user.class.php' );

$module = new Core_Abstract_Module_User();

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
 * Build Page Tabs
 */
$gui->getTabs( 'roles' );


// DB
$dbh = Core_DBH::getDBH(); // Get Database Handle

$rows = array();

$sth = $dbh->prepare("
	SELECT
		ID,
		Title,
		Description
	FROM roles
	ORDER BY Title
	;");

$sth->execute();

$rows = $sth->fetchAll( PDO::FETCH_ASSOC );


/**
 * PAGE BODY
 */
//------------------------------------------------------------------------------------------------------------+
?>
					<!-- CONTENTS -->

					<div style="max-width: 400px; margin: 0 auto 10px; padding-left: 35px; padding-right: 35px;">
						<div class="row">
							<div class="text-center">
								<a class="btn btn-primary btn-lg btn-block" href="./user/role/add">
									<i class="fa fa-plus"></i>&nbsp;<?php echo T_('Add A New Role'); ?>
								</a>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-default">

								<div class="panel-heading">
									<h3 class="panel-title"><?php echo T_('Overview'); ?></h3>
								</div>

								<div class="panel-body">

									<div class="table-responsive">
										<table class="table table-striped table-bordered table-hover" id="overview">
											<thead>
												<tr>
													<th><?php echo T_('Title'); ?></th>
													<th><?php echo T_('Description'); ?></th>
													<th></th>
												</tr>
											</thead>
											<tbody>
<?php
//------------------------------------------------------------------------------------------------------------+

foreach($rows as $key => $value)
{
?>
												<tr>
													<td><?php echo htmlspecialchars( $value['Title'], ENT_QUOTES); ?></td>
													<td><?php echo htmlspecialchars( $value['Description'], ENT_QUOTES); ?></td>
													<td>
														<div class="text-center">
															<a class="btn btn-danger" href="./user/role/del/<?php echo htmlspecialchars( $value['ID'], ENT_QUOTES); ?>">
																<i class="fa fa-trash"></i>
															</a>
														</div>
													</td>
												</tr>
<?php
}
unset($rows);

//------------------------------------------------------------------------------------------------------------+
?>
											</tbody>
										</table>
									</div>

									<script>
									$(document).ready(function(){
										$('#overview').DataTable();
									});
									</script>

								</div>
							</div>
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
