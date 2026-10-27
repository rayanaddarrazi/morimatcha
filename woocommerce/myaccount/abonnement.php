<?php
/**
 * Je abonnement: de kaart met alle opties en de laatste leveringen.
 *
 * @var string $titel
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$user   = wp_get_current_user();
$orders = kj_laatste_bestellingen( $user->ID, 3 );
$t      = fn( $k ) => (string) kj_option( $k, '' );

kj_account_kop( $titel );
wc_get_template( 'myaccount/abonnement-blok.php' );

if ( $orders ) {
	wc_get_template( 'myaccount/bestellingen-lijst.php', array(
		'orders' => $orders,
		'titel'  => $t( 'acc_leveringen' ),
		'link'   => $t( 'acc_alle_bestellingen' ),
	) );
}
