<?php
/**
 * TIJDELIJK: zet de bibliotheekplaatjes van WebP terug naar het JPG/PNG-origineel (en terug).
 *
 * Voor de test xSpeed Cache Pro tegenover Smush Pro. Verwijder dit bestand en de require-regel
 * in functions.php als de test klaar is.
 *
 * Gebruik (alleen ingelogd als beheerder, via de REST API):
 *   POST /wp-json/kj/v1/beeld-omzetten  { "modus": "dry" | "uitvoeren" | "terug", "offset": 0, "limit": 5 }
 *
 * - dry: laat zien wat er zou gebeuren, verandert niets.
 * - uitvoeren: wijst het plaatje naar het originele JPG/PNG en maakt de formaten opnieuw als JPG/PNG.
 *   De oude WebP-bestanden worden NIET verwijderd en de oude gegevens worden bewaard.
 * - terug: zet de bewaarde WebP-gegevens terug.
 *
 * @package morimatcha
 */

add_action( 'rest_api_init', function () {
    register_rest_route( 'kj/v1', '/beeld-omzetten', array(
        'methods'             => 'POST',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        },
        'callback'            => 'kj_beeld_omzetten',
    ) );
} );

/**
 * @param WP_REST_Request $request Verzoek met modus, offset en limit.
 * @return array
 */
function kj_beeld_omzetten( WP_REST_Request $request ) {
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $modus  = in_array( $request['modus'], array( 'dry', 'uitvoeren', 'terug' ), true ) ? $request['modus'] : 'dry';
    $offset = max( 0, (int) $request['offset'] );
    $limit  = min( 20, max( 1, (int) ( $request['limit'] ?: 5 ) ) );

    // Zolang de WebP-filter aan staat, zou de omzetting weer WebP maken. Dan stoppen we.
    if ( 'uitvoeren' === $modus && has_filter( 'image_editor_output_format' ) ) {
        $formats = apply_filters( 'image_editor_output_format', array(), '', 'image/jpeg' );
        if ( ! empty( $formats['image/jpeg'] ) ) {
            return new WP_Error( 'kj_filter_aan', 'De WebP-filter staat nog aan.', array( 'status' => 409 ) );
        }
    }

    $ids = get_posts( array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'post_mime_type' => array( 'image/jpeg', 'image/png' ),
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'posts_per_page' => $limit,
        'offset'         => $offset,
        'fields'         => 'ids',
    ) );
    $totaal = (int) ( new WP_Query( array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'post_mime_type' => array( 'image/jpeg', 'image/png' ),
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ) ) )->found_posts;

    $upload = wp_get_upload_dir();
    $rijen  = array();

    foreach ( $ids as $id ) {
        $file = get_post_meta( $id, '_wp_attached_file', true );
        $meta = wp_get_attachment_metadata( $id );
        $rij  = array( 'id' => (int) $id, 'file' => $file );

        if ( 'terug' === $modus ) {
            $bak = get_post_meta( $id, '_kj_webp_terug', true );
            if ( empty( $bak['file'] ) ) {
                $rij['uitkomst'] = 'overgeslagen: geen bewaarde WebP-gegevens';
            } else {
                update_post_meta( $id, '_wp_attached_file', $bak['file'] );
                wp_update_attachment_metadata( $id, $bak['meta'] );
                $rij['uitkomst'] = 'terug naar ' . $bak['file'];
            }
            $rijen[] = $rij;
            continue;
        }

        if ( ! is_array( $meta ) || ! preg_match( '/\.webp$/i', (string) $file ) ) {
            $rij['uitkomst'] = 'overgeslagen: geen WebP met origineel';
            $rijen[]         = $rij;
            continue;
        }

        $dir = dirname( $file );
        if ( ! empty( $meta['original_image'] ) ) {
            $orig_naam = $meta['original_image'];
        } else {
            // Geen vermelding van het origineel: zoek een bestand met dezelfde naam als JPG of PNG.
            $orig_naam = '';
            $basis     = preg_replace( '/\.webp$/i', '', basename( $file ) );
            foreach ( array( 'png', 'jpg', 'jpeg' ) as $ext ) {
                if ( file_exists( $upload['basedir'] . '/' . ( '.' === $dir ? '' : $dir . '/' ) . $basis . '.' . $ext ) ) {
                    $orig_naam = $basis . '.' . $ext;
                    break;
                }
            }
            if ( '' === $orig_naam ) {
                $rij['uitkomst'] = 'overgeslagen: geen WebP met origineel';
                $rijen[]         = $rij;
                continue;
            }
        }

        $orig   = ( '.' === $dir ? '' : $dir . '/' ) . $orig_naam;
        $abs    = $upload['basedir'] . '/' . $orig;
        $rij['origineel'] = $orig;

        if ( ! file_exists( $abs ) ) {
            $rij['uitkomst'] = 'overgeslagen: origineel ontbreekt';
            $rijen[]         = $rij;
            continue;
        }

        if ( 'dry' === $modus ) {
            $rij['uitkomst'] = 'zou omgezet worden (' . size_format( filesize( $abs ) ) . ')';
            $rijen[]         = $rij;
            continue;
        }

        // Uitvoeren: eerst bewaren, dan omzetten.
        if ( ! get_post_meta( $id, '_kj_webp_terug', true ) ) {
            update_post_meta( $id, '_kj_webp_terug', array( 'file' => $file, 'meta' => $meta ) );
        }
        update_post_meta( $id, '_wp_attached_file', $orig );
        $nieuw = wp_generate_attachment_metadata( $id, $abs );

        if ( is_wp_error( $nieuw ) || empty( $nieuw['file'] ) ) {
            update_post_meta( $id, '_wp_attached_file', $file );
            wp_update_attachment_metadata( $id, $meta );
            $rij['uitkomst'] = 'mislukt, teruggezet';
        } else {
            wp_update_attachment_metadata( $id, $nieuw );
            $rij['uitkomst'] = 'omgezet: ' . $nieuw['file'] . ', ' . count( $nieuw['sizes'] ?? array() ) . ' formaten';
        }
        $rijen[] = $rij;
    }

    return array(
        'modus'  => $modus,
        'offset' => $offset,
        'limit'  => $limit,
        'totaal' => $totaal,
        'volgende' => ( $offset + $limit < $totaal ) ? $offset + $limit : null,
        'rijen'  => $rijen,
    );
}
