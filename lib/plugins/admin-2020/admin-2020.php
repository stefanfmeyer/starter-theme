<?php
/*
Plugin Name: Admin 2020 Lite
Plugin URI: https://admintwentytwenty.com
Description: Powerful WordPress admin theme with a streamlined dashboard, global search, dark mode and much more.
Version: 2.0.6
Author: Admin 2020
Text Domain: admin2020
Domain Path: /admin/languages
Author URI: https://admintwentytwenty.com
*/

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;

////PRODUCT ID
$productid = "5fb144db00b05d1058a9d3cc";///LITE 
////VERSION
$version = '2.0.6'; 
////NAME
$pluginName = 'Admin 2020 Lite';
///PATH
$plugin_path = plugin_dir_url( __FILE__ );

require plugin_dir_path( __FILE__ ) . 'admin/inlcudes/class-admin-2020.php';

$plugin = new Admin_2020($productid,$version,$pluginName,$plugin_path);
$plugin->run();

/// SHOW ERRORS
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
