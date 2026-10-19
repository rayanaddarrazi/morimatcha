<?php
/**
 * Winkelwagenpaneel (drawer): schuift van rechts in, met overlay, focus trap en sluiten via Esc (zie script.js).
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'kj_cart_drawer_inner' ) ) {
	return;
}
?>
<div id="cart-drawer" class="cart-drawer" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( (string) kj_option( 'cart_titel', 'Je winkelwagen' ) ); ?>" aria-hidden="true" inert>
	<div class="cart-drawer__overlay" data-cart-close></div>
	<div class="cart-drawer__paneel">
		<div class="cart-drawer__kop">
			<p class="cart-drawer__titel"><?php echo esc_html( (string) kj_option( 'cart_titel', 'Je winkelwagen' ) ); ?></p>
			<button type="button" class="cart-drawer__sluiten" data-cart-close aria-label="<?php esc_attr_e( 'Sluiten', 'morimatcha' ); ?>"><?php echo kj_svg( 'sluiten' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		</div>
		<div class="cart-drawer__binnen" style="display: contents;"><?php echo kj_cart_drawer_inner(); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	</div>
</div>
