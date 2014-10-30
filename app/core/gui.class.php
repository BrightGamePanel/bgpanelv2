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

	// This Module Settings
	private $module_title = '';
	private $module_icon = '';

	// Parent Module Settings
	private $parent_module_title = '';
	private $parent_module_href = '';

	// Module Options
	private $empty_navbar 	= FALSE;
	private $no_sidebar		= FALSE;


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
			$this->module_title = $bgp_module::getModuleSetting( 'title' );
			$this->module_icon = $bgp_module::getModuleSetting( 'icon' );

			// Get parent module properties if this module is a subpage of a module
			if ( is_subclass_of($bgp_module, $bgp_module::$module_definition['class_definition']['@attributes']['classname'] ) ) {

				// Hack
				$parentModule = new $bgp_module::$module_definition['class_definition']['@attributes']['classname'](); // Ugly, but it works ;-)

				$this->parent_module_title = $parentModule::getModuleSetting( 'title' );
				$this->parent_module_href = $parentModule::getModuleSetting( 'href' );

				unset($parentModule);
			}

			// Sets the module options
			if ( !empty($bgp_module::$module_definition['module_options']) ) {

				if ( !empty($bgp_module::getModuleOption( 'empty_navbar' )) ) {

					$this->empty_navbar = boolval( $bgp_module::getModuleOption( 'empty_navbar' ) );
				}

				if ( !empty($bgp_module::getModuleOption( 'no_sidebar' )) ) {

					$this->no_sidebar = boolval( $bgp_module::getModuleOption( 'no_sidebar' ) );
				}
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
<html ng-app="bgpApp" lang="<?php

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

		<base href="<?php echo BASE_URL; ?>">

		<!-- Javascript -->
			<script src="./gui/angularjs/js/angular.min.js"></script>
			<script src="./gui/jquery/js/jquery-2.1.1.min.js"></script>
			<script src="./gui/bootstrap3/js/bootstrap.min.js"></script>
			<!-- Metis Menu Plugin -->
    		<script src="./gui/metisMenu/js/metisMenu.min.js"></script>
			<!-- SB Admin 2 -->
			<script src="./gui/bootstrap3/js/sb-admin-2.js"></script>
		<!-- Style -->
			<!-- Bootstrap 3 -->
			<link href="./gui/bootstrap3/css/<?php echo htmlspecialchars( Core_GUI::getBS3Template(), ENT_QUOTES ); ?>" rel="stylesheet">
			<!-- MetisMenu -->
			<link href="./gui/metisMenu/css/metisMenu.min.css" rel="stylesheet">
			<!-- SB Admin 2 -->
			<link href="./gui/bootstrap3/css/dashboard.css" rel="stylesheet">
			<!-- Font Awesome 4 -->
			<link href="./gui/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<!-- Favicon -->
			<link rel="icon" href="./gui/img/favicon.ico">
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body ng-controller="bgpController">
	<div id="wrapper">

		<!-- NAVIGATION -->
		<nav class="navbar navbar-default navbar-static-top" role="navigation">
<?php
//------------------------------------------------------------------------------------------------------------+

		// Display Navigation Bar
		echo $this->getNavBar();

//------------------------------------------------------------------------------------------------------------+
?>

<?php
//------------------------------------------------------------------------------------------------------------+

		// Display Sidebar
		if (!$this->no_sidebar) {
			echo $this->getSideBar();
		}

//------------------------------------------------------------------------------------------------------------+
?>
		</nav>
		<!-- END: NAVIGATION -->

		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="row">
				<!-- MAIN -->
				<div class="col-lg-12">
<?php
//------------------------------------------------------------------------------------------------------------+

		// Page Header
		// Title

		if (!empty($this->parent_module_title)) {
//------------------------------------------------------------------------------------------------------------+
?>
					<h1 class="page-header">
						<i class="<?php echo htmlspecialchars( $this->module_icon, ENT_QUOTES ); ?>"></i>
						<?php echo htmlspecialchars( $this->parent_module_title, ENT_QUOTES ); ?>
						<i class="fa fa-angle-right"></i>
						<small><?php echo htmlspecialchars( $this->module_title, ENT_QUOTES ); ?></small>
					</h1>
<?php
//------------------------------------------------------------------------------------------------------------+
		}
		else {
//------------------------------------------------------------------------------------------------------------+
?>
					<h1 class="page-header"><i class="<?php echo htmlspecialchars( $this->module_icon, ENT_QUOTES ); ?>"></i>&nbsp;<?php echo htmlspecialchars( $this->module_title, ENT_QUOTES ); ?></h1>
<?php
//------------------------------------------------------------------------------------------------------------+
		}

//------------------------------------------------------------------------------------------------------------+
?>
					<!-- ALERTS -->
					<div id="message" class="alert alert-dismissible" role="alert" ng-show="msg" ng-class="'alert-' + msgType">
						<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<strong ng-bind="msg"></strong>
					</div>
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
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">BrightGamePanel V2</a>
				</div>

<?php

		// Breadcrumbs
		echo $this->getNavBarBreadcrumbs();

?>

				<ul class="nav navbar-top-links navbar-right">
<?php

		if (!$this->empty_navbar)
		{
//------------------------------------------------------------------------------------------------------------+
?>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-bell fa-fw"></i>  <i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu dropdown-alerts" role="menu">
							<li>
								<a href="#">
									<div>
										<i class="fa fa-comment fa-fw"></i> New Comment
										<span class="pull-right text-muted small">4 minutes ago</span>
									</div>
								</a>
							</li>
							<li class="divider"></li>
							<li>
								<a href="#">
									<div>
										<i class="fa fa-twitter fa-fw"></i> 3 New Followers
										<span class="pull-right text-muted small">12 minutes ago</span>
									</div>
								</a>
							</li>
							<li class="divider"></li>
							<li>
								<a class="text-center" href="#">
									<strong>See All Alerts</strong>
									<i class="fa fa-angle-right"></i>
								</a>
							</li>
						</ul>
						<!-- /.dropdown-alerts -->
					</li>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu dropdown-user" role="menu">
							<li role="presentation" class="dropdown-header"><?php
								echo htmlspecialchars(
									$_SESSION['INFORMATION']['firstname'] .
									' ' .
									$_SESSION['INFORMATION']['lastname'] .
									' @' .
									$_SESSION['USERNAME']
									, ENT_QUOTES );
							?></li>
							<li>
								<a href="./myaccount"><i class="fa fa-gear fa-fw"></i>&nbsp;<?php echo T_('Settings'); ?></a>
							</li>
						</ul>
						<!-- /.dropdown-user -->
					</li>
	                <!-- /.dropdown -->
	                <li>
	                	<a href="./logout"><i class="fa fa-sign-out fa-fw"></i></a>
	                </li>
<?php
//------------------------------------------------------------------------------------------------------------+
		}

?>
				</ul>
				<!-- END: TOP NAVBAR -->
<?php
//------------------------------------------------------------------------------------------------------------+
	}



	/**
	 * Display Navbar Breadcrumbs
	 *
	 * @param none
	 * @return String
	 * @access private
	 */
	private function getNavBarBreadcrumbs()
	{

//------------------------------------------------------------------------------------------------------------+
?>
				<!-- Breadcrumbs -->
				<div class="nav navbar-left">
<?php

		if (!$this->empty_navbar)
		{
//------------------------------------------------------------------------------------------------------------+
?>
					<ol class="navbar-breadcrumbs">
						<li class="active"><span class="glyphicon glyphicon-home"></span>&nbsp;<?php echo T_('Home'); ?></li>
<?php
//------------------------------------------------------------------------------------------------------------+

			if (!empty($this->parent_module_title)) {
//------------------------------------------------------------------------------------------------------------+
?>
						<li><a href="<?php echo $this->parent_module_href; ?>"><?php echo htmlspecialchars( $this->parent_module_title, ENT_QUOTES ); ?></a></li>
						<li class="active"><?php echo htmlspecialchars( $this->module_title, ENT_QUOTES ); ?></li>
<?php
//------------------------------------------------------------------------------------------------------------+
			}
			else {
//------------------------------------------------------------------------------------------------------------+
?>
						<li class="active"><?php echo htmlspecialchars( $this->module_title, ENT_QUOTES ); ?></li>
<?php
//------------------------------------------------------------------------------------------------------------+
			}

//------------------------------------------------------------------------------------------------------------+
?>
					</ol>
<?php
//------------------------------------------------------------------------------------------------------------+
		}

?>
				</div>
				<!-- END: Breadcrumbs -->
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
		$items = $this->getSideBarItems();

//------------------------------------------------------------------------------------------------------------+
?>
				<!-- SIDEBAR -->
				<div class="navbar-default sidebar" role="navigation">
					<div class="sidebar-nav navbar-collapse">
						<ul class="nav" id="side-menu">
<?php
//------------------------------------------------------------------------------------------------------------+

		$i = 0; // First index
		$last_index = 99; // Last key

		while ( $i <= $last_index )
		{
			if (!array_key_exists($i, $items)) {
				$i++;
				continue;
			}

			$txt 		= key($items[$i]);

			$href 		= $items[$i][$txt]['href'];
			$icon 		= $items[$i][$txt]['icon'];
			$sub_menu 	= $items[$i][$txt]['sub_menu'];

			// Translate
			$txt_t 		= T_( $txt );

			if (empty($sub_menu))
			{
//------------------------------------------------------------------------------------------------------------+
?>
							<li>
								<a <?php if ($this->module_title == $txt) echo 'class="active"'; ?> href="<?php echo $href; ?>"><i class="<?php echo $icon; ?>"></i>&nbsp;<?php echo $txt_t; ?></a>
<?php
//------------------------------------------------------------------------------------------------------------+
			}
			else
			{
//------------------------------------------------------------------------------------------------------------+
?>
							<li id="<?php echo $txt; ?>" class="">
								<a href="<?php echo $href; ?>"><i class="<?php echo $icon; ?>"></i>&nbsp;<?php echo $txt_t; ?><i class="fa arrow"></i></a>
								<ul class="nav nav-second-level">
<?php
//------------------------------------------------------------------------------------------------------------+

				$j = 0;
				foreach ($sub_menu as $menu_key => $menu)
				{

					// Add separator after the first iteration
					if ($j != 0)
					{
//------------------------------------------------------------------------------------------------------------+
?>
									<li class="divider"></li>
<?php
//------------------------------------------------------------------------------------------------------------+
					}

					// Format
					$menu_key = ucfirst($menu_key);

					// Translate
					$menu_key = T_( $menu_key );

//------------------------------------------------------------------------------------------------------------+
?>
									<li class="sidebar-header"><?php echo $menu_key; ?></li>
<?php
//------------------------------------------------------------------------------------------------------------+

					foreach ($menu as $menu_sub_key => $menu_sub_menu)
					{

						// Format
						$menu_sub_menu['txt'] = ucwords( str_replace( '-', ' ', $menu_sub_key ) );

						// Translate
						$menu_sub_menu['txt'] = T_( $menu_sub_menu['txt'] );

//------------------------------------------------------------------------------------------------------------+
?>
									<li><a <?php if ($this->module_title == $menu_sub_menu['txt']) echo 'class="active"'; ?> href="<?php echo $menu_sub_menu['href']; ?>"><i class="<?php echo $menu_sub_menu['icon']; ?>"></i>&nbsp;<?php echo $menu_sub_menu['txt']; ?></a></li>
<?php
//------------------------------------------------------------------------------------------------------------+

						// Toggle the parent menu item in JS
						if ($this->module_title == $menu_sub_menu['txt'])
						{
//------------------------------------------------------------------------------------------------------------+
?>
									<script>
										$('#<?php echo $txt; ?>').addClass('active');
									</script>
<?php
//------------------------------------------------------------------------------------------------------------+
						}
					}

					$j++;
				}

//------------------------------------------------------------------------------------------------------------+
?>
								</ul>
								<!-- /.nav-second-level -->
<?php
//------------------------------------------------------------------------------------------------------------+
			}

//------------------------------------------------------------------------------------------------------------+
?>
							</li>
<?php
//------------------------------------------------------------------------------------------------------------+

			$i++;
		}

//------------------------------------------------------------------------------------------------------------+
?>
						</ul>
					</div>
					<!-- /.sidebar-collapse -->
				</div>
				<!-- END: SIDEBAR -->

<?php
//------------------------------------------------------------------------------------------------------------+
	}



	/**
	 * Get Sidebar Items
	 *
	 * @param none
	 * @return array
	 * @access private
	 */
	private function getSideBarItems()
	{
		$privilege = Core_AuthService::getSessionPrivilege();
		$manifestFiles = array();

		// Read all "sidebar.gui.xml" files under the "app/modules" directory
		$handle = opendir( MODS_DIR );

		if ($handle) {

			// Foreach modules
			while (false !== ($entry = readdir($handle))) {

				// Dump specific directories
				if ($entry != "." && $entry != "..") {

					// Analyze module name
					$parts = explode('.', $entry);

					if (!empty( $parts[1] )) {
						$role = $parts[0];
						$module = $parts[1];
					}
					else {
						$role = NULL;
						$module = $parts[0];
					}

					// Case: "admin.module" OR "user.module"
					if (!empty($role) && $privilege == ucfirst($role)) {

						// Get the manifest
						$manifest = MODS_DIR . '/' . $role . '.' . $module . '/sidebar.gui.xml';

						if (is_file( $manifest )) {
							$manifestFiles[] = simplexml_load_file( $manifest ); // Store the object
						}
					}

					// Case: "module"
					else {

						// Get the manifest
						$manifest = MODS_DIR . '/' . $module . '/sidebar.gui.xml';

						if (is_file( $manifest )) {
							$manifestFiles[] = simplexml_load_file( $manifest );
						}
					}
				}
			}

			closedir($handle);
		}

		if (!empty($manifestFiles)) {

			$items = array();

			// XML Object to Array

			foreach( $manifestFiles as $manifest ) {

				$txt  = (string)$manifest->{'module_sidebar'}->txt;
				$rank = (int)$manifest->{'module_sidebar'}->rank;

				$item[$txt]['rank'] = $rank;
				$item[$txt]['href'] = (string)$manifest->{'module_sidebar'}->href;
				$item[$txt]['icon'] = (string)$manifest->{'module_sidebar'}->icon;

				// Processing sub-menu if any

				if ( !empty($manifest->{'module_sidebar'}->{'sub_menu'}) ) {
					
					$sub_menu = (array)$manifest->{'module_sidebar'}->{'sub_menu'};

					foreach ($sub_menu as $sub_menu_key => $sub_menu_item) {

						$sub_menu_item = (array)$sub_menu_item;

						foreach ($sub_menu_item as $sub_menu_item_href => $sub_menu_item_link) {

							$sub_menu_item_href = (string)$sub_menu_item_href;

							// Push to array

							$item[$txt]['sub_menu'][$sub_menu_key][$sub_menu_item_href]['href'] = (string)$sub_menu_item_link->{'href'};
							$item[$txt]['sub_menu'][$sub_menu_key][$sub_menu_item_href]['icon'] = (string)$sub_menu_item_link->{'icon'};
						}
					}
				}
				else {

					$item[$txt]['sub_menu'] = array();
				}

				$items = array_merge($items, $item); // Push
			}

			$sideBarItems = array();

			// Sort Array

			foreach ($items as $key => $item) {

				$rank = $item['rank'];
				unset($item['rank']);

				// Free key
				if (!isset($sideBarItems[$rank])) {

					$sideBarItems[$rank][$key] = $item; // Push
				}
				// Key not available
				else {

					$i = 1;
					while ( isset($sideBarItems[ $rank + $i ]) ) {
						$i++;
					}

					$sideBarItems[ $rank + $i ][$key] = $item; // Push
				}
			}

			// Return Array
			return $sideBarItems;
		}

		return array();
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
				</div>
				<!-- END: MAIN -->
			</div>
			<!-- END: ROW -->

			<hr>

			<!-- FOOTER -->
			<footer>
				<div class="pull-left">
					Copyleft - 2014. Released Under <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPLv3</a>.<br />
					All images are copyrighted by their respective owners.
				</div>

				<div class="pull-right text-right">
					<a href="http://www.bgpanel.net/" target="_blank">Bright Game Panel</a>&nbsp;V2
					<br />
					<a href="https://github.com/warhawk3407/bgpanelv2/" target="_blank"><i class="fa fa-github fa-fw"></i></a>&nbsp;
					<a href="https://twitter.com/BrightGamePanel/" target="_blank"><i class="fa fa-twitter fa-fw"></i></a>&nbsp;
					<a href="http://getbootstrap.com/" target="_blank">
						<img height="14" src="data:image/png;base64,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">
					</a>
				</div>
			</footer>
			<!-- END: FOOTER -->

		</div>
		<!-- /#page-wrapper -->
	</div>
	<!-- /#wrapper -->

	<!-- Powered By Bright Game Panel -->

	</body>
</html>
<?php
//------------------------------------------------------------------------------------------------------------+
	}

}
