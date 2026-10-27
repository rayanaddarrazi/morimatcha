<?php
/**
 * Mijn account: eigen pagina Abonnement, Nederlandse adressen en kaartgegevens uit het abonnement.
 */

defined( 'ABSPATH' ) || exit;

/* ---------- Eindpunten ---------- */

add_filter( 'woocommerce_get_query_vars', function ( $vars ) {
    $vars['abonnement'] = 'abonnement';
    return $vars;
} );

add_action( 'init', function () {
    add_rewrite_endpoint( 'abonnement', EP_ROOT | EP_PAGES );
} );

/** Tekst uit Instellingen Mori, met een Nederlandse standaardtekst als het veld leeg is. */
function kj_acc( $sleutel, $standaard = '' ) {
    $waarde = (string) kj_option( $sleutel, '' );
    return '' !== $waarde ? $waarde : $standaard;
}

/** Aantal bestellingen van een klant (alle statussen behalve concepten). */
function kj_account_aantal_bestellingen( $user_id ) {
    static $cache = array();
    if ( ! isset( $cache[ $user_id ] ) ) {
        $cache[ $user_id ] = count( wc_get_orders( array( 'customer_id' => $user_id, 'limit' => -1, 'return' => 'ids', 'status' => array_keys( wc_get_order_statuses() ) ) ) );
    }
    return $cache[ $user_id ];
}

/** Het menu bovenaan: uit Instellingen Mori, met een teller bij Bestellingen en een statuspunt bij Abonnement. */
function kj_account_menu() {
    $rijen = (array) kj_option( 'acc_menu', array() );
    $menu  = array();
    $nu    = WC()->query->get_current_endpoint();
    $user  = get_current_user_id();
    $abo   = kj_abo_van_klant( $user );
    foreach ( $rijen as $rij ) {
        $sleutel = $rij['sleutel'];
        $url     = 'dashboard' === $sleutel ? wc_get_page_permalink( 'myaccount' ) : wc_get_account_endpoint_url( $sleutel );
        $actief  = 'dashboard' === $sleutel ? '' === $nu : ( $nu === $sleutel || ( 'orders' === $sleutel && 'view-order' === $nu ) );
        $teller  = 'orders' === $sleutel ? kj_account_aantal_bestellingen( $user ) : 0;
        $punt    = 'abonnement' === $sleutel && $abo ? $abo['status'] : '';
        $menu[]  = array( 'naam' => $rij['naam'], 'icoon' => $rij['icoon'], 'url' => $url, 'actief' => $actief, 'teller' => $teller, 'punt' => $punt );
    }
    return $menu;
}

/** Miniaturen van de producten in een bestelling (maximaal $max) en het aantal dat er nog bij komt. */
function kj_order_miniaturen( $order, $max = 3 ) {
    $html  = array();
    $items = array_values( $order->get_items() );
    foreach ( array_slice( $items, 0, $max ) as $item ) {
        $product = $item->get_product();
        $html[]  = $product ? kj_cart_thumb( $product, 'thumbnail' ) : '';
    }
    return array( array_filter( $html ), max( 0, count( $items ) - $max ) );
}

/** Welke stap van de bestelling is bereikt: 1 besteld, 2 betaald, 3 bezorgd. 0 bij geannuleerd of mislukt. */
function kj_order_stap( $order ) {
    switch ( $order->get_status() ) {
        case 'completed':
            return 3;
        case 'processing':
            return 2;
        case 'pending':
        case 'on-hold':
            return 1;
    }
    return 0;
}

/* ---------- Gegevens van het abonnement ---------- */

function kj_abo_titel( $abo ) {
    $product = wc_get_product( (int) $abo['product'] );
    $naam    = $product ? $product->get_name() : 'Mori Matcha';
    return $abo['gram'] ? sprintf( '%s, %d gram', $naam, (int) $abo['gram'] ) : $naam;
}

/** Aantal dagen tot een datum (nooit negatief). */
function kj_dagen_tot( $datum ) {
    return max( 0, (int) ceil( ( strtotime( $datum ) - strtotime( wp_date( 'Y-m-d' ) ) ) / DAY_IN_SECONDS ) );
}

/**
 * Argumenten voor de abonnementskaart, per status.
 */
