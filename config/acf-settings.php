<?php

// Register Image select fields
add_action('acf/register_fields', 'register_fields');
function my_register_fields()
{
    include_once('acf-image-select/acf-image-select.php');
}


// Theme Admin Menu
if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title'     => 'General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'        => false
    ));

    acf_add_options_sub_page(array(
        'page_title'     => 'Header Settings',
        'menu_title'    => 'Header',
        'parent_slug'    => 'theme-general-settings',
    ));

    acf_add_options_sub_page(array(
        'page_title'     => 'Footer Settings',
        'menu_title'    => 'Footer',
        'parent_slug'    => 'theme-general-settings',
    ));
}


// Register ACF Options pages
if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title'     => 'General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'        => false
    ));

    acf_add_options_sub_page(array(
        'page_title'     => 'Header Settings',
        'menu_title'    => 'Header',
        'parent_slug'    => 'theme-general-settings',
    ));

    acf_add_options_sub_page(array(
        'page_title'     => 'Footer Settings',
        'menu_title'    => 'Footer',
        'parent_slug'    => 'theme-general-settings',
    ));


    $user = wp_get_current_user();
    $user_email = $user->user_email;

    if (in_array($user_email, array(
        'stefan@blueappleuk.com',
        'david@blueappleuk.com',
        'blueapplemaster@blueappleuk.com',
        'dean@blueappleuk.com',
        'mark@blueappleuk.com'
    ), true)) {


        // Add Menus here
    }
}

// Remove empty p tags from editor
function remove_empty_p($content)
{
    $content = force_balance_tags($content);
    $content = preg_replace('#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content);
    $content = preg_replace('~\s?<p>(\s|&nbsp;)+</p>\s?~', '', $content);
    return $content;
}
add_filter('the_content', 'remove_empty_p', 20, 1);
remove_filter('acf_the_content', 'wpautop');

//Remove WPAUTOP from ACF TinyMCE Editor
function acf_wysiwyg_remove_wpautop()
{
    remove_filter('acf_the_content', 'wpautop');
}
add_action('acf/init', 'acf_wysiwyg_remove_wpautop');

//Default image field
add_action('acf/render_field_settings/type=image', 'add_default_value_to_image_field');
function add_default_value_to_image_field($field)
{
    acf_render_field_setting($field, array(
        'label'            => 'Default Image',
        'instructions'        => 'Appears when creating a new post',
        'type'            => 'image',
        'name'            => 'default_value',
    ));
}


add_filter('acf/load_value/type=image', 'reset_default_image', 10, 3);
function reset_default_image($value, $post_id, $field)
{
    if (!$value) {
        $value = $field['default_value'];
    }

    return $value;
}