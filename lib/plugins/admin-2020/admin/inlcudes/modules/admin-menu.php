<?php
if (!defined('ABSPATH')) {
    exit();
}

class Admin_2020_module_admin_menu
{
    public function __construct($version, $path, $utilities)
    {
        $this->version = $version;
        $this->path = $path;
        $this->utils = $utilities;
    }

    /**
     * Loads menu actions
     * @since 1.0
     */

    public function start()
    {
		///REGISTER THIS COMPONENT
		add_filter('admin2020_register_component', array($this,'register'));
		
		if(!$this->utils->enabled($this)){
			return;
		}
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		
		if($this->utils->is_locked($optionname)){
			return;
		}
		
        add_action('admin_enqueue_scripts', [$this, 'add_styles'], 0);
		add_action('admin_enqueue_scripts', [$this, 'add_scripts'], 0);
		add_action('admin_enqueue_scripts', [$this, 'remove_styles'], 99999);
		add_filter('parent_file', array( $this, 'build_admin_menu'),999);
		add_action('adminmenu', array( $this, 'output_admin_menu' ));
		add_filter('admin_body_class', array($this, 'add_body_classes'));
		
		
    }
	
	/**
	* Output body classes
	* @since 1 
	*/
	
	public function add_body_classes($classes) {
		
		$bodyclass = " a2020_admin_menu";
		
		return $classes.$bodyclass;
	}
	

	/**
	 * Register admin bar component
	 * @since 1.4
	 * @variable $components (array) array of registered admin 2020 components
	 */
	public function register($components){
		
		array_push($components,$this);
		return $components;
		
	}
	
