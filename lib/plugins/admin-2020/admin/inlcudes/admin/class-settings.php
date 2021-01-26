<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_settings {
	
	public function __construct($version, $path, $utilities, $plugin_name) {
	
		$this->version = $version;
		$this->path = $path;
		$this->utils = $utilities;
		$this->plugin_name = $plugin_name;
	
	}
	
	/**
	 * Loads Admin 2020 settings on init
	 * @since 1.4
	 */
	
	public function start(){
		
		add_action('plugins_loaded', array( $this, 'load_modules' ));
		add_action('plugins_loaded', array( $this, 'upgrade_legacy_settings' ));
		add_action('admin_init', array( $this, 'add_settings' ),0);
		add_action('admin_menu', array( $this, 'add_menu_items'));
		add_action('admin_head', array( $this, 'admin_color_scheme'));
		add_action('network_admin_menu', array( $this, 'add_menu_items_network'));
		add_action('admin_enqueue_scripts', array( $this, 'add_scripts' ),0);
		add_filter( 'plugin_row_meta' , array($this,'add_settings_link'),10,2 );
		
		
		add_action('wp_ajax_a2020_save_modules', array($this,'a2020_save_modules'));
		add_action('wp_ajax_a2020_save_settings', array($this,'a2020_save_settings'));
		add_action('wp_ajax_a2020_save_analytics', array($this,'a2020_save_analytics'));
		add_action('wp_ajax_a2020_remove_analytics', array($this,'a2020_remove_analytics'));
		add_action('wp_ajax_a2020_remove_licence', array($this,'a2020_remove_licence'));
		add_action('wp_ajax_a2020_save_videos', array($this,'a2020_save_videos'));
		add_action('wp_ajax_a2020_delete_video', array($this,'a2020_delete_video'));
		add_action('wp_ajax_a2020_export_settings', array($this,'a2020_export_settings'));
		add_action('wp_ajax_a2020_import_settings', array($this,'a2020_import_settings'));
		
	}
	
	/**
	* Removes default wp color schema
	* @since 1.4
	*/
	public function admin_color_scheme() {
		global $_wp_admin_css_colors;
		$_wp_admin_css_colors = array();
		
		//MAKE SURE WE HAVE DEFAULT COLOR SCHEME
		$args = array(
			'ID' => get_current_user_id(),
			'admin_color' => 'default'
		);
		wp_update_user( $args );
	}
	
	/**
	* Adds link to admin 2020 settings page
	* @since 1.4
	*/
	public function add_settings_link( $plugin_meta, $plugin_file_name ) {
	
		if ($plugin_file_name == "admin-2020/admin-2020.php"){
		  $plugin_meta[] = '<a href="admin.php?page=admin2020-settings">'.__('Settings','admin2020').'</a>';
		  
		  if(!$this->utils->is_premium()){
			  $plugin_meta[] = '<a href="https://admintwentytwenty.com/pricing/" target="_BLANK" class="uk-text-success uk-text-bold">'.__('Upgrade to Pro','admin2020').'</a>';
		  }
		  
		}
		return $plugin_meta;
	}
	
	/**
	* Adds old admin 2020 settings and imports them to version 2
	* @since 1.4
	*/
	public function upgrade_legacy_settings(){
		
		if(is_network_admin()){
			$optionname = 'admin2020_settings_network';
		} else {
			$optionname = 'admin2020_settings';
		}
		
		$a2020_options = get_option($optionname);
		
		if(isset($a2020_options['upgraded'])){
			return;
		}
		///LIGHT LOGO
		if(isset($a2020_options['admin2020_image_field_0'])){
			if($a2020_options['admin2020_image_field_0'] != ""){
				$logo = $a2020_options['admin2020_image_field_0'];
				$a2020_options['modules']['admin2020_admin_bar']['light-logo'] = $logo;
				unset($a2020_options['admin2020_image_field_0']);
			}
		}
		///DARK LOGO
		if(isset($a2020_options['admin2020_image_field_dark'])){
			if($a2020_options['admin2020_image_field_dark'] != ""){
				$logo = $a2020_options['admin2020_image_field_dark'];
				$a2020_options['modules']['admin2020_admin_bar']['dark-logo'] = $logo;
				unset($a2020_options['admin2020_image_field_dark']);
			}
		}
		///LOGIN IMAGE
		if(isset($a2020_options['admin2020_login_background'])){
			if($a2020_options['admin2020_login_background'] != ""){
				$image = $a2020_options['admin2020_login_background'];
				$a2020_options['modules']['admin2020_admin_login']['login-background'] = $image;
				unset($a2020_options['admin2020_login_background']);
			}
		}
		///CUSTOM CSS
		if(isset($a2020_options['admin2020_custom_css'])){
			if($a2020_options['admin2020_custom_css'] != ""){
				$image = $a2020_options['admin2020_custom_css'];
				$a2020_options['modules']['admin2020_admin_advanced']['custom-css'] = $image;
				unset($a2020_options['admin2020_custom_css']);
			}
		}
		///CUSTOM JS
		if(isset($a2020_options['admin2020_custom_js'])){
			if($a2020_options['admin2020_custom_js'] != ""){
				$image = $a2020_options['admin2020_custom_js'];
				$a2020_options['modules']['admin2020_admin_advanced']['custom-js'] = $image;
				unset($a2020_options['admin2020_custom_js']);
			}
		}
		///LICENCE
		if(isset($a2020_options['admin2020_pluginPage_licence_key'])){
			if($a2020_options['admin2020_pluginPage_licence_key'] != ""){
				$image = $a2020_options['admin2020_pluginPage_licence_key'];
				$a2020_options['modules']['activation']['key'] = $image;
				unset($a2020_options['admin2020_pluginPage_licence_key']);
			}
		}
		
		$a2020_options['upgraded'] = true;  
		
		if(is_array($a2020_options)){
		   update_option($optionname,$a2020_options);
		}
		
	}
	
	
	
	/**
	* Enqueue Admin 2020 settings scripts
	* @since 1.4
	*/
	
	public function add_scripts(){
		
		if(isset($_GET['page'])) {
			
			if($_GET['page'] == 'admin2020-settings'){
		  
				wp_enqueue_script('admin2020-settings', $this->path . 'assets/js/admin2020/settings.min.js', array('jquery'));
				wp_localize_script('admin2020-settings', 'admin2020_settings_ajax', array(
					'ajax_url' => admin_url('admin-ajax.php'),
					'security' => wp_create_nonce('admin2020-settings-security-nonce'),
				));
				
				wp_enqueue_script('admin-bar-settings-js', $this->path . 'assets/js/admin2020/admin-bar-settings.min.js', array('jquery'));
				
				//TOKENIZE2
				wp_enqueue_script('tokenize', $this->path . 'assets/js/tokenize/tokenize2.min.js', array('jquery'));
				wp_register_style('tokenize-css', $this->path . 'assets/css/tokenize/tokenize2.min.css', array());
				wp_enqueue_style('tokenize-css');
				
				///COLOR PICKER
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker');
				
			}
		}
	  
	}
	
	/**
	* Save modules from ajax
	* @since 1.4
	*/
	
	public function a2020_save_analytics(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-settings-security-nonce', 'security') > 0) {
			
			$view = $_POST['view'];
			$code = $_POST['code'];
			$modulename = $_POST['module'];
			
			$a2020_options = get_option( 'admin2020_settings' );
			
			if($view == "" || $code == "" || $modulename == ""){
				$message = __("Unable to connect account",'admin2020');
				echo $this->utils->ajax_error_message($message);
				die();
			}
			
			$a2020_options['modules'][$modulename]['view_id'] = $view;
			$a2020_options['modules'][$modulename]['refresh_token'] = $code;
			
			
			update_option( 'admin2020_settings', $a2020_options);
			
			$returndata = array();
			$returndata['success'] = true;
			$returndata['message'] = __('Analytics account connected','admin2020');
			echo json_encode($returndata);
			
			die();
			
			
		}
		die();	
		
	}
	
	
	
	/**
	* remove analytics from ajax
	* @since 1.4
	*/
	
	public function a2020_remove_analytics(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-settings-security-nonce', 'security') > 0) {
			
			$modulename = $this->utils->clean_ajax_input($_POST['module']);
			
			$a2020_options = get_option( 'admin2020_settings' );
			
			$a2020_options['modules'][$modulename]['view_id'] = "";
			$a2020_options['modules'][$modulename]['refresh_token'] = "";
			
			
			update_option( 'admin2020_settings', $a2020_options);
			
			$returndata = array();
			$returndata['success'] = true;
			$returndata['message'] = __('Analytics account removed','admin2020');
			echo json_encode($returndata);
			
			die();
			
			
		}
		die();	
		
	}
	
	/**
	* remove licence from ajax
	* @since 1.4
	*/
	
	public function a2020_remove_licence(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-settings-security-nonce', 'security') > 0) {
			
			$network = $this->utils->clean_ajax_input($_POST['network']);
			
			if($network === 'true'){
				$a2020_options = get_option( 'admin2020_settings_network' );
			} else {
				$a2020_options = get_option( 'admin2020_settings' );
			}
			
			$a2020_options['modules']['activation']['key'] = "";
			
			if($network === 'true'){
				update_option( 'admin2020_settings_network', $a2020_options);
			} else {
				update_option( 'admin2020_settings', $a2020_options);
			}
			
			$returndata = array();
			$returndata['success'] = true;
			$returndata['message'] = __('Licence removed','admin2020');
			echo json_encode($returndata);
			
			die();
			
			
		}
		die();	
		
	}
	
	/**
	* Save modules from ajax
	* @since 1.4
	*/
	
	public function a2020_save_modules(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-settings-security-nonce', 'security') > 0) {
			
			$options = $this->utils->clean_ajax_input($_POST['options']);
			$network = $this->utils->clean_ajax_input($_POST['network']);
			
			if($network === 'true'){
				$a2020_options = get_option( 'admin2020_settings_network');
			    $netty = 'this is a network';
			} else {
				$a2020_options = get_option( 'admin2020_settings');
				$netty = 'nope';
			}
			
			if($options == "" || !is_array($options)){
				$message = __("No options supplied to save",'admin2020');
				echo $this->utils->ajax_error_message($message);
				die();
			}
			
			foreach($options as $option){
				$option_name = $option[0];
				$option_value = $option[1];
				$a2020_options['modules'][$option_name]['status'] = $option_value;
			}
			
			if($network === 'true'){
				update_option( 'admin2020_settings_network', $a2020_options);
			} else {
				update_option( 'admin2020_settings', $a2020_options);
			}
			
			$returndata = array();
			$returndata['success'] = true;
			$returndata['message'] = __('Modules saved','admin2020');
			echo json_encode($returndata);
			
			die();
			
			
		}
		die();	
		
	}
	
	/**
	* Save admin 2020 settings from ajax
	* @since 1.4
	*/
	
	public function a2020_save_settings(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-settings-security-nonce', 'security') > 0) {
			
			$options = $this->utils->clean_ajax_input($_POST['options']);
			$network = $this->utils->clean_ajax_input($_POST['network']);
			
			if($network === 'true'){
				$a2020_options = get_option( 'admin2020_settings_network');
			} else {
				$a2020_options = get_option( 'admin2020_settings');
			}
			
			if($options == "" || !is_array($options)){
				$message = __("No options supplied to save",'admin2020');
				echo $this->utils->ajax_error_message($message);
				die();
			}
			
			foreach($options as $option){
				if(!is_array($option)){
					$option = $this->utils->clean_ajax_input($option);
				}
				$module_name = $option[0];
				$option_name = $option[1];
				$value = $option[2];
				$a2020_options['modules'][$module_name][$option_name] = $value;
			}
			
			if(is_array($a2020_options)){
				if($network === 'true'){
					update_option( 'admin2020_settings_network', $a2020_options);
				} else {
					update_option( 'admin2020_settings', $a2020_options);
				}
				$returndata = array();
				$returndata['success'] = true;
				$returndata['message'] = __('Settings saved. You may need to refresh for changes to take effect.','admin2020');
				echo json_encode($returndata);
			} else {
				$message = __("Something went wrong",'admin2020');
				echo $this->utils->ajax_error_message($message);
				die();
			}
			
			
			
			die();
			
			
		}
		die();	
		
	}
	
	
	/**
	* Export admin 2020 settings
	* @since 1.4
	*/
	
	public function a2020_export_settings(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-settings-security-nonce', 'security') > 0) {
			
			$a2020_options = get_option( 'admin2020_settings' );
			if(isset($a2020_options['modules']['activation']['key'])){
				$a2020_options['modules']['activation']['key'] = "";
			}
			if(isset($a2020_options['modules']['admin2020_google_analytics']['view_id'])){
				$a2020_options['modules']['admin2020_google_analytics']['view_id'] = "";
				$a2020_options['modules']['admin2020_google_analytics']['refresh_token'] = "";
			}
			echo json_encode($a2020_options);
			
			
		}
		die();	
		
	}
	
	/**
	* Import admin 2020 settings
	* @since 1.4
	*/
	
	public function a2020_import_settings(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-settings-security-nonce', 'security') > 0) {
			
			$new_options = $this->utils->clean_ajax_input($_POST['settings']); 
			
			if(is_array($new_options)){
				update_option( 'admin2020_settings', $new_options);
			}
			
			echo __("Settings Imported","admin2020");
			
			
		}
		die();	
		
	}
	
	/**
	* Saves custom user videos
	* @since 1.4
	*/
	
	public function a2020_save_videos(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-settings-security-nonce', 'security') > 0) {
			
			$video = $this->utils->clean_ajax_input($_POST['data']);
			$a2020_options = get_option( 'admin2020_settings' );
			
			if($video == "" || !is_array($video)){
				$message = __("No video supplied to save",'admin2020');
				echo $this->utils->ajax_error_message($message);
				die();
			}
			
			if(isset($a2020_options['modules']['admin2020_admin_overview']['videos'])){
				
				$currentvideos = $a2020_options['modules']['admin2020_admin_overview']['videos'];
				
				foreach($currentvideos as $avideo){
					if($video[0] == $avideo[0]){
						$message = __("Video name must be unique",'admin2020');
						echo $this->utils->ajax_error_message($message);
						die();
					}
				}
				
				array_push($currentvideos,$video);
				$a2020_options['modules']['admin2020_admin_overview']['videos'] = $currentvideos;
				
				
			} else {
				
				$a2020_options['modules']['admin2020_admin_overview']['videos'] = array($video);
			}
			
			
			
			if(is_array($a2020_options)){
				
				ob_start();
				
				foreach ($a2020_options['modules']['admin2020_admin_overview']['videos'] as $video) { ?>
					<tr>
						<td><?php echo $video[0]?></td>
						<td><?php echo $video[1]?></td>
						<td><?php echo $video[2]?></td>
						<td><?php echo $video[3]?></td>
						<td><a href="#" class="uk-button-danger uk-icon-button" onclick="a2020_delete_video('<?php echo $video[0]?>')" style="width:25px;height:25px;" uk-icon="icon:trash;ratio:0.8"></a></td>
					</tr>
				<?php } 
				
				
				$table = ob_get_clean();
				
				update_option( 'admin2020_settings', $a2020_options);
				$returndata = array();
				$returndata['success'] = true;
				$returndata['message'] = __('Video saved','admin2020');
				$returndata['content'] = $table;
				echo json_encode($returndata);
			} else {
				$message = __("Something went wrong",'admin2020');
				echo $this->utils->ajax_error_message($message);
				die();
			}
			
			
			
			die();
			
			
		}
		die();	
		
	}
	
	
	public function a2020_delete_video(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-settings-security-nonce', 'security') > 0) {
			
			$video_name = $this->utils->clean_ajax_input($_POST['name']);
			$a2020_options = get_option( 'admin2020_settings' );
			
			if($video_name == ""){
				$message = __("No video supplied to delete",'admin2020');
				echo $this->utils->ajax_error_message($message);
				die();
			}
			
			if(isset($a2020_options['modules']['admin2020_admin_overview']['videos'])){
				
				$currentvideos = $a2020_options['modules']['admin2020_admin_overview']['videos'];
				$tempvideos = array();
				
				foreach($currentvideos as $avideo){
					if($video_name != $avideo[0]){
						array_push($tempvideos,$avideo);
					}
				}
				
				$a2020_options['modules']['admin2020_admin_overview']['videos'] = $tempvideos;
				
				
			} 
			
			
			
			if(is_array($a2020_options)){
				
				ob_start();
				
				foreach ($tempvideos as $video) { ?>
					<tr>
						<td><?php echo $video[0]?></td>
						<td><?php echo $video[1]?></td>
						<td><?php echo $video[2]?></td>
						<td><?php echo $video[3]?></td>
						<td><a href="#" class="uk-button-danger uk-icon-button" onclick="a2020_delete_video('<?php echo $video[0]?>')" style="width:25px;height:25px;" uk-icon="icon:trash;ratio:0.8"></a></td>
					</tr>
				<?php } 
				
				
				$table = ob_get_clean();
				
				update_option( 'admin2020_settings', $a2020_options);
				$returndata = array();
				$returndata['success'] = true;
				$returndata['message'] = __('Video deleted','admin2020');
				$returndata['content'] = $table;
				echo json_encode($returndata);
			} else {
				$message = __("Something went wrong",'admin2020');
				echo $this->utils->ajax_error_message($message);
				die();
			}
			
			
			
			die();
			
			
		}
		die();	
		
	}
	
	/**
	 * Adds admin 2020 settings
	 * @since 1.4
	 */
	
	public function add_settings(){
		
		register_setting( 'admin2020_global_settings', 'admin2020_settings' );
		register_setting( 'admin2020_global_settings', 'admin2020_settings_network' );
		
	}
	
	/**
	 * Loads admin2020 modules
	 * @since 1.4
	 */
	
	public function load_modules(){
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/admin-bar.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/admin-menu.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/admin-theme.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/admin-login.php';
		
		
		
		$admin_bar = new Admin_2020_module_admin_bar($this->version,$this->path,$this->utils);
		$admin_bar->start();
		
		$admin_menu = new Admin_2020_module_admin_menu($this->version,$this->path,$this->utils);
		$admin_menu->start();
		
		$admin_theme = new Admin_2020_module_admin_theme($this->version,$this->path,$this->utils);
		$admin_theme->start();
		
		$admin_login = new Admin_2020_module_admin_login($this->version,$this->path,$this->utils);
		$admin_login->start();
		
		
		
		///PREMIUM
		if($this->utils->is_premium()){
			
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/premium/admin-overview.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/premium/admin-analytics.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/premium/admin-woocommerce.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/premium/admin-folders.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/premium/admin-content.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/premium/admin-advanced.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/premium/admin-menu-editor.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/premium/admin-overview-cards.php';
			//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/premium/admin-pages.php';
			
			
			$admin_folders = new Admin_2020_module_admin_folders($this->version,$this->path,$this->utils);
			$admin_folders->start();
			
			$admin_overview = new Admin_2020_module_admin_overview($this->version,$this->path,$this->utils);
			$admin_overview->start();
			
			$admin_analytics = new Admin_2020_module_google_analytics($this->version,$this->path,$this->utils);
			$admin_analytics->start();
			
			$admin_analytics = new Admin_2020_module_woocommerce($this->version,$this->path,$this->utils);
			$admin_analytics->start();
			
			$admin_content = new Admin_2020_module_admin_content($this->version,$this->path,$this->utils);
			$admin_content->start();
	
			$admin_advanced = new Admin_2020_module_admin_advanced($this->version,$this->path,$this->utils);
			$admin_advanced->start();
			
			$admin_menu_editor = new Admin_2020_module_admin_menu_editor($this->version,$this->path,$this->utils);
			$admin_menu_editor->start();
			
			//$adminpages = new Admin_2020_module_admin_pages($this->version,$this->path,$this->utils);
			//$adminpages->start();
			
			$cards = new Admin_2020_overview_cards();
			$cards->start(); 
			
		}
		
	}	
	
	
	/**
	 * Renders Admin Pages
	 * @since 1.4
	 */
	
	public function add_menu_items(){
		
		//add_submenu_page( 'admin2020-top-level', __('Settings','admin2020'), __('Settings','admin2020'), 'manage_options', 'admin2020-settings', array($this,'options_page_render'));
		add_options_page( 'Admin 2020', 'Admin 2020', 'manage_options', 'admin2020-settings', array($this,'options_page_render') );
		
	}
	
	/**
	 * Renders Admin Pages
	 * @since 1.4
	 */
	
	public function add_menu_items_network(){
		
		add_submenu_page(
			'settings.php', // Parent element
			'Admin 2020', // Text in browser title bar
			'Admin 2020', // Text to be displayed in the menu.
			'manage_options', // Capability
			'admin2020-settings', // Page slug, will be displayed in URL
			array($this,'options_page_render')  // Callback function which displays the page
		);
		
	}
	
	/**
	 * Renders Options Page
	 * @since 1.4
	 */
	
	public function options_page_render(){
		
		?>
		<div class="wrap">
			<div class="uk-container uk-container-expand">
				<?php 
				$this->build_header();
				$this->build_navigation();
				$this->render_settings();
				?>
			</div>	
		</div>
		<?php
	}
	
	/**
	 * Renders header
	 * @since 1.4
	 */
	
	public function build_header(){
		
		?>
		<div class="uk-grid-small uk-margin-bottom" uk-grid>
			<div class="uk-width-auto">
				<img src="https://admintwentytwenty.com/wp-content/uploads/LOGO-WHITE-circle.png" width="50">
			</div>
			<div class="uk-width-auto">
				<div class="uk-h3 uk-margin-remove-bottom"><?php echo $this->plugin_name ?></div>
				<div class="uk-text-meta"><?php echo __('Version','admin2020').': '.$this->version ?></div>
			</div>
			<div class="uk-width-expand uk-text-right">
				<button class="uk-button uk-button-default uk-margin-small-right a2020_make_light" 
				uk-tooltip="delay:300;title:<?php _e('Exports Admin 2020 settings to JSON file')?>"
				onclick="a2020_export_settings()"><?php _e('Export','admin2020')?></button>
				<a id="admin2020_download_settings" style="display: none;"></a>
				
				
				
				
				<div class="js-upload uk-form-custom" uk-form-custom="">
					<input accept=".json" type="file" single="" id="admin2020_export_settings" onchange="a2020_import_settings()">
					
					<button class="uk-button uk-button-default a2020_make_light" 
					uk-tooltip="delay:300;title:<?php _e('Imports Admin 2020 settings to from JSON')?>"><?php _e('Import','admin2020')?></button>
				</div>
				
			</div>
		</div>
		<?php
	}
	
	/**
	 * Renders Navigation
	 * @since 1.4
	 */
	
	public function build_navigation(){
		
		$components = array();
		$components = apply_filters( 'admin2020_register_component', $components );
		$this->components = $components;
		
		
		?>
		
		<ul class="uk-tab uk-margin-top" uk-switcher>
			
			<li><a href="#"><?php _e('Modules','admin2020') ?></a></li>
			
			<?php foreach($components as $component) { 
				
				$info = $component->component_info();
				$title = $info['title'];
				
				?>
				
				<li><a href="#"><?php echo $title; ?></a></li>
			
			<?php } ?>
		</ul>
		
		<?php
	}
	
	/**
	 * Renders settings tabs
	 * @since 1.4
	 */
	 
	public function render_settings(){
		
		$components = $this->components;
		$network = 'false'; 
		
		if( is_network_admin() ){  
			$network = 'true';
		}
		?>
		
		<ul class="uk-switcher uk-margin">
			
			
			<li><?php $this->render_module_settings() ?></li><!-- General -->
			
			<?php foreach($components as $component) { 
				
				$info = $component->component_info();
				$title = $info['title']; 
				
				?>
				
				<li>
					<div class="uk-h3"><?php echo $title?></div>
					<div class="uk-card uk-card-default uk-card-body">
						<?php $component->render_settings(); ?>
					</div>
					<div class="uk-width-1-1 uk-margin-top">
						<button class="uk-button uk-button-primary" onclick="a2020_save_settings(<?php echo $network ?> )" type="button"><?php _e('Save','admin2020') ?></button>
					</div>
				</li>
					
			<?php } ?>		
		</ul>
		
		<?php
	}
	
	/**
	 * Renders module settings
	 * @since 1.4
	 */
	 
	public function render_module_settings(){
		
		$components = $this->components;
		$a2020_options = get_option( 'admin2020_settings' );
		$network = 'false';
		
		if(is_network_admin()){
			$network = 'true';
		}
		
		$key = $this->utils->get_option('activation','key');
		$message = true;
		$activated = true;
		
		if($key != "" && !get_transient( 'admin2020_activated')){
			$activated = false;
		} 
		
		if($key == "" || !$key || !get_transient( 'admin2020_activated')){
			$activated = false;
		}
		
		?>
		<div class="uk-h3"><?php _e('Modules','admin2020')?></div>
		<div class="uk-card uk-card-default uk-card-body " id="a2020_all_modules">
			
			
			<?php if($activated) { ?>
			
				<div class="uk-width-xlarge" uk-grid>
					<div class="uk-width-1-1@ uk-width-2-3@m">
						<div class="uk-h5 uk-margin-remove"><?php echo _e('Licence','admin2020')?></div>
						<div class="uk-text-meta"><?php echo _e('Admin 2020 licence is active. To remove licence or change licence click remove.','admin2020')?></div>
					</div>
					<div class="uk-width-1-1@ uk-width-1-3@m">
						<button class="uk-button uk-button-small uk-button-default" onclick="a2020_remove_licence(<?php echo $network?>)" ><?php _e('Remove','admin2020')?></button>
					</div>	
				</div>	
			
			<?php } ?>
			
			<?php if( is_network_admin() ) { ?>
			
				<?php
				$a2020_options = get_option( 'admin2020_settings_network' );
				$enabled;
				
				//echo '<pre>' . print_r( $a2020_options, true ) . '</pre>';
				
				if(isset($a2020_options['modules']['network_override']['status'])){
					$enabled = $a2020_options['modules']['network_override']['status'];
				}
				
				$checked = 'checked';
				
				if($enabled == 'false'){
					$checked = '';
				}
				?>
				
				<div class="uk-width-xlarge" uk-grid>
					<div class="uk-width-1-1@ uk-width-2-3@m">
						<div class="uk-h5 uk-margin-remove"><?php echo _e('Override Subsite Settings?','admin2020')?></div>
						<div class="uk-text-meta"><?php echo _e('If this is enabled then all the settings here will be applied to your subsites and they will not be able to be changed from a subsite.','admin2020')?></div>
					</div>
					<div class="uk-width-1-1@ uk-width-1-3@m">
						<label class="admin2020_switch uk-margin-left">
							<input class="a2020_module" name="network_override" type="checkbox" <?php echo $checked ?>>
							<span class="admin2020_slider constant_dark"></span>
						</label>
					</div>	
				</div>	
				
			<?php } ?>
			
			<?php foreach($components as $component) { 
			
				$info = $component->component_info();
				$title = $info['title']; 
				$description = $info['description']; 
				$optionname = $info['option_name'];
				if(isset($a2020_options['modules'][$optionname]['status'])){
					$enabled = $a2020_options['modules'][$optionname]['status'];
				} else {
					$enabled = 'true';
				}
				
				$checked = 'checked';
				
				if($enabled == 'false'){
					$checked = '';
				}
				
				?>
				<div class="uk-width-xlarge" uk-grid>
					<div class="uk-width-1-1@ uk-width-2-3@m">
						<div class="uk-h5 uk-margin-remove"><?php echo $title?></div>
						<div class="uk-text-meta"><?php echo $description?></div>
					</div>
					<div class="uk-width-1-1@ uk-width-1-3@m">
						<label class="admin2020_switch uk-margin-left">
							<input class="a2020_module" name="<?php echo $optionname?>" type="checkbox" <?php echo $checked ?>>
							<span class="admin2020_slider constant_dark"></span>
						</label>
					</div>	
				</div>	
				
			<?php } ?>	
		</div>	
		<div class="uk-width-1-1 uk-margin-top">
			<button class="uk-button uk-button-primary" onclick="a2020_save_modules(<?php echo $network ?>)" type="button"><?php _e('Save','admin2020') ?></button>	
		</div>
		<?php
	}
	
}
