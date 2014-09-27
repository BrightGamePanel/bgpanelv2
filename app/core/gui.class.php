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



class Core_GUI
{
	// Module Title
	private $module_title = '';

	// Module Options
	private $empty_navbar;
	private $no_sidebar;


	/**
	 * Default Constructor
	 *
	 * @param Object $bgp_module
	 * @return void
	 * @access public
	 */
	function __construct( $bgp_module )
	{
		if ( !empty($bgp_module) && is_object($bgp_module) && is_subclass_of($bgp_module, 'BGP_Module') ) {
			$this->module_title = $bgp_module->module_definition['module_settings']['title'];

			if ( !empty($bgp_module->module_definition['module_options']) ) {
				$this->empty_navbar = (!empty($bgp_module->module_definition['module_options']['empty_navbar'])) ? boolval($bgp_module->module_definition['module_options']['empty_navbar']) : FALSE;
				$this->no_sidebar = (!empty($bgp_module->module_definition['module_options']['no_sidebar'])) ? boolval($bgp_module->module_definition['module_options']['no_sidebar']) : FALSE;
			}
		}
		else {
			trigger_error("Core_GUI -> Missing module !", E_USER_ERROR);
		}
	}



	/**
	 * Get BGP Bootstrap 3 Template Filename
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public static function getBS3Template()
	{
		if ( !empty($_SESSION['TEMPLATE']) ) {
			return $_SESSION['TEMPLATE'];
		}
		else {
			switch (Core_AuthService::getSessionPrivilege()) {
				case 'Admin':
					return BGP_ADMIN_TEMPLATE;

				case 'User':
					return BGP_USER_TEMPLATE;
				
				default:
					return 'bootstrap.min.css';
			}
		}
	}



	/**
	 * Display Module Header
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public function getHeader()
	{
//------------------------------------------------------------------------------------------------------------+
?>
<!DOCTYPE html>
<html ng-app="" lang="<?php

	// Language
	if ( isset($_SESSION['LANG']) ) {
		echo htmlspecialchars( substr($_SESSION['LANG'], 0, 2), ENT_QUOTES );
	} else {
		echo htmlspecialchars( substr(CONF_DEFAULT_LOCALE, 0, 2), ENT_QUOTES );
	}

			?>">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- Powered By Bright Game Panel -->

		<title><?php

		// Tab Title
		echo htmlspecialchars( $this->module_title . ' - ' . BGP_PANEL_NAME, ENT_QUOTES );

		?></title>

		<!-- Javascript -->
			<script src="./gui/angularjs/js/angular.min.js"></script>
			<script src="./gui/jquery/js/jquery-2.1.1.min.js"></script>
			<script src="./gui/bootstrap3/js/bootstrap.min.js"></script>
		<!-- Style -->
			<!-- Bootstrap 3 -->
			<link href="./gui/bootstrap3/css/<?php echo htmlspecialchars( Core_GUI::getBS3Template(), ENT_QUOTES ); ?>" rel="stylesheet">
			<link href="./gui/bootstrap3/css/dashboard.css" rel="stylesheet">
		<!-- Favicon -->
			<link rel="icon" href="./gui/img/favicon.ico">
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body ng-controller="bgpController">
<?php
//------------------------------------------------------------------------------------------------------------+

		// Display Navigation Bar
		echo $this->getNavBar();

//------------------------------------------------------------------------------------------------------------+
?>

		<!-- BODY -->
		<div class="container-fluid">
			<div class="row">
<?php
//------------------------------------------------------------------------------------------------------------+

		// Display Sidebar
		if (!$this->no_sidebar) {
			echo $this->getSideBar();
		}

//------------------------------------------------------------------------------------------------------------+
?>
				<!-- MAIN -->
<?php
//------------------------------------------------------------------------------------------------------------+

		// Fix Body Position
		if (!$this->no_sidebar) {
			// With Sidebar
?>
				<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<?php
		}
		else {
			// No Sidebar
?>
				<div class="col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 main">
<?php
		}

//------------------------------------------------------------------------------------------------------------+
?>
					<h1 class="page-header"><?php echo htmlspecialchars( $this->module_title, ENT_QUOTES ); ?></h1>

					<!-- ALERTS -->
					<div id="messages" ng-show="messages" ng-bind="messages"></div>
					<!-- END: ALERTS -->

<?php
//------------------------------------------------------------------------------------------------------------+
	}



	/**
	 * Display Module Navigation Bar
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public function getNavBar()
	{
//------------------------------------------------------------------------------------------------------------+
?>
		<!-- TOP NAVBAR -->
		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">BrightGamePanel V2</a>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
<?php

		if (!$this->empty_navbar)
		{
//------------------------------------------------------------------------------------------------------------+
?>
						<li><a href="#">Dashboard</a></li>
						<li><a href="#">Settings</a></li>
						<li><a href="#">Profile</a></li>
						<li><a href="#">Help</a></li>
<?php
//------------------------------------------------------------------------------------------------------------+
		}

?>
					</ul>
				</div>
			</div>
		</div>
		<!-- END: TOP NAVBAR -->
<?php
//------------------------------------------------------------------------------------------------------------+
	}



	/**
	 * Display Module Sidebar
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public function getSideBar()
	{
//------------------------------------------------------------------------------------------------------------+
?>
				<!-- SIDEBAR -->
				<div class="col-sm-3 col-md-2 sidebar">
					<ul class="nav nav-sidebar">
						<li class="active"><a href="#">Overview</a></li>
						<li><a href="#">Reports</a></li>
						<li><a href="#">Analytics</a></li>
						<li><a href="#">Export</a></li>
					</ul>
					<ul class="nav nav-sidebar">
						<li><a href="">Nav item</a></li>
						<li><a href="">Nav item again</a></li>
						<li><a href="">One more nav</a></li>
						<li><a href="">Another nav item</a></li>
						<li><a href="">More navigation</a></li>
					</ul>
					<ul class="nav nav-sidebar">
						<li><a href="">Nav item again</a></li>
						<li><a href="">One more nav</a></li>
						<li><a href="">Another nav item</a></li>
					</ul>
				</div>
				<!-- END: SIDEBAR -->

<?php
//------------------------------------------------------------------------------------------------------------+
	}



	/**
	 * Display Module Footer
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public function getFooter()
	{
//------------------------------------------------------------------------------------------------------------+
?>
					<hr>

					<!-- FOOTER -->
					<footer>
						<div class="pull-left">
							Copyleft - 2014. Released Under <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPLv3</a>.<br />
							All images are copyrighted by their respective owners.
						</div>
						<div class="pull-right" style="text-align: right;">
							<a href="http://www.bgpanel.net/" target="_blank">Bright Game Panel</a> V2<br />
							Built with <a href="http://getbootstrap.com/" target="_blank">Bootstrap 3</a>.
						</div>
					</footer>
					<!-- END: FOOTER -->
				</div>
				<!-- END: MAIN -->
			</div>
			<!-- END: ROW -->
		</div>
		<!-- END: BODY -->

		<!-- Powered By Bright Game Panel -->

	</body>
</html>
<?php
//------------------------------------------------------------------------------------------------------------+
	}

}
