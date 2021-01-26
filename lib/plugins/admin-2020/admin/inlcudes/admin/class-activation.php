<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_activation {
	
	public function __construct($version,$path,$utilities,$id) {
	
		$this->version = $version;
		$this->path = $path;
		$this->utils = $utilities;
		$this->productid = $id;
	
	}
	
	/**
	 * Registers plugin licence
	 * @since 1.4
	 */
	
	public function start(){
		
		
		$key = $this->utils->get_key('activation','key');
		$message = true;
		
		if($key != "" && !get_transient( 'admin2020_activated')){
			$message = $this->register($key,is_network_admin());
		}
		
		if($key == "" || !$key || $message != true || !get_transient( 'admin2020_activated')){
			$this->list_activation_message();
			add_action('admin_init', array( $this, 'register_actions' ),0);
		}
		
		if(!$this->utils->is_premium()){
			$this->list_upgrade_message(); 
		}
		
	} 
	
	/**
	 * Adds activation actions
	 * @since 1.4
	 */
	
	public function register_actions(){
		
		add_action('admin_enqueue_scripts', array( $this, 'add_scripts' ),0);
		add_action('wp_ajax_a2020_check_licence_key', array($this,'a2020_check_licence_key'));
		
	}
	 
	 /**
	* Enqueue Admin 2020 scripts
	* @since 1.4
	*/
	
	public function add_scripts(){
	  
		wp_enqueue_script('admin2020-activation', $this->path . 'assets/js/admin2020/activation.min.js', array('jquery'));
		wp_localize_script('admin2020-activation', 'admin2020_activation_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'security' => wp_create_nonce('admin2020-activation-security-nonce'),
		));
	  
	}
	
	
	/**
	 * Outputs activation message
	 * @since 1.4
	 */
	
	public function list_activation_message(){
		
		if(is_network_admin()){
			
			add_action('network_admin_notices', function(){
		    	echo '<div id="activationpanel" class="notice notice-warning" style="display: block !important;visibility: visible !important;">' . $this->create_activation_message() . '</div>';
			});
			
		} else {
			
			add_action('admin_notices', function(){
				echo '<div id="activationpanel" class="notice notice-warning" style="display: block !important;visibility: visible !important;">' . $this->create_activation_message() . '</div>';
			});
			
		}
		
	}
	
	/**
	 * Outputs activation message
	 * @since 1.4
	 */
	
	public function list_upgrade_message(){
		
		if(!get_transient('a2020_upgrade_notice')){
			add_action('admin_notices', function(){
				echo '<div id="a2020_upgrade_message" class="notice notice-warning" style="display: block !important;visibility: visible !important;">' . $this->create_upgrade_message() . '</div>';
			});
			set_transient( 'a2020_upgrade_notice', true, 43200 );
		}
		
	}
	
	
	/**
	 * Outputs activation message
	 * @since 1.4
	 */
	
	public function create_activation_message(){
		
		if(is_network_admin()){
			$network = 'true';
		} else {
			$network = 'false';
		}
		
		ob_start();
		
		
		?>
		<div class="uk-grid-small" uk-grid>
			<div class="uk-width-1-1@s uk-width-expand@m">
				<div class="uk-h4 uk-margin-remove"><?php _e('Admin 2020 is not activated','admin2020')?></div>
				<div class="uk-text-meta"><?php _e('Please add a valid licence to activate','admin2020')?></div>
			</div>
			<div class="uk-width-1-1@s uk-width-1-3@m">
				<input class="uk-input" id="a2020-licence-key" placeholder="<?php _e('Enter your licence key','admin2020') ?>">
			</div>
			<div class="uk-width-1-1@s uk-width-auto@m">
				<button class="uk-button uk-button-secondary" onclick="check_licence_key(<?php echo $network?>)"><?php _e('Activate','admin2020') ?></button>
			</div>
		</div>
		
		<?php
		return ob_get_clean();
		
	}
	
	
	/**
	 * Outputs upgrade message
	 * @since 1.4
	 */
	
	public function create_upgrade_message(){ 
		ob_start();
		
		
		?>
		<div class="uk-grid-small" uk-grid>
			<div class="uk-width-1-1@s uk-width-expand@m">
				<div class="uk-h4 uk-margin-remove"><?php _e('Supercharge your admin with Admin 2020 Pro','admin2020')?></div>
				<div class="uk-text-meta"><?php _e('Unlock powerful features and speed up your workflow with Admin 2020 Pro','admin2020')?></div>
			</div>
			<div class="uk-width-1-1@s uk-width-auto@m">
				<a class="uk-button uk-button-primary" href="https://admintwentytwenty.com/pricing" target="_BLANK"><?php _e('Upgrade','admin2020') ?></a>
				<button class="uk-button uk-button-default" onclick="jQuery('#a2020_upgrade_message').fadeOut(300)"><?php _e('Dimiss','admin2020') ?></button>
			</div>
		</div>
		
		<?php
		return ob_get_clean();
		
	}
	
	
	/**
	 * Receives key from front end
	 * @since 1.4
	 */
	
	public function a2020_check_licence_key(){
		
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-activation-security-nonce', 'security') > 0) {
			
			$key = $this->utils->clean_ajax_input($_POST['key']);
			$network = $this->utils->clean_ajax_input($_POST['network']);
			$message = $this->register($key,$network);
		
			if($message === true){
				$returndata = array();
				$returndata['success'] = true;
				$returndata['message'] = __('Admin 2020 succesfully activated','admin2020');
				echo json_encode($returndata); 
				die();
			} else {
				echo $this->utils->ajax_error_message($message);
				die();
			}
			
		}
		
		die();
	}
	
	
	/**
	 * Validates licence key
	 * @since 1.4
	 */
	
	public function register($key,$network){
		
		$data = array();
		$data["key"] = $key;
		$data["increment_usage_count"] = true;
		$productid = $this->productid;
		$domain = get_home_url();
		$themessage = __('Unable to register admin 2020 at this time','admin2020');
		
		if(!$productid || $productid == ""){
			return $themessage;
		}
		
		if(!$key || $key == ""){
			return __('No licence key provided','admin2020');
		}
		
		$remote = wp_remote_get( 'https://admintwentytwenty.com/validate/validate.php?id='.$this->productid.'&k='.$key.'&d='.$domain, array(
		  'timeout' => 10,
		  'headers' => array(
			  'Accept' => 'application/json'
		  ) )
		);
		
		
		
		if ( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {
		
			$remote = json_decode( $remote['body'] );
			$state = $remote->state;
			$themessage = $remote->message;
			
			if ($state != "false"){
			  set_transient( 'admin2020_activated', true, 12 * HOUR_IN_SECONDS );
			  
			  if($network === 'true'){
				  $a2020_options = get_option( 'admin2020_settings_network');
				  $a2020_options['modules']['activation']['key'] = $key;
				  update_option( 'admin2020_settings_network', $a2020_options);
			  } else {
				  $a2020_options = get_option( 'admin2020_settings');
				  $a2020_options['modules']['activation']['key'] = $key;
				  update_option( 'admin2020_settings', $a2020_options);
			  }
			  
			  return true;
			} else {
			  delete_transient('admin2020_activated');
			  return $themessage;
			}
		
		} else {
			delete_transient('admin2020_activated');
			return $themessage;
		}
		
	}
		
	
	
}