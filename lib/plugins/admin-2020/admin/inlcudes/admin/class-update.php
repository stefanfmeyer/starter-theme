<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_update {
	
	public function __construct($version,$path,$utilities,$id) {
	
		$this->version = $version;
		$this->path = $path;
		$this->utils = $utilities;
		$this->productid = $id;
		$this->transient = 'a2020_update_admin-2020';
	
	}
	
	/**
	 * Registers plugin licence
	 * @since 1.4
	 */
	
	public function start(){
		
		$key = $this->utils->get_key('activation','key');
		$message = true;
		
		if($key == "" || !get_transient( 'admin2020_activated')){
			return;
		}
		
		add_action('admin_init', array( $this, 'register_actions' ),0);
		add_filter('plugins_api', array($this,'a2020_plugin_info'), 20, 3);
		add_filter('site_transient_update_plugins', array($this,'a2020_push_update') );
		add_action('upgrader_process_complete', array($this,'a2020_after_update'), 10, 2 );
		add_filter('plugin_row_meta' , array($this,'add_settings_link'),10,2 );
		add_action('wp_ajax_a2020_check_for_updates', array($this,'a2020_check_for_updates'));
		
	} 
	
	/**
	 * Adds activation actions
	 * @since 1.4
	 */
	
	public function register_actions(){
		
		add_action('admin_enqueue_scripts', array( $this, 'add_scripts' ),0);
		
		
	}
	
	/**
	* Adds link to look for update
	* @since 1.4
	*/
	public function add_settings_link( $plugin_meta, $plugin_file_name ) {
	
		if ($plugin_file_name == "admin-2020/admin-2020.php"){
		  $plugin_meta[] = '<a href="#" onclick="a2020_check_for_updates()">'.__('Check for updates','admin2020').'</a>';
		}
		return $plugin_meta;
	}
	 
	 /**
	* Enqueue Admin 2020 scripts
	* @since 1.4
	*/
	
	public function add_scripts(){
	  
		wp_enqueue_script('admin2020-update', $this->path . 'assets/js/admin2020/update.min.js', array('jquery'));
		wp_localize_script('admin2020-update', 'admin2020_update_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'security' => wp_create_nonce('admin2020-update-security-nonce'),
		));
	  
	}
	
	
	/**
	 * Sets user preferences from ajax
	 * @since 1.4
	 */
	public function a2020_check_for_updates(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-update-security-nonce', 'security') > 0) {
			
			
			$state = delete_transient($this->transient);
			
			$key = $this->utils->get_key('activation','key');
			$domain = get_home_url();
			$update_url = 'https://admintwentytwenty.com/validate/update.php?id='.$this->productid.'&k='.$key.'&d='.$domain;
	 
			// info.json is the file with the actual plugin information on your server
			$remote = wp_remote_get( $update_url, array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/json'
				) )
			);
	 
			if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
				set_transient( $this->transient, $remote, 43200 ); // 12 hours cache
			}
	 
		
	 
			if( $remote ) {
		 
				$remote = json_decode( $remote['body'] );
				$state = $remote->state;
				
				if ($state == "false"){
					return $transient;
				}
				
				
				// your installed plugin version should be on the line below! You can obtain it dynamically of course 
				if( $remote && version_compare( $this->version, $remote->version, '<' ) ) {
					$returndata = array();
					$returndata['success'] = true;
					$returndata['message'] = __('Update available','admin2020');
					echo json_encode($returndata);
					die();
				}
			}
			
			$message = __("No Updates available",'admin2020');
			echo $this->utils->ajax_error_message($message);
			die();
			
		}
		die();
	}
	
	/**
	* Fetches plugin update info
	* @since 1.4
	*/
	
	public function a2020_plugin_info( $res, $action, $args ){
		
		// do nothing if this is not about getting plugin information
		if( 'plugin_information' !== $action ) {
			return $res;
		}
	 
		$plugin_slug = 'admin-2020'; // we are going to use it in many places in this function
	 
		// do nothing if it is not our plugin
		if( $plugin_slug !== $args->slug ) {
			return $res;
		}
		
	 
		// trying to get from cache first
		if( false == $remote = get_transient( $this->transient ) ) {
	 
			// info.json is the file with the actual plugin information on your server
			$key = $this->utils->get_key('activation','key');
			$domain = get_home_url();
			$update_url = 'https://admintwentytwenty.com/validate/update.php?id='.$this->productid.'&k='.$key.'&d='.$domain;
			
			$remote = wp_remote_get( $update_url, array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/json'
				) )
			);
	 
			if ( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {
				set_transient( $this->transient, $remote, 43200 ); // 12 hours cache
			}
	 
		}
		
			
			 
		if( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {
			
			$remote = json_decode( $remote['body'] );
			$state = $remote->state;
			
			if ($state == "false"){
				return $res;
			}	
	 
			$res = new stdClass();
	 
			$res->name = $remote->name;
			$res->slug = $plugin_slug;
			$res->version = $remote->version;
			$res->tested = $remote->tested;
			$res->requires = $remote->requires;
			$res->download_link = $remote->download_url;
			$res->trunk = $remote->download_url;
			$res->requires_php = '5.3';
			$res->last_updated = $remote->last_updated;
			$res->sections = array(
				'description' => $remote->sections->description,
				'installation' => $remote->sections->installation,
				// you can add your custom sections (tabs) here
			);
	 
			if( !empty( $remote->sections->screenshots ) ) {
				$res->sections['screenshots'] = $remote->sections->screenshots;
			}
	 
			$res->banners = array(
				'low' => $remote->banners->low,
				'high' => $remote->banners->high,
			);
			return $res;
	 
		}
	 
		return $res;
	 
	}
	
	/**
	* Pushes plugin update to plugin table
	* @since 1.4
	*/
	
	public function a2020_push_update( $transient ){
		
			 
		if ( empty($transient->checked ) ) {
			return $transient;
		}
	 
		// trying to get from cache first, to disable cache comment 10,20,21,22,24
		if( false == $remote = get_transient( $this->transient ) ) {
			
			$key = $this->utils->get_key('activation','key');
			$domain = get_home_url();
			$update_url = 'https://admintwentytwenty.com/validate/update.php?id='.$this->productid.'&k='.$key.'&d='.$domain;
	 
			// info.json is the file with the actual plugin information on your server
			$remote = wp_remote_get( $update_url, array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/json'
				) )
			);
	 
			if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
				set_transient( $this->transient, $remote, 43200 ); // 12 hours cache
			}
	 
		}
	 
		if( $remote ) {
	 
			$remote = json_decode( $remote['body'] );
			$state = $remote->state;
			
			if ($state == "false"){
				return $transient;
			}
	 	   
			
			// your installed plugin version should be on the line below! You can obtain it dynamically of course 
			if( $remote && version_compare( $this->version, $remote->version, '<' ) ) {
				$res = new stdClass();
				$res->slug = 'admin-2020';
				$res->plugin = 'admin-2020/admin-2020.php'; // it could be just YOUR_PLUGIN_SLUG.php if your plugin doesn't have its own directory
				$res->new_version = $remote->version;
				$res->tested = $remote->tested;
				$res->package = $remote->download_url;
				$transient->response[$res->plugin] = $res;
			}
		}
		
		return $transient;
	}
	
	
	/**
	* Cleans cache after update
	* @since 1.4
	*/
	
	
	public function a2020_after_update( $upgrader_object, $options ) {
		if ( $options['action'] == 'update' && $options['type'] === 'plugin' )  {
			// just clean the cache when new plugin version is installed
			delete_transient( $this->upgrade_transient );
		}
	}
	
	
		
	
	
}