function kj_abo_kaart_args( $abo ) {
    $t       = fn( $k ) => (string) kj_option( $k, '' );
    $prijs   = kj_format_price( (float) $abo['prijs'] );
    $betaal  = $abo['betaalwijze'] ? $abo['betaalwijze'] : '';
    $korting = (int) kj_option( 'abonnement_korting', 10 );
    $args    = array(
        'status'     => $abo['status'],
        'label'      => $t( 'acc_label' ),
        'titel'      => kj_abo_titel( $abo ),
        'frequentie' => str_replace( '%d', $korting, $t( 'acc_frequentie' ) ),
        'gram'       => (int) $abo['gram'],
        'voortgang'  => -1,
    );
    $opzeggen = array( 'label' => $t( 'acc_knop_opzeggen' ), 'type' => 'opzeggen', 'attrs' => array( 'type' => 'button', 'data-dialoog-open' => 'dlg-opzeggen' ) );

    switch ( $abo['status'] ) {
        case 'gepauzeerd':
            $tot                  = $abo['pause_tot'] ? kj_abo_datum( $abo['pause_tot'] ) : '';
            $args['status_tekst'] = $t( 'acc_gepauzeerd' );
            $args['gegevens']     = array( array( $t( 'acc_gepauzeerd_tot' ), $tot ), array( $t( 'acc_per_levering' ), $prijs ), array( $t( 'acc_betaalwijze' ), $betaal ) );
            $args['uitleg_links'] = $tot ? sprintf( $t( 'acc_gepauzeerd_uitleg' ), $tot ) : '';
            $args['icoon']        = 'pauze';
            $args['acties']       = array(
                array( 'label' => $t( 'acc_knop_hervatten' ), 'primair' => true, 'attrs' => array( 'type' => 'submit', 'form' => 'form-hervatten' ) ),
                $opzeggen,
            );
            break;

        case 'betaling':
            $order                = $abo['openstaand_order'] ? wc_get_order( (int) $abo['openstaand_order'] ) : null;
            $args['status_tekst'] = $t( 'acc_betaling_status' );
            $args['melding']      = sprintf( $t( 'acc_betaling_melding' ), $prijs );
            $args['gegevens']     = array( array( $t( 'acc_openstaand' ), $prijs ), array( $t( 'acc_voor_levering' ), kj_abo_datum( $abo['volgende'] ) ), array( $t( 'acc_betaalwijze' ), $betaal ) );
            $args['uitleg_links'] = $t( 'acc_betaling_uitleg' );
            $args['icoon']        = 'klok';
            $args['acties']       = array(
                array( 'label' => $t( 'acc_knop_betalen' ), 'primair' => true, 'url' => $order ? $order->get_checkout_payment_url() : wc_get_page_permalink( 'myaccount' ) ),
                $opzeggen,
            );
            break;

        case 'opgezegd':
            $args['status_tekst'] = $t( 'acc_opgezegd_status' );
            $args['gegevens']     = array(
                array( $t( 'acc_laatste_levering' ), kj_abo_datum( $abo['laatste'], 'j F Y' ) ),
                array( $t( 'acc_opgezegd_op' ), kj_abo_datum( $abo['opgezegd_op'], 'j F Y' ) ),
                array( $t( 'acc_ontvangen' ), ( 1 === (int) $abo['geleverd'] ? str_replace( 'blikken', 'blik', sprintf( $t( 'acc_ontvangen_waarde' ), 1 ) ) : sprintf( $t( 'acc_ontvangen_waarde' ), (int) $abo['geleverd'] ) ) ),
            );
            $args['uitleg_links'] = $t( 'acc_opgezegd_uitleg' );
            $args['acties']       = array( array( 'label' => $t( 'acc_knop_opnieuw' ), 'primair' => true, 'attrs' => array( 'type' => 'submit', 'form' => 'form-opnieuw' ) ) );
            break;

        default: // actief
            $dagen                = kj_dagen_tot( $abo['volgende'] );
            $args['status_tekst'] = $t( 'acc_actief' );
            $args['gegevens']     = array( array( $t( 'acc_volgende' ), kj_abo_datum( $abo['volgende'] ) ), array( $t( 'acc_per_levering' ), $prijs ), array( $t( 'acc_betaalwijze' ), $betaal ) );
            $args['uitleg_links'] = sprintf( $t( 'acc_nog_dagen' ), $dagen );
            $args['uitleg_rechts'] = sprintf( $t( 'acc_verstuurd' ), kj_abo_datum( wp_date( 'Y-m-d', strtotime( $abo['volgende'] . ' -2 days' ) ) ) );
            $args['voortgang']    = (int) max( 0, min( 100, round( ( 30 - $dagen ) / 30 * 100 ) ) );
            $args['acties']       = array(
                array( 'label' => $t( 'acc_knop_pauzeren' ), 'attrs' => array( 'type' => 'button', 'data-dialoog-open' => 'dlg-pauzeren' ) ),
                array( 'label' => $t( 'acc_knop_maat' ), 'attrs' => array( 'type' => 'button', 'data-dialoog-open' => 'dlg-maat' ) ),
                $opzeggen,
            );
    }
    return $args;
}

/** De laatste bestellingen van een klant (betaald). */
function kj_laatste_bestellingen( $user_id, $aantal = 3 ) {
    return wc_get_orders( array(
        'customer_id' => $user_id,
        'status'      => array( 'wc-processing', 'wc-completed', 'wc-on-hold' ),
        'limit'       => $aantal,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ) );
}

/** Status van een bestelling voor de badge. */
function kj_order_badge( $order ) {
    switch ( $order->get_status() ) {
        case 'completed':
            return array( kj_acc( 'acc_status_bezorgd', 'Bezorgd' ), 'blad' );
        case 'on-hold':
        case 'pending':
            return array( kj_acc( 'acc_status_wacht', 'Wacht op betaling' ), 'letop' );
        case 'cancelled':
        case 'failed':
            return array( kj_acc( 'acc_status_geannuleerd', 'Geannuleerd' ), 'fout' );
        case 'refunded':
            return array( kj_acc( 'acc_status_terugbetaald', 'Terugbetaald' ), 'lijn' );
    }
    return array( kj_acc( 'acc_status_behandeling', 'In behandeling' ), 'blad' );
}

