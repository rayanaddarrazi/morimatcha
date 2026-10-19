<?php

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce-koppeling: winkelwagenpaneel, aankoopkeuze (eenmalig / elke maand) en AJAX.
 * De rest (abonnementen, account) staat in inc/abonnementen.php.
 */

if ( ! class_exists( 'WooCommerce' ) ) {
    function kj_cart_count() {
        return 0;
    }
    return;
}

/**
 * Standaard WooCommerce-stijlen uit: het thema levert alle opmaak zelf.
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Aantal items in de winkelwagen.
 */
function kj_cart_count() {
    return WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

/**
 * Levering van een winkelwagenregel: 'eenmalig' of 'maand'.
 */
function kj_cart_item_levering( $cart_item ) {
    return ( isset( $cart_item['mori_levering'] ) && 'maand' === $cart_item['mori_levering'] ) ? 'maand' : 'eenmalig';
}

/**
 * Aankoopkeuze meenemen in de winkelwagen (los per keuze, dus eenmalig en maandelijks zijn aparte regels).
 */
add_filter( 'woocommerce_add_cart_item_data', function ( $data ) {
    if ( isset( $_REQUEST['mori_levering'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        $levering = 'maand' === sanitize_key( wp_unslash( $_REQUEST['mori_levering'] ) ) ? 'maand' : 'eenmalig'; // phpcs:ignore WordPress.Security.NonceVerification
        $data['mori_levering'] = $levering;
    }
    return $data;
} );

add_filter( 'woocommerce_get_item_data', function ( $item_data, $cart_item ) {
    if ( 'maand' === kj_cart_item_levering( $cart_item ) ) {
        $item_data[] = array(
            'key'   => __( 'Levering', 'morimatcha' ),
            'value' => (string) kj_option( 'levering_label_maand', 'elke maand' ),
        );
    }
    return $item_data;
}, 10, 2 );

add_action( 'woocommerce_checkout_create_order_line_item', function ( $item, $cart_item_key, $values ) {
    if ( isset( $values['mori_levering'] ) ) {
        $item->add_meta_data( '_mori_levering', $values['mori_levering'], true );
        if ( 'maand' === $values['mori_levering'] ) {
            $item->add_meta_data( __( 'Levering', 'morimatcha' ), (string) kj_option( 'levering_label_maand', 'elke maand' ), true );
        }
    }
}, 10, 3 );

/**
 * Prijs per gram van een maat (voor de maatkiezer).
 */
function kj_price_per_gram( $price, $gram ) {
    if ( ! $gram ) {
        return '';
    }
    return kj_format_price( (float) $price / (float) $gram );
}

/**
 * Gewicht in gram uit de variatienaam of het gewichtveld.
 */
function kj_variation_gram( $variation ) {
    $weight = (float) $variation->get_weight();
    if ( $weight > 0 ) {
        return $weight < 5 ? $weight * 1000 : $weight; // kg of g
    }
    $attr = implode( ' ', array_values( $variation->get_attributes() ) );
    return (float) preg_replace( '/[^0-9.]/', '', $attr );
}

/**
 * Het winkelwagenpaneel (inhoud). Wordt ook als WooCommerce-fragment ververst.
 */
function kj_cart_drawer_inner() {
    $cart = WC()->cart;
    if ( ! $cart ) {
        return '';
    }

    $drempel   = (float) kj_option( 'gratis_verzending_vanaf', 40 );
    $subtotaal = (float) $cart->get_subtotal() + (float) $cart->get_subtotal_tax();
    $rest      = max( 0, $drempel - $subtotaal );
    $voortgang = $drempel > 0 ? min( 100, round( $subtotaal / $drempel * 100 ) ) : 100;

    ob_start();
    ?>
    <div class="cart-drawer__verzending" style="--voortgang: <?php echo (int) $voortgang; ?>%">
        <p>
            <?php
            if ( $cart->is_empty() ) {
                echo esc_html( sprintf( (string) kj_option( 'cart_verzending_start', 'Gratis verzending vanaf € %s.' ), kj_format_amount( $drempel, 0 ) ) );
            } elseif ( $rest <= 0 ) {
                echo esc_html( (string) kj_option( 'cart_verzending_gratis', 'Je bestelling wordt gratis verzonden.' ) );
            } else {
                echo esc_html( sprintf( (string) kj_option( 'cart_verzending_bijna', 'Nog € %s tot gratis verzending.' ), kj_format_amount( $rest ) ) );
            }
            ?>
        </p>
        <div class="cart-drawer__voortgang" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo (int) $voortgang; ?>"><span></span></div>
    </div>

    <div class="cart-drawer__producten">
        <?php if ( $cart->is_empty() ) : ?>
            <p class="cart-drawer__leeg"><?php echo esc_html( (string) kj_option( 'cart_leeg', 'Je winkelwagen is nog leeg.' ) ); ?></p>
        <?php else : ?>
            <?php foreach ( $cart->get_cart() as $key => $item ) :
                $product = $item['data'];
                if ( ! $product || ! $product->exists() ) {
                    continue;
                }
                $levering = kj_cart_item_levering( $item );
                $is_maand = 'maand' === $levering;
                $parent   = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() ) : $product;
                $naam     = $parent ? $parent->get_name() : $product->get_name();
                $maat     = $product->is_type( 'variation' ) ? wc_get_formatted_variation( $product, true, false ) : '';
                $maat     = $maat ? preg_replace( '/^[^:]+:\s*/', '', $maat ) : '';
                $label    = $is_maand ? (string) kj_option( 'levering_label_maand', 'elke maand' ) : (string) kj_option( 'levering_label_eenmalig', 'eenmalig' );
                $meta     = trim( ( $maat ? $maat . ', ' : '' ) . $label );
                $prijs    = $is_maand ? kj_subscription_price( $product ) : (float) $product->get_price();
                $regel    = $prijs * (int) $item['quantity'];
                ?>
                <div class="cart-regel" data-key="<?php echo esc_attr( $key ); ?>">
                    <div class="cart-regel__beeld"><?php echo wp_get_attachment_image( $product->get_image_id() ?: ( $parent ? $parent->get_image_id() : 0 ), 'mori-480', false, array( 'loading' => 'lazy', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
                    <div class="cart-regel__midden">
                        <p class="cart-regel__naam"><?php echo esc_html( $naam ); ?></p>
                        <p class="cart-regel__meta"><?php echo $is_maand ? kj_svg( 'herhalen' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $meta ); ?></span></p>
                        <div class="aantal" role="group" aria-label="<?php esc_attr_e( 'Aantal', 'morimatcha' ); ?>">
                            <button type="button" data-cart-qty="-1" aria-label="<?php esc_attr_e( 'Minder', 'morimatcha' ); ?>"<?php echo (int) $item['quantity'] <= 1 ? ' disabled' : ''; ?>><?php echo kj_svg( 'min' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
                            <span class="aantal__waarde"><?php echo (int) $item['quantity']; ?></span>
                            <button type="button" data-cart-qty="1" aria-label="<?php esc_attr_e( 'Meer', 'morimatcha' ); ?>"><?php echo kj_svg( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
                        </div>
                    </div>
                    <div class="cart-regel__rechts">
                        <span class="cart-regel__prijs"><?php echo esc_html( kj_format_price( $regel ) ); ?></span>
                        <button type="button" class="cart-regel__verwijder" data-cart-remove><?php echo esc_html( (string) kj_option( 'cart_verwijder', 'Verwijder' ) ); ?></button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="cart-drawer__totaal">
        <div class="cart-drawer__rij"><span><?php esc_html_e( 'Subtotaal', 'morimatcha' ); ?></span><span><?php echo esc_html( kj_format_price( $subtotaal ) ); ?></span></div>
        <div class="cart-drawer__rij"><span><?php esc_html_e( 'Verzending', 'morimatcha' ); ?></span><span><?php echo ( $rest <= 0 && ! $cart->is_empty() ) ? esc_html( (string) kj_option( 'cart_verzending_gratis_kort', 'Gratis' ) ) : esc_html( (string) kj_option( 'cart_verzending_later', 'Bij het afrekenen' ) ); ?></span></div>
        <div class="cart-drawer__rij cart-drawer__rij--totaal"><span><?php esc_html_e( 'Totaal', 'morimatcha' ); ?></span><span><?php echo esc_html( kj_format_price( $subtotaal ) ); ?></span></div>
        <?php
        kj_component( 'button', array(
            'label'    => (string) kj_option( 'cart_afrekenen', 'Afrekenen' ),
            'url'      => wc_get_checkout_url(),
            'variant'  => 'primair',
            'class'    => 'kj-button--vol',
            'disabled' => $cart->is_empty(),
        ) );
        ?>
        <p class="cart-drawer__noot"><?php echo esc_html( (string) kj_option( 'cart_noot' ) ); ?></p>
    </div>
    <?php
    return ob_get_clean();
}

add_action( 'woocommerce_before_calculate_totals', function ( $cart ) {
    if ( is_admin() && ! wp_doing_ajax() ) {
        return;
    }
    foreach ( $cart->get_cart() as $item ) {
        if ( 'maand' === kj_cart_item_levering( $item ) ) {
            $item['data']->set_price( kj_subscription_price( $item['data'] ) );
        }
    }
}, 20 );

/**
 * Fragmenten: paneel en teller verversen na een wijziging.
 */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
    $fragments['.cart-drawer__binnen'] = '<div class="cart-drawer__binnen">' . kj_cart_drawer_inner() . '</div>';
    $fragments['[data-cart-count]']    = '<span class="site-header__teller" data-cart-count>' . kj_cart_count() . '</span>';
    return $fragments;
} );

/**
 * AJAX zonder jQuery: toevoegen, aantal wijzigen, verwijderen. Geeft het paneel en de teller terug.
 */
function kj_cart_ajax_response() {
    WC()->cart->calculate_totals();
    wp_send_json_success( array(
        'drawer' => kj_cart_drawer_inner(),
        'count'  => kj_cart_count(),
    ) );
}

add_action( 'wp_ajax_mori_cart', 'kj_ajax_cart' );
add_action( 'wp_ajax_nopriv_mori_cart', 'kj_ajax_cart' );
function kj_ajax_cart() {
    check_ajax_referer( 'mori_cart', 'nonce' );

    if ( null === WC()->cart ) {
        wc_load_cart();
    }

    $actie = isset( $_POST['actie'] ) ? sanitize_key( wp_unslash( $_POST['actie'] ) ) : '';

    if ( 'toevoegen' === $actie ) {
        $product_id   = absint( $_POST['product_id'] ?? 0 );
        $variation_id = absint( $_POST['variation_id'] ?? 0 );
        $levering     = ( isset( $_POST['mori_levering'] ) && 'maand' === sanitize_key( wp_unslash( $_POST['mori_levering'] ) ) ) ? 'maand' : 'eenmalig';
        $aantal       = max( 1, absint( $_POST['aantal'] ?? 1 ) );
        $_REQUEST['mori_levering'] = $levering;

        $variation = array();
        if ( $variation_id ) {
            $variation = wc_get_product( $variation_id )->get_variation_attributes();
        }
        $ok = WC()->cart->add_to_cart( $product_id, $aantal, $variation_id, $variation );
        if ( ! $ok ) {
            wp_send_json_error( array( 'melding' => wp_strip_all_tags( implode( ' ', wc_get_notices( 'error' ) ? array_column( wc_get_notices( 'error' ), 'notice' ) : array( __( 'Toevoegen is niet gelukt.', 'morimatcha' ) ) ) ) ) );
        }
    } elseif ( 'aantal' === $actie ) {
        $key = sanitize_text_field( wp_unslash( $_POST['key'] ?? '' ) );
        $qty = absint( $_POST['aantal'] ?? 1 );
        WC()->cart->set_quantity( $key, $qty, true );
    } elseif ( 'verwijderen' === $actie ) {
        $key = sanitize_text_field( wp_unslash( $_POST['key'] ?? '' ) );
        WC()->cart->remove_cart_item( $key );
    }

    wc_clear_notices();
    kj_cart_ajax_response();
}

add_action( 'wp_ajax_mori_cart_ophalen', 'kj_ajax_cart_ophalen' );
add_action( 'wp_ajax_nopriv_mori_cart_ophalen', 'kj_ajax_cart_ophalen' );
function kj_ajax_cart_ophalen() {
    if ( null === WC()->cart ) {
        wc_load_cart();
    }
    kj_cart_ajax_response();
}

/**
 * Gegevens voor het script.
 */
add_action( 'wp_enqueue_scripts', function () {
    wp_localize_script( 'kj-script', 'moriCart', array(
        'ajax'  => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'mori_cart' ),
    ) );
}, 20 );

/**
 * Nieuwsbriefaanmelding (eigen lijstje in de database, zichtbaar in het dashboard).
 */
add_action( 'init', function () {
    register_post_type( 'nieuwsbrief', array(
        'labels'       => array( 'name' => 'Nieuwsbrief', 'singular_name' => 'Aanmelding', 'menu_name' => 'Nieuwsbrief', 'all_items' => 'Alle aanmeldingen' ),
        'public'       => false,
        'show_ui'      => true,
        'menu_icon'    => 'dashicons-email-alt',
        'menu_position' => 25,
        'supports'     => array( 'title' ),
        'capabilities' => array( 'create_posts' => 'do_not_allow' ),
        'map_meta_cap' => true,
    ) );
} );

add_action( 'admin_post_nopriv_mori_nieuwsbrief', 'kj_handle_nieuwsbrief' );
add_action( 'admin_post_mori_nieuwsbrief', 'kj_handle_nieuwsbrief' );
function kj_handle_nieuwsbrief() {
    $terug = wp_get_referer() ? remove_query_arg( 'nieuwsbrief', wp_get_referer() ) : home_url( '/' );
    $mail  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    if ( ! isset( $_POST['mori_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mori_nonce'] ) ), 'mori_nieuwsbrief' ) || ! is_email( $mail ) ) {
        wp_safe_redirect( add_query_arg( 'nieuwsbrief', 'fout', $terug ) . '#nieuwsbrief-mail' );
        exit;
    }
    $bestaat = get_posts( array( 'post_type' => 'nieuwsbrief', 'title' => $mail, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
    if ( ! $bestaat ) {
        wp_insert_post( array( 'post_type' => 'nieuwsbrief', 'post_title' => $mail, 'post_status' => 'publish' ) );
    }
    wp_safe_redirect( add_query_arg( 'nieuwsbrief', 'ok', $terug ) . '#nieuwsbrief-mail' );
    exit;
}

/**
 * Maten van een product: bij een variabel product de variaties, anders het product zelf.
 *
 * @return array<int,array{id:int,label:string,gram:float,price:float,regular:float,in_stock:bool,per_gram:string,koppen:int}>
 */
function kj_product_sizes( $product ) {
    $product = is_numeric( $product ) ? wc_get_product( $product ) : $product;
    if ( ! $product ) {
        return array();
    }

    $per_kop = max( 0.1, (float) kj_option( 'gram_per_kop', 2 ) );
    $rows    = array();
    $list    = $product->is_type( 'variable' ) ? array_map( 'wc_get_product', $product->get_children() ) : array( $product );

    foreach ( $list as $item ) {
        if ( ! $item || ! $item->is_purchasable() && ! $item->exists() ) {
            continue;
        }
        $gram = kj_variation_gram( $item );
        $rows[] = array(
            'id'       => $item->get_id(),
            'label'    => $item->is_type( 'variation' ) ? trim( preg_replace( '/^[^:]+:\s*/', '', wc_get_formatted_variation( $item, true, false ) ) ) : $item->get_name(),
            'gram'     => $gram,
            'price'    => (float) $item->get_price(),
            'regular'  => (float) $item->get_regular_price(),
            'in_stock' => $item->is_in_stock(),
            'per_gram' => $gram ? kj_format_amount( (float) $item->get_price() / $gram ) : '',
            'koppen'   => $gram ? (int) floor( $gram / $per_kop ) : 0,
        );
    }

    usort( $rows, fn( $a, $b ) => $a['gram'] <=> $b['gram'] );

    return $rows;
}

/**
 * Welke maat is het voordeligst per gram?
 */
function kj_cheapest_size_id( $sizes ) {
    $best = null;
    foreach ( $sizes as $size ) {
        if ( $size['gram'] && ( null === $best || ( $size['price'] / $size['gram'] ) < ( $best['price'] / $best['gram'] ) ) ) {
            $best = $size;
        }
    }
    return $best ? $best['id'] : 0;
}

/**
 * Productgegevens voor de aankoopblokken (productpagina en kaart).
 */
function kj_product_badges( $product_id ) {
    return array(
        'blad'   => (string) get_field( 'badge_blad', $product_id ),
        'smaken' => array_values( array_filter( array_column( (array) get_field( 'smaakbadges', $product_id ), 'tekst' ) ) ),
    );
}

/**
 * Prijs van de eenmalige keuze en het abonnement voor één maat.
 */
function kj_size_prices( $size ) {
    $korting = (float) kj_option( 'abonnement_korting', 10 );
    $maand   = $korting > 0 ? floor( round( $size['price'] * ( 100 - $korting ), 4 ) ) / 100 : $size['price'];
    return array( 'eenmalig' => $size['price'], 'maand' => $maand );
}

/**
 * Abonnementsprijs: korting uit Instellingen Mori.
 */
function kj_subscription_price( $product ) {
    $price   = (float) $product->get_price();
    $korting = (float) kj_option( 'abonnement_korting', 10 );
    return $korting > 0 ? floor( round( $price * ( 100 - $korting ), 4 ) ) / 100 : $price;
}

/**
 * Winkelwagen-scripts en productscript.
 */
add_action( 'wp_enqueue_scripts', function () {
    if ( is_product() ) {
        wp_register_script( 'kj-product', get_template_directory_uri() . '/assets/js/product.js', array( 'kj-script' ), filemtime( get_stylesheet_directory() . '/assets/js/product.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
        wp_enqueue_script( 'kj-product' );
    }
}, 30 );

/**
 * Geen standaard WooCommerce-wrappers, broodkruimels of notices op productpagina en shop (het thema doet dat zelf).
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );


/**
 * Snelheid: WooCommerce-scripts (en daarmee jQuery) alleen laden waar ze nodig zijn: kassa en account.
 * Het winkelwagenpaneel en de productpagina hebben hun eigen kleine scripts.
 */
add_action( 'wp_enqueue_scripts', function () {
    if ( is_checkout() || is_account_page() ) {
        return;
    }
    foreach ( array( 'wc-cart-fragments', 'woocommerce', 'wc-add-to-cart', 'jquery-blockui', 'js-cookie', 'sourcebuster-js', 'wc-order-attribution', 'wc-jquery-blockui' ) as $handle ) {
        wp_dequeue_script( $handle );
    }
}, 100 );

/** jQuery Migrate is niet nodig (WooCommerce en dit thema gebruiken geen verouderde jQuery-functies). */
add_action( 'wp_default_scripts', function ( $scripts ) {
    if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
    }
} );

/** Metabeschrijving: per pagina (veld "SEO-beschrijving") of de standaardtekst uit Instellingen Mori. */
add_action( 'wp_head', function () {
    $tekst = '';
    if ( is_singular() ) {
        $tekst = (string) get_field( 'seo_beschrijving', get_queried_object_id() );
    }
    if ( ! $tekst ) {
        $tekst = (string) kj_option( 'seo_beschrijving_standaard', '' );
    }
    if ( $tekst ) {
        echo '<meta name="description" content="' . esc_attr( $tekst ) . '">' . "\n";
    }
}, 1 );
