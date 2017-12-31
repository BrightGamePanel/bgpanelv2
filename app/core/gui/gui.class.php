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
}