	/**
	 * Returns component info for settings page
	 * @since 1.4
	 */
	public function component_info(){
		
		$data = array();
		$data['title'] = __('Menu','admin2020');
		$data['option_name'] = 'admin2020_admin_menu';
		$data['description'] = __('Creates new admin menu.','admin2020');
		return $data;
		
	}
	/**
	 * Returns settings for module
	 * @since 1.4
	 */
	 public function render_settings(){
		  
		wp_enqueue_media();
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		
		$light_background = $this->utils->get_option($optionname,'light-background');
		$dark_background = $this->utils->get_option($optionname,'dark-background');
		$search_enabled = $this->utils->get_option($optionname,'search-enabled');
		$shrunk_enabled = $this->utils->get_option($optionname,'shrunk-enabled');
		///GET POST TYPES
		$args = array('public'   => true);
		$output = 'objects'; 
		$post_types = get_post_types( $args, $output );
		
		$disabled_for = $this->utils->get_option($optionname,'disabled-for');
		if($disabled_for == ""){
			$disabled_for = array();
		}
		///GET ROLES
		global $wp_roles;
		///GET USERS
		$blogusers = get_users();
		
		?>
		<div class="uk-grid" id="a2020_menu_settings" uk-grid>
		  
		<!-- BACKGROUND COLOUR -->
		<div class="uk-width-1-1@ uk-width-1-3@m">
		  <div class="uk-h5 "><?php _e('Background Color','admin2020')?></div>
		  <div class="uk-text-meta"><?php _e("Sets a background colour for the admin menu.",'admin2020') ?></div>
		</div>
		<div class="uk-width-1-1@ uk-width-1-3@m">
		  <div class="uk-h5"><?php _e('Light','admin2020')?></div>
		  
		  <input class=" a2020_setting" id="light-background" 
		  module-name="<?php echo $optionname?>" 
		  name="light-background" 
		  type="text"
		  data-default-color="#fff"
		  value="<?php echo $light_background?>">
		  
		</div>	
		
		<script>
		  jQuery(document).ready(function($){
			  $('#a2020_menu_settings #light-background').wpColorPicker();
		  });
		</script>
		
		<div class="uk-width-1-1@ uk-width-1-3@m">
		  <div class="uk-h5"><?php _e('Dark','admin2020')?></div>
		  
		  <input class="a2020_setting" id="dark-background" 
		  module-name="<?php echo $optionname?>" 
		  name="dark-background" 
		  type="text"
		  data-default-color="#111"
		  value="<?php echo $dark_background?>">
		  
		</div>	
		
		<script>
		  jQuery(document).ready(function($){
			  $('#a2020_menu_settings #dark-background').wpColorPicker();
		  });
		</script>
		
		<div class="uk-width-1-1">
		  <hr >
		</div>
		
		<!-- LOCKED FOR USERS / ROLES -->
		<div class="uk-width-1-1@ uk-width-1-3@m">
		  <div class="uk-h5 "><?php _e('Menu Disabled for','admin2020')?></div>
		  <div class="uk-text-meta"><?php _e("Admin 2020 menu will be disabled for any users or roles you select",'admin2020') ?></div>
		</div>
		<div class="uk-width-1-1@ uk-width-1-3@m">
		  
		  
		  <select class="a2020_setting" id="a2020-role-types" name="disabled-for" module-name="<?php echo $optionname?>" multiple>
			  
			<?php
			$sel = '';
			
			if(in_array('Super Admin', $disabled_for)){
			$sel = 'selected';
			}
			?>  
			<option value="Super Admin" <?php echo $sel?>><?php _e('Super Admin','admin2020') ?></option>
			<?php
			foreach ($wp_roles->roles as $role){
			  $rolename = $role['name'];
			  $sel = '';
			  
			  if(in_array($rolename, $disabled_for)){
				  $sel = 'selected';
			  }
			  ?>
			  <option value="<?php echo $rolename ?>" <?php echo $sel?>><?php echo $rolename ?></option>
			  <?php
			}
			  
			foreach ($blogusers as $user){
				$username = $user->display_name;
				$sel = '';
				
				if(in_array($username, $disabled_for)){
					$sel = 'selected';
				}
				?>
				<option value="<?php echo $username ?>" <?php echo $sel?>><?php echo $username ?></option>
				<?php
			}
			?>
		  </select>
		  
		  <script>
			  jQuery('#a2020_menu_settings #a2020-role-types').tokenize2({
				  placeholder: '<?php _e('Select roles or users','admin2020') ?>'
			  });
			  jQuery(document).ready(function ($) {
				  $('#a2020_menu_settings #a2020-role-types').on('tokenize:select', function(container){
					  $(this).tokenize2().trigger('tokenize:search', [$(this).tokenize2().input.val()]);
				  });
			  })
		  </script>
		  
		</div>
		<div class="uk-width-1-1@ uk-width-1-3@m">
		</div>
		
		<!-- DISABLE MENU SEARCH -->
		<div class="uk-width-1-1@ uk-width-1-3@m">
		  <div class="uk-h5 "><?php _e('Disable Search','admin2020')?></div>
		  <div class="uk-text-meta"><?php _e("Disables admin menu search",'admin2020') ?></div>
		</div>
		<div class="uk-width-1-1@ uk-width-2-3@m">
		  
		  <?php
		  $checked = '';
		  if($search_enabled == 'true'){
			  $checked = 'checked';
		  }
		  ?>
		  
		  <label class="admin2020_switch uk-margin-left">
			  <input class="a2020_setting" name="search-enabled" module-name="<?php echo $optionname?>" type="checkbox" <?php echo $checked ?>>
			  <span class="admin2020_slider constant_dark"></span>
		  </label>
		  
		</div>	
		
		<!-- COLLAPSED MENU -->
		<div class="uk-width-1-1@ uk-width-1-3@m">
		  <div class="uk-h5 "><?php _e('Set collapsed menu as default','admin2020')?></div>
		  <div class="uk-text-meta"><?php _e("If enabled, the menu will default to the shrunk menu for users that haven't set a preference.",'admin2020') ?></div>
		</div>
		<div class="uk-width-1-1@ uk-width-2-3@m">
		  
		  <?php
		  $checked = '';
		  if($shrunk_enabled == 'true'){
			  $checked = 'checked';
		  }
		  ?>
		  
		  <label class="admin2020_switch uk-margin-left">
			  <input class="a2020_setting" name="shrunk-enabled" module-name="<?php echo $optionname?>" type="checkbox" <?php echo $checked ?>>
			  <span class="admin2020_slider constant_dark"></span>
		  </label>
		  
		</div>	
		  
		  	
		</div>	
		
		<?php
	  }
    /**
     * Adds admin bar styles
     * @since 1.0
     */

    public function add_styles()
    {
		
		if(is_rtl()){
			
			//RTL MENU STYLEWS
			wp_register_style('admin2020_admin_menu',$this->path . 'assets/css/modules/admin-menu-rtl.css',array(), $this->version);
			wp_enqueue_style('admin2020_admin_menu');
			
		} else {
			
			//MENU STYLES
			wp_register_style('admin2020_admin_menu',$this->path . 'assets/css/modules/admin-menu.css',array(), $this->version);
			wp_enqueue_style('admin2020_admin_menu');
			
		}
    }
	
	/**
	* Enqueue Admin Bar 2020 scripts
	* @since 1.4
	*/
	
