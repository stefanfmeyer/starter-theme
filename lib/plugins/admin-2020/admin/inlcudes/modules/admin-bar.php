<?php
if (!defined('ABSPATH')) {
    exit();
}

class Admin_2020_module_admin_bar
{
    public function __construct($version, $path, $utilities)
    {
        $this->version = $version;
        $this->path = $path;
        $this->utils = $utilities;
		$this->front = false;
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
		
		
        add_action('admin_head', [$this, 'rebuild_admin_bar']);
        add_action('admin_enqueue_scripts', [$this, 'add_styles'], 0);
		add_action('admin_enqueue_scripts', [$this, 'add_scripts'], 0);
		add_filter('admin_body_class', array($this, 'add_body_classes'));
		add_action('wp_ajax_a2020_master_search', array($this,'a2020_master_search'));
		
		
    }
	
	/**
	* Output body classes
	* @since 1 
	*/
	
	public function add_body_classes($classes) {
		
		$bodyclass = " a2020_admin_bar";
		
		return $classes.$bodyclass;
	}
	
	
	/**
	 * Loads admin bar on front
	 * @since 1.0
	 */
	public function start_front(){
		
		
		
		if(!$this->utils->enabled($this)){
			return;
		}
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		$admin_front = $this->utils->get_option($optionname,'load-front');
		$hide_admin = $this->utils->get_option($optionname,'hide-admin');
		
		if($this->utils->is_locked($optionname)){
			return;
		}
		
		if($hide_admin == 'true') {
			add_filter('show_admin_bar', 'is_blog_admin');
			return;
		}
		
		if($admin_front != 'true') {
			return;
		}
		
		$this->front = true;
		
		add_action('wp_head', [$this, 'rebuild_admin_bar']);
		add_action('wp_head',array($this,'add_body_styles'));
		add_action('wp_body_open', [$this, 'output_admin_front']);
		
		add_action('wp_enqueue_scripts', [$this, 'add_styles'], 99);
		add_action('wp_enqueue_scripts', [$this, 'add_scripts'], 0);
		
		add_action('wp_ajax_a2020_master_search', array($this,'a2020_master_search'));
		
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
		$data['title'] = __('Admin Bar','admin2020');
		$data['option_name'] = 'admin2020_admin_bar';
		$data['description'] = __('Creates new admin bar, adds user off canvas menu and builds global search','admin2020');
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
		
		$light_logo = $this->utils->get_option($optionname,'light-logo');
		$dark_logo = $this->utils->get_option($optionname,'dark-logo');
		$search_enabled = $this->utils->get_option($optionname,'search-enabled');
		$new_enabled = $this->utils->get_option($optionname,'new-enabled');
		$view_enabled = $this->utils->get_option($optionname,'view-enabled');
		$post_types_enabled = $this->utils->get_option($optionname,'post-types-search');
		$post_types_create = $this->utils->get_option($optionname,'post-types-create');
		$light_background = $this->utils->get_option($optionname,'light-background');
		$dark_background = $this->utils->get_option($optionname,'dark-background');
		$dark_enabled = $this->utils->get_option($optionname,'dark-enabled');
		$admin_hidden = $this->utils->get_option($optionname,'hide-admin');
		$admin_front = $this->utils->get_option($optionname,'load-front');
		///GET POST TYPES
		$post_types = $this->utils->get_post_types();
		
		$disabled_for = $this->utils->get_option($optionname,'disabled-for');
		if($disabled_for == ""){
			$disabled_for = array();
		}
		if($post_types == ""){
			$post_types = array();
		}
		if($post_types_enabled == ""){
			$post_types_enabled = array();
		}
		if($post_types_create == ""){
			$post_types_create = array();
		}
		///GET ROLES
		global $wp_roles;
		///GET USERS
		$blogusers = get_users();
		
		?>
		<div class="uk-grid" id="a2020_bar_settings" uk-grid>
			<!-- LOGO SETTINGS -->
			<div class="uk-width-1-1@ uk-width-1-3@m">
				<div class="uk-h5 "><?php _e('Admin Logo','admin2020')?></div>
				<div class="uk-text-meta"><?php _e("Sets the logo for the admin bar and also the logo for the login page if login styles is enabled. If no logo is set for dark mode then then the light logo will be used.",'admin2020') ?></div>
			</div>
			<div class="uk-width-1-1@ uk-width-1-3@m">
				<div class="uk-h5"><?php _e('Light','admin2020')?></div>
				
				<input class="uk-input uk-margin-bottom a2020_setting" id="light-logo-url" 
				module-name="<?php echo $optionname?>" 
				name="light-logo" 
				placeholder="<?php _e('Light Logo Url','admin2020')?>"
				value="<?php echo $light_logo?>">
				
				<button class="uk-button uk-button-default" type="button" id="a2020_select_light_logo"><?php _e('Select Light Logo','admin2020')?></button>
				<img class="uk-image uk-margin-left" id="a2020_light_logo_preview" src="<?php echo $light_logo?>" style="height:40px;">
			</div>	
			<div class="uk-width-1-1@ uk-width-1-3@m">
				<div class="uk-h5"><?php _e('Dark','admin2020')?></div>
				
				<input class="uk-input uk-margin-bottom a2020_setting" id="dark-logo-url" 
				module-name="<?php echo $optionname?>" 
				name="dark-logo" 
				placeholder="<?php _e('Dark Logo Url','admin2020')?>"
				value="<?php echo $dark_logo?>">
				
				<button class="uk-button uk-button-default" type="button" id="a2020_select_dark_logo"><?php _e('Select Dark Logo','admin2020')?></button>
				<img class="uk-image uk-margin-left" id="a2020_dark_logo_preview" src="<?php echo $dark_logo?>" style="height:40px;">
			</div>	
			
			<!-- BACKGROUND COLOUR -->
			<div class="uk-width-1-1@ uk-width-1-3@m">
				<div class="uk-h5 "><?php _e('Background Color','admin2020')?></div>
				<div class="uk-text-meta"><?php _e("Sets a background colour for the admin bar.",'admin2020') ?></div>
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
					$('#a2020_bar_settings #light-background').wpColorPicker();
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
					$('#a2020_bar_settings #dark-background').wpColorPicker();
				});
			</script>
			
			<div class="uk-width-1-1">
				<hr >
			</div>
			
			<!-- LOCKED FOR USERS / ROLES -->
			<div class="uk-width-1-1@ uk-width-1-3@m">
			  <div class="uk-h5 "><?php _e('Admin Bar Disabled for','admin2020')?></div>
			  <div class="uk-text-meta"><?php _e("Admin 2020 admin bar will be disabled for any users or roles you select",'admin2020') ?></div>
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
				  jQuery('#a2020_bar_settings #a2020-role-types').tokenize2({
					  placeholder: '<?php _e('Select roles or users','admin2020') ?>'
				  });
				  jQuery(document).ready(function ($) {
					  $('#a2020_bar_settings #a2020-role-types').on('tokenize:select', function(container){
						  $(this).tokenize2().trigger('tokenize:search', [$(this).tokenize2().input.val()]);
					  });
				  })
			  </script>
			  
			</div>
			<div class="uk-width-1-1@ uk-width-1-3@m">
			</div>
			
			<!-- DISABLE SEARCH -->
			<div class="uk-width-1-1@ uk-width-1-3@m">
				<div class="uk-h5 "><?php _e('Disable Search','admin2020')?></div>
				<div class="uk-text-meta"><?php _e("Disables search icon and global search function from admin bar",'admin2020') ?></div>
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
			<!-- DISABLE NEW BUTTON -->
			<div class="uk-width-1-1@ uk-width-1-3@m">
				<div class="uk-h5 "><?php _e('Disable Create Button','admin2020')?></div>
				<div class="uk-text-meta"><?php _e("Disables the 'new' button in the admin bar",'admin2020') ?></div>
			</div>
			<div class="uk-width-1-1@ uk-width-2-3@m">
				
				<?php
				$checked = '';
				if($new_enabled == 'true'){
					$checked = 'checked';
				}
				?>
				
				<label class="admin2020_switch uk-margin-left">
					<input class="a2020_setting" name="new-enabled" module-name="<?php echo $optionname?>" type="checkbox" <?php echo $checked ?>>
					<span class="admin2020_slider constant_dark"></span>
				</label>
				
			</div>	
			
			<!-- DISABLE VIEW WEBSITE BUTTON -->
			<div class="uk-width-1-1@ uk-width-1-3@m">
				<div class="uk-h5 "><?php _e('Disable View Website Button','admin2020')?></div>
				<div class="uk-text-meta"><?php _e("Disables the view website link button in the admin bar",'admin2020') ?></div>
			</div>
			<div class="uk-width-1-1@ uk-width-2-3@m">
				
				<?php
				$checked = '';
				if($view_enabled == 'true'){
					$checked = 'checked';
				}
				?>
				
				<label class="admin2020_switch uk-margin-left">
					<input class="a2020_setting" name="view-enabled" module-name="<?php echo $optionname?>" type="checkbox" <?php echo $checked ?>>
					<span class="admin2020_slider constant_dark"></span>
				</label>
				
			</div>
			
			<!-- DARK MODE AS DEFAULT -->
			<div class="uk-width-1-1@ uk-width-1-3@m">
				<div class="uk-h5 "><?php _e('Set Dark Mode as Default','admin2020')?></div>
				<div class="uk-text-meta"><?php _e("If enabled, dark mode will default to true for users that haven't set a preference.",'admin2020') ?></div>
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
			
			<!-- LOAD ADMIN 2020 ADMIN BAR ON FRONT ENDS -->
			<div class="uk-width-1-1@ uk-width-1-3@m">
				<div class="uk-h5 "><?php _e('Load admin 2020 bar on front end','admin2020')?></div>
				<div class="uk-text-meta"><?php _e("If enabled, admin 2020 admin bar will be load on the front end. Please note, this will not work on all themes and styling will vary.",'admin2020') ?></div>
			</div>
			<div class="uk-width-1-1@ uk-width-2-3@m">
				
				<?php
				$checked = '';
				if($admin_front == 'true'){
					$checked = 'checked';
				}
				?>
				
				<label class="admin2020_switch uk-margin-left">
					<input class="a2020_setting" name="load-front" module-name="<?php echo $optionname?>" type="checkbox" <?php echo $checked ?>>
					<span class="admin2020_slider constant_dark"></span>
				</label>
				
			</div>
			
			<!-- HIDE ADMIN BAR ON FRONT END -->
			<div class="uk-width-1-1@ uk-width-1-3@m">
				<div class="uk-h5 "><?php _e('Hide admin bar on front end','admin2020')?></div>
				<div class="uk-text-meta"><?php _e("If enabled, front end admin bar will not load.",'admin2020') ?></div>
			</div>
			<div class="uk-width-1-1@ uk-width-2-3@m">
				
				<?php
				$checked = '';
				if($admin_hidden == 'true'){
					$checked = 'checked';
				}
				?>
				
				<label class="admin2020_switch uk-margin-left">
					<input class="a2020_setting" name="hide-admin" module-name="<?php echo $optionname?>" type="checkbox" <?php echo $checked ?>>
					<span class="admin2020_slider constant_dark"></span>
				</label>
				
			</div>
			
			<!-- POST TYPES AVAILABLE IN SEARCH -->
			<div class="uk-width-1-1@ uk-width-1-3@m">
				<div class="uk-h5 "><?php _e('Post Types available in Search','admin2020')?></div>
				<div class="uk-text-meta"><?php _e("The global search will only search the selected post types.",'admin2020') ?></div>
			</div>
			<div class="uk-width-1-1@ uk-width-1-3@m">
				
				
				<select class="a2020_setting" id="a2020-post-types" name="post-types-search" module-name="<?php echo $optionname?>" multiple>
					<?php
					foreach ($post_types as $type){
						$name = $type->name;
						$label = $type->label;
						$sel = '';
						
						if(in_array($name, $post_types_enabled)){
							$sel = 'selected';
						}
						?>
						<option value="<?php echo $name ?>" <?php echo $sel?>><?php echo $label ?></option>
						<?php
					}
					?>
				</select>
				
				<script>
					jQuery('#a2020_bar_settings #a2020-post-types').tokenize2({
						placeholder: '<?php _e('Select Post Types','admin2020') ?>'
					});
					jQuery(document).ready(function ($) {
						$('#a2020_bar_settings #a2020-post-types').on('tokenize:select', function(container){
							$(this).tokenize2().trigger('tokenize:search', [$(this).tokenize2().input.val()]);
						});
					})
				</script>
				
			</div>	
			
			<div class="uk-width-1-1@ uk-width-1-3@m">
				</div>
			<!-- POST TYPES AVAILABLE IN NEW BUTTON -->
				<div class="uk-width-1-1@ uk-width-1-3@m">
					<div class="uk-h5 "><?php _e('Post Types available in create button (new)','admin2020')?></div>
					<div class="uk-text-meta"><?php _e("Only the selected post types will show up in the create dropdown.",'admin2020') ?></div>
				</div>
				<div class="uk-width-1-1@ uk-width-1-3@m">
					
					
					<select class="a2020_setting" id="a2020-create-post-types" name="post-types-create" module-name="<?php echo $optionname?>" multiple>
						<?php
						foreach ($post_types as $type){
							$name = $type->name;
							$label = $type->label;
							$sel = '';
							
							if(in_array($name, $post_types_create)){
								$sel = 'selected';
							}
							?>
							<option value="<?php echo $name ?>" <?php echo $sel?>><?php echo $label ?></option>
							<?php
						}
						?>
					</select>
					
					<script>
						jQuery('#a2020_bar_settings #a2020-create-post-types').tokenize2({
							placeholder: '<?php _e('Select Post Types','admin2020') ?>'
						});
						jQuery(document).ready(function ($) {
							$('#a2020_bar_settings #a2020-create-post-types').on('tokenize:select', function(container){
								$(this).tokenize2().trigger('tokenize:search', [$(this).tokenize2().input.val()]);
							});
						})
					</script>
					
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
		
		///ENSURE WE ARE NOT LOADING ON FRONT UNLESS NECESSARY
		global $pagenow;
		if(!is_user_logged_in() && $pagenow != 'wp-login.php'){
			return;
		}
		
        wp_register_style(
            'admin2020_admin_bar',
            $this->path . 'assets/css/modules/admin-bar.css',
            array('admin-bar'),
            $this->version
        );
        wp_enqueue_style('admin2020_admin_bar');
    }
	
	/**
	* Enqueue Admin Bar 2020 scripts
	* @since 1.4
	*/
	
	public function add_scripts(){
	  
	  
	  ///ENSURE WE ARE NOT LOADING ON FRONT UNLESS NECESSARY
	  global $pagenow;
	  if(!is_user_logged_in() && $pagenow != 'wp-login.php'){
		  return;
	  }
	  ///UIKIT FRAMEWORK
	  wp_enqueue_script('admin-bar-js', $this->path . 'assets/js/admin2020/admin-bar.min.js', array('jquery'));
	  wp_localize_script('admin-bar-js', 'admin2020_admin_bar_ajax', array(
		  'ajax_url' => admin_url('admin-ajax.php'),
		  'security' => wp_create_nonce('admin2020-admin-bar-security-nonce'),
	  ));
	  
	}
	
	
	/**
	* Adds custom css html element
	* @since 1.4
	*/
	
	public function add_body_styles(){
		
		  echo '<style type="text/css">';
		  echo 'html { margin-top: 0 !important; }';
		  echo '</style>';
		  
	}
	
	/**
	* Searches all WP content
	* @since 1.4
	*/
	
	public function a2020_master_search(){
		if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('admin2020-admin-bar-security-nonce', 'security') > 0) {
			
			$term = $_POST['search'];
			
			//BUILD SEARCH ARGS
			$args = array(
			  'numberposts' => - 1, 
			  's' => $term, 
			  'post_status' => array('publish', 'pending', 'draft', 'future', 'private', 'inherit'),
			);
			
			if(isset($_POST['posttypes'])){
				$postTypes = $_POST['posttypes'];
				$args['post_type'] = $postTypes;
			}
			if(isset($_POST['categories'])){
				$categories = $_POST['categories'];
				$args['category'] = $categories;
			}
			if(isset( $_POST['users'])){
				$users =  $_POST['users'];
				$args['author__in'] = $users;
			}
			
			
			$foundposts = get_posts($args);
			
			ob_start();
			
			?>
			<p class="uk-text-meta"><?php echo count($foundposts).' '.__('items found for')?>: <strong><?php echo $term?></strong></p>
			<table class="uk-table uk-table-justify uk-table-divider">
				<thead>
					<tr>
						<th><?php _e('Title','admin2020')?></th>
						<th><?php _e('Type','admin2020')?></th>
						<th><?php _e('User','admin2020')?></th>
						<th><?php _e('Date','admin2020')?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					
				<?php	
				
				foreach ($foundposts as $item){
					
					$author_id = $item->post_author;
					$editurl = get_edit_post_link($item);
					$public = get_permalink($item);
					?>
					<tr>
						<td><a uk-tooltip="<?php echo __('Edit','admin2020').' '.get_post_type($item)?>" href="<?php echo $editurl ?>"><?php echo get_the_title($item)?></a></td>
						<td><span class="uk-label"><?php echo get_post_type($item)?></span></td>
						<td><?php echo the_author_meta( 'user_login' , $author_id )?></td>
						<td><?php echo get_the_date(get_option('date_format'),$item)?></td>
						<td><a target="_blank" uk-tooltip="<?php echo __('View','admin2020').' '.get_post_type($item)?>" href="<?php echo $public ?>"><span uk-icon="link"></span></a></td>
					</tr>
					<?php
					
				}
				
				?>
				</tbody>
			</table><?php
			
			$results = ob_get_clean();
			
			$returndata = array();
			$returndata['html'] = $results;
			echo json_encode($returndata);
		}
		die();
	}

    /**
     * Disables default admin bar and outputs new
     * @since 1.0
     */

    public function rebuild_admin_bar() {
		
		if (!is_admin_bar_showing()) {
			return false;
		}
		
		global $wp_admin_bar;
		
		if (empty($wp_admin_bar)) {
			return false;
		}

		$legacyadmin = $this->utils->get_user_preference('legacy_admin_links');
		$darkmode = $this->utils->get_user_preference('darkmode');
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		$light_background = $this->utils->get_option($optionname,'light-background');
		$dark_background = $this->utils->get_option($optionname,'dark-background');
		$dark_enabled = $this->utils->get_option($optionname,'dark-enabled');
		
		$class = '';
		
		if($darkmode == 'true'){
			$class= 'a2020_night_mode uk-light';
		} else if ($darkmode == '' && $dark_enabled == 'true'){
			$class = "a2020_night_mode uk-light";
		}
			
		
		if($light_background != ""){
			$light_without_hex = str_replace('#', '', $light_background);
			$hexRGB = $light_without_hex;
			if(hexdec(substr($hexRGB,0,2))+hexdec(substr($hexRGB,2,2))+hexdec(substr($hexRGB,4,2))< 381){
				$class = " a2020_night_mode uk-light";
			}
			?>
			<style type="text/css">
			.a2020-admin-bar {background:<?php echo $light_background?> !important;}
			</style>
			<?php
		}
		if($dark_background != ""){
			$light_without_hex = str_replace('#', '', $dark_background);
			$hexRGB = $light_without_hex;
			if(hexdec(substr($hexRGB,0,2))+hexdec(substr($hexRGB,2,2))+hexdec(substr($hexRGB,4,2)) > 381){
				$class = "";
			}
			?>
			<style type="text/css">
			.a2020_night_mode .a2020-admin-bar {background:<?php echo $dark_background?> !important;}
			
			<?php if($this->front == true){ ?>
			   .a2020_night_mode.a2020-admin-bar {background:<?php echo $dark_background?> !important;}
			<?php } ?>
			
			</style>
			<?php
		}
		
		$frontwrap = '';
		
		if($this->front == true){
			$frontwrap = 'a2020-front-wrap';
		}

        /// START MENU BUILD
        ob_start();
        ?>
		
		<div uk-sticky="sel-target: . a2020-admin-bar;" id="<?php echo $frontwrap ?>">
			<nav class="uk-navbar-container uk-navbar-transparent uk-background-default a2020-admin-bar uk-padding-small uk-padding-remove-vertical a2020_dark_anchor <?php echo $class ?>" id="" uk-navbar>
			
				<div class="uk-navbar-left">
				
					<?php $this->build_logo(); ?>
					
					<div class="admin2020_legacy_admin">
						<?php 
						if($legacyadmin != 'true'){
							echo wp_admin_bar_render(); 
						}
						?>
					</div>
				
				</div>
				
				<div class="uk-navbar-right">
				
					<?php $this->build_nav_right(); ?>
				
				</div>
			
			</nav>
		
			<?php $this->build_user_offcanvas(); ?>
	
		</div>
		<?php 
		
		
	    ///OUTPUT NEW MENU
	    $wp_admin_bar = ob_get_clean();
		
		if($this->front === false){
	    	echo $wp_admin_bar;
		} else {
			$this->a2020_admin_bar = $wp_admin_bar;
		}
    }
	
	/**
	 * Outputs the admin bar on front
	 * @since 2.0.4
	 */
	
	public function output_admin_front(){
		
		if(isset($this->a2020_admin_bar)){
			echo $this->a2020_admin_bar;
		}
		
	}
	
	/**
	 * Disables off canvas user menu
	 * @since 1.0
	 */
	
	public function build_search_bar() {
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
		$search_enabled = $this->utils->get_option($optionname,'search-enabled');
		$post_types_enabled = $this->utils->get_option($optionname,'post-types-search');
		
		if($search_enabled == 'true'){
			return;
		}
		
		///GET POST TYPES
		$args = array('public'   => true);
		$output = 'objects'; 
		$post_types = $this->utils->get_post_types();
		$temp = array();
		
		if(is_array($post_types_enabled)){
			foreach($post_types as $type){
				if(in_array($type->name, $post_types_enabled)){
					array_push($temp, $type);
				}
			}
			$post_types = $temp;
		}
		
		
		///GET CATEGORIES
		$categories = get_categories( array(
			'orderby' => 'name',
			'order'   => 'ASC',
			'hide_empty' => false,
		));
		///GET USERS
		$blogusers = get_users();
		
		$darkmode = $this->utils->get_user_preference('darkmode');
		
		$class = '';
		
		if($darkmode == 'true'){
			$class= 'uk-light';
		}
		?>
		
		<li id="a2020_admin_bar_search_wrap">
			<a class="uk-navbar-toggle" href="#" uk-toggle="target: .ma-admin-search-results; animation: uk-animation-slide-top">
				<span uk-tooltip="<?php _e('Search website','admin2020')?>" class="uk-icon-button" uk-icon="search"></span>
			</a>
		</li>
		
		<div class="ma-admin-search-results <?php echo $class?>" uk-modal>
			<div class="uk-modal-dialog uk-modal-body ma-admin-search-results-inner uk-padding-remove" style="">
				
				<div class="uk-padding-small a2020-border-bottom" style="padding-top:15px;padding-bottom:15px;">
					<div class="uk-text-bold"><?php _e('Search','admin2020')?></div>
					<button class="uk-modal-close-default" type="button" uk-close></button>
				</div>
		
				
				<div class="uk-padding">
					<div class="uk-grid-small" uk-grid>
						<div class="uk-width-expand">
							<div class="uk-inline" style="width: 100%;">
								<span class="uk-form-icon" uk-icon="icon: search"></span>
								<input class="uk-input" type="text" id="a2020_master_search" placeholder="<?php _e('Search','admin2020') ?>"autofocus>
							</div>
						</div>
						<div class="uk-width-auto">
							<button class="uk-button uk-button-default a2020_make_square" uk-toggle="target: #master_search_wrap"><span uk-icon="settings" class="uk-icon"></span></button>
						</div>
					</div>
					<div class="uk-grid-small" uk-grid id="master_search_wrap" hidden>
						<div class="uk-width-1-1@s uk-width-1-3@m">
							<button class="uk-button uk-button-default uk-width-1-1"><?php _e('Post Types','admin2020') ?></button>
							
							<div uk-dropdown="mode: click">
								<ul class="uk-list uk-margin-remove-bottom" id="admin2020_search_post_types">
									<?php
									foreach($post_types as $type){
										$name = $type->name;
										$label = $type->label;
										?>
										<li>
											<input class="uk-checkbox" type="checkbox" value="<?php echo $name?>" checked>
											<span><?php echo $label ?></span>
										</li>
										
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="uk-width-1-1@s uk-width-1-3@m">
							<button class="uk-button uk-button-default uk-width-1-1"><?php _e('Categories','admin2020') ?></button>
							
							<div uk-dropdown="mode: click">
								<ul class="uk-list uk-margin-remove-bottom" id="admin2020_search_categories">
									<?php
									foreach($categories as $type){
										$name = $type->name;
										$label = $type->term_id;
										?>
										<li>
											<input class="uk-checkbox" type="checkbox" value="<?php echo $label?>">
											<span><?php echo $name ?></span>
										</li>
										
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="uk-width-1-1@s uk-width-1-3@m">
							<button class="uk-button uk-button-default uk-width-1-1"><?php _e('User','admin2020') ?></button>
							
							<div uk-dropdown="mode: click">
								<ul class="uk-list uk-margin-remove-bottom" id="admin2020_search_users">
									<?php
									foreach($blogusers as $type){
										$name = $type->user_login;
										$id = $type->ID;
										?>
										<li>
											<input class="uk-checkbox" type="checkbox" value="<?php echo $id?>">
											<span><?php echo $name ?></span>
										</li>
										
										<?php
									}
									?>
								</ul>
							</div>
						</div>
					</div>
					
					<div class="uk-width-1-1 uk-margin-top uk-overflow-auto" style="max-height: 500px" id="a2020_master_search_results">
						<div class="uk-placeholder" id="">
							<?php _e('Start searching to see results','admin2020') ?>
						</div>
					</div>
				</div>

		
			</div>
		</div>
		
		<?php
		
	}	
	
	/**
	 * Disables off canvas user menu
	 * @since 1.0
	 */
	
	public function build_user_offcanvas() {
		
		$current_user = $this->utils->get_user();
		
		$username = $current_user->user_login;
		$email = $current_user->user_email;
		$first = $current_user->user_firstname;
		$last = $current_user->user_lastname;
		$roles = $current_user->roles;
		$userid = $current_user->ID;
		
		$darkmode = $this->utils->get_user_preference('darkmode');
		$dark_enabled = $this->utils->get_option('admin2020_admin_bar','dark-enabled');
		
		$screenoptions = $this->utils->get_user_preference('screen_options');
		$legacyadmin = $this->utils->get_user_preference('legacy_admin_links');
		
		if($first == "" || $last == ""){
			$name_string = $username;
		} else {
			$name_string = $first . " " . $last;
		}
		
		$dark_on = '';
		
		if ($darkmode == 'true') {
			$dark_on = 'checked';
		} else if ($darkmode == '' && $dark_enabled == 'true'){
			$dark_on = 'checked';
		}
		?>
		
		<!-- OFFCANVAS USER MENU -->
		<div id="offcanvas-user-menu" uk-offcanvas="flip: true; overlay: true;mode:push">
			<div class="uk-offcanvas-bar" style="border-left:1px solid rgba(162,162,162,.2)">
				
				<button class="uk-offcanvas-close" type="button" uk-close></button>
			
				<div class="uk-grid-small" uk-grid>
					<div class="uk-width-auto">
						<div class="offcanvas_user_image">
							<img class="uk-border-circle" width="50" height="50" src="<?php echo get_avatar_url($this->utils->get_user_id()) ?>">  
						</div>
					</div>
					<div class="uk-width-expand">
						<div class="uk-h4 uk-margin-remove"><?php echo $name_string ?></div>
						<div class="uk-text-meta"><?php echo $email ?></div>
					</div>
					
					<div class="uk-width-1-1 uk-margin-top">
						
						<ul class="uk-subnav uk-subnav-pill" uk-switcher>
							<li><a href="#"><?php _e('Overview','admin2020') ?></a></li>
							<li><a href="#"><?php _e('Preferences','admin2020') ?></a></li>
						</ul>
						
						<ul class="uk-switcher" style="margin-top: 30px;">
							
							<li>
								<ul class="uk-nav uk-nav-default uk-margin-bottom">
									<li>
										<a href="<?php echo get_home_url() ?>"><span class="uk-margin-right" uk-icon="icon: link"></span><?php _e('View Website','admin2020')?></a>
									</li>
									<li>
										<a href="<?php echo get_edit_profile_url($userid) ?>"><span class="uk-margin-right" uk-icon="icon: user"></span><?php _e('View Profile','admin2020')?></a>
									</li>
								</ul>
								
								<?php $this->build_notifications() ?>
							</li>
							
							
							<li>
								
								<div class="uk-grid-small" uk-grid>
									
									<div class="uk-width-2-3">
										<?php _e('Dark Mode','admin2020')?>
									</div>
									
									<div class="uk-width-1-3">
										<label class="admin2020_switch uk-margin-left">
											<input type="checkbox" id="maAdminSwitchDarkMode" <?php echo $dark_on ?>>
											<span class="admin2020_slider constant_dark"></span>
										</label>
									</div>
									
									<div class="uk-width-2-3 a2020_wp_admin_screen_option">
										<?php _e('Show screen options','admin2020')?>
									</div>
									
									<div class="uk-width-1-3 a2020_wp_admin_screen_option">
										<label class="admin2020_switch uk-margin-left">
											<input type="checkbox" id="showscreenoptions" <?php checked( $screenoptions, 'true' ); ?>>
											<span class="admin2020_slider constant_dark"></span>
										</label>
									</div>
									
									<div class="uk-width-2-3 a2020_wp_admin_bar_option">
										<?php _e('Hide WP links','admin2020')?>
									</div>
									
									<div class="uk-width-1-3 a2020_wp_admin_bar_option">
										<label class="admin2020_switch uk-margin-left">
											<input type="checkbox" id="hiddelegacylinks" <?php checked( $legacyadmin, 'true' ); ?>>
											<span class="admin2020_slider constant_dark"></span>
										</label>
									</div>
									
								</div>	
								
							</li>
							
						</ul>
						
						<div class="uk-position-bottom uk-padding uk-width-1-1 a2020_logout uk-background-secondary" style="padding-top:15px;padding-bottom:15px;">
							<a href="<?php echo wp_logout_url() ?>"><span class="uk-margin-right" uk-icon="icon: sign-out"></span><?php _e('Logout','admin2020')?></a>
						</div>
					</div>	
				</div>	
			
			</div>
		</div>
		<?php
	}	
	
	/**
	 * Builds notification area
	 * @since 1.4
	 */

	public function build_notifications() {
		
		$updates = $this->utils->get_total_updates();
		$total_updates = $updates['total'];
		$pluginupdates = $updates['plugin'];
		$themeupdates = $updates['theme'];
		$wordpressupdates = $updates['wordpress'];
		$adminurl = get_admin_url();
		
		?> <div id="a2020-update-wrap">
			<div class="uk-h5 uk-margin-remove-top"><?php _e('Updates','admin2020')?></div> <?php
		
		if ($total_updates < 1){ ?>
		
			<p class="uk-text-meta"><?php _e('Everything is up to date','admin2020') ?></p>
			
		<?php } else {	?>
			
		
			<ul class="uk-nav uk-nav-default uk-margin-bottom" id="admin2020_updates_center">
			
			   <li>
				 <a href="<?php echo $adminurl.'update-core.php'?>" id="" style="position:relative;">
				   <span class="uk-margin-right" uk-icon="icon: refresh"></span><?php _e('All Updates','admin2020')?>
				   <span class="uk-badge uk-position-center-right uk-text-primary" style="background:#f0506e"><?php echo $total_updates?></span>
				 </a>
			   </li>
			
			   <?php if ($wordpressupdates > 0){?>
			   <li>
				 <a href="<?php echo $adminurl.'update-core.php'?>" id="" style="position:relative;">
				   <span class="uk-margin-right" uk-icon="icon: wordpress"></span><?php _e('WordPress','admin2020')?>
				   <span class="uk-badge uk-position-center-right uk-text-primary" style="background:#f0506e"><?php echo $wordpressupdates?></span>
				 </a>
			   </li>
			   <?php } ?>
			
			   <?php if (count($pluginupdates) > 0){?>
			   <li>
				 <a href="<?php echo $adminurl.'plugins.php'?>" id="" style="position:relative;">
				   <span class="uk-margin-right" uk-icon="icon: bolt"></span><?php _e('Plugins','admin2020')?>
				   <span class="uk-badge uk-position-center-right uk-text-primary" style="background:#f0506e"><?php echo count($pluginupdates)?></span>
				 </a>
			   </li>
			 <?php } ?>
			
			   <?php if (count($themeupdates) > 0){?>
			   <li>
				 <a href="<?php echo $adminurl.'themes.php'?>" id="" style="position:relative;">
				   <span class="uk-margin-right" uk-icon="icon: paint-bucket"></span><?php _e('Themes','admin2020')?>
				   <span class="uk-badge uk-position-center-right uk-text-primary" style="background:#f0506e"><?php echo count($themeupdates)?></span>
				 </a>
			   </li>
			 <?php } ?>
			
			</ul>
		</div>
		
		<?php } ?>
		
		<div id="a2020-notification-wrap">
			<ul uk-accordion>
				<li class="uk-open">
					<a class="uk-accordion-title uk-h5" style="font-size: 16px;" href="#"><?php _e('Notifications','admin2020')?></a>
					<div class="uk-accordion-content" id="admin2020_notification_center">
						
					</div>
				</li>
			</ul>
		</div>		
		<?php
		
	}
	
    /**
     * Builds admin bar logo
     * @since 1.4
     */

    public function build_logo() {
		
		$info = $this->component_info();
		$optionname = $info['option_name'];
        //GET LOGOS
        $logo = $this->utils->get_logo($optionname);
        $dark_logo = $this->utils->get_dark_logo($optionname);
        global $wp_admin_bar;
        //GET HOME URL
        $adminurl = get_admin_url();
        $homeurl = $adminurl;
		
		$redirect = $this->utils->get_option('admin2020_admin_login','login-redirect');
		
		if($redirect == 'true'){
			$homeurl =  home_url() . "/wp-admin/admin.php?page=admin_2020_overview";
		} 
        ?>
		
		<ul class="uk-navbar-nav">
			<li class="uk-visible@m" id="a2020_toggle_wrap">
				<a href="#" style="padding-left: 0;" id="a2020_menu_toggle">
					<span uk-tooltip="delay:500;title:<?php _e('Toggle menu') ?>" class="uk-icon-button" uk-icon="menu"></span>
				</a>
			</li>
			<li class="uk-hidden@m">
				<a href="#" style="padding-left: 0;" id="a2020_menu_mobile_toggle">
					<span uk-tooltip="delay:500;title:<?php _e('Toggle menu') ?>" class="uk-icon-button" uk-icon="menu"></span>
				</a>
			</li>
			<li class="uk-active">
				<a href="<?php echo $homeurl; ?>" class="uk-padding-remove-horizontal ma-admin-site-logo">
					<img alt="<?php echo get_bloginfo( 'name' )?>" class="light" src="<?php echo $logo; ?>">
					<img alt="<?php echo get_bloginfo( 'name' )?>" class="dark" src="<?php echo $dark_logo; ?>">
				</a>
			</li>
			
		</ul>
		
		<?php
    }

    /**
     * Build Right admin bar Links
     * @since 1.4
     */

    public function build_nav_right()
    {
		$info = $this->component_info();
		$optionname = $info['option_name'];
		$new_enabled = $this->utils->get_option($optionname,'new-enabled');
		$view_enabled = $this->utils->get_option($optionname,'view-enabled');
		$post_types_create = $this->utils->get_option($optionname,'post-types-create');
		
		
		$post_types = $this->utils->get_post_types();
		$temp = array();
		
		if(is_array($post_types_create)){
			foreach($post_types as $type){
				if(in_array($type->name, $post_types_create)){
					array_push($temp, $type);
				}
			}
			$post_types = $temp;
		}
			
		
		
        $total_updates = $this->utils->get_total_updates();
		$screenoptions = $this->utils->get_user_preference('screen_options');
        
		$gavar_url = get_avatar_url($this->utils->get_user_id());
		
		$current_user = $this->utils->get_user();
		
		$username = $current_user->user_login;
		$first = $current_user->user_firstname;
		$last = $current_user->user_lastname;
		
		$darkmode = $this->utils->get_user_preference('darkmode');
		$screenoptions = $this->utils->get_user_preference('screen_options');
		
		if($first == "" || $last == ""){
			$name_string = str_split($username,1);
			$name_string = $name_string[0];
		} else {
			$name_string = str_split($first,1)[0].str_split($last,1)[0];
		}	
		
        ?>
		
		<div class="uk-navbar-right">
		
			<ul class="uk-navbar-nav">
				
				<li>
					<hr class="uk-divider-vertical uk-margin-small-right"  >
				</li>
				
				<?php $this->build_search_bar();  ?>
				
				<?php if($view_enabled != 'true'){ ?>
				<li>
					<a href="<?php echo get_home_url() ?>" target="_blank">
						<span uk-tooltip="<?php _e('View website','admin2020')?>" class="uk-icon-button" uk-icon="link"></span>
					</a>
				</li>
				<?php } ?>
				
				<?php if ($screenoptions == 'true'){ ?>
				<li>
					<a href="#" id="maAdminToggleScreenOptions" onclick="jQuery('#screen-meta').toggleClass('a2020_open_sc');">
						<span uk-tooltip="<?php _e('Show screen options','admin2020')?>" class="uk-icon-button" uk-icon="settings"></span>
					</a>
				</li>
				<?php } ?>
				
				<?php if($new_enabled != 'true'){ ?>
				<li>
					<a href="#" target="_blank">
						<span class="uk-button uk-button-small uk-border-pill uk-button-primary" ><?php _e('new','admin2020')?></span>
					</a>
					
					<div uk-dropdown="offset:0;pos:bottom-justify;">
						<ul class="uk-nav uk-dropdown-nav">
							<?php foreach ($post_types as $type){ 
								
								$nicename = $type->labels->singular_name;
								$type = $type->name;
								$link = 'post-new.php?post_type='.$type;
								
								?>	
								<li><a href="<?php echo $link?>"><?php echo $nicename?></a></li>
							<?php } ?>
						</ul>
					</div>
				</li>
				<?php } ?>
				
				
				<li uk-toggle="target: #offcanvas-user-menu" style="position:relative">
					<a href="#" class="ma-admin-profile-img">
						
						<div style="position:relative;">
							
							<?php 
							if(strpos($gavar_url,'gravatar.com')!==false){ ?>
								
								<span  class="uk-icon-button uk-button-primary uk-text-bold uk-text-small" style="font-size:12px;"><?php echo $name_string?></span>
								
							<?php } else { ?>
							
								<img src="<?php echo $gavar_url ?>">
							
							<?php } ?>
						
						</div>
						
						<span class="uk-badge uk-position-top-right-out admin2020notificationBadge uk-animation-scale-up" style="display:none;"><?php echo $total_updates['total']; ?></span>
					</a>
				</li>
			
			
			
			</ul>
		
		</div>
		
		<?php
    }
}
