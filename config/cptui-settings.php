<?php 
function cptui_register_my_cpts() {

/**
 * Post Type: Newsfeed.
 */

$labels = [
    "name" => __( "Newsfeed", "custom-post-type-ui" ),
    "singular_name" => __( "Newsfeed", "custom-post-type-ui" ),
];

$args = [
    "label" => __( "Newsfeed", "custom-post-type-ui" ),
    "labels" => $labels,
    "description" => "",
    "public" => true,
    "publicly_queryable" => true,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "delete_with_user" => false,
    "exclude_from_search" => false,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "rewrite" => [ "slug" => "news/newsfeed", "with_front" => true ],
    "query_var" => true,
    "menu_position" => 35,
    "menu_icon" => "dashicons-format-aside",
    "supports" => [ "title" ],
];

register_post_type( "cpt_newsevents", $args );


/**
 * Post Type: Careers.
 */

$labels = [
    "name" => __( "Careers", "custom-post-type-ui" ),
    "singular_name" => __( "Career", "custom-post-type-ui" ),
];

$args = [
    "label" => __( "Careers", "custom-post-type-ui" ),
    "labels" => $labels,
    "description" => "",
    "public" => true,
    "publicly_queryable" => true,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => true,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "delete_with_user" => false,
    "exclude_from_search" => false,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => true,
    "rewrite" => [ "slug" => "careers", "with_front" => true ],
    "query_var" => true,
    "menu_icon" => "dashicons-admin-users",
    "supports" => [ "title" ],
];

register_post_type( "cpt_careers", $args );

/**
 * Post Type: Staff.
 */

$labels = [
    "name" => __( "Staff", "custom-post-type-ui" ),
    "singular_name" => __( "Staff", "custom-post-type-ui" ),
];

$args = [
    "label" => __( "Staff", "custom-post-type-ui" ),
    "labels" => $labels,
    "description" => "",
    "public" => true,
    "publicly_queryable" => false,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "delete_with_user" => false,
    "exclude_from_search" => false,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "rewrite" => [ "slug" => "our-staff", "with_front" => true ],
    "query_var" => true,
    "menu_icon" => "dashicons-admin-users",
    "supports" => [ "title" ],
];

register_post_type( "cpt_staff", $args );

/**
 * Post Type: Page Content.
 */

$labels = [
    "name" => __( "Page Content", "custom-post-type-ui" ),
    "singular_name" => __( "Page Content", "custom-post-type-ui" ),
];

$args = [
    "label" => __( "Page Content", "custom-post-type-ui" ),
    "labels" => $labels,
    "description" => "",
    "public" => false,
    "publicly_queryable" => false,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "delete_with_user" => false,
    "exclude_from_search" => true,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "rewrite" => false,
    "query_var" => true,
    "menu_position" => 22,
    "menu_icon" => "dashicons-format-gallery",
    "supports" => [ "title" ],
];

register_post_type( "cpt_page_content", $args );

/**
 * Post Type: Policies.
 */

$labels = [
    "name" => __( "Policies", "custom-post-type-ui" ),
    "singular_name" => __( "Policy", "custom-post-type-ui" ),
];

$args = [
    "label" => __( "Policies", "custom-post-type-ui" ),
    "labels" => $labels,
    "description" => "",
    "public" => true,
    "publicly_queryable" => false,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "delete_with_user" => false,
    "exclude_from_search" => false,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "rewrite" => [ "slug" => "policies", "with_front" => true ],
    "query_var" => true,
    "menu_icon" => "dashicons-media-default",
    "supports" => [ "title" ],
];

register_post_type( "cpt_policies", $args );


// Newsletters removed in favour of Newsfeed Posts with "Newsletter" Category
// This will eradicate the need to edit the current newsletter configuration
// Newletters CPTUI ACF Fields have also been removed
// /**
//  * Post Type: Newsletters.
//  */

// $labels = [
//     "name" => __( "Newsletters", "custom-post-type-ui" ),
//     "singular_name" => __( "Newsletter", "custom-post-type-ui" ),
// ];

// $args = [
//     "label" => __( "Newsletters", "custom-post-type-ui" ),
//     "labels" => $labels,
//     "description" => "",
//     "public" => true,
//     "publicly_queryable" => true,
//     "show_ui" => true,
//     "show_in_rest" => true,
//     "rest_base" => "",
//     "rest_controller_class" => "WP_REST_Posts_Controller",
//     "has_archive" => false,
//     "show_in_menu" => true,
//     "show_in_nav_menus" => true,
//     "delete_with_user" => false,
//     "exclude_from_search" => false,
//     "capability_type" => "post",
//     "map_meta_cap" => true,
//     "hierarchical" => false,
//     "rewrite" => [ "slug" => "news/newsletters", "with_front" => true ],
//     "query_var" => true,
//     "menu_position" => 31,
//     "menu_icon" => "dashicons-format-aside",
//     "supports" => [ "title" ],
// ];

// register_post_type( "cpt_newsletter", $args );

// /**
//  * Post Type: Schools.
//  */

// $labels = [
//     "name" => __( "Schools", "custom-post-type-ui" ),
//     "singular_name" => __( "School", "custom-post-type-ui" ),
// ];

// $args = [
//     "label" => __( "Schools", "custom-post-type-ui" ),
//     "labels" => $labels,
//     "description" => "",
//     "public" => true,
//     "publicly_queryable" => false,
//     "show_ui" => true,
//     "show_in_rest" => true,
//     "rest_base" => "",
//     "rest_controller_class" => "WP_REST_Posts_Controller",
//     "has_archive" => false,
//     "show_in_menu" => true,
//     "show_in_nav_menus" => true,
//     "delete_with_user" => false,
//     "exclude_from_search" => true,
//     "capability_type" => "post",
//     "map_meta_cap" => true,
//     "hierarchical" => false,
//     "rewrite" => [ "slug" => "cpt_schools", "with_front" => true ],
//     "query_var" => true,
//     "menu_icon" => "dashicons-groups",
//     "supports" => [ "title" ],
// ];

// register_post_type( "cpt_schools", $args );

/**
 * Post Type: CTA's.
 */

$labels = [
    "name" => __( "CTA's", "custom-post-type-ui" ),
    "singular_name" => __( "CTA", "custom-post-type-ui" ),
];

$args = [
    "label" => __( "CTA's", "custom-post-type-ui" ),
    "labels" => $labels,
    "description" => "",
    "public" => false,
    "publicly_queryable" => false,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "delete_with_user" => false,
    "exclude_from_search" => false,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "rewrite" => [ "slug" => "cpt_cta_content", "with_front" => true ],
    "query_var" => true,
    "menu_position" => 18,
    "menu_icon" => "dashicons-format-gallery",
    "supports" => [ "title" ],
];

register_post_type( "cpt_cta_content", $args );

/**
 * Post Type: Sidebar Content.
 */

$labels = [
    "name" => __( "Sidebar Content", "custom-post-type-ui" ),
    "singular_name" => __( "Sidebar Content", "custom-post-type-ui" ),
];

$args = [
    "label" => __( "Sidebar Content", "custom-post-type-ui" ),
    "labels" => $labels,
    "description" => "",
    "public" => false,
    "publicly_queryable" => false,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "delete_with_user" => false,
    "exclude_from_search" => true,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "rewrite" => false,
    "query_var" => true,
    "menu_position" => 22,
    "menu_icon" => "dashicons-format-gallery",
    "supports" => [ "title" ],
];

register_post_type( "cpt_sidebar_content", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );
?>