	public function add_scripts(){
	  
	  ///MENU FRAMEWORK
	  wp_enqueue_script('admin-menu-js', $this->path . 'assets/js/admin2020/admin-menu.min.js', array('jquery'));
	  wp_localize_script('admin-menu-js', 'admin2020_admin_menu_ajax', array(
		  'ajax_url' => admin_url('admin-ajax.php'),
		  'security' => wp_create_nonce('admin2020-admin-menu-security-nonce'),
	  ));
	  
	}
	
	/**
	* Removes wp default menu styling
	* @since 1.4
	*/
	
	public function remove_styles(){
		
		wp_dequeue_style('admin-menu');
		wp_deregister_style('admin-menu');
		wp_register_style(
			'admin-menu',
			$this->path . 'assets/css/modules/blank.css',
			array(),
			$this->version
		);
		wp_enqueue_style('admin-menu');
		
	}
	/**
	* Scans admin directory for menu links
	* @since 1.4
	*/
	public function get_admin_files(){
		
		$absolutepath = ABSPATH . '/wp-admin'."/";
		$files = array_diff(scandir($absolutepath), array('.', '..'));

		if (is_multisite()){
		  $pathtonetwork = ABSPATH . '/wp-admin'."/network/";
		  $networkfiles = array_diff(scandir($pathtonetwork), array('.', '..'));
		  $files = array_merge($files,$networkfiles);
		}
		
		return $files;
		
	}
	
	/**
	* Builds new admin menu
	* @since 1.4
	*/
	
	public function build_admin_menu($parent_file){
		
		global $menu, $pagenow,$admin2020_menu;
		$this->original_menu = $menu;
		//disable default menu
		$menu = array();
		
		$darkmode = $this->utils->get_user_preference('darkmode');
		$dark_enabled = $this->utils->get_option('admin2020_admin_bar','dark-enabled');
			
		$class = '';
		
		if($darkmode == 'true'){
			$class= 'uk-light';
		} else if ($darkmode == '' && $dark_enabled == 'true'){
			$class = " uk-light";
		}
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		$light_background = $this->utils->get_option($optionname,'light-background');
		$dark_background = $this->utils->get_option($optionname,'dark-background');
		$search_enabled = $this->utils->get_option($optionname,'search-enabled');
		
		
		
		
		ob_start();
		
		if($light_background != ""){
			
			$light_without_hex = str_replace('#', '', $light_background);
			$hexRGB = $light_without_hex;
			if(hexdec(substr($hexRGB,0,2))+hexdec(substr($hexRGB,2,2))+hexdec(substr($hexRGB,4,2))< 381){
				$class = " uk-light";
			}
			
			?>
			<style type="text/css">
			.admin2020_menu {background:<?php echo $light_background?>;}
			</style>
			<?php
		}
		if($dark_background != ""){
			
			$light_without_hex = str_replace('#', '', $dark_background);
			$hexRGB = $light_without_hex;
			if(hexdec(substr($hexRGB,0,2))+hexdec(substr($hexRGB,2,2))+hexdec(substr($hexRGB,4,2))> 381){
				$class = "";
			}
			?>
			<style type="text/css">
			.a2020_night_mode .admin2020_menu {background:<?php echo $dark_background?>;}
			</style>
			<?php
		}
		?>
		
		<div class="admin2020_menu a2020_dark_anchor uk-background-default uk-height-1-1 uk-padding-small <?php echo $class?> uk-padding-remove-horizontal">
			
			<span class="uk-icon-button uk-position-small uk-position-top-right uk-hidden@m" id="close_mobile_nav" style="margin-top:5px" uk-icon="close"></span>
			
			<ul class="uk-nav-default uk-nav-parent-icon uk-margin-top" uk-nav>
				
				<?php if ($search_enabled != 'true') { ?>
				<li class="a2020_menu_searcher_wrap">
					<div class="uk-inline uk-width-1-1 a2020_menu_search" style="margin-bottom: 30px;">
						<span class="uk-form-icon" uk-icon="icon: search;ratio:0.8"></span>
						<input class="uk-input uk-form-small" id="a2020_menu_searcher" placeholder="<?php _e('Search','admin2020') ?>">
					</div>
				</li>
				<?php } ?>
				
				<?php $this->handle_top_level_menu_items($this->original_menu);?>
				
				
			</ul>
			
		</div>	
		
		
		<?php
		
		$admin2020_menu = ob_get_clean();
		
		return $parent_file;
		
	}
	
	
	/**
	* Loops through top level menu items
	* @since 1.4
	*/
	public function handle_top_level_menu_items($the_menu){
		
		global $submenu;
		$this->original_submenu = $submenu;
		
		foreach ($the_menu as $menu_item){
			
			$menu_name = $menu_item[0];
			$menu_link = $menu_item[2];
			$divider = false;
			
			if (strpos($menu_link,"separator") !== false){
				$divider = true;
				$this->handle_divider($menu_item);
				continue;
			}
			
			if(!$menu_name){
				continue;
			}
			
			if(isset($submenu[$menu_link])){
				$sub_menu_items = $submenu[$menu_link];
			} else {
				$sub_menu_items = false;
			}
			
			$link = $this->get_menu_link($menu_item);
			
			$classes = $this->get_menu_clases($menu_item,$submenu);
			
			?>
			
			<li class="<?php echo $classes?>" id="<?php echo $menu_item[5]?>">
				<a class="menu-icon-generic <?php echo $classes ?>" href="<?php echo $link?>">
					
					<?php $this->get_icon($menu_item) ?>
					
					<span class="a2020-menu-title wp-menu-name"><?php echo $menu_name?></span>
					
				</a>
				
				<?php
				if(is_array($sub_menu_items)){
					$this->handle_sub_level_menu_items($sub_menu_items);
				}
				?>
				
			</li>
			
			<?php
		}
		
	}
	
