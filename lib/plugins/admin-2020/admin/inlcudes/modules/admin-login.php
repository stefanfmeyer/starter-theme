<?php
if (!defined('ABSPATH')) {
    exit();
}

class Admin_2020_module_admin_login
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
		
    }
	
	public function build_front(){
		
		if(!$this->utils->enabled($this)){
			return;
		}
		
		add_action('login_head', [$this, 'add_styles'], 0);
		add_filter('login_body_class', array($this, 'add_body_classes'));
		add_filter('login_redirect', array($this,'redirectToOverview'), 10, 3 );
		add_filter( 'login_headerurl', array($this,'login_logo_url') );
		
	}
	
	public function login_logo_url($url) {
		return get_home_url();
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
	 * redirects to overview page after login
	 * @since 1.4
	 */
	public function redirectToOverview($redirect_to, $request, $user){
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		$redirect = $this->utils->get_option($optionname,'login-redirect');
		
		if($redirect == 'true'){
			return home_url() . "/wp-admin/admin.php?page=admin_2020_overview";
		} else {
			return $redirect_to;
		}
	}
	
	/**
	 * Returns component info for settings page
	 * @since 1.4
	 */
	public function component_info(){
		
		$data = array();
		$data['title'] = __('Login','admin2020');
		$data['option_name'] = 'admin2020_admin_login';
		$data['description'] = __('Styles the admin login page.','admin2020');
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
		 
		 $background = $this->utils->get_option($optionname,'login-background');
		 $dark_enabled = $this->utils->get_option($optionname,'dark-enabled');
		 $redirect = $this->utils->get_option($optionname,'login-redirect');
		 
		 ?>
		 <div class="uk-grid" id="a2020_login_settings" uk-grid>
			 <!-- LOGO SETTINGS -->
			 <div class="uk-width-1-1@ uk-width-1-3@m">
				 <div class="uk-h5 "><?php _e('Admin Logo','admin2020')?></div>
				 <div class="uk-text-meta"><?php _e("Sets an optional background image for the login page.",'admin2020') ?></div>
			 </div>
			 <div class="uk-width-1-1@ uk-width-1-3@m">
				 
				 <input class="uk-input uk-margin-bottom a2020_setting" id="login-background" 
				 module-name="<?php echo $optionname?>" 
				 name="login-background" 
				 placeholder="<?php _e('Login background url','admin2020')?>"
				 value="<?php echo $background?>">
				 
				 <button class="uk-button uk-button-default" type="button" id="a2020_select_login_background"><?php _e('Select login background','admin2020')?></button>
				 <img class="uk-image uk-margin-left" id="a2020_login_background_preview" src="<?php echo $background?>" style="height:40px;">
			 </div>	
			 <div class="uk-width-1-1@ uk-width-1-3@m">
			 </div>
			 
			 
			 <!-- DARK MODE -->
			 <div class="uk-width-1-1@ uk-width-1-3@m">
				 <div class="uk-h5 "><?php _e('Dark Mode','admin2020')?></div>
				 <div class="uk-text-meta"><?php _e("Login style will match dark theme if enabled",'admin2020') ?></div>
			 </div>
			 <div class="uk-width-1-1@ uk-width-2-3@m">
				 
				 <?php
				 $checked = '';
				 if($dark_enabled == 'true'){
					 $checked = 'checked';
				 }
				 ?>
				 
				 <label class="admin2020_switch uk-margin-left">
					 <input class="a2020_setting" name="dark-enabled" module-name="<?php echo $optionname?>" type="checkbox" <?php echo $checked ?>>
					 <span class="admin2020_slider constant_dark"></span>
				 </label>
				 
			 </div>	
			 
			 <!-- REDIRECT LOGIN -->
			  <div class="uk-width-1-1@ uk-width-1-3@m">
				  <div class="uk-h5 "><?php _e('Redirect to overview page','admin2020')?></div>
				  <div class="uk-text-meta"><?php _e("If enabled, after logging in users will be redirected to the overview page",'admin2020') ?></div>
			  </div>
			  <div class="uk-width-1-1@ uk-width-2-3@m">
				  
				  <?php
				  $checked = '';
				  if($redirect == 'true'){
					  $checked = 'checked';
				  }
				  ?>
				  
				  <label class="admin2020_switch uk-margin-left">
					  <input class="a2020_setting" name="login-redirect" module-name="<?php echo $optionname?>" type="checkbox" <?php echo $checked ?>>
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
		
        ///GOOGLE FONTS
		wp_register_style('custom-google-fonts', 'https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap', array());
		wp_enqueue_style('custom-google-fonts');
		///UIKIT FRAMEWORK
		wp_register_style('admin2020_app', $this->path . 'assets/css/app.css', array(), $this->version);
		wp_enqueue_style('admin2020_app');
		///A2020 THEME
		wp_register_style('admin2020_theme', $this->path . 'assets/css/modules/admin-theme.css', array(), $this->version);
		wp_enqueue_style('admin2020_theme');
		///LOGIN STYLES
		wp_register_style('admin2020_login', $this->path . 'assets/css/modules/admin-login.css', array(), $this->version);
		wp_enqueue_style('admin2020_login');
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		
		$logo = $this->utils->get_logo('admin2020_admin_bar');
		$darkmode = $this->utils->get_option($optionname,'dark-enabled');
		
		if($darkmode == 'true'){
			$logo = $this->utils->get_dark_logo('admin2020_admin_bar');
		}
		
		$background = $this->utils->get_option($optionname,'login-background');
		
		?>
		<style type="text/css"> h1 a {  background-image:url('<?php echo $logo?>')  !important; } </style>
		<?php
		
		if($background != ''){
			?>
			<style type="text/css"> body {  background-image:url('<?php echo $background?>')  !important; } </style>
			<?php
		}
		
    }
	
	
	/**
	* Output body classes
	* @since 1 
	*/
	
	public function add_body_classes($classes) {
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		$darkmode = $this->utils->get_option($optionname,'dark-enabled');
	
		if ($darkmode == 'true') {
			$classes[] = "a2020_night_mode uk-light";
		}
		
		return $classes;
	}
	
	
}
