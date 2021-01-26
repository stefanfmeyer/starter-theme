<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_styles{
	
	public function __construct($version,$path,$utilities) {
	
		$this->version = $version;
		$this->path = $path;
		$this->utils = $utilities;
	
	}
	
	/**
	 * Loads Admin 2020 on init
	 * @since 1.4
	 */
	
	public function start(){
		
		add_action('admin_init', array( $this, 'register_actions' ),0);
		add_filter('mailpoet_conflict_resolver_whitelist_style',array($this,'admin2020_mailPoet_styles'));
		add_filter('mailpoet_conflict_resolver_whitelist_script',array($this,'admin2020_mailPoet_scripts'));
		add_filter('fluentform_skip_no_conflict',array($this,'admin2020_fluentform_styles'));
		add_filter('fluentcrm_skip_no_conflict',array($this,'admin2020_fluentform_styles'));
		add_filter('ninja_table_skip_no_confict',array($this,'admin2020_fluentform_styles'));
		
	}
	
	
	/**
	 * Loads Admin 2020 on front end
	 * @since 1.4
	 */
	
	public function start_front(){
		
		$admin_front = $this->utils->get_option('admin2020_admin_bar','load-front');
		$hide_admin = $this->utils->get_option('admin2020_admin_bar','hide-admin');
		
		if($hide_admin == 'true') {
			add_filter('show_admin_bar', 'is_blog_admin');
			return;
		}
		
		if($admin_front != 'true') {
			return;
		}
		
		add_action('wp_enqueue_scripts', array( $this, 'add_styles_front' ),0);
		add_action('wp_enqueue_scripts', array( $this, 'add_scripts' ),0);
		
		
	}
	
	
	
	/**
	 * Adds admin 2020 actions
	 * @since 1.4
	 */
	
	public function register_actions(){
		
		add_action('admin_enqueue_scripts', array( $this, 'add_styles' ),0);
		add_action('admin_enqueue_scripts', array( $this, 'load_plugin_compatability_styles' ),0);
		add_action('admin_enqueue_scripts', array( $this, 'add_scripts' ),0);
		add_filter('admin_body_class', array($this, 'add_body_classes'));
		add_filter('admin2020_register_compatability_plugin_style', array($this,'register_compatability_styles'));
		
	}
	
	/**
	 * Loads stylesheet helpers if they exist for extra plugins
	 * @since 1.4
	 */
	
	public function load_plugin_compatability_styles(){
		
		$supportedplugins = array();
		$supportedplugins = apply_filters( 'admin2020_register_compatability_plugin_style', $supportedplugins );
		
		$activeplugins = get_option('active_plugins');
		foreach ($activeplugins as $plugin) {
			
			$string = explode('/', $plugin);
			$pluginname = $string[0];
			
			if(isset($supportedplugins[$pluginname])){
				
				if($supportedplugins[$pluginname] != ""){
					
					wp_register_style('a2020_' . $pluginname . '_css', $supportedplugins[$pluginname], array(), $this->version);
					wp_enqueue_style('a2020_' . $pluginname . '_css');
					
				}
				
			}
			
		}
	}
	
	/**
	 * Registers style sheets for extra plugins
	 * @since 1.4
	 */
	
	public function register_compatability_styles($supportedplugins){
		
		if(!is_array($supportedplugins)){
			$supportedplugins = array();
		}
		$supportedplugins['woocommerce'] = $this->path . 'assets/css/plugins/plugin_woocommerce.css';
		$supportedplugins['gravityforms'] = $this->path . 'assets/css/plugins/plugin_gravityforms.css';
		$supportedplugins['wordfence'] = $this->path . 'assets/css/plugins/plugin_wordfence.css';
		$supportedplugins['admin-menu-editor'] = $this->path . 'assets/css/plugins/plugin_admin-menu-editor.css';
		$supportedplugins['admin-menu-editor-pro'] = $this->path . 'assets/css/plugins/plugin_admin-menu-editor.css';
		$supportedplugins['advanced-custom-fields'] = $this->path . 'assets/css/plugins/plugin_advanced-custom-fields.css';
		$supportedplugins['advanced-custom-fields-pro'] = $this->path . 'assets/css/plugins/plugin_advanced-custom-fields.css';
		$supportedplugins['all-in-one-wp-migration'] = $this->path . 'assets/css/plugins/plugin_all-in-one-wp-migration.css';
		$supportedplugins['breeze'] = $this->path . 'assets/css/plugins/plugin_breeze.css';
		$supportedplugins['codepress-admin-columns'] = $this->path . 'assets/css/plugins/plugin_codepress-admin-columns.css';
		$supportedplugins['contact-form-7'] = $this->path . 'assets/css/plugins/plugin_contact-form-7.css';
		$supportedplugins['jetpack'] = $this->path . 'assets/css/plugins/plugin_jetpack.css';
		$supportedplugins['litespeed-cache'] = $this->path . 'assets/css/plugins/plugin_litespeed-cache.css';
		$supportedplugins['mailpoet'] = $this->path . 'assets/css/plugins/plugin_mailpoet.css';
		$supportedplugins['meta-box'] = $this->path . 'assets/css/plugins/plugin_meta-box.css';
		$supportedplugins['modern-events-calendar-lite'] = $this->path . 'assets/css/plugins/plugin_modern-events-calendar-lite.css';
		$supportedplugins['redirection'] = $this->path . 'assets/css/plugins/plugin_redirection.css';
		$supportedplugins['seo-by-rank-math'] = $this->path . 'assets/css/plugins/plugin_seo-by-rank-math.css';
		$supportedplugins['sfwd-lms'] = $this->path . 'assets/css/plugins/plugin_sfwd-lms.css';
		$supportedplugins['updraftplus'] = $this->path . 'assets/css/plugins/plugin_updraftplus.css';
		$supportedplugins['visualizer'] = $this->path . 'assets/css/plugins/plugin_visualizer.css';
		$supportedplugins['w3-total-cache'] = $this->path . 'assets/css/plugins/plugin_w3-total-cache.css';
		$supportedplugins['wordpress-seo'] = $this->path . 'assets/css/plugins/plugin_wordpress-seo.css';
		$supportedplugins['wp-seopress'] = $this->path . 'assets/css/plugins/plugin_wp-seopress.css';
		$supportedplugins['wpforms-lite'] = $this->path . 'assets/css/plugins/plugin_wpforms.css';
		$supportedplugins['wpforms'] = $this->path . 'assets/css/plugins/plugin_wpforms.css';
		$supportedplugins['wp-rocket'] = $this->path . 'assets/css/plugins/plugin_wp-rocket.css';
		$supportedplugins['wpdatatables'] = $this->path . 'assets/css/plugins/plugin_wpdatatables.css';
		
		
		return $supportedplugins;
	}
	
	
	/**
	 * White lists scripts for fluent forms
	 * @since 1.4
	 */
	public function admin2020_fluentform_styles($isSkip){
		return array('uikit','uikit-icons','admin2020-utilities');
	}
	
	/**
	 * White lists styles for mailpoet
	 * @since 1.4
	 */
	
	public function admin2020_mailPoet_styles($styles){
		
		$styles[] = 'admin-2020';
		
		return $styles;
		
	}
	
	/**
	 * White lists scripts for mailpoet
	 * @since 1.4
	 */
	
	public function admin2020_mailPoet_scripts($scripts){
		
		$scripts[] = 'admin-2020';
		
		return $scripts;
		
	}
	
	/**
	 * Enqueue Admin 2020 styles
	 * @since 1.4
	 */
	 
	 public function add_styles(){
		 
		///ENSURE WE ARE NOT LOADING ON FRONT UNLESS NECESSARY
		global $pagenow;
		if(!is_user_logged_in() && $pagenow != 'wp-login.php'){
			return;
		}
		 
		///GOOGLE FONTS
		wp_register_style('custom-google-fonts', 'https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap', array());
		wp_enqueue_style('custom-google-fonts');
			
		if(is_rtl()){
		    ////RTL	
			wp_register_style('admin2020_app', $this->path . 'assets/css/app_rtl.css', array(), $this->version);
			wp_enqueue_style('admin2020_app');
			
		}  else {
			////LTR
			wp_register_style('admin2020_app', $this->path . 'assets/css/app.css', array(), $this->version);
			wp_enqueue_style('admin2020_app');
		}
	 }
	 
	 
	 /**
	  * Enqueue Admin 2020 styles for front
	  * @since 1.4
	  */
	  
	  public function add_styles_front(){
		  
		 ///ENSURE WE ARE NOT LOADING ON FRONT UNLESS NECESSARY
		 global $pagenow;
		 if(!is_user_logged_in() && $pagenow != 'wp-login.php'){
			 return;
		 }
		  
		 ///GOOGLE FONTS
		 wp_register_style('custom-google-fonts', 'https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap', array());
		 wp_enqueue_style('custom-google-fonts');
			 
		 if(is_rtl()){
			 ////RTL	
			 wp_register_style('admin2020_app', $this->path . 'assets/css/app_front_rtl.css', array(), $this->version);
			 wp_enqueue_style('admin2020_app');
			 
		 }  else {
			 ////LTR
			 wp_register_style('admin2020_app', $this->path . 'assets/css/app_front.css', array(), $this->version);
			 wp_enqueue_style('admin2020_app');
		 }
	  }
	 
	 /**
	  * Enqueue Admin 2020 scripts
	  * @since 1.4
	  */
	  
	  public function add_scripts(){
		  
		///UIKIT FRAMEWORK
		wp_enqueue_script('uikit', $this->path . 'assets/js/uikit/uikit.min.js', array('jquery'));
		wp_enqueue_script('uikit-icons', $this->path . 'assets/js/uikit/uikit-icons.min.js', array('jquery'));
		///ADMIN 2020 SCRIPTS
		wp_enqueue_script('admin2020-utilities', $this->path . 'assets/js/admin2020/utilities.min.js', array('jquery'));
		wp_localize_script('admin2020-utilities', 'admin2020_utilities_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'security' => wp_create_nonce('admin2020-utilities-security-nonce'),
		));
		  
	  }
	
	/**
	* Output body classes
	* @since 1
	*/
	
	public function add_body_classes($classes) {
		
		$darkmode = $this->utils->get_user_preference('darkmode');
		$menu_state = $this->utils->get_user_preference('menu_collapse');
		$shrunk_enabled = $this->utils->get_option('admin2020_admin_menu','shrunk-enabled');
		$dark_enabled = $this->utils->get_option('admin2020_admin_bar','dark-enabled');
		$bodyclass = '';
	
		if ($darkmode == 'true') {
			$bodyclass = " a2020_night_mode";
		} else if ($darkmode == '' && $dark_enabled == 'true'){
			$bodyclass = " a2020_night_mode";
		}
		
		if ($menu_state == 'true') {
			$bodyclass = $bodyclass . " a2020_menu_small";
		} else if ($menu_state == '' && $shrunk_enabled == 'true'){
			$bodyclass = $bodyclass . " a2020_menu_small";
		} 
		
		return $classes.$bodyclass;
	}

}
