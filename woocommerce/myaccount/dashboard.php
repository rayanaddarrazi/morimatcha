<?php
/**
 * Overzicht: kerncijfers, het abonnement, de laatste bestellingen en hulp.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$user     = wp_get_current_user();
$abo      = kj_abo_van_klant( $user->ID );
$orders   = wc_get_orders( array( 'customer_id' => $user->ID, 'limit' => 3, 'orderby' => 'date', 'order' => 'DESC', 'status' => array_keys( wc_get_order_statuses() ) ) );
$aantal   = kj_account_aantal_bestellingen( $user->ID );
$adres    = wc_get_account_formatted_address( 'billing' );
$hulp     = wp_get_nav_menu_items( (int) ( get_nav_menu_locations()['footer_hulp'] ?? 0 ) ) ?: array();
$geen     = kj_acc( 'acc_stat_geen', 'Geen' );

// Kerncijfers
if ( $abo && 'actief' === $abo['status'] ) {
	$levering = kj_abo_datum( $abo['volgende'] );
	$levering_sub = sprintf( kj_acc( 'acc_stat_levering_sub', 'Nog %d dagen' ), kj_dagen_tot( $abo['volgende'] ) );
} elseif ( $abo && 'gepauzeerd' === $abo['status'] && $abo['pause_tot'] ) {
	$levering     = kj_abo_datum( $abo['pause_tot'] );
	$levering_sub = kj_acc( 'acc_stat_levering_gepauzeerd', 'Abonnement gepauzeerd' );
} else {
	$levering     = $geen;
	$levering_sub = $abo ? '' : kj_acc( 'acc_stat_geen_abo', 'Geen abonnement' );
}
$laatste = $orders ? wp_date( 'j F', $orders[0]->get_date_created()->getTimestamp() ) : $geen;
$laatste_sub = $orders ? kj_order_namen( $orders[0] ) : '';

?>
<h1 class="screen-reader-text"><?php echo esc_html( kj_acc( 'acc_overzicht_titel', 'Overzicht' ) ); ?></h1>

<ul class="account-cijfers">
	<li class="account-cijfer">
		<span class="account-cijfer__label"><?php echo esc_html( kj_acc( 'acc_stat_volgende', 'Volgende levering' ) ); ?></span>
		<span class="account-cijfer__waarde font-serif"><?php echo esc_html( $levering ); ?></span>
		<?php if ( $levering_sub ) : ?><span class="account-cijfer__sub"><?php echo esc_html( $levering_sub ); ?></span><?php endif; ?>
	</li>
	<li class="account-cijfer">
		<span class="account-cijfer__label"><?php echo esc_html( kj_acc( 'acc_stat_bestellingen', 'Bestellingen' ) ); ?></span>
		<span class="account-cijfer__waarde font-serif"><?php echo (int) $aantal; ?></span>
		<a class="account-cijfer__sub account-cijfer__link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php echo esc_html( kj_acc( 'acc_alle_bestellingen', 'Alle bestellingen' ) ); ?></a>
	</li>
	<li class="account-cijfer">
		<span class="account-cijfer__label"><?php echo esc_html( kj_acc( 'acc_stat_laatste', 'Laatste bestelling' ) ); ?></span>
		<span class="account-cijfer__waarde font-serif"><?php echo esc_html( $laatste ); ?></span>
		<?php if ( $laatste_sub ) : ?><span class="account-cijfer__sub"><?php echo esc_html( $laatste_sub ); ?></span><?php endif; ?>
	</li>
</ul>

<div class="account-raster">
	<div class="account-raster__hoofd">
		<?php wc_get_template( 'myaccount/abonnement-blok.php' ); ?>
		<?php if ( $orders ) {
			wc_get_template( 'myaccount/bestellingen-lijst.php', array(
				'orders' => $orders,
				'titel'  => kj_acc( 'acc_recent_titel', 'Laatste bestellingen' ),
				'link'   => kj_acc( 'acc_alle_bestellingen', 'Alle bestellingen' ),
			) );
		} ?>
	</div>

	<aside class="account-raster__zij">
		<section class="account-paneel">
			<h2 class="account-paneel__titel font-serif"><?php echo esc_html( kj_acc( 'acc_adres_kop', 'Bezorgadres' ) ); ?></h2>
			<?php if ( $adres ) : ?>
				<address class="account-paneel__tekst"><?php echo wp_kses_post( $adres ); ?></address>
			<?php else : ?>
				<p class="account-paneel__tekst account-paneel__tekst--leeg"><?php echo esc_html( kj_acc( 'acc_adres_leeg' ) ); ?></p>
			<?php endif; ?>
			<a class="account-paneel__link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>"><?php echo esc_html( $adres ? kj_acc( 'acc_adres_bewerken', 'Adres wijzigen' ) : kj_acc( 'acc_adres_toevoegen', 'Adres toevoegen' ) ); ?></a>
		</section>

		<?php if ( $hulp ) : ?>
			<section class="account-paneel">
				<h2 class="account-paneel__titel font-serif"><?php echo esc_html( kj_acc( 'acc_hulp_titel', 'Hulp nodig?' ) ); ?></h2>
				<ul class="account-paneel__links">
					<?php foreach ( $hulp as $punt ) : ?>
						<li><a href="<?php echo esc_url( $punt->url ); ?>"><span><?php echo esc_html( $punt->title ); ?></span><?php echo kj_svg( 'pijl-rechts' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>
	</aside>
</div>
