<?php
/**
 * Btw op verzending: het bedrag dat de klant ziet blijft gelijk, ook als de btw per winkelwagen verschilt.
 *
 * WooCommerce rekent verzendkosten als bedrag exclusief btw. Omdat de btw de producten volgt (9% of 21%),
 * rekenen we terug naar een kostprijs zodat de klant altijd het ingestelde bedrag inclusief btw betaalt.
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'woocommerce_package_rates', function ( $rates, $package ) {
    if ( ! wc_tax_enabled() || 'yes' !== get_option( 'woocommerce_prices_include_tax' ) ) {
        return $rates;
    }

    foreach ( $rates as $rate ) {
        if ( 'flat_rate' !== $rate->get_method_id() || (float) $rate->get_cost() <= 0 ) {
            continue;
        }

        // Het ingestelde bedrag (inclusief btw) staat in de instellingen van de verzendmethode
        $instellingen = get_option( 'woocommerce_flat_rate_' . $rate->get_instance_id() . '_settings', array() );
        $bruto        = isset( $instellingen['cost'] ) ? (float) $instellingen['cost'] : (float) $rate->get_cost();

        // Btw die WooCommerce over dit bedrag zou rekenen, om het gemiddelde btw-percentage van de winkelwagen te vinden.
        $proef = array_sum( WC_Tax::calc_shipping_tax( $bruto, WC_Tax::get_shipping_tax_rates() ) );
        $netto = $proef > 0 ? round( $bruto / ( 1 + $proef / $bruto ), wc_get_price_decimals() ) : $bruto;

        $rate->set_cost( $netto );
        $rate->set_taxes( WC_Tax::calc_shipping_tax( $netto, WC_Tax::get_shipping_tax_rates() ) );
    }

    return $rates;
}, 20, 2 );

/** Is gratis verzending beschikbaar, dan is dat de enige keuze. */
add_filter( 'woocommerce_package_rates', function ( $rates ) {
    $gratis = array_filter( $rates, function ( $rate ) {
        return 'free_shipping' === $rate->get_method_id();
    } );
    return $gratis ? $gratis : $rates;
}, 100 );
