<?php
if (!defined('ABSPATH')) {
    exit();
}

class Admin_2020_module_admin_theme
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
		
		///DISABLE IF CUSTOMISER
		global $pagenow;
		if($pagenow == 'customize.php'){
			return;
		}
		
		
        add_action('admin_enqueue_scripts', [$this, 'add_styles'], 0);
		add_action('admin_enqueue_scripts', [$this, 'add_scripts'], 0);
		add_action('admin_enqueue_scripts', [$this, 'remove_styles'], 99999);
		add_action('admin_head',array($this,'add_body_styles'),0);
		add_filter('admin_body_class', array($this, 'add_body_classes'));
		
		
		
    }
	
	/**
	 * Loads menu actions
	 * @since 1.0
	 */

	public function build_front()
	{
		
		if(!$this->utils->enabled($this)){
			return;
		}
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		
		if($this->utils->is_locked($optionname)){
			return;
		}
		
		///DISABLE IF CUSTOMISER
		global $pagenow;
		if($pagenow == 'customize.php'){
			return;
		}
		
		add_action('login_head',array($this,'add_body_styles_front'));
		add_action('wp_head',array($this,'add_body_styles'));
		
		
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
		$data['title'] = __('Theme','admin2020');
		$data['option_name'] = 'admin2020_admin_theme';
		$data['description'] = __('Creates the main theme for admin 2020. Disables default WordPress theme and applies admin 2020.','admin2020');
		return $data;
		
	}
	/**
	 * Returns settings for module
	 * @since 1.4
	 */
	 public function render_settings(){
		  
		  
		  $info = $this->component_info();
		  $optionname = $info['option_name'];
		  
		  $dark_background = $this->utils->get_option($optionname,'dark-background');
		  $light_background = $this->utils->get_option($optionname,'light-background');
		  $dark_primary = $this->utils->get_option($optionname,'dark-primary-color');
		  $light_primary = $this->utils->get_option($optionname,'light-primary-color');
		  $card_padding = $this->utils->get_option($optionname,'card-padding');
		  
		  $disabled_for = $this->utils->get_option($optionname,'disabled-for');
		  if($disabled_for == ""){
			  $disabled_for = array();
		  }
		  ///GET ROLES
		  global $wp_roles;
		  ///GET USERS
		  $blogusers = get_users();
		  ?>
		  <div class="uk-grid" id="a2020_theme_settings" uk-grid>
			  <!-- LOCKED FOR USERS / ROLES -->
			  <div class="uk-width-1-1@ uk-width-1-3@m">
				  <div class="uk-h5 "><?php _e('Theme Disabled for','admin2020')?></div>
				  <div class="uk-text-meta"><?php _e("Admin 2020 theme will be disabled for any users or roles you select",'admin2020') ?></div>
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
					  jQuery('#a2020_theme_settings #a2020-role-types').tokenize2({
						  placeholder: '<?php _e('Select roles or users','admin2020') ?>'
					  });
					  jQuery(document).ready(function ($) {
						  $('#a2020_theme_settings #a2020-role-types').on('tokenize:select', function(container){
							  $(this).tokenize2().trigger('tokenize:search', [$(this).tokenize2().input.val()]);
						  });
					  })
				  </script>
				  
			  </div>	
			  
			  <div class="uk-width-1-1@ uk-width-1-3@m"></div>
			  
			  <!-- BACKGROUND COLOUR -->
			  <div class="uk-width-1-1@ uk-width-1-3@m">
				  <div class="uk-h5 "><?php _e('Background Color','admin2020')?></div>
				  <div class="uk-text-meta"><?php _e("Sets a background colour for admin pages.",'admin2020') ?></div>
			  </div>
			  <div class="uk-width-1-1@ uk-width-1-3@m">
				  <div class="uk-h5"><?php _e('Light','admin2020')?></div>
				  
				  <input class=" a2020_setting" id="light-background" 
				  module-name="<?php echo $optionname?>" 
				  name="light-background" 
				  type="text"
				  data-default-color="#f8f8f8"
				  value="<?php echo $light_background?>">
				  
			  </div>	
			  
			  <script>
				  jQuery(document).ready(function($){
					  $('#a2020_theme_settings #light-background').wpColorPicker();
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
					  $('#a2020_theme_settings #dark-background').wpColorPicker();
				  });
			  </script>
			  
			  
			  <!-- PRIMARY LINK COLOUR -->
				<div class="uk-width-1-1@ uk-width-1-3@m">
					<div class="uk-h5 "><?php _e('Primary Color','admin2020')?></div>
					<div class="uk-text-meta"><?php _e("Sets the primary color throughout Admin 2020.",'admin2020') ?></div>
				</div>
				<div class="uk-width-1-1@ uk-width-1-3@m">
					
					<input class=" a2020_setting" id="light-primary-color" 
					module-name="<?php echo $optionname?>" 
					name="light-primary-color" 
					type="text"
					data-default-color="#0c5cef"
					value="<?php echo $light_primary?>">
					
				</div>	
				
				<script>
					jQuery(document).ready(function($){
						$('#a2020_theme_settings #light-primary-color').wpColorPicker();
					});
				</script>
				
				<div class="uk-width-1-1@ uk-width-1-3@m">
					
					<input class="a2020_setting" id="dark-primary-color" 
					module-name="<?php echo $optionname?>" 
					name="dark-primary-color" 
					type="text"
					data-default-color="#0c5cef"
					value="<?php echo $dark_primary?>">
					
				</div>	
				
				<script>
					jQuery(document).ready(function($){
						$('#a2020_theme_settings #dark-primary-color').wpColorPicker();
					});
				</script>
				
				
				<!-- PRIMARY LINK COLOUR -->
				<div class="uk-width-1-1@ uk-width-1-3@m">
					<div class="uk-h5 "><?php _e('Padding','admin2020')?></div>
					<div class="uk-text-meta"><?php _e("Sets padding (in px) for cards, metaboxes and other items in the UI.",'admin2020') ?></div>
				</div>
				<div class="uk-width-1-1@ uk-width-2-3@m">
					
					<input class="a2020_setting" id="card-padding" 
					module-name="<?php echo $optionname?>" 
					name="card-padding" 
					type="number"
					placeholder="20"
					value="<?php echo $card_padding?>">
					
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
		
        wp_register_style(
            'admin2020_admin_theme',
            $this->path . 'assets/css/modules/admin-theme.css',
            array(),
            $this->version
        );
        wp_enqueue_style('admin2020_admin_theme');
    }
	
	/**
	* Enqueue Admin Bar 2020 scripts
	* @since 1.4
	*/
	
	public function add_scripts(){
	  
	  ///UIKIT FRAMEWORK
	  wp_enqueue_script('admin-theme-js', $this->path . 'assets/js/admin2020/admin-theme.min.js', array('jquery'));
	  wp_localize_script('admin-theme-js', 'admin2020_admin_theme_ajax', array(
		  'ajax_url' => admin_url('admin-ajax.php'),
		  'security' => wp_create_nonce('admin2020-admin-theme-security-nonce'),
	  ));
	  
	}
	
	/**
	* Output body classes
	* @since 1 
	*/
	
	public function add_body_classes($classes) {
		
		$darkmode = $this->utils->get_user_preference('darkmode');
		$dark_enabled = $this->utils->get_option('admin2020_admin_bar','dark-enabled');
		$bodyclass = ' a2020_dark_anchor a2020_admin_theme';
	
		if ($darkmode == 'true') {
			$bodyclass = $bodyclass." uk-light";
		} else if ($darkmode == '' && $dark_enabled == 'true'){
			$bodyclass = $bodyclass." uk-light";
		}
		
		return $classes.$bodyclass;
	}
	
	/**
	* Removes wp default menu styling
	* @since 1.4
	*/
	
	public function remove_styles(){
		
		return;
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
	* Adds custom css for custom background colors
	* @since 1.4
	*/
	
	public function add_body_styles(){
		
		if(!is_user_logged_in()){
			return;
		}
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		$light_background = $this->utils->get_option($optionname,'light-background');
		$dark_background = $this->utils->get_option($optionname,'dark-background');
		$dark_enabled = $this->utils->get_option('admin2020_admin_bar','dark-enabled');
		
		$light_primary = $this->utils->get_option($optionname,'light-primary-color');
		$dark_primary = $this->utils->get_option($optionname,'dark-primary-color');
		$card_padding = $this->utils->get_option($optionname,'card-padding');
		$darkmode = $this->utils->get_user_preference('darkmode');
		
		if ($light_background != ""){
		  echo '<style type="text/css">';
		  echo '#wpwrap { background-color: ' . $light_background . '}';
		  echo '</style>';
		}
		
		
		if ($dark_background != ""){
		  echo '<style type="text/css">';
		  echo 'body.a2020_night_mode #wpwrap { background-color: ' . $dark_background . '}';
		  echo '</style>';
		}
		
		if ($card_padding != ""){
		  
		  echo '<style type="text/css">';
		  echo ':root { --a2020-card-padding:' . $card_padding . 'px}';
		  echo '</style>';
		}
		
		if ($light_primary != "" && $darkmode != "true"){
			
		  $wash = $this->color_luminance($light_primary,1);
		  $final_wash = $this->hex2rgb($wash);
		  
		  echo '<style type="text/css">';
		  echo ':root { --a2020-primary:' . $light_primary . '}';
		  echo ':root { --a2020-primary-darker:' . $this->color_luminance($light_primary,-0.3) . '}';
		  echo ':root { --a2020-primary-darker-extra:' . $this->color_luminance($light_primary,-0.5) . '}';
		  echo ':root { --a2020-primary-darker-dark:' . $this->color_luminance($light_primary,-0.7) . '}';
		  echo ':root { --a2020-primary-lighter:' . $this->color_luminance($light_primary,2) . '}';
		  echo ':root { --a2020-primary-wash: rgba(' . $final_wash . ',0.1)}';
		  echo '</style>';
		}
		
		if ($dark_primary != "" && $darkmode == "true"){
			
		  $wash = $this->color_luminance($dark_primary,3);
		  $final_wash = $this->hex2rgb($wash);
		  
		  echo '<style type="text/css">';
		  echo ':root { --a2020-primary:' . $dark_primary . '}';
		  echo ':root { --a2020-primary-darker:' . $this->color_luminance($dark_primary,-0.3) . '}';
		  echo ':root { --a2020-primary-darker-extra:' . $this->color_luminance($dark_primary,-0.5) . '}';
		  echo ':root { --a2020-primary-lighter:' . $this->color_luminance($dark_primary,2) . '}';
		  echo ':root { --a2020-primary-darker-dark:' . $this->color_luminance($dark_primary,-0.7) . '}';
		  echo ':root { --a2020-primary-wash: rgba(' . $final_wash . ',0.1)}';
		  echo '</style>';
		}
		
		if ($darkmode == '' && $dark_enabled == 'true' && $dark_primary != "" ){
			
			$wash = $this->color_luminance($dark_primary,3);
			$final_wash = $this->hex2rgb($wash);
			
			echo '<style type="text/css">';
			echo ':root { --a2020-primary:' . $dark_primary . '}';
			echo ':root { --a2020-primary-darker:' . $this->color_luminance($dark_primary,-0.3) . '}';
			echo ':root { --a2020-primary-darker-extra:' . $this->color_luminance($dark_primary,-0.5) . '}';
			echo ':root { --a2020-primary-lighter:' . $this->color_luminance($dark_primary,2) . '}';
			echo ':root { --a2020-primary-darker-dark:' . $this->color_luminance($dark_primary,-0.7) . '}';
			echo ':root { --a2020-primary-wash: rgba(' . $final_wash . ',0.1)}';
			echo '</style>';
			  
		}
	}
	
	/**
	* Adds custom css for custom background colors on login page
	* @since 1.4
	*/
	
	public function add_body_styles_front(){
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		$light_background = $this->utils->get_option($optionname,'light-background');
		$dark_background = $this->utils->get_option($optionname,'dark-background');
		
		$light_primary = $this->utils->get_option($optionname,'light-primary-color');
		$dark_primary = $this->utils->get_option($optionname,'dark-primary-color');
		$darkmode = $this->utils->get_option('admin2020_admin_login','dark-enabled');
		
		if ($light_primary != "" && $darkmode != "true"){
			
		  $wash = $this->color_luminance($light_primary,1);
		  $final_wash = $this->hex2rgb($wash);
		  
		  echo '<style type="text/css">';
		  echo ':root { --a2020-primary:' . $light_primary . '}';
		  echo ':root { --a2020-primary-darker:' . $this->color_luminance($light_primary,-0.3) . '}';
		  echo ':root { --a2020-primary-darker-extra:' . $this->color_luminance($light_primary,-0.5) . '}';
		  echo ':root { --a2020-primary-lighter:' . $this->color_luminance($light_primary,2) . '}';
		  echo ':root { --a2020-primary-wash: rgba(' . $final_wash . ',0.1)}';
		  echo '</style>';
		}
		
		if ($dark_primary != "" && $darkmode == "true"){
			
		  $wash = $this->color_luminance($dark_primary,3);
		  $final_wash = $this->hex2rgb($wash);
		  
		  echo '<style type="text/css">';
		  echo ':root { --a2020-primary:' . $dark_primary . '}';
		  echo ':root { --a2020-primary-darker:' . $this->color_luminance($dark_primary,-0.3) . '}';
		  echo ':root { --a2020-primary-darker-extra:' . $this->color_luminance($dark_primary,-0.5) . '}';
		  echo ':root { --a2020-primary-lighter:' . $this->color_luminance($dark_primary,2) . '}';
		  echo ':root { --a2020-primary-wash: rgba(' . $final_wash . ',0.1)}';
		  echo '</style>';
		}
	}
	
	
	public function color_luminance( $hex, $percent ) {
	
			// validate hex string
	
			$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );
			$new_hex = '#';
	
			if ( strlen( $hex ) < 6 ) {
				$hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
			}
	
			// convert to decimal and change luminosity
			for ($i = 0; $i < 3; $i++) {
				$dec = hexdec( substr( $hex, $i*2, 2 ) );
				$dec = min( max( 0, $dec + $dec * $percent ), 255 );
				$new_hex .= str_pad( dechex( $dec ) , 2, 0, STR_PAD_LEFT );
			}
	
			return $new_hex;
	}
	
	public function hex2rgb( $colour ) {
			if ( $colour[0] == '#' ) {
					$colour = substr( $colour, 1 );
			}
			if ( strlen( $colour ) == 6 ) {
					list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
			} elseif ( strlen( $colour ) == 3 ) {
					list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
			} else {
					return false;
			}
			$r = hexdec( $r );
			$g = hexdec( $g );
			$b = hexdec( $b );
			return  $r.','.$g.','.$b;
	}
	
	
}
