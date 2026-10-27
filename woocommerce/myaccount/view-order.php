<?php
/**
 * Eén bestelling: status met voortgang, artikelen, kosten en adres.
 *
 * @var int $order_id
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id );
if ( ! $order || (int) $order->get_customer_id() !== get_current_user_id() ) {
	echo '<div class="account-leeg"><p>' . esc_html( kj_acc( 'acc_order_niet_gevonden', 'Deze bestelling bestaat niet.' ) ) . '</p></div>';
	return;
}

list( $badge, $stijl ) = kj_order_badge( $order );
$stap    = kj_order_stap( $order );
$stappen = array(
	1 => kj_acc( 'acc_order_stap_1', 'Besteld' ),
	2 => kj_acc( 'acc_order_stap_2', 'Betaald' ),
	3 => kj_acc( 'acc_order_stap_3', 'Bezorgd' ),
);
$datum    = wp_date( 'j F Y', $order->get_date_created()->getTimestamp() );
$verzend  = (float) $order->get_shipping_total() + (float) $order->get_shipping_tax();
$btw      = (float) $order->get_total_tax();
$adres    = $order->get_formatted_billing_address();
$terug    = '<a class="account-kop__terug" href="' . esc_url( wc_get_account_endpoint_url( 'orders' ) ) . '">' . kj_svg( 'pijl-rechts' ) . '<span>' . esc_html( kj_acc( 'acc_order_terug', 'Alle bestellingen' ) ) . '</span></a>';

kj_account_kop( sprintf( kj_acc( 'acc_bestel_nummer', 'Bestelling %s' ), $order->get_order_number() ), sprintf( kj_acc( 'acc_bestel_besteld_op', 'Besteld op %s' ), $datum ), $terug );
?>

<section class="order-status">
	<div class="order-status__kop">
		<?php kj_component( 'badge', array( 'text' => $badge, 'stijl' => $stijl, 'punt' => true ) ); ?>
		<?php if ( $order->needs_payment() ) : ?>
			<a class="account-knop" href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>"><?php echo esc_html( kj_acc( 'acc_knop_betalen', 'Nu betalen' ) ); ?></a>
		<?php endif; ?>
	</div>
	<?php if ( $stap ) : ?>
		<ol class="order-stappen">
			<?php foreach ( $stappen as $nr => $naam ) : ?>
				<li class="order-stappen__stap<?php echo $nr < $stap ? ' is-klaar' : ''; ?><?php echo $nr === $stap ? ' is-nu' : ''; ?>"><span class="order-stappen__punt"><?php echo $nr <= $stap ? kj_svg( 'vinkje' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span class="order-stappen__naam"><?php echo esc_html( $naam ); ?></span></li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>
	<?php if ( $order->has_status( 'on-hold' ) ) : ?>
		<div class="order-status__betaling"><?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?></div>
	<?php endif; ?>
</section>

<div class="account-raster">
	<div class="account-raster__hoofd">
		<section class="order-artikelen">
			<h2 class="order-artikelen__titel font-serif"><?php echo esc_html( kj_acc( 'acc_order_artikelen', 'Artikelen' ) ); ?></h2>
			<ul>
				<?php foreach ( $order->get_items() as $item ) :
					$product = $item->get_product();
					?>
					<li class="order-regel">
						<span class="order-regel__foto"><?php echo $product ? kj_cart_thumb( $product, 'thumbnail' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="order-regel__tekst">
							<span class="order-regel__naam"><?php echo esc_html( kj_item_naam( $item ) ); ?></span>
							<span class="order-regel__sub"><?php echo esc_html( sprintf( kj_acc( 'acc_order_aantal', 'Aantal: %d' ), $item->get_quantity() ) ); ?><?php echo 'maand' === $item->get_meta( '_mori_levering' ) ? ' · ' . esc_html( kj_acc( 'acc_order_elke_maand', 'Elke maand een blik' ) ) : ''; ?></span>
						</span>
						<span class="order-regel__prijs"><?php echo wp_kses_post( wc_price( (float) $item->get_total() + (float) $item->get_total_tax(), array( 'currency' => $order->get_currency() ) ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $order->get_items() ) : ?>
				<div class="order-artikelen__acties">
					<a class="account-knop account-knop--licht" href="<?php echo esc_url( kj_order_opnieuw_url( $order ) ); ?>"><?php echo esc_html( kj_acc( 'acc_bestel_opnieuw', 'Opnieuw bestellen' ) ); ?></a>
				</div>
			<?php endif; ?>
		</section>
	</div>

	<aside class="account-raster__zij">
		<section class="account-paneel">
			<h2 class="account-paneel__titel font-serif"><?php echo esc_html( kj_acc( 'acc_order_kosten', 'Kosten' ) ); ?></h2>
			<dl class="order-kosten">
				<div><dt><?php echo esc_html( kj_acc( 'acc_order_subtotaal', 'Subtotaal' ) ); ?></dt><dd><?php echo wp_kses_post( wc_price( (float) $order->get_total() - $verzend, array( 'currency' => $order->get_currency() ) ) ); ?></dd></div>
				<div><dt><?php echo esc_html( kj_acc( 'acc_order_verzending', 'Verzending' ) ); ?></dt><dd><?php echo $verzend > 0 ? wp_kses_post( wc_price( $verzend, array( 'currency' => $order->get_currency() ) ) ) : esc_html( kj_acc( 'acc_order_gratis', 'Gratis' ) ); ?></dd></div>
				<div class="order-kosten__totaal"><dt><?php echo esc_html( kj_acc( 'acc_order_totaal', 'Totaal' ) ); ?></dt><dd><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd></div>
				<?php if ( $btw > 0 ) : ?>
					<div class="order-kosten__btw"><dt><?php echo esc_html( kj_acc( 'acc_order_btw', 'Waarvan btw' ) ); ?></dt><dd><?php echo wp_kses_post( wc_price( $btw, array( 'currency' => $order->get_currency() ) ) ); ?></dd></div>
				<?php endif; ?>
			</dl>
		</section>

		<section class="account-paneel">
			<h2 class="account-paneel__titel font-serif"><?php echo esc_html( kj_acc( 'acc_order_adres', 'Bezorgadres' ) ); ?></h2>
			<address class="account-paneel__tekst"><?php echo $adres ? wp_kses_post( $adres ) : '-'; ?></address>
			<?php if ( $order->get_payment_method_title() ) : ?>
				<p class="account-paneel__sub"><?php echo esc_html( kj_acc( 'acc_order_betaling', 'Betaalwijze' ) . ': ' . $order->get_payment_method_title() ); ?></p>
			<?php endif; ?>
		</section>
	</aside>
</div>
