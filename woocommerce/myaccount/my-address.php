<?php
/**
 * Adressen: het adres dat standaard in de kassa staat, met een knop om het te wijzigen.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$t       = fn( $k, $d = '' ) => (string) kj_option( $k, $d );
$adres   = wc_get_account_formatted_address( 'billing' );
kj_account_kop( kj_account_tabnaam( 'edit-address' ) );
?>
<section class="account-kaart">
	<h2 class="account-kaart__titel font-serif"><?php echo esc_html( $t( 'acc_adres_kop', 'Bezorgadres' ) ); ?></h2>
	<p class="account-kaart__hulp"><?php echo esc_html( $t( 'acc_adres_hulp', '' ) ); ?></p>
	<?php if ( $adres ) : ?>
		<address class="account-adres"><?php echo wp_kses_post( $adres ); ?></address>
	<?php else : ?>
		<p class="account-adres account-adres--leeg"><?php echo esc_html( $t( 'acc_adres_leeg', '' ) ); ?></p>
	<?php endif; ?>
	<div class="account-formulier__acties">
		<a class="account-knop account-knop--licht" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ); ?>"><?php echo esc_html( $adres ? $t( 'acc_adres_bewerken', 'Adres wijzigen' ) : $t( 'acc_adres_toevoegen', 'Adres toevoegen' ) ); ?></a>
	</div>
</section>
