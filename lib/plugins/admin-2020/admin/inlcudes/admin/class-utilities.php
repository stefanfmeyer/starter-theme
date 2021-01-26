<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_utilities {
	
	public function __construct($version,$path,$productid) {
	
		$this->version = $version;
		$this->path = $path;
		$this->product_id = $productid;
		$this->product_ids = array("5ebc6198701f5d0a0812c618","5ebe7e0d701f5d079480905e","5ebe7e66701f5d0fe0b4c1ab","5f6a0e2a701f5d10e8be56a3",'beta');
		$this->post_types = '';
		$this->current_user_id = 0;
		$this->current_user = '';
		$this->a2020_options = '';
		
	}
	
	/**
	 * Adds utility ajax functions
	 * @since 1.4
	 */
	public function start(){
		
		add_action('wp_ajax_a2020_save_user_prefences', array($this,'a2020_save_user_prefences'));
		
	}
	
	/**
	 * Gets user id
	 * @since 2.0.4
	 */
	public function get_user_id(){
		
		if($this->current_user_id > 0){
			
			return $this->current_user_id;
			
		} else {
		
			$this->current_user_id = get_current_user_id();
			return $this->current_user_id;
			
		}
		
	}
	
	/**
	 * Gets user object
	 * @since 2.0.4
	 */
	public function get_user(){
		
		
		if(is_object($this->current_user)){
			
			return $this->current_user;
			
		} else {
		
			$this->current_user = wp_get_current_user();
			return $this->current_user;
			
		}
		
		
	}
	
	/**
	 * Gets plugin options
	 * @since 2.0.4
	 */
	public function get_a2020_options(){
		
		
		if(is_array($this->a2020_options)){
			
			return $this->a2020_options;
			
		} else {
		
			$this->a2020_options = get_option( 'admin2020_settings' ); 
			return $this->a2020_options;
			
		}
		
		
	}
	
	
	/**
	 * Gets post types
	 * @since 2.0.4
	 */
	public function get_post_types(){
		
		if(is_array($this->post_types)){
			
			return $this->post_types;
			
		} else {
		
			$args = array('public'  => true);
			$output = 'objects'; 
			$post_types = get_post_types( $args, $output );
			$this->post_types = $post_types;
			return $post_types;
			
		}
		
	}
	
	/**
	 * Sets user preferences from ajax
	 * @since 1.4
	 */
	public function a2020_save_user_prefences(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-utilities-security-nonce', 'security') > 0) {
			
			$pref = $this->clean_ajax_input($_POST['pref']);
			$value = $this->clean_ajax_input($_POST['value']);
			
			if($pref == "" ){
				$message = __("No preferences supplied to save",'admin2020');
				echo $this->ajax_error_message($message);
				die();
			}
			
			$userid = get_current_user_id();
			$current = get_user_meta($userid, 'admin2020_preferences', true);
			
			if(is_array($current)){
				$current[$pref] = $value;
			} else {
				$current = array();
				$current[$pref] = $value;
			}
			
			$state = update_user_meta($userid, 'admin2020_preferences', $current);
			
			if($state){
				$returndata = array();
				$returndata['success'] = true;
				$returndata['message'] = __('Preferences saved','admin2020');
				echo json_encode($returndata);
			} else {	
				$message = __("Unable to save user preferences",'admin2020');
				echo $this->ajax_error_message($message);
				die();
			}
			
			
			
		}
		die();	
		
	}
	
	/**
	 * Sanitises and strips tags of input from ajax
	 * @since 1.4
	 * @variables $values = item to clean (array or string)
	 */
	public function clean_ajax_input($values){
		
		if(is_array($values)){
			foreach ($values as $index => $in){
				if(is_array($in)){
					$values[$index] = $this->clean_ajax_input($in);
				} else {	
					$values[$index] = strip_tags($in);
				}
			}
		} else {
			$values = strip_tags($values);
		}
		
		return $values;
	}
	
	/**
	 * Returns ajax error
	 * @since 1.4
	 * @variables $message = error message to send back to user (string)
	 */
	public function ajax_error_message($message){
		$returndata = array();
		$returndata['error'] = true;
		$returndata['error_message'] = $message;
		return json_encode($returndata);
	}
	/**
	 * Gets user set logo
	 * @since 1.4
	 */
	 
	public function get_logo($module){
	
		$logo = $this->get_option($module,'light-logo');
		
		if ($logo == ""){
			$logo = esc_url($this->path.'/assets/img/default_logo.png');
		}
		
		return $logo;
	}
	
	/**
	 * Gets user set dark logo
	 * @since 1.4
	 */
	 
	public function get_dark_logo($module){
	
		$logo = $this->get_option($module,'dark-logo');
		
		if ($logo == ""){
			$logo =  $this->get_logo($module,'light-logo');
		}
		
		return $logo;
	}
	
	/**
	 * Checks whether the module is disabled
	 * @variables $item - PHP class object
	 * @since 1.4
	 */
	
	public function enabled($item){
		
		$meta = $item->component_info();
		$optionname = $meta['option_name'];
		
		$a2020_options = $this->get_a2020_options();
		
		if(is_multisite() && $this->is_site_wide('admin-2020/admin-2020.php')){
			
			$a2020_network_options = get_blog_option(get_main_network_id(),'admin2020_settings_network');
			
			if(isset($a2020_network_options['modules']['network_override']['status'])){
				$enabled = $a2020_network_options['modules']['network_override']['status'];
				
				if($enabled == 'true'){
					
					$a2020_options = $a2020_network_options;
					
				} 
				
			}
			
		} 
		
		if(is_network_admin()){
			$a2020_options = get_option( 'admin2020_settings_network' );
		}
		
		if(isset($a2020_options['modules'][$optionname]['status'])){
			$enabled = $a2020_options['modules'][$optionname]['status'];
		} else {
			$enabled = 'true';
		}
		
		if($enabled == 'false'){
			return false;
		} else {
			return true;
		}
		
	}
	
	/**
	 * Gets user options
	 * @since 1.2
	 */
	
	public function get_option($module_name = false,$option_name = false){
		
		if($module_name == false || $option_name == false){
			return '';
		}
		$a2020_options = $this->get_a2020_options();
		$option = '';
		
		if(is_multisite() && $this->is_site_wide('admin-2020/admin-2020.php')){
			
			$a2020_network_options = get_blog_option(get_main_network_id(),'admin2020_settings_network');
			
			if(isset($a2020_network_options['modules']['network_override']['status'])){
				$enabled = $a2020_network_options['modules']['network_override']['status'];
				
				if($enabled == 'true'){
					
					$a2020_options = $a2020_network_options;
					
				} 
				
			}
			
		} 
		
		if(is_network_admin()){
			$a2020_options = get_option( 'admin2020_settings_network' );
		}
			
		if(isset($a2020_options['modules'][$module_name][$option_name])){
			$value = $a2020_options['modules'][$module_name][$option_name];
			if($value != ""){
				$option = $value;
			}
		}
		
		return $option;
	
	}
	
	
	/**
	 * Gets user options
	 * @since 1.2
	 */
	
	public function get_key($module_name = false,$option_name = false){
		
		if($module_name == false || $option_name == false){
			return '';
		}
		$a2020_options = $this->get_a2020_options();
		$option = '';
		
		if(is_multisite() && $this->is_site_wide('admin-2020/admin-2020.php')){
			
			$a2020_network_options = get_blog_option(get_main_network_id(),'admin2020_settings_network');
			$a2020_options = $a2020_network_options;
			
		} 
		
		if(is_network_admin()){
			$a2020_options = get_option( 'admin2020_settings_network' );
		}
			
		if(isset($a2020_options['modules'][$module_name][$option_name])){
			$value = $a2020_options['modules'][$module_name][$option_name];
			if($value != ""){
				$option = $value;
			}
		}
		
		return $option;
	
	}
	
	public function is_site_wide( $plugin ) {
		
		if ( ! is_multisite() ) {
			return false;
		}
	 
		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}
	 
		return false;
	}
	
	/**
	 * Checks if module is locked for role / user
	 * @since 1.2
	 */
	
	public function is_locked($optionname){
		
		
		$disabled_for = $this->get_option($optionname,'disabled-for');
		
		if(!is_array($disabled_for)){
			return false;
		}
		
		if(!function_exists('wp_get_current_user')){
			return false;
		}
		
		
		$current_user = $this->get_user();
		
		
		$current_name = $current_user->display_name;
		$current_roles = $current_user->roles;
		$formattedroles = array();
		$all_roles = wp_roles()->get_names();
		
		
		if(in_array($current_name, $disabled_for)){
			return true;
		}
		
		
		///MULTISITE SUPER ADMIN
		if(is_super_admin() && is_multisite()){
			if(in_array('Super Admin',$disabled_for)){
				return true;
			} else {
				return false;
			}
		}
		
		///NORMAL SUPER ADMIN
		if($current_user->ID === 1){
			if(in_array('Super Admin',$disabled_for)){
				return true;
			} else {
				return false;
			}
		}
		
		foreach ($current_roles as $role){
			
			$role_name = $all_roles[$role];
			if(in_array($role_name,$disabled_for)){
				return true;
			}
			
		}
		
	}
	
	
	/**
	 * Sorts arrays by key 'order'
	 * @since 1.4
	 */
	public function sort_array($tosort){
		
		usort($tosort, array($this,"sort_array_helper"));
		
		return $tosort;
		
	}
	
	/**
	 * usort function for menu arrays
	 * @since 1.4
	 */
	
	public function sort_array_helper($a,$b)
	{
		$result = 0;
		if(!isset($a['order'])){
			return $result;
		}
		if ($a['order'] > $b['order']) {
			$result = 1;
		} else if ($a['order'] < $b['order']) {
			$result = -1;
		}
		return $result; 
	}
	
	/**
	 * Gets user preferences
	 * @since 1.2
	 * @variable $pref name of pref to fetch (string)
	 */
	public function get_user_preference($pref){
		
		$userid = $this->get_user_id();
		$current = get_user_meta($userid, 'admin2020_preferences', true);
		$value = false;
		
		if(is_array($current)){
			if(isset($current[$pref])){
				$value = $current[$pref];
			}
		}	
		
		return $value;
		
	}
	
	
	public function get_total_updates(){
		$returndata = array();
		$returndata['total'] = 0;
		$returndata['wordpress'] = 0;
		$returndata['theme'] = 0;
		$returndata['plugin'] = 0;
		
		if(!is_admin()){
			return $returndata;
		}
		
		if(!current_user_can('install_plugins')){
			return $returndata;
		}
		
		$totalupdates = 0;
		
		if (is_super_admin() && is_admin()){
			////GET UPDATES
			$pluginupdates = get_plugin_updates();
			$themeupdates = get_theme_updates();
			$wordpressupdates = get_core_updates();
			
			if(isset($wordpressupdates[0])){
			  $wpversion =  $wordpressupdates[0]->version;
			  global $wp_version;
			
			  if ($wpversion > $wp_version){
				$wordpressupdates = 1;
			  } else {
				$wordpressupdates = 0;
			  }
			} else {
			  $wordpressupdates = 0;
			}
			
			$totalupdates = count($pluginupdates) + count($themeupdates) + $wordpressupdates;
			
			$returndata['total'] = $totalupdates;
			$returndata['wordpress'] =$wordpressupdates;
			$returndata['theme'] = $themeupdates;
			$returndata['plugin'] = $pluginupdates;
		
		}
		
		return $returndata;
		
	}
	
	/**
	 * Returns an araay of dates betwen two dates
	 * @since 1.2
	 */
	
	public function date_array($startdate,$enddate){
		
		$enddate = date('Y-m-d', strtotime($enddate . ' + 1 day') ); 
	
		$period = new DatePeriod(
		   new DateTime($startdate),
		   new DateInterval('P1D'),
		   new DateTime($enddate)
		);
		
		$date_array = array();
		
		foreach ($period as $key => $value) {
		  $the_date = $value->format('d/m/Y');
		  array_push($date_array,$the_date);
		}
		
		return $date_array;
		
	}
	
	public function is_premium(){
		
		if(in_array($this->product_id, $this->product_ids)){
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Formats file sizes
	 * @since 1.2
	 */
	public function formatBytes($size, $precision = 0){
		$base = log($size, 1024);
		$suffixes = array('', 'Kb', 'Mb', 'Gb', 'Tb');
		
		return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
	}
	
}



