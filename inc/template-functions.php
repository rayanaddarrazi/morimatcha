<?php

defined( 'ABSPATH' ) || exit;

/**
 * Template Functions
 */

/**
 * Format a price number the Dutch way: 19.95 => "19,95", 40 => "40".
 */
function kj_format_amount( $amount, $decimals = 2 ) {
    $amount = (float) $amount;
    return number_format( $amount, $decimals, ',', '.' );
}

/**
 * "€ 19,95"
 */
function kj_format_price( $amount, $decimals = 2 ) {
    return '€ ' . kj_format_amount( $amount, $decimals );
}

/**
 * Tekst die op mobiel korter is dan op desktop (zoals in het ontwerp). Geeft veilige HTML terug.
 */
function kj_rt( $desktop, $mobiel = '' ) {
    $desktop = (string) $desktop;
    $mobiel  = (string) $mobiel;
    if ( '' === $mobiel || $mobiel === $desktop ) {
        return esc_html( $desktop );
    }
    return '<span class="lg:hidden">' . esc_html( $mobiel ) . '</span><span class="hidden lg:inline">' . esc_html( $desktop ) . '</span>';
}

/**
 * Inline a theme SVG from assets/icons/ (line icons use currentColor).
 */
function kj_svg( $name, $class = '' ) {
    $file = get_theme_file_path( '/assets/icons/' . sanitize_file_name( $name ) . '.svg' );
    if ( ! file_exists( $file ) ) {
        return '';
    }
    $svg = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
    $svg = preg_replace( '/^<svg/', '<svg aria-hidden="true" focusable="false"' . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ), ltrim( $svg ), 1 );
    return $svg;
}

/**
 * Attachment ID from an ACF image array, ID or URL-less value.
 */
function kj_image_id( $image ) {
    if ( is_array( $image ) && isset( $image['ID'] ) ) {
        return (int) $image['ID'];
    }
    if ( is_numeric( $image ) ) {
        return (int) $image;
    }
    return 0;
}

/**
 * Logo uit Instellingen Mori; zonder upload valt het terug op het ontwerp-logo in het thema.
 *
 * @param string $kleur  'zwart' (lichte achtergrond) of 'wit' (donkere achtergrond / foto)
 * @param string $type   'volledig' of 'woordmerk'
 */
function kj_logo( $kleur = 'zwart', $type = 'volledig', $class = '' ) {
    $field = 'logo_' . $type . '_' . $kleur;
    $image = get_field( $field, 'option' );
    $name  = (string) kj_option( 'bedrijfsnaam', get_bloginfo( 'name' ) );

    if ( $image && ! empty( $image['ID'] ) ) {
        return wp_get_attachment_image( $image['ID'], 'full', false, array(
            'class'    => $class,
            'alt'      => $name,
            'decoding' => 'async',
        ) );
    }

    $file = 'logo-' . ( 'volledig' === $type ? '' : 'woordmerk-' ) . $kleur . '.png';
    if ( ! file_exists( get_theme_file_path( '/assets/images/' . $file ) ) ) {
        return '';
    }

    return sprintf(
        '<img src="%s" alt="%s" class="%s" decoding="async">',
        esc_url( get_theme_file_uri( '/assets/images/' . $file ) ),
        esc_attr( $name ),
        esc_attr( $class )
    );
}

/**
 * URL van een ACF-linkveld (array) of tekst.
 */
function kj_link_url( $link ) {
    if ( is_array( $link ) ) {
        return (string) ( $link['url'] ?? '' );
    }
    return (string) $link;
}

/**
 * Argumenten voor kj_component('button') uit een ACF-linkveld.
 */
function kj_link_args( $link, $variant = 'primair', $extra = array() ) {
    if ( ! is_array( $link ) || empty( $link['title'] ) ) {
        return null;
    }
    return array_merge( array(
        'label'   => $link['title'],
        'url'     => $link['url'] ?? '',
        'target'  => $link['target'] ?? '',
        'variant' => $variant,
    ), $extra );
}

/**
 * Achtergrondkleur-klasse van een sectie (ACF select 'achtergrond').
 */
function kj_section_bg( $default = 'rijstpapier' ) {
    $bg = get_sub_field( 'achtergrond' );
    $bg = $bg ? $bg : $default;
    $ruimte = get_sub_field( 'ruimte' );
    $ruimte = ( $ruimte && 'standaard' !== $ruimte ) ? ' ruimte-' . (int) $ruimte : '';
    return 'sectie sectie--' . sanitize_html_class( $bg ) . $ruimte;
}

/**
 * Ankerattribuut voor een sectie.
 */
function kj_anchor_attr() {
    $anker = get_sub_field( 'anker' );
    return $anker ? ' id="' . esc_attr( ltrim( (string) $anker, '#' ) ) . '"' : '';
}

/**
 * Is de eerste sectie van deze pagina een fotohero? Dan krijgt de header het thema "Op foto".
 */
function kj_header_over_photo() {
    if ( is_admin() ) {
        return false;
    }
    if ( function_exists( 'is_singular' ) && is_singular( 'page' ) && function_exists( 'get_field' ) ) {
        $rows = get_field( 'pagebuilder' );
        if ( is_array( $rows ) && ! empty( $rows[0]['acf_fc_layout'] ) ) {
            return in_array( $rows[0]['acf_fc_layout'], array( 'hero', 'fotoband' ), true );
        }
    }
    return false;
}

/**
 * Menu-items van een WordPress-menulocatie (name + url).
 *
 * @return array<int,array{naam:string,url:string,actief:bool}>
 */
function kj_menu_items( $location ) {
    $locations = get_nav_menu_locations();
    if ( empty( $locations[ $location ] ) ) {
        return array();
    }
    $items = wp_get_nav_menu_items( $locations[ $location ] );
    if ( ! $items ) {
        return array();
    }
    $out = array();
    foreach ( $items as $item ) {
        if ( (int) $item->menu_item_parent ) {
            continue;
        }
        $out[] = array(
            'naam'   => $item->title,
            'kort'   => (string) $item->description,
            'klassen' => implode( ' ', array_filter( (array) $item->classes ) ),
            'url'    => $item->url,
            'actief' => $item->current || $item->current_item_ancestor,
        );
    }
    return $out;
}

add_action( 'after_setup_theme', function () {
    register_nav_menus( array(
        'hoofdmenu'    => __( 'Hoofdmenu (header)', 'morimatcha' ),
        'footer_shop'  => __( 'Footer: kolom Shop', 'morimatcha' ),
        'footer_hulp'  => __( 'Footer: kolom Hulp', 'morimatcha' ),
        'footer_mori'  => __( 'Footer: kolom Mori', 'morimatcha' ),
    ) );
} );

/**
 * Afbeeldingen: nieuwe formaten worden WebP (de originele upload blijft als JPG/PNG-terugval bestaan).
 */
add_filter( 'image_editor_output_format', function ( $formats ) {
    $formats['image/jpeg'] = 'image/webp';
    $formats['image/png']  = 'image/webp';
    return $formats;
} );
