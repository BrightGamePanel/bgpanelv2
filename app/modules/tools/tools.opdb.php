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

require( MODS_DIR . '/' . basename(__DIR__) . '/tools.class.php' );

$module = new BGP_Module_Tools_Opdb( 'opdb' );

/**
 * Call GUI Builder
 */
$gui = new Core_GUI( $module );

/**
 * Javascript Generator
 */
$js = new Core_JS_GUI( $module );

/**
 * Build Page Header
 */
$gui->getHeader();


// Analyze DB

$dbh = Core_DBH::getDBH(); // Get Database Handle

$tables = array();
$analysis = array();
$i = 0;

$result = $dbh->query( "SHOW TABLES" );
$tables[] = $result->fetchAll( PDO::FETCH_NUM );
$tables = $tables[0];

if (!empty($tables)) {
	foreach ($tables as $table)
	{
		$table = $table[0];

		if (strstr($table, DB_PREFIX)) {
			$sth = $dbh->prepare("ANALYZE TABLE " . $table . ";");

			$sth->execute();

			$fetched = $sth->fetchAll( PDO::FETCH_ASSOC );

			$analysis[$i] = $fetched[0];
			$i++;
		}
	}
}


/**
 * PAGE BODY
 */
//------------------------------------------------------------------------------------------------------------+
?>
					<!-- CONTENTS -->
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class="panel panel-default">

								<div class="panel-heading">
									<h3 class="panel-title"><?php echo T_('Database Optimization Tool'); ?></h3>
								</div>

								<div class="panel-body">
									<div class="alert alert-info" role="alert">
										<strong><?php echo T_('Tip'); ?></strong><br />
										<?php echo T_('This operation tells the MySQL server to clean up the database tables, optimizing them for better performance.'); ?><br />
										<?php echo T_('It is recommended that you run this at least once a month.'); ?>
									</div>

									<div class="well" style="max-width: 400px; margin: 0 auto 10px; padding-left: 35px; padding-right: 35px;">
										<form ng-submit="processForm()">
											<div class="row">
												<div class="text-center">
													<button class="btn btn-primary btn-lg btn-block" type="submit"><?php echo T_('Optimize!'); ?></button>
												</div>
											</div>
										</form>
									</div>

									<div class="table-responsive">
										<table class="table table-striped table-bordered table-hover" id="dbAnalysis">
											<thead>
												<tr>
													<th><?php echo T_('Table'); ?></th>
													<th><?php echo T_('Operation'); ?></th>
													<th><?php echo T_('Msg_Type'); ?></th>
													<th><?php echo T_('Message'); ?></th>
												</tr>
											</thead>
											<tbody>
<?php
//------------------------------------------------------------------------------------------------------------+

foreach($analysis as $key => $value)
{
?>
												<tr>
													<td><?php echo $value['Table']; ?></td>
													<td><?php echo $value['Op']; ?></td>
													<td><?php echo $value['Msg_type']; ?></td>
													<td><?php echo $value['Msg_text']; ?></td>
												</tr>
<?php
}
unset($analysis);

//------------------------------------------------------------------------------------------------------------+
?>
											</tbody>
										</table>
									</div>

									<script>
									$(document).ready(function(){
										$('#dbAnalysis').DataTable({
											"paging": false,
											"searching": false,
											"columnDefs": [
												{ "orderable": false, "targets": [1,2] }
											]
										});
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
 * @arg $task
 * @arg $inputs
 * @arg $redirect
 */

$js->getAngularController( 'optimizeDB', array(), './tools/opdb' );

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