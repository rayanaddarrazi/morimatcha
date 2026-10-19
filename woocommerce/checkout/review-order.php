<?php
/**
 * Samenvatting rechts: bestelling, kortingscode, totalen en het donkere blok "Over je abonnement".
 * Dit hele element wordt door WooCommerce ververst bij elke wijziging.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$cart      = WC()->cart;
$heeft_abo = kj_cart_has_abo();
$verzend   = $cart->get_shipping_total() + $cart->get_shipping_tax();
$kosteloos = 0 === (int) round( $verzend * 100 );
?>
<div class="woocommerce-checkout-review-order-table kassa-samenvatting">

	<section class="kaart kassa-samenvatting__kaart">
		<h2 class="font-serif text-h3"><?php echo esc_html( (string) kj_option( 'afr_samenvatting', '' ) ); ?></h2>

		<ul class="kassa-regels">
			<?php foreach ( $cart->get_cart() as $sleutel => $item ) :
				$product = $item['data'];
				if ( ! $product || ! $product->exists() || $item['quantity'] <= 0 ) {
					continue;
				}
				$maand = 'maand' === kj_cart_item_levering( $item );
				$gram  = kj_variation_gram( $product );
				$naam  = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() )->get_name() : $product->get_name();
				?>
				<li class="kassa-regel">
					<span class="kassa-regel__foto"><?php echo kj_cart_thumb( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="kassa-regel__tekst">
						<span class="kassa-regel__naam"><?php echo esc_html( $naam ); ?><?php echo $item['quantity'] > 1 ? ' &times; ' . (int) $item['quantity'] : ''; ?></span>
						<?php
						$meta = array();
						if ( $gram ) {
							$meta[] = (int) $gram . ' gram';
						}
						if ( $maand ) {
							$meta[] = (string) kj_option( 'levering_label_maand', 'elke maand' );
						}
						if ( $meta ) :
							?>
							<span class="kassa-regel__meta"><?php echo $maand ? kj_svg( 'herhalen' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( implode( ', ', $meta ) ); ?></span>
						<?php endif; ?>
					</span>
					<span class="kassa-regel__prijs"><?php echo wp_kses_post( $cart->get_product_subtotal( $product, $item['quantity'] ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( wc_coupons_enabled() ) : ?>
			<div class="kassa-korting" data-korting>
				<label class="screen-reader-text" for="mori-kortingscode"><?php echo esc_html( (string) kj_option( 'afr_kortingscode', '' ) ); ?></label>
				<input type="text" id="mori-kortingscode" class="kassa-korting__veld" placeholder="<?php echo esc_attr( (string) kj_option( 'afr_kortingscode', '' ) ); ?>" autocomplete="off">
				<button type="button" class="kj-button kj-button--secundair kassa-korting__knop" data-korting-knop><span class="kj-button__label"><?php echo esc_html( (string) kj_option( 'afr_toepassen', '' ) ); ?></span></button>
			</div>
			<p class="kassa-korting__melding" data-korting-melding role="status"></p>
		<?php endif; ?>

		<dl class="kassa-totalen">
			<div><dt><?php echo esc_html( (string) kj_option( 'afr_subtotaal', '' ) ); ?></dt><dd><?php echo wp_kses_post( $cart->get_cart_subtotal() ); ?></dd></div>
			<?php foreach ( $cart->get_coupons() as $code => $coupon ) : ?>
				<div><dt><?php echo esc_html( wc_cart_totals_coupon_label( $coupon, false ) ); ?></dt><dd>-<?php echo wp_kses_post( wc_price( $cart->get_coupon_discount_amount( $code, $cart->display_prices_including_tax() ) ) ); ?></dd></div>
			<?php endforeach; ?>
			<div><dt><?php echo esc_html( (string) kj_option( 'afr_verzending', '' ) ); ?></dt><dd><?php echo $kosteloos ? esc_html__( 'Gratis', 'morimatcha' ) : wp_kses_post( wc_price( $verzend ) ); ?></dd></div>
		</dl>

		<div class="kassa-totaal">
			<div class="kassa-totaal__rij">
				<span class="kassa-totaal__label"><?php echo esc_html( (string) kj_option( 'afr_totaal', '' ) ); ?></span>
				<span class="kassa-totaal__bedrag"><?php echo wp_kses_post( $cart->get_total() ); ?></span>
			</div>
			<p class="kassa-totaal__btw"><?php echo esc_html( (string) kj_option( 'afr_btw', '' ) ); ?></p>
		</div>
	</section>

	<?php if ( $heeft_abo ) :
		$nu       = (float) $cart->get_total( 'edit' );
		$maandbed = kj_cart_abo_bedrag();
		?>
		<section class="kassa-abo">
			<p class="eyebrow eyebrow--licht"><span class="eyebrow__lijn"></span><span><?php echo esc_html( (string) kj_option( 'afr_abo_eyebrow', '' ) ); ?></span></p>
			<dl class="kassa-abo__rijen">
				<div><dt><?php esc_html_e( 'Nu betalen', 'morimatcha' ); ?></dt><dd><?php echo esc_html( sprintf( (string) kj_option( 'afr_abo_nu', '%s' ), kj_format_price( $nu ) ) ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Volgende levering', 'morimatcha' ); ?></dt><dd><?php echo esc_html( sprintf( (string) kj_option( 'afr_abo_volgende', '%1$s, %2$s' ), wp_date( 'j F', strtotime( '+1 month' ) ), kj_format_price( $maandbed ) ) ); ?></dd></div>
				<div><dt><?php echo esc_html( (string) kj_option( 'afr_abo_opzeggen_label', '' ) ); ?></dt><dd><?php echo esc_html( (string) kj_option( 'afr_abo_opzeggen_tekst', '' ) ); ?></dd></div>
			</dl>
		</section>
	<?php endif; ?>

</div>
