<?php
/**
 * Inloggen, registreren en beveiliging.
 *
 * Accounts staan als gebruikers (rol Klant) in de WordPress-database: wp_users en wp_usermeta. Wachtwoorden worden
 * door WordPress gehasht (nooit leesbaar opgeslagen). Hier staan de regels eromheen:
 *  - registreren op de accountpagina (voornaam, achternaam, e-mailadres, eigen wachtwoord);
 *  - in de kassa wordt automatisch een account gemaakt op het e-mailadres (wachtwoord via mail);
 *  - beperking van pogingen, algemene foutmeldingen, honeypot en kleine beveiligingsmaatregelen.
 */

defined( 'ABSPATH' ) || exit;

const KJ_POGINGEN_MAX    = 5;
const KJ_POGINGEN_MINUTEN = 15;
const KJ_WACHTWOORD_MIN  = 10;

/** Tekst uit Instellingen Mori. */
function kj_toegang_tekst( $sleutel, $standaard = '' ) {
    return (string) kj_option( $sleutel, $standaard );
}

/** Het IP-adres van de bezoeker, alleen voor het tellen van pogingen. */
function kj_toegang_ip() {
    return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'onbekend'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
}

/* ---------- Wachtwoord: kiezen op de accountpagina, genereren in de kassa ---------- */

add_filter( 'pre_option_woocommerce_registration_generate_password', function () {
    return ( defined( 'WOOCOMMERCE_CHECKOUT' ) || ( function_exists( 'is_checkout' ) && is_checkout() ) ) ? 'yes' : 'no';
} );
add_filter( 'pre_option_woocommerce_registration_generate_username', function () {
    return 'yes';
} );

/** Zonder account geen bestelling: de kassa maakt het account zelf aan op het e-mailadres. */
add_filter( 'woocommerce_checkout_registration_required', '__return_true' );
add_filter( 'woocommerce_checkout_registration_enabled', '__return_true' );

/* ---------- Registreren ---------- */

// De Engelse privacytekst van WooCommerce onder het formulier is niet nodig: de voorwaarden staan bij het vinkje.
add_action( 'init', function () {
    remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );
} );

