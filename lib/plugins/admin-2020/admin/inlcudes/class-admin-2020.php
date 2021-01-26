<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020{
	
	public function __construct($productid,$version,$pluginName,$path) { 
	
		$this->version = $version;
		$this->plugin_name = $pluginName;
		$this->productid = $productid;
		$this->path = $path;
	
	}
	
	/**
	 * Loads Admin 2020 dependancies
	 * @since 1.0
	 */
	
	public function run(){
		
		if ( is_admin() ){
			$this->load_admin();
			$this->build_admin_app();
		} else {
			$this->load_front();
			$this->build_front_app();
		}
		
	}
	
	/**
	 * Loads Admin 2020 admin dependancies
	 * @since 1.4
	 */
	 
	public function load_admin(){
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/admin/class-utilities.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/admin/class-styles.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/admin/class-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/admin/class-activation.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/admin/class-update.php';
		
	}
	
	/**
	 * Builds Admin 2020 Plugin for back end
	 * @since 1.4
	 */
	 
	public function build_admin_app(){
		
		$utilities = new Admin_2020_utilities($this->version,$this->path, $this->productid);
		$utilities->start();
		
		$styles = new Admin_2020_styles($this->version,$this->path,$utilities);
		$styles->start();
		
		$settings = new Admin_2020_settings($this->version, $this->path, $utilities, $this->plugin_name);
		$settings->start();
		
		$activation = new Admin_2020_activation($this->version,$this->path,$utilities,$this->productid);
		$activation->start();
		
		$update = new Admin_2020_update($this->version,$this->path,$utilities,$this->productid);
		$update->start();
		
		$this->admin2020_lang_loader();
		
	}
	
	
	/**
	 * translation files action
	 * @since 1.4
	 */
	public function load_plugin_textdomain() {

		add_action( 'plugins_loaded', array($this,'admin2020_lang_loader'),-999);

	}

	/**
	 * Loads translation files 
	 * @since 1.4
	 */
	public function admin2020_lang_loader() { 

		load_plugin_textdomain('admin2020',false,dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages'); 

	}
	
	
	
	
	/**
	 * Loads Admin 2020 front dependancies
	 * @since 1.4
	 */
	 
	public function load_front(){
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/admin/class-utilities.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/modules/admin-login.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/admin/class-utilities.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/admin/class-styles.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/modules/admin-bar.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/modules/admin-theme.php';
		
		
	}
	
	/**
	 * Builds Admin 2020 Plugin for front end
	 * @since 1.4 
	 */
	 
	public function build_front_app(){
		
		$front_utilities = new Admin_2020_utilities($this->version,$this->path,$this->productid);
		$front_utilities->start();
		
		$admin_styles = new Admin_2020_styles($this->version,$this->path,$front_utilities);
		$admin_styles->start_front();
		
		$admin_bar = new Admin_2020_module_admin_bar($this->version,$this->path,$front_utilities);
		$admin_bar->start_front();
		
		$login = new Admin_2020_module_admin_login($this->version,$this->path,$front_utilities);
		$login->build_front();
		
		$theme = new Admin_2020_module_admin_theme($this->version,$this->path,$front_utilities);
		$theme->build_front();
		
		if($front_utilities->is_premium()){
			
			//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/modules/premium/admin-pages.php';
			//$admin_pages = new Admin_2020_module_admin_pages($this->version,$this->path,$front_utilities);
			//$admin_pages->start_front();
			
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inlcudes/modules/premium/admin-advanced.php';
			$advanced = new Admin_2020_module_admin_advanced($this->version,$this->path,$front_utilities);
			$advanced->start_front();
			
		}
		
	}
	
}
