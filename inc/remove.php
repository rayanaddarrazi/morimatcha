<?php

defined( 'ABSPATH' ) || exit;

// Prevent File Modifications
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', true );
}

/**
 * Remove Actions
 */
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

// Remove the REST API endpoint.
remove_action( 'rest_api_init', 'wp_oembed_register_route' );

// Remove REST API in HTTP Headers
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );

// Remove Emoji Styles
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// Disable XML RPC
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Remove Admin Bar
 */
add_filter( 'show_admin_bar', '__return_false' );

/**
 * Admin cleanup: alleen Pagina's en Behandelingen blijven over.
 */
add_action( 'admin_menu', function () {
    remove_menu_page( 'edit-comments.php' );
    remove_menu_page( 'edit.php' ); // Berichten
} );

/**
 * Pagina's worden volledig met ACF opgebouwd: geen standaard editor.
 */
add_action( 'init', function () {
    remove_post_type_support( 'page', 'editor' );
}, 100 );

/**
 * Disable Gutenberg
 */
// Disable Gutenberg on the back end.
add_filter( 'use_block_editor_for_post', '__return_false' );

// Disable Gutenberg for all post types (incl. custom post types).
add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );

// Disable Gutenberg for widgets.
add_filter( 'use_widgets_block_editor', '__return_false' );

add_action( 'wp_enqueue_scripts', function () {
    // Remove CSS on the front end.
    wp_dequeue_style( 'wp-block-library' );

    // Remove Gutenberg theme.
    wp_dequeue_style( 'wp-block-library-theme' );

    // Remove inline global CSS on the front end.
    wp_dequeue_style( 'global-styles' );

    // Remove classic-themes CSS for backwards compatibility for button blocks.
    wp_dequeue_style( 'classic-theme-styles' );
}, 20 );

// WordPress 6.8+ print global styles late (wp_footer) and hoist them into the head: stop that at the source.
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles' );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_stored_styles' );
remove_action( 'wp_footer', 'wp_enqueue_stored_styles', 1 );

add_action( 'wp_enqueue_scripts', function () {
    foreach ( array( 'global-styles', 'classic-theme-styles', 'wp-global-styles-placeholder', 'wp-block-styles-placeholder' ) as $handle ) {
        wp_dequeue_style( $handle );
        wp_deregister_style( $handle );
    }
}, 100000 );

/**
 * Stop Heartbeat API
 */
add_action( 'init', function () {
    wp_deregister_script( 'heartbeat' );
}, 1 );
