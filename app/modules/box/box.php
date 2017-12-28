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

$module = new Core_Abstract_Module_Box();

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


// DB
$dbh = Core_DBH::getDBH(); // Get Database Handle

$rows = array();

$sth = $dbh->prepare("
	SELECT
		box.box_id,
		box.name,
		ip.ip
	FROM box AS box
	JOIN box_ip AS ip
		ON box.box_id = ip.box_id
	WHERE
		ip.is_default = '1'
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
								<a class="btn btn-primary btn-lg btn-block" href="./box/add">
									<i class="fa fa-plus"></i>&nbsp;<?php echo T_('Add A New Box'); ?>
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
													<th><?php echo T_('Machine Name'); ?></th>
													<th><?php echo T_('Default IP Address'); ?></th>
													<th><?php echo T_('Network Status'); ?></th>
													<th><?php echo T_('Bandwidth Usage'); ?></th>
													<th><?php echo T_('CPU'); ?></th>
													<th><?php echo T_('RAM'); ?></th>
													<th><?php echo T_('Load Average'); ?> (15 min)</th>
													<th><?php echo T_('HDD'); ?></th>
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
													<td><?php echo htmlspecialchars( $value['name'], ENT_QUOTES); ?></td>
													<td><?php echo htmlspecialchars( $value['ip'], ENT_QUOTES); ?></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td>
														<div class="text-center">
															<a class="btn btn-default" href="./box/edit/<?php echo htmlspecialchars( $value['box_id'], ENT_QUOTES); ?>">
																<i class="fa fa-edit"></i>
															</a>&nbsp;
															<a class="btn btn-primary" href="./box/view/<?php echo htmlspecialchars( $value['box_id'], ENT_QUOTES); ?>">
																<i class="fa fa-info-circle"></i>
															</a>&nbsp;
															<a class="btn btn-danger" href="./box/del/<?php echo htmlspecialchars( $value['box_id'], ENT_QUOTES); ?>">
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
										$('#overview').DataTable({
											"columnDefs": [
												{ "orderable": false, "targets": [4,5,6,7,8] }
											]
										});
									});
									</script>

								</div>
							</div>
						</div>
					</div>

					<div class="well">
						<?php echo T_('Last Update'); ?> : <span class="label label-default"><?php echo bgp_format_date(BGP_LAST_CRON_RUN); ?></span>
<?php

if ( bgp_format_date(BGP_LAST_CRON_RUN) == 'Never' ) {
?>
						<br /><?php echo T_('Setup the cron job to enable box monitoring!'); ?>
<?php
}

?>
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
