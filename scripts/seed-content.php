<?php
/**
 * Seed: zet de inhoud uit het Figma-ontwerp in WordPress (instellingen, afbeeldingen, producten, menu's, pagina's).
 *
 * Gebruik (vanuit de themamap):  wp eval-file scripts/seed-content.php
 *
 * Het script is herhaalbaar: bestaande afbeeldingen, producten en pagina's worden bijgewerkt.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$theme = get_template_directory();

/**
 * Importeer een afbeelding uit scripts/seed/images in de mediabibliotheek (één keer) en geef het ID terug.
 */
function kj_seed_image( $file, $alt ) {
    $existing = get_posts( array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => 1,
        'meta_key'       => '_kj_seed_key',
        'meta_value'     => $file,
        'fields'         => 'ids',
    ) );

    if ( $existing ) {
        update_post_meta( $existing[0], '_wp_attachment_image_alt', $alt );
        return (int) $existing[0];
    }

    $source = get_template_directory() . '/scripts/seed/images/' . $file . '.jpg';
    if ( ! file_exists( $source ) ) {
        WP_CLI::warning( 'Afbeelding ontbreekt: ' . $file );
        return 0;
    }
    $tmp = wp_tempnam( $file );
    copy( $source, $tmp );

    $id = media_handle_sideload( array( 'name' => $file . '.jpg', 'tmp_name' => $tmp ), 0, $alt );
    if ( is_wp_error( $id ) ) {
        WP_CLI::error( $file . ': ' . $id->get_error_message() );
    }
    update_post_meta( $id, '_kj_seed_key', $file );
    update_post_meta( $id, '_wp_attachment_image_alt', $alt );

    return (int) $id;
}

/** Importeer een themabeeld (logo, betaallogo) in de mediabibliotheek. */
function kj_seed_theme_image( $file, $alt ) {
    $existing = get_posts( array(
        'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1,
        'meta_key' => '_kj_seed_key', 'meta_value' => $file, 'fields' => 'ids',
    ) );
    if ( $existing ) {
        return (int) $existing[0];
    }
    $source = get_template_directory() . '/assets/images/' . $file;
    $tmp    = wp_tempnam( $file );
    copy( $source, $tmp );
    $id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), 0, $alt );
    if ( is_wp_error( $id ) ) {
        WP_CLI::error( $file . ': ' . $id->get_error_message() );
    }
    update_post_meta( $id, '_kj_seed_key', $file );
    update_post_meta( $id, '_wp_attachment_image_alt', $alt );
    return (int) $id;
}

foreach ( glob( $theme . '/scripts/seed/part-*.php' ) as $part ) {
    WP_CLI::log( '== ' . basename( $part ) );
    include $part;
}

flush_rewrite_rules();
WP_CLI::success( 'Inhoud geplaatst.' );
