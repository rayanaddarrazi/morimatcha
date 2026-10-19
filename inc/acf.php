<?php

defined( 'ABSPATH' ) || exit;

/**
 * ACF Pro Options: één instellingenpagina "Instellingen Mori".
 */
add_action( 'acf/init', 'kj_register_acf_options_pages' );
function kj_register_acf_options_pages() {

    if ( ! function_exists( 'acf_add_options_page' ) ) {
        return;
    }

    acf_add_options_page( array(
        'page_title' => __( 'Instellingen Mori', 'morimatcha' ),
        'menu_title' => __( 'Instellingen Mori', 'morimatcha' ),
        'menu_slug'  => 'instellingen-mori',
        'icon_url'   => 'dashicons-admin-generic',
        'position'   => 60,
    ) );
}

// ACF JSON Save/Load
add_filter( 'acf/settings/save_json', function ( $path ) {
    return get_template_directory() . '/acf-json';
} );

add_filter( 'acf/settings/load_json', function ( $paths ) {
    unset( $paths[0] );
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
} );

/**
 * Auto Escape ACF Field
 */
function kj_acf( $field_name, $post_id = null ) {

    if ( 'sub' === $post_id ) {
        $field_value = get_sub_field( $field_name );
    } else {
        $field_value = $post_id ? get_field( $field_name, $post_id ) : get_field( $field_name );
    }

    echo kj_escape_html( $field_value );
}

function kj_escape_html( $text ) {

    // Escape HTML while preserving allowed tags
    return wp_kses( (string) $text, array(
        'br'     => array(),
        'p'      => array(),
        'h2'     => array(),
        'h3'     => array(),
        'h4'     => array(),
        'strong' => array(),
        'em'     => array(),
        'span'   => array(),
        'ul'     => array(),
        'ol'     => array(),
        'li'     => array(),
        'a'      => array(
            'href'   => array(),
            'title'  => array(),
            'target' => array(),
            'rel'    => array(),
        ),
    ) );
}

/**
 * Optiewaarde uit Instellingen Mori.
 */
function kj_option( $name, $default = '' ) {
    $value = get_field( $name, 'option' );
    return ( null === $value || false === $value || '' === $value ) ? $default : $value;
}

/**
 * Render a component from templates/components/ with arguments.
 *
 * @param string $component Filename without .php
 * @param array  $args      Passed to the template as $args
 */
function kj_component( $component, $args = array() ) {
    get_template_part( 'templates/components/' . $component, null, $args );
}

/**
 * Return a component as string.
 */
function kj_component_html( $component, $args = array() ) {
    ob_start();
    kj_component( $component, $args );
    return ob_get_clean();
}