add_filter( 'woocommerce_process_registration_errors', function ( $errors, $gebruikersnaam, $wachtwoord, $email ) {
    // Honeypot: een echt mens ziet dit veld niet.
    if ( ! empty( $_POST['kj_website'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        $errors->add( 'bot', kj_toegang_tekst( 'toegang_fout_algemeen', 'Het lukt niet om je account te maken. Probeer het later opnieuw.' ) );
        return $errors;
    }

    // Maximaal 5 accounts per uur per apparaat.
    $sleutel = 'kj_reg_' . md5( kj_toegang_ip() );
    $aantal  = (int) get_transient( $sleutel );
    if ( $aantal >= 5 ) {
        $errors->add( 'te-vaak', kj_toegang_tekst( 'toegang_fout_te_vaak', 'Je hebt het te vaak geprobeerd. Wacht een poosje en probeer het opnieuw.' ) );
        return $errors;
    }
    set_transient( $sleutel, $aantal + 1, HOUR_IN_SECONDS );

    $voornaam = isset( $_POST['voornaam'] ) ? trim( wc_clean( wp_unslash( $_POST['voornaam'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
    $achternaam = isset( $_POST['achternaam'] ) ? trim( wc_clean( wp_unslash( $_POST['achternaam'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
    if ( '' === $voornaam || '' === $achternaam ) {
        $errors->add( 'naam', kj_toegang_tekst( 'toegang_fout_naam', 'Vul je voor- en achternaam in.' ) );
    }
    if ( ! is_email( $email ) ) {
        $errors->add( 'email', kj_toegang_tekst( 'toegang_fout_email', 'Vul een geldig e-mailadres in.' ) );
    }
    if ( mb_strlen( (string) $wachtwoord ) < KJ_WACHTWOORD_MIN ) {
        $errors->add( 'wachtwoord', sprintf( kj_toegang_tekst( 'toegang_fout_wachtwoord', 'Kies een wachtwoord van minimaal %d tekens.' ), KJ_WACHTWOORD_MIN ) );
    } elseif ( 0 === strcasecmp( (string) $wachtwoord, (string) $email ) ) {
        $errors->add( 'wachtwoord-email', kj_toegang_tekst( 'toegang_fout_wachtwoord_email', 'Je wachtwoord mag niet je e-mailadres zijn.' ) );
    }
    if ( empty( $_POST['voorwaarden'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        $errors->add( 'voorwaarden', kj_toegang_tekst( 'toegang_fout_voorwaarden', 'Ga akkoord met de algemene voorwaarden om een account te maken.' ) );
    }
    return $errors;
}, 10, 4 );

/** Naam bij het nieuwe account. */
add_filter( 'woocommerce_new_customer_data', function ( $data ) {
    $voornaam   = isset( $_POST['voornaam'] ) ? wc_clean( wp_unslash( $_POST['voornaam'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
    $achternaam = isset( $_POST['achternaam'] ) ? wc_clean( wp_unslash( $_POST['achternaam'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
    if ( $voornaam ) {
        $data['first_name']   = $voornaam;
        $data['last_name']    = $achternaam;
        $data['display_name'] = trim( $voornaam . ' ' . $achternaam );
    }
    return $data;
} );
add_action( 'woocommerce_created_customer', function ( $klant_id ) {
    $voornaam   = isset( $_POST['voornaam'] ) ? wc_clean( wp_unslash( $_POST['voornaam'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
    $achternaam = isset( $_POST['achternaam'] ) ? wc_clean( wp_unslash( $_POST['achternaam'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
    if ( $voornaam ) {
        update_user_meta( $klant_id, 'billing_first_name', $voornaam );
        update_user_meta( $klant_id, 'billing_last_name', $achternaam );
    }
} );

/** Na het registreren: direct naar je account. */
add_filter( 'woocommerce_registration_redirect', function () {
    return wc_get_page_permalink( 'myaccount' );
} );

/** Registreren staat voor klanten aan; de standaard-registratie van WordPress zelf blijft uit. */
add_filter( 'pre_option_users_can_register', '__return_zero' );

/* ---------- Inloggen: algemene foutmelding en beperking van pogingen ---------- */

function kj_pogingen_sleutel( $gebruiker ) {
    return 'kj_login_' . md5( strtolower( (string) $gebruiker ) . '|' . kj_toegang_ip() );
}

/** Na een reeks fouten kan niemand meer inloggen op dat account vanaf dit apparaat, ook niet met het juiste wachtwoord. Dit moet als laatste filter draaien. */
add_filter( 'authenticate', function ( $gebruiker, $naam ) {
    if ( ! $naam ) {
        return $gebruiker;
    }
    if ( (int) get_transient( kj_pogingen_sleutel( $naam ) ) >= KJ_POGINGEN_MAX ) {
        return new WP_Error( 'te_vaak', sprintf( kj_toegang_tekst( 'toegang_fout_login_te_vaak', 'Je hebt te vaak een verkeerd wachtwoord ingevuld. Probeer het over %d minuten opnieuw of kies een nieuw wachtwoord.' ), KJ_POGINGEN_MINUTEN ) );
    }
    return $gebruiker;
}, 100, 2 );

add_action( 'wp_login_failed', function ( $naam ) {
    $sleutel = kj_pogingen_sleutel( $naam );
    set_transient( $sleutel, (int) get_transient( $sleutel ) + 1, KJ_POGINGEN_MINUTEN * MINUTE_IN_SECONDS );
} );
add_action( 'wp_login', function ( $naam ) {
    delete_transient( kj_pogingen_sleutel( $naam ) );
} );

/** Niet verklappen of een e-mailadres bij ons bekend is. */
add_filter( 'authenticate', function ( $gebruiker ) {
    if ( is_wp_error( $gebruiker ) && array_intersect( $gebruiker->get_error_codes(), array( 'invalid_username', 'invalid_email', 'incorrect_password' ) ) ) {
        return new WP_Error( 'login_fout', kj_toegang_tekst( 'toegang_fout_login', 'Het e-mailadres of wachtwoord klopt niet.' ) );
    }
    return $gebruiker;
}, 99 );

/* ---------- Kleine beveiligingsmaatregelen ---------- */

add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', true );
}

/** Gebruikerslijst niet openbaar via de REST-API en niet te raden via ?author=1. */
add_filter( 'rest_endpoints', function ( $routes ) {
    if ( ! is_user_logged_in() ) {
        unset( $routes['/wp/v2/users'], $routes['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $routes;
} );
add_action( 'template_redirect', function () {
    if ( ! is_admin() && isset( $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
    if ( is_author() ) {
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
} );

add_action( 'send_headers', function () {
    header( 'X-Content-Type-Options: nosniff' );
    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
} );

/** Klanten komen niet in het dashboard; zij blijven in hun account. */
add_action( 'admin_init', function () {
    if ( wp_doing_ajax() || current_user_can( 'edit_posts' ) || current_user_can( 'manage_woocommerce' ) ) {
        return;
    }
    wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
    exit;
} );
add_filter( 'show_admin_bar', function ( $toon ) {
    return ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_woocommerce' ) ) ? $toon : false;
} );
