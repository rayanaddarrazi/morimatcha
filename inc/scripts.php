<?php

defined( 'ABSPATH' ) || exit;

/**
 * Scripts
 */
function kj_scripts() {

    wp_register_script( 'kj-script', get_template_directory_uri() . '/assets/js/script.js', array(), filemtime( get_stylesheet_directory() . '/assets/js/script.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
    wp_enqueue_script( 'kj-script' );

    // Pagebuilder blocks JavaScript
    wp_register_script( 'kj-pagebuilder', get_template_directory_uri() . '/assets/js/pagebuilder.js', array(), filemtime( get_stylesheet_directory() . '/assets/js/pagebuilder.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
    wp_enqueue_script( 'kj-pagebuilder' );
}
add_action( 'wp_enqueue_scripts', 'kj_scripts' );

/**
 * Stylesheets
 */
function kj_styles() {

    wp_register_style( 'style', get_template_directory_uri() . '/assets/css/style.css', array(), filemtime( get_theme_file_path( '/assets/css/style.css' ) ), 'all' );
    wp_enqueue_style( 'style' );
}
add_action( 'wp_enqueue_scripts', 'kj_styles' );

/**
 * Preload de lettertypes (latin) tegen layoutverschuiving.
 */
add_action( 'wp_head', function () {
    $base = get_template_directory_uri() . '/assets/fonts/';
    foreach ( array( 'fraunces-latin.woff2', 'hanken-grotesk-latin.woff2' ) as $font ) {
        printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( $base . $font ) );
    }
}, 1 );
