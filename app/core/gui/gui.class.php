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



class Core_GUI
{

	// This Module Settings
	private $module_name = '';
	private $module_title = '';
	private $module_icon = '';

	// This Module Dependencies
	private $module_dependencies = array();

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
		if ( !empty($bgp_module) && is_object($bgp_module) && is_subclass_of($bgp_module, 'Core_Abstract_Module') ) {

			$this->module_name = $bgp_module::getModuleName( );
			$this->module_title = $bgp_module::getModuleSetting( 'title' );
			$this->module_icon = $bgp_module::getModuleSetting( 'icon' );
			$this->module_dependencies = $bgp_module::getModuleDependencies( );

			// Get parent module properties if this module is a subpage of a module
			if ( is_subclass_of($bgp_module, $bgp_module::getModuleClassName() ) ) {

				$parentClassName = $bgp_module::getModuleClassName();
				$parentModule = new $parentClassName();

				$this->parent_module_title = $parentModule::getModuleSetting( 'title' );
				$this->parent_module_href = $parentModule::getModuleSetting( 'href' );

				unset($parentModule);
			}

			// Sets the module options
			if ( !empty($bgp_module::$module_definition['module_options']) ) {

				$empty_navbar = $bgp_module::getModuleOption( 'empty_navbar' );
				if ( !empty($empty_navbar) ) {

					$this->empty_navbar = boolval( $bgp_module::getModuleOption( 'empty_navbar' ) );
				}

				$no_sidebar = $bgp_module::getModuleOption( 'no_sidebar' );
				if ( !empty($no_sidebar) ) {

					$this->no_sidebar = boolval( $bgp_module::getModuleOption( 'no_sidebar' ) );
				}
			}
		}
		else {

			trigger_error("Core_GUI -> Missing module !", E_USER_ERROR);
		}
	}



    public static function setModulePageTitle( $page = '' ) {

        $title = self::$module_name;

        if (!empty($page)) {
            $module_pages = Core_Abstract_Module::getModulePages();

            if (!empty($module_pages)) {
                $module_pages = $module_pages['page'];

                if (isset($module_pages[0])) {
                    foreach ($module_pages as $key => $value) {
                        if ($value['name'] == $page) {
                            $title = $value['description'];
                            break;
                        }
                    }
                }
                else {
                    $title = $module_pages['description'];
                }
            }
        }

        self::$module_definition['module_settings']['title'] = $title;
    }


	/**
	 * Retrieves from the manifest file
	 * required CSS stylesheets
	 *
	 * @param none
	 * @return String
	 * @access private
	 */
	private function getCSSDepends() {

		if ( !empty($this->module_dependencies) && !empty($this->module_dependencies['stylesheets']) ) {
			foreach ($this->module_dependencies['stylesheets'] as $depend)
			{
//------------------------------------------------------------------------------------------------------------+
?>
			<!-- <?php echo $depend['comment']; ?> -->
			<link href="<?php echo $depend['href']; ?>" rel="stylesheet">
<?php
//------------------------------------------------------------------------------------------------------------+
			}
		}
	}



	/**
	 * Retrieves from the manifest file
	 * required JS scripts
	 *
	 * @param none
	 * @return String
	 * @access private
	 */
	private function getJSDepends() {

		if ( !empty($this->module_dependencies) && !empty($this->module_dependencies['javascripts']) ) {
			foreach ($this->module_dependencies['javascripts'] as $depend)
			{
//------------------------------------------------------------------------------------------------------------+
?>
			<!-- <?php echo $depend['comment']; ?> -->
			<script src="<?php echo $depend['src']; ?>"></script>
<?php
//------------------------------------------------------------------------------------------------------------+
			}
		}
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

			<!-- TOP HEADER NAVBAR -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">Bright Game Panel V2</a>
			</div>
			<!-- END: TOP HEADER NAVBAR -->

<?php

		// Breadcrumbs
		echo $this->getNavBarBreadcrumbs();

?>

			<!-- TOP NAVBAR -->
			<ul class="nav navbar-top-links navbar-right">
<?php

		if (!$this->empty_navbar)
		{
//------------------------------------------------------------------------------------------------------------+
?>
<?php

			// Flags
			echo $this->getNavBarFlags();

?>

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
						<li class="divider"></li>
						<li>
							<a href="./myaccount"><i class="fa fa-gear fa-fw"></i>&nbsp;<?php echo T_('Settings'); ?></a>
						</li>
					</ul>
				</li>

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
					<li><a href="#"><span class="fa fa-home fa-fw"></span><?php echo T_('Home'); ?></a></li>
<?php
//------------------------------------------------------------------------------------------------------------+

			if (!empty($this->parent_module_title)) {
//------------------------------------------------------------------------------------------------------------+
?>
					<li><a href="<?php echo $this->parent_module_href; ?>"><?php echo htmlspecialchars( $this->parent_module_title, ENT_QUOTES ); ?></a></li>
					<li class="active"><a><?php echo htmlspecialchars( $this->module_title, ENT_QUOTES ); ?></a></li>
<?php
//------------------------------------------------------------------------------------------------------------+
			}
			else {
//------------------------------------------------------------------------------------------------------------+
?>
					<li class="active"><a><?php echo htmlspecialchars( $this->module_title, ENT_QUOTES ); ?></a></li>
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
	 * Display Flags
	 *
	 * @param none
	 * @return String
	 * @access private
	 */
	private function getNavBarFlags()
	{

//------------------------------------------------------------------------------------------------------------+
?>
				<!-- Flags -->
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
<?php
//------------------------------------------------------------------------------------------------------------+

		if ( boolval(BGP_MAINTENANCE_MODE) === TRUE ) {
//------------------------------------------------------------------------------------------------------------+
?>
						<i class="fa fa-flag fa-fw faa-float animated"></i>  <i class="fa fa-caret-down"></i>
<?php
//------------------------------------------------------------------------------------------------------------+
		}
		else {
//------------------------------------------------------------------------------------------------------------+
?>
						<i class="fa fa-flag-o fa-fw"></i>  <i class="fa fa-caret-down"></i>
<?php
//------------------------------------------------------------------------------------------------------------+
		}

//------------------------------------------------------------------------------------------------------------+
?>
					</a>
					<ul class="dropdown-menu dropdown-alerts" role="menu">
						<li role="presentation" class="dropdown-header"><?php echo T_('System Alerts'); ?></li>
						<li class="divider"></li>
<?php
//------------------------------------------------------------------------------------------------------------+

		if ( boolval(BGP_MAINTENANCE_MODE) === TRUE ) {
//------------------------------------------------------------------------------------------------------------+
?>
						<li>
							<a>
								<div>
									<i class="fa fa-exclamation-triangle fa-fw"></i>&nbsp;Maintenance Mode
									<span class="pull-right text-muted small">Enabled</span>
								</div>
							</a>
						</li>
<?php
//------------------------------------------------------------------------------------------------------------+
		}
		else {
//------------------------------------------------------------------------------------------------------------+
?>
						<li>
							<a>
								<div>
									<i class="fa fa-smile-o fa-fw"></i>&nbsp;Nothing to report
									<span class="pull-right text-muted small"></span>
								</div>
							</a>
						</li>
<?php
//------------------------------------------------------------------------------------------------------------+
		}

//------------------------------------------------------------------------------------------------------------+
?>
					</ul>
				</li>
				<!-- END: Flags -->
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
						<li id="side-title"><?php echo T_('Menu'); ?></li>
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
			</div>
			<!-- END: SIDEBAR -->

<?php
//------------------------------------------------------------------------------------------------------------+
	}



	/**
	 * Display Module Tabs (Navigation)
	 *
	 * @param String $activeTab
	 * @return String
	 * @access public
	 */
	public function getTabs( $activeTab = '' ) {
		$tabs = $this->getTabsItems();

		if (!empty($tabs))
		{
//------------------------------------------------------------------------------------------------------------+
?>
					<!-- TABS -->
					<ul class="nav nav-tabs" role="tablist">
<?php
//------------------------------------------------------------------------------------------------------------+

			foreach ($tabs as $key => $tab)
			{

//------------------------------------------------------------------------------------------------------------+
?>
						<li role="presentation" <?php

				if ($key == $activeTab) {
					echo "class=\"active\"";
				}

				?>><a <?php

				if ($key != $activeTab) {
					echo "href=\"" . $tab['href'] . "\"";
				}

				?>><i class="<?php echo $tab['icon']; ?>"></i>&nbsp;<?php echo ucfirst( T_( $key ) ); ?></a></li>
<?php
//------------------------------------------------------------------------------------------------------------+

			}

//------------------------------------------------------------------------------------------------------------+
?>
					</ul>
					<!-- END: TABS -->

<?php
//------------------------------------------------------------------------------------------------------------+
		}
	}



	/**
	 * Parse GUI Manifest Files As XML Obj
	 * For Each Modules
	 * 
	 * @param none
	 * @return array
	 * @access private
	 */
	private function parseGUIManifestFiles ()
	{
		$manifestFiles = array();
		
		$handle = opendir( MODS_DIR );

		if ($handle) {
		
			// Foreach modules
			while (false !== ($entry = readdir($handle))) {
		
				// Dump specific directories
				if ($entry != "." && $entry != "..") {
		
					$module = $entry;

					// Get the manifest
					$manifest = MODS_DIR . '/' . $module . '/gui.manifest.xml';
	
					if (is_file( $manifest )) {
						$manifestFiles[] = simplexml_load_file( $manifest );
					}
				}
			}
		
			closedir($handle);
		}

		return $manifestFiles;
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
		$manifestFiles = $this->parseGUIManifestFiles();

		if (!empty($manifestFiles)) {

			$items = array();

			// XML Object to Array

			foreach( $manifestFiles as $manifest )
			{
				if (!empty($manifest->{'module_sidebar'}))
				{

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
	 * Get Tabs Items
	 *
	 * @param none
	 * @return array
	 * @access private
	 */
	private function getTabsItems()
	{
		$items = array();

		// Get the manifest
		$manifest = MODS_DIR . '/' . $this->module_name . '/gui.manifest.xml';
		
		if (is_file( $manifest ))
		{
			$manifest = simplexml_load_file( $manifest );

			if (!empty($manifest->{'module_tabs'}))
			{
				$tabs = $manifest->{'module_tabs'};
				
				foreach ($tabs as $tab) {

					foreach ($tab as $key => $value) {

						$items[ $key ][ 'href' ] = (string)$manifest->{'module_tabs'}->$key->href;
						$items[ $key ][ 'icon' ] = (string)$manifest->{'module_tabs'}->$key->icon;
					}
				}
			}
		}

		return $items;
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

					<a href="#" class="go-top"><i class="fa fa-chevron-circle-up fa-fw"></i></a>

					<!-- FOOTER -->
					<footer>
						<div class="pull-left">
							Copyleft <img id="footer-copyleft-logo" height="12" src="./gui/img/copyleft.png"> 2015. Released under the <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPLv3</a>.<br />
							All images are copyrighted by their respective owners.
						</div>

						<div class="pull-right text-right">
							<a href="http://www.bgpanel.net/" target="_blank">Bright Game Panel</a>&nbsp;V2
							<br />
							<a href="https://github.com/warhawk3407/bgpanelv2/" target="_blank"><i class="fa fa-github fa-fw"></i></a>&nbsp;
							<a href="https://twitter.com/BrightGamePanel/" target="_blank"><i class="fa fa-twitter fa-fw"></i></a>&nbsp;
							<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=7SDPVBR9EMQZS" target="_blank"><i class="fa fa-paypal fa-fw"></i></a>&nbsp;
						</div>
					</footer>
					<!-- END: FOOTER -->

				</div>
				<!-- END: MAIN -->
			</div>
			<!-- END: ROW -->
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
