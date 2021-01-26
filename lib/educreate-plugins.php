<?php

/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme EduCreate
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 *
 * Depending on your implementation, you may want to change the include call:
 *
 * Parent Theme:
 * require_once get_template_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Child Theme:
 * require_once get_stylesheet_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Plugin:
 * require_once dirname( __FILE__ ) . '/path/to/class-tgm-plugin-activation.php';
 */
require_once get_template_directory() . '/lib/class-tgm-plugin-activation.php';

add_action('tgmpa_register', 'require_these_plugins_register_required_plugins');

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function require_these_plugins_register_required_plugins()
{
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		// Wordpress Directory Plugins unless stated otherwise
		array(
			'name'      => 'ACF Better Search',
			'slug'      => 'acf-better-search',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		array(
			'name'      => 'ACF Extended',
			'slug'      => 'acf-extended',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		array(
			'name'      => 'ACF Restrict Color Picker',
			'slug'      => 'acf-restrict-color-picker',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		array(
			'name'      => 'ACF RGBA Color Picker',
			'slug'      => 'acf-rgba-color-picker',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		// From Github Repo
		array(
			'name'      => 'ACF Image Select',
			'slug'      => 'ACF-Image-Select',
			'source'    => 'https://github.com/cyberwani/ACF-Image-Select/archive/master.zip',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		// TODO - Include acf pro from github
		// // ACF PRO local plugin from lib/Plugins folder
		array(
			'name'      => 'Advanced Custom Fields Pro', // The plugin name.
			'slug'      => 'advanced-custom-fields-pro',
			'source'    => 'https://github.com/Blue-Apple-Education/advanced-custom-fields-pro/archive/master.zip',
			'required'           => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),


		array(
			'name'      => 'Advanced Custom Fields: Table Field',
			'slug'      => 'advanced-custom-fields-table-field',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		array(
			'name'      => 'Classic Editor',
			'slug'      => 'classic-editor',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		array(
			'name'      => 'Contact Form 7',
			'slug'      => 'contact-form-7',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		array(
			'name'      => 'GDPR Cookie Consent (CCPA Ready)',
			'slug'      => 'cookie-law-info',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		array(
			'name'      => 'Custom Post Type UI',
			'slug'      => 'custom-post-type-ui',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		array(
			'name'      => 'Yoast Duplicate Post',
			'slug'      => 'duplicate-post',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),


		array(
			'name'      => 'FileBird – WordPress Media Library Folders & File Manager',
			'slug'      => 'filebird',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		// WP Pusher
		array(
			'name'      => 'WPPusher', // The plugin name.
			'slug'      => 'wppusher', get_stylesheet_directory() . '/plugins/wppusher.zip',
			'required'           => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		array(
			'name'      => 'Login IP & Country Restriction',
			'slug'      => 'login-ip-country-restriction',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		array(
			'name'      => 'Simple History',
			'slug'      => 'simple-history',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		array(
			'name'      => 'Yoast SEO',
			'slug'      => 'wordpress-seo',
			'required'  => true,
			'force_activation'   => true,
			'force_deactivation' => true,
		),

		// External Source Plugin
		// array(
		// 	'name'         => 'ACF Image Select', // The plugin name.
		// 	'slug'         => 'ACF-Image-Select', // The plugin slug (typically the folder name).
		// 	'source'       => 'https://s3.amazonaws.com/tgm/tgm-new-media-plugin.zip', // The plugin source.
		// 	'required'     => true, // If false, the plugin is only 'recommended' instead of required.
		// 	'external_url' => 'https://github.com/thomasgriffin/New-Media-Image-Uploader', // If set, overrides default API URL and points to an external URL.
		// ),

		// This is an example of how to include a plugin bundled with a theme.
		// array(
		// 	'name'               => 'TGM Example Plugin', // The plugin name.
		// 	'slug'               => 'tgm-example-plugin', // The plugin slug (typically the folder name).
		// 	'source'             => get_template_directory() . '/lib/plugins/tgm-example-plugin.zip', // The plugin source.
		// 	'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		// 	'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		// 	'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		// 	'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
		// 	'external_url'       => '', // If set, overrides default API URL and points to an external URL.
		// 	'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		// ),

		// This is an example of how to include a plugin from an arbitrary external source in your theme.
		// array(
		// 	'name'         => 'TGM New Media Plugin', // The plugin name.
		// 	'slug'         => 'tgm-new-media-plugin', // The plugin slug (typically the folder name).
		// 	'source'       => 'https://s3.amazonaws.com/tgm/tgm-new-media-plugin.zip', // The plugin source.
		// 	'required'     => true, // If false, the plugin is only 'recommended' instead of required.
		// 	'external_url' => 'https://github.com/thomasgriffin/New-Media-Image-Uploader', // If set, overrides default API URL and points to an external URL.
		// ),

		// This is an example of the use of 'is_callable' functionality. A user could - for instance -
		// have WPSEO installed *or* WPSEO Premium. The slug would in that last case be different, i.e.
		// 'wordpress-seo-premium'.
		// By setting 'is_callable' to either a function from that plugin or a class method
		// `array( 'class', 'method' )` similar to how you hook in to actions and filters, TGMPA can still
		// recognize the plugin as being installed.
		// array(
		// 	'name'        => 'WordPress SEO by Yoast',
		// 	'slug'        => 'wordpress-seo',
		// 	'is_callable' => 'wpseo_init',
		// ),
	);

	/** Array of configuration settings. Amend each line as needed.** 
	 * 
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard strings available, please help us make TGMPA even better by giving us access to these translations or by sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(

		'id'           => 'educreate',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => 'Please ensure all plugins are installed and activated. If you are experiencing issues, please contact the Developer at stefan@blueappleuk.com',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.

		/*
		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'educreate' ),
			'menu_title'                      => __( 'Install Plugins', 'educreate' ),
			/* translators: %s: plugin name. * /
			'installing'                      => __( 'Installing Plugin: %s', 'educreate' ),
			/* translators: %s: plugin name. * /
			'updating'                        => __( 'Updating Plugin: %s', 'educreate' ),
			'oops'                            => __( 'Something went wrong with the plugin API.', 'educreate' ),
			'notice_can_install_required'     => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme requires the following plugin: %1$s.',
				'This theme requires the following plugins: %1$s.',
				'educreate'
			),
			'notice_can_install_recommended'  => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme recommends the following plugin: %1$s.',
				'This theme recommends the following plugins: %1$s.',
				'educreate'
			),
			'notice_ask_to_update'            => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
				'educreate'
			),
			'notice_ask_to_update_maybe'      => _n_noop(
				/* translators: 1: plugin name(s). * /
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'educreate'
			),
			'notice_can_activate_required'    => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				'educreate'
			),
			'notice_can_activate_recommended' => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				'educreate'
			),
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'educreate'
			),
			'update_link' 					  => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'educreate'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'educreate'
			),
			'return'                          => __( 'Return to Required Plugins Installer', 'educreate' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'educreate' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', 'educreate' ),
			/* translators: 1: plugin name. * /
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'educreate' ),
			/* translators: 1: plugin name. * /
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'educreate' ),
			/* translators: 1: dashboard link. * /
			'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'educreate' ),
			'dismiss'                         => __( 'Dismiss this notice', 'educreate' ),
			'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'educreate' ),
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'educreate' ),

			'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
		),
		*/
	);

	tgmpa($plugins, $config);
}