	/**
	* Gets correct link for menu item
	* @since 1.4
	*/
	
	public function get_menu_link($menu_item){
		
		$menu_link = $menu_item[2];
		$files = $this->get_admin_files();
		$this->files = $files;
		
		if (strpos($menu_link, 'admin.php') !== false) {
			$link = $menu_link;
		} 
		else if (strpos($menu_link, '.php') !== false) {
			
			$link = $menu_link;
			if (strpos($menu_link, '/') !== false) {
				$pieces = explode("/", $menu_link);
				if (strpos($pieces[0], '.php') !== true || !file_exists(get_admin_url().$menu_link)) {
					$link = 'admin.php?page=' . $menu_link;
				}
			}
		
			$querypieces = explode("?", $link);
			$temp = $querypieces[0];
			
			if( !in_array( $temp ,$files )){
				$link = 'admin.php?page=' . $menu_link;
			}
		
		}  else {
			
			$link = 'admin.php?page=' . $menu_link;
		
		}
		
		if (strpos($menu_link, "/wp-content/") !== false) {
			
			$link = 'admin.php?page=' . $menu_link;
			
		}
		
		//CHECK IF INTERNAL URL
		if (strpos($menu_link, get_site_url()) !== false) {
			
			$link = $menu_link;
			
		}
		
		///CHECK IF EXTERNAL LINK
		if(strpos($menu_link, 'https://') !== false || strpos($menu_link, 'http://') !== false) {
			
			$link = $menu_link;
			
		}
		
		///UPDRAFT PLUS WORKAROUND
		if($link == 'admin.php?page=updraftplus'){
			
			$link = 'options-general.php?page=updraftplus';
			
		}
		
		return $link;
		
	}
	
	/**
	* Gets correct classes for top level menu item
	* @since 1.4
	*/
	
	public function get_menu_clases($menu_item,$sub_menu){
		
		$menu_link = $menu_item[2];
		$classes = $menu_item[4];
		
		if(isset($sub_menu[$menu_link])){
			$classes = $classes . ' wp-has-submenu uk-parent ';
			$classes = $classes . ' ' . $this->check_if_active($menu_item,$sub_menu[$menu_link]);
		} else {
			$classes = $classes . ' ' . $this->check_if_single_active($menu_item);
		}
		
		return $classes;
		
	}
	
	/**
	* Checks if we are on an active link or sub link
	* @since 1.4
	*/
	
	public function check_if_active($menu_item,$sub_menu){
		
		if(!is_array($sub_menu)){
			return "";
		}
		
		global $pagenow;
		
		$currentquery = $_SERVER['QUERY_STRING'];
		if ($currentquery) {
			$currentquery = '?' . $currentquery;
		}
		$wholestring = $pagenow . $currentquery;
		$visibility = 'hidden';
		$open = 'wp-not-current-submenu';
		$files = $this->files;
		
		foreach ($sub_menu as $sub) {
			if (strpos($sub[2], '.php') !== false) {
				$link = $sub[2];

				$querypieces = explode("?", $link);
				$temp = $querypieces[0];

				if( !in_array( $temp ,$files )){
					$link = 'admin.php?page=' . $sub[2];
				}
				
			} else {
				$link = 'admin.php?page=' . $sub[2];
			}

			$linkclass = '';
			if ($wholestring == $link) {
				$linkclass = "wp-has-current-submenu wp-menu-open";
				$open = 'uk-active uk-open wp-menu-open wp-has-current-submenu';
				$visibility = '';
				break;
			}
		}
		
		return $open;
		
	}
	
