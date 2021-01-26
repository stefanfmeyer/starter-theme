<?php
/* Change Menu */
function wpse_custom_menu_order($menu_ord)
{
    if (!$menu_ord) return true;

    return array(
        // Top Level
        'index.php',
        'edit.php',
        'edit.php?post_type=page',

        // WP Customisation
        'upload.php',
        'simple_history_page',

        // PAGE CONTENT
        'nestedpages',
        'edit.php?post_type=cpt_cta_content',

        // BLOG
        'edit.php?post_type=cpt_newsevents',
        'edit.php?post_type=cpt_newsletter',

        // Page Content

'theme-settings',
        // THEME SETTINGS
        'themes.php',
        'plugins.php',
        'users.php',
        'options-general.php',
        'educreate-admin',

        // THEME PLUGINS
        'edit.php?post_type=acf-field-group',
        'cptui_main_menu',
        'video-tutorials',
        'wps_overview_page',


        // SITE PLUGINS
        'edit.php?post_type=cookielawinfo',
    );
}
add_filter('custom_menu_order', 'wpse_custom_menu_order', 10, 1);
add_filter('menu_order', 'wpse_custom_menu_order', 10, 1);

/*** Remove menu items for all users ***/

function remove_menu_pages()
{
    $user = wp_get_current_user();
    $user_email = $user->user_email;
    if (!in_array($user_email, array(
        'stefan@blueappleuk.com',
        'david@blueappleuk.com',
        'blueapplemaster@blueappleuk.com',
        'dean@blueappleuk.com',
        'mark@blueappleuk.com'
    ), true)) {

        /* Theme Settings */
        remove_menu_page('themes.php');
        remove_menu_page('plugins.php');
        // remove_menu_page( 'users.php');
        remove_menu_page('edit-comments.php');
        remove_menu_page('tools.php');
        remove_menu_page('options-general.php');
        remove_menu_page('theme-settings');
        remove_menu_page('update-core.php');
        remove_menu_page('admin.php?page=wps_overview_page');

        /* Theme Plugins */
        remove_menu_page('edit.php?post_type=acf-field-group');
        remove_menu_page('cptui_main_menu');

        /* Plugins */
        remove_menu_page('edit.php?post_type=cookielawinfo');
        remove_menu_page('admin.php?page=wpseo_dashboard');
    }
};

add_action('admin_menu', 'remove_menu_pages', 999);