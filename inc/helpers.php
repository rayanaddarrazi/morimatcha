<?php

defined( 'ABSPATH' ) || exit;

/**
 * Theme Support
 */
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'woocommerce' );

    // Tussenmaten voor srcset, zodat de browser nooit een te grote foto hoeft te decoderen.
    add_image_size( 'mori-480', 480, 0 );
    add_image_size( 'mori-720', 720, 0 );
    add_image_size( 'mori-1000', 1000, 0 );
    add_image_size( 'mori-1440', 1440, 0 );
    add_image_size( 'mori-1920', 1920, 0 );
} );

/**
 * No Core update Emails
 */
add_filter( 'auto_core_update_send_email', '__return_false' );
