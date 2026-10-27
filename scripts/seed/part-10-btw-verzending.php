<?php
/**
 * Seed: btw en verzending (Nederland).
 * Matcha 9% (voedingsmiddel), accessoires en sets 21%. Prijzen zijn inclusief btw, verzending 6,95 inclusief btw.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

update_option( 'woocommerce_calc_taxes', 'yes' );
update_option( 'woocommerce_prices_include_tax', 'yes' );
update_option( 'woocommerce_tax_based_on', 'shipping' );
update_option( 'woocommerce_shipping_tax_class', 'inherit' );
update_option( 'woocommerce_tax_display_shop', 'incl' );
update_option( 'woocommerce_tax_display_cart', 'incl' );
update_option( 'woocommerce_tax_total_display', 'single' );

// Belastingtarieven voor Nederland (één keer aanmaken)
global $wpdb;
$bestaand = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_country = 'NL'" );
if ( ! $bestaand ) {
    WC_Tax::_insert_tax_rate( array( 'tax_rate_country' => 'NL', 'tax_rate' => '21.0000', 'tax_rate_name' => 'BTW 21%', 'tax_rate_priority' => 1, 'tax_rate_compound' => 0, 'tax_rate_shipping' => 1, 'tax_rate_order' => 1, 'tax_rate_class' => '' ) );
    WC_Tax::_insert_tax_rate( array( 'tax_rate_country' => 'NL', 'tax_rate' => '9.0000', 'tax_rate_name' => 'BTW 9%', 'tax_rate_priority' => 1, 'tax_rate_compound' => 0, 'tax_rate_shipping' => 1, 'tax_rate_order' => 1, 'tax_rate_class' => 'reduced-rate' ) );
}

// Matcha (categorie Matcha) naar 9%, de rest op 21%
foreach ( wc_get_products( array( 'limit' => -1, 'status' => 'any' ) ) as $product ) {
    $matcha = has_term( 'matcha', 'product_cat', $product->get_id() );
    $product->set_tax_status( 'taxable' );
    $product->set_tax_class( $matcha ? 'reduced-rate' : '' );
    $product->save();
}

// Verzending: 6,95 inclusief btw, btw volgt de producten, gratis vanaf 40
foreach ( WC_Data_Store::load( 'shipping-zone' )->get_zones() as $z ) {
    $zone = new WC_Shipping_Zone( $z->zone_id );
    foreach ( $zone->get_shipping_methods() as $methode ) {
        if ( 'flat_rate' === $methode->id ) {
            $instellingen             = $methode->instance_settings;
            $instellingen['tax_status'] = 'taxable';
            $instellingen['cost']       = '6.95';
            update_option( 'woocommerce_flat_rate_' . $methode->instance_id . '_settings', $instellingen );
        }
    }
}
