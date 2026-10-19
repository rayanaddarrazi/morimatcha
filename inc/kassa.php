<?php
/**
 * Kassa, bevestiging en Mijn account: velden, regels en hulpfuncties.
 */

defined( 'ABSPATH' ) || exit;

/** Zit er een maandabonnement in de winkelwagen? */
function kj_cart_has_abo() {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return false;
    }
    foreach ( WC()->cart->get_cart() as $item ) {
        if ( 'maand' === kj_cart_item_levering( $item ) ) {
            return true;
        }
    }
    return false;
}

/** Bedrag per maand van de abonnementsregels in de winkelwagen (incl. btw). */
function kj_cart_abo_bedrag() {
    $totaal = 0;
    foreach ( WC()->cart->get_cart() as $item ) {
        if ( 'maand' === kj_cart_item_levering( $item ) ) {
            $totaal += (float) $item['line_total'] + (float) $item['line_tax'];
        }
    }
    return $totaal;
}

/** Bij een abonnement is een account nodig; het wachtwoord kiest de klant via de mail. */
add_filter( 'woocommerce_checkout_registration_required', function ( $nodig ) {
    return kj_cart_has_abo() ? true : $nodig;
} );

/** Welke kassa- of accountpagina is dit? Steeds met kassa-header en -footer. */
function kj_is_kassa() {
    return function_exists( 'is_checkout' ) && is_checkout();
}

/** Stap in de kassa-header: 1 Gegevens, 2 Betalen, 3 Klaar. */
function kj_kassa_stap() {
    if ( is_wc_endpoint_url( 'order-received' ) ) {
        return 3;
    }
    if ( is_wc_endpoint_url( 'order-pay' ) ) {
        return 2;
    }
    return 1;
}

/* ---------- Velden ---------- */

add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
    $label = fn( $k, $d ) => (string) kj_option( $k, $d );
    $billing = array(
        'billing_email'      => array( 'type' => 'email', 'label' => $label( 'afr_email_label', 'E-mailadres' ), 'required' => true, 'class' => array( 'veld', 'veld--vol' ), 'autocomplete' => 'email', 'priority' => 10, 'description' => $label( 'afr_email_hulp', '' ) ),
        'billing_first_name' => array( 'label' => $label( 'afr_voornaam', 'Voornaam' ), 'required' => true, 'class' => array( 'veld', 'veld--half' ), 'autocomplete' => 'given-name', 'priority' => 20 ),
        'billing_last_name'  => array( 'label' => $label( 'afr_achternaam', 'Achternaam' ), 'required' => true, 'class' => array( 'veld', 'veld--half' ), 'autocomplete' => 'family-name', 'priority' => 30 ),
        'billing_postcode'   => array( 'label' => $label( 'afr_postcode', 'Postcode' ), 'required' => true, 'class' => array( 'veld', 'veld--half' ), 'autocomplete' => 'postal-code', 'validate' => array( 'postcode' ), 'priority' => 40 ),
        'billing_huisnummer' => array( 'label' => $label( 'afr_huisnummer', 'Huisnummer' ), 'required' => false, 'class' => array( 'veld', 'veld--half' ), 'autocomplete' => 'address-line2', 'priority' => 50 ),
        'billing_address_1'  => array( 'label' => $label( 'afr_straat', 'Straat' ), 'required' => true, 'class' => array( 'veld', 'veld--half', 'veld--auto' ), 'autocomplete' => 'address-line1', 'priority' => 60 ),
        'billing_city'       => array( 'label' => $label( 'afr_plaats', 'Plaats' ), 'required' => true, 'class' => array( 'veld', 'veld--half', 'veld--auto' ), 'autocomplete' => 'address-level2', 'priority' => 70, 'description' => '' ),
        'billing_country'    => array( 'type' => 'hidden', 'default' => 'NL', 'required' => false, 'priority' => 80 ),
    );
    $fields['billing']  = $billing;
    $fields['shipping'] = array();
    $fields['order']    = array();
    return $fields;
}, 20 );

add_filter( 'woocommerce_checkout_get_value', function ( $waarde, $veld ) {
    return ( 'billing_country' === $veld ) ? 'NL' : $waarde;
}, 10, 2 );

/** Huisnummer controleren en samenvoegen met de straat. */
add_action( 'woocommerce_after_checkout_validation', function ( $data, $errors ) {
    $nummer = isset( $_POST['billing_huisnummer'] ) ? trim( wp_unslash( $_POST['billing_huisnummer'] ) ) : ''; // phpcs:ignore WordPress.Security
    if ( ! preg_match( '/^\d+\s?[a-zA-Z0-9\-\/ ]{0,6}$/', $nummer ) ) {
        $errors->add( 'billing_huisnummer_error', (string) kj_option( 'afr_huisnummer_fout', 'Vul je huisnummer in, bijvoorbeeld 12 of 12A.' ), array( 'id' => 'billing_huisnummer' ) );
    }
    if ( empty( $_POST['terms'] ) ) { // phpcs:ignore WordPress.Security
        $errors->add( 'terms_error', __( 'Ga akkoord met de algemene voorwaarden om te bestellen.', 'morimatcha' ), array( 'id' => 'terms' ) );
    }
}, 10, 2 );