	/**
	* Checks if we are on an active link or sub link
	* @since 1.4
	*/
	
	public function check_if_single_active($sub_menu_item){
		
		global $pagenow;
		
		$currentquery = $_SERVER['QUERY_STRING'];
		if ($currentquery) {
			$currentquery = '?' . $currentquery;
		}
		$wholestring = $pagenow . $currentquery;
		$visibility = 'hidden';
		$open = 'wp-not-current-submenu';
		$files = $this->files;
		
		if (strpos($sub_menu_item[2], '.php') !== false) {
			$link = $sub_menu_item[2];
	
			$querypieces = explode("?", $link);
			$temp = $querypieces[0];
	
			if( !in_array( $temp ,$files )){
				$link = 'admin.php?page=' . $sub_menu_item[2];
			}
			
		} else {
			$link = 'admin.php?page=' . $sub_menu_item[2];
		}
	
		$linkclass = '';
		if ($wholestring == $link) {
			$linkclass = "uk-active current";
		}
		
		
		return $linkclass;
	
	}
	
	/**
	* Builds nav dividers
	* @since 1.4
	*/
	
	public function handle_divider($divider){
		
		
		if(isset($divider['name'])){
			
			?>
			
			<li class="uk-nav-header uk-text-bold uk-margin-small-bottom" style="text-transform: none"><?php echo $divider['name'] ?></li>
			<li class="uk-nav-divider divider-placeholder"></li>
			
			<?php
			
		} else {
			?>
			
			<li class="uk-nav-divider"></li>
			
			<?php
		}
		
	}
	
	/**
	* Gets top level menu item icon
	* @since 1.4
	*/
	
	public function get_icon($menu_item){
		
		/// LIST OF AVAILABLE MENU ICONS
		$icons = array('dashicons-dashboard' => 'grid',
		'dashicons-admin-post' => 'file-text',
		'dashicons-database' => 'database',
		'dashicons-admin-media' => 'image',
		'dashicons-admin-page' => 'album',
		'dashicons-admin-comments' => 'comment',
		'dashicons-admin-appearance' => 'paint-bucket',
		'dashicons-admin-plugins' => 'bolt',
		'dashicons-admin-users' => 'users',
		'dashicons-admin-tools' => 'cog',
		'dashicons-chart-bar' => 'thumbnails',
		'dashicons-admin-settings' => 'settings');
		
		// SET MENU ICON
		$theicon = '';
		$wpicon = $menu_item[6];
		
		if(isset($menu_item['icon'])){
			if($menu_item['icon'] != "" ){
				
				?><span class="uk-icon-button" uk-icon="icon:<?php echo $menu_item['icon'] ?>;ratio:0.8"></span><?php 
				return;
			}
		}

		if(isset($icons[$wpicon])){
		
			//ICON IS SET BY ADIM 2020		
			?><span class="uk-icon-button" uk-icon="icon:<?php echo $icons[$wpicon] ?>;ratio:0.8"></span><?php 
			return;
			
		}

		if (!$theicon) {
			if (strpos($wpicon, 'http') !== false || strpos($wpicon, 'data:') !== false) {
				
				///ICON IS IMAGE 
				?><span class="uk-icon uk-icon-image uk-icon-button" style="background-image: url(<?php echo $wpicon ?>);"></span><?php
				
			} else {
				
				///ICON IS ::BEFORE ELEMENT
				?><div class="wp-menu-image dashicons-before <?php echo $wpicon ?> uk-icon uk-icon-image uk-icon-button"></div><?php
				
			}
		}
		
	}
	
	/**
	* Loops through sub menu items
	* @since 1.4
	*/
	
	public function handle_sub_level_menu_items($sub_menu){
		
		?>
		
		<ul class="uk-nav-sub wp-submenu wp-submenu-wrap">
			
		<?php
		foreach ($sub_menu as $sub_item){
			
			$sub_menu_name = $sub_item[0];
			$sub_menu_link = $sub_item[2];
			$link = $this->get_menu_link($sub_item);
			$class = $this->check_if_single_active($sub_item)
			
			?>
			<li class="<?php echo $class?>">
				<a class="<?php echo $class?>" href="<?php echo $link ?>"><?php echo $sub_menu_name ?></a>
			</li>
			<?php
			
		}
		?>
		
		</ul>
		
		<?php
		
	}
	
	/**
	* Outputs Admin menu
	* @since 1.4
	*/
	
	public function output_admin_menu(){
		
		global $admin2020_menu,$menu,$submenu;
		echo $admin2020_menu;
		$menu = $this->original_menu;
		$submenu = $this->original_submenu;
		
	}
	
	
}