/** Pad naar "opnieuw bestellen" van WooCommerce: zet de regels van een eerdere bestelling weer in de winkelwagen. */
function kj_order_opnieuw_url( $order ) {
    return wp_nonce_url( add_query_arg( 'order_again', $order->get_id(), wc_get_cart_url() ), 'woocommerce-order_again' );
}

/** Inhoud van de eindpunten. */
add_action( 'woocommerce_account_abonnement_endpoint', function () {
    wc_get_template( 'myaccount/abonnement.php', array( 'titel' => (string) kj_option( 'acc_abo_titel', '' ) ) );
} );

add_action( 'woocommerce_account_dashboard', function () {}, 99 );

/** Titel van een account-tab uit het menu van Instellingen Mori. */
function kj_account_tabnaam( $sleutel ) {
    foreach ( (array) kj_option( 'acc_menu', array() ) as $rij ) {
        if ( $rij['sleutel'] === $sleutel ) {
            return $rij['naam'];
        }
    }
    return '';
}

/** Titel van een tab, met eventueel een korte regel eronder. Terug-link of acties kunnen rechts staan. */
function kj_account_kop( $titel, $sub = '', $rechts = '' ) {
    echo '<div class="account-kop"><div><h1 class="account-kop__titel font-serif">' . esc_html( $titel ) . '</h1>';
    if ( $sub ) {
        echo '<p class="account-kop__sub">' . esc_html( $sub ) . '</p>';
    }
    echo '</div>' . $rechts . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
}

/** Wanneer de klant lid werd, bijvoorbeeld "oktober 2026". */
function kj_account_sinds( $user ) {
    $abo   = kj_abo_van_klant( $user->ID );
    $datum = $abo && $abo['sinds'] ? strtotime( $abo['sinds'] ) : strtotime( $user->user_registered );
    return wp_date( 'F Y', min( $datum, strtotime( $user->user_registered ) ) );
}

/* ---------- Gegevens en adressen ---------- */

/** Weergavenaam vragen we niet, die volgt de voor- en achternaam. */
add_filter( 'woocommerce_save_account_details_required_fields', function ( $velden ) {
    unset( $velden['account_display_name'] );
    return $velden;
} );
add_action( 'woocommerce_save_account_details', function ( $user_id ) {
    $user = get_userdata( $user_id );
    wp_update_user( array( 'ID' => $user_id, 'display_name' => trim( $user->first_name . ' ' . $user->last_name ) ) );
} );

/** Adres in het account: dezelfde velden als in de kassa, in Nederland. */
add_filter( 'woocommerce_billing_fields', function ( $velden ) {
    if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return $velden;
    }
    $t = fn( $k, $d ) => (string) kj_option( $k, $d );
    return array(
        'billing_first_name' => array( 'label' => $t( 'afr_voornaam', 'Voornaam' ), 'required' => true, 'class' => array( 'form-row-first' ), 'autocomplete' => 'given-name', 'priority' => 10 ),
        'billing_last_name'  => array( 'label' => $t( 'afr_achternaam', 'Achternaam' ), 'required' => true, 'class' => array( 'form-row-last' ), 'autocomplete' => 'family-name', 'priority' => 20 ),
        'billing_postcode'   => array( 'label' => $t( 'afr_postcode', 'Postcode' ), 'required' => true, 'class' => array( 'form-row-first' ), 'autocomplete' => 'postal-code', 'validate' => array( 'postcode' ), 'priority' => 30 ),
        'billing_city'       => array( 'label' => $t( 'afr_plaats', 'Plaats' ), 'required' => true, 'class' => array( 'form-row-last' ), 'autocomplete' => 'address-level2', 'priority' => 40 ),
        'billing_address_1'  => array( 'label' => $t( 'acc_adres_straat', 'Straat en huisnummer' ), 'required' => true, 'class' => array( 'form-row-wide' ), 'autocomplete' => 'street-address', 'priority' => 50 ),
        'billing_country'    => array( 'type' => 'hidden', 'default' => 'NL', 'required' => false, 'priority' => 60 ),
    );
}, 30 );

/** Adressen: zonder keuze het overzicht, met "billing" het formulier (alleen factuuradres, geen verzendadres). */
add_action( 'init', function () {
    remove_action( 'woocommerce_account_edit-address_endpoint', 'woocommerce_account_edit_address' );
    add_action( 'woocommerce_account_edit-address_endpoint', function ( $type ) {
        if ( 'billing' === wc_edit_address_i18n( sanitize_title( (string) $type ), true ) ) {
            WC_Shortcode_My_Account::edit_address( 'billing' );
        } else {
            wc_get_template( 'myaccount/my-address.php' );
        }
    } );
}, 20 );