add_filter( 'woocommerce_checkout_posted_data', function ( $data ) {
    $nummer = isset( $data['billing_huisnummer'] ) ? trim( $data['billing_huisnummer'] ) : '';
    if ( $nummer && ! empty( $data['billing_address_1'] ) ) {
        $data['billing_address_1'] = trim( $data['billing_address_1'] ) . ' ' . $nummer;
    }
    unset( $data['billing_huisnummer'] );
    return $data;
} );

/** Veldopmaak: geen "(optioneel)", sterretjes weg. */
add_filter( 'woocommerce_form_field', function ( $veld ) {
    return preg_replace( '/<abbr class="required"[^>]*>\*<\/abbr>|<span class="required"[^>]*>\*<\/span>|<span class="optional">[^<]*<\/span>|&nbsp;/', '', $veld );
} );

/** Bij betaling klaar: naar bevestiging van onze kassa. Meldingen bovenaan het formulier. */
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );

/** Winkelwagenpagina: het paneel opent, de pagina zelf bestaat niet apart. */
add_action( 'template_redirect', function () {
    if ( function_exists( 'is_cart' ) && is_cart() && WC()->cart && ! WC()->cart->is_empty() ) {
        wp_safe_redirect( add_query_arg( 'winkelwagen', 'open', wc_get_page_permalink( 'shop' ) ) );
        exit;
    }
} );

/** Kassa- en accountscripts. */
add_action( 'wp_enqueue_scripts', function () {
    if ( kj_is_kassa() && ! is_wc_endpoint_url( 'order-received' ) ) {
        wp_enqueue_script( 'mori-kassa', get_template_directory_uri() . '/assets/js/kassa.js', array( 'jquery', 'wc-checkout' ), _KAYJILESEN_VERSION, true );
        wp_localize_script( 'mori-kassa', 'moriKassa', array( 'huisnummerFout' => (string) kj_option( 'afr_huisnummer_fout', '' ) ) );
    }
    if ( function_exists( 'is_account_page' ) && is_account_page() ) {
        wp_enqueue_script( 'mori-account', get_template_directory_uri() . '/assets/js/account.js', array(), _KAYJILESEN_VERSION, true );
    }
}, 30 );

/** Titel van de laatste bestelling voor Mijn account. */
function kj_order_regel( $order ) {
    $namen = array();
    foreach ( $order->get_items() as $item ) {
        $product = $item->get_product();
        if ( $product && $product->is_type( 'variation' ) ) {
            $gram    = kj_variation_gram( $product );
            $namen[] = wc_get_product( $product->get_parent_id() )->get_name() . ( $gram ? ' ' . (int) $gram . ' gram' : '' );
        } else {
            $namen[] = $item->get_name();
        }
    }
    return sprintf( 'Bestelling %s, %s', $order->get_order_number(), implode( ', ', $namen ) );
}

/** Foto van het blik voor in de samenvatting. */
function kj_cart_thumb( $product, $size = 'mori-480' ) {
    $id = $product->get_image_id();
    if ( ! $id && $product->is_type( 'variation' ) ) {
        $id = wc_get_product( $product->get_parent_id() )->get_image_id();
    }
    return $id ? wp_get_attachment_image( $id, $size, false, array( 'alt' => '', 'loading' => 'eager' ) ) : '';
}

/** Betaalmethode: tekst en logo's uit Instellingen Mori. */
function kj_betaal_info( $gateway_id ) {
    foreach ( (array) kj_option( 'afr_betaalmethoden', array() ) as $rij ) {
        if ( ! empty( $rij['sleutel'] ) && false !== strpos( $gateway_id, $rij['sleutel'] ) ) {
            return $rij;
        }
    }
    return array( 'sleutel' => '', 'naam' => '', 'uitleg' => '' );
}

function kj_betaal_logos( $sleutel ) {
    $map = array(
        'ideal'      => array( 'betaal-ideal.png' ),
        'bancontact' => array( 'betaal-bancontact.png' ),
        'creditcard' => array( 'betaal-visa.png', 'betaal-mastercard.png' ),
    );
    $html = '';
    foreach ( $map[ $sleutel ] ?? array() as $bestand ) {
        $html .= '<img src="' . esc_url( get_template_directory_uri() . '/assets/images/' . $bestand ) . '" alt="" loading="lazy" width="40" height="24">';
    }
    return $html;
}

/** "Er is al een account": de link moet naar inloggen wijzen (de losse inlogknop van WooCommerce bestaat in dit thema niet). */
add_filter( 'woocommerce_registration_error_email_exists', function ( $bericht ) {
    return str_replace( 'href="#" class="showlogin"', 'href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '"', $bericht );
} );
