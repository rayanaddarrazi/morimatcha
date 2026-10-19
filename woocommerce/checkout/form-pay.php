<?php
/**
 * Betalen van een bestaande bestelling (bijvoorbeeld de volgende levering van een abonnement).
 *
 * @var WC_Order $order
 * @var array    $available_gateways
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$t = fn( $k ) => (string) kj_option( $k, '' );
?>
<form id="order_review" method="post" class="kassa kassa--betalen">
	<div class="container kassa__enkel">
		<h1 class="kassa__titel font-serif"><?php echo esc_html( $t( 'afr_betaal_titel' ) ); ?></h1>

		<section class="kaart kassa-kaart">
			<ul class="kassa-regels">
				<?php foreach ( $order->get_items() as $item ) :
					$product = $item->get_product();
					?>
					<li class="kassa-regel">
						<span class="kassa-regel__foto"><?php echo $product ? kj_cart_thumb( $product ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="kassa-regel__tekst"><span class="kassa-regel__naam"><?php echo esc_html( $item->get_name() ); ?></span></span>
						<span class="kassa-regel__prijs"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="kassa-totaal">
				<div class="kassa-totaal__rij">
					<span class="kassa-totaal__label"><?php echo esc_html( $t( 'afr_totaal' ) ); ?></span>
					<span class="kassa-totaal__bedrag"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
				</div>
				<p class="kassa-totaal__btw"><?php echo esc_html( $t( 'afr_btw' ) ); ?></p>
			</div>
		</section>

		<div id="payment" class="kassa-betalen">
			<?php if ( $available_gateways ) : ?>
				<section class="kaart kassa-kaart">
					<ul class="wc_payment_methods payment_methods methods kassa-methodes">
						<?php foreach ( $available_gateways as $gateway ) :
							$info = kj_betaal_info( $gateway->id );
							?>
							<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> kassa-methode">
								<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio kassa-methode__radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?>>
								<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="kassa-methode__label">
									<span class="kassa-methode__vink" aria-hidden="true"></span>
									<span class="kassa-methode__tekst">
										<span class="kassa-methode__naam"><?php echo esc_html( $info['naam'] ? $info['naam'] : $gateway->get_title() ); ?></span>
										<?php if ( $info['uitleg'] ) : ?><span class="kassa-methode__uitleg"><?php echo esc_html( $info['uitleg'] ); ?></span><?php endif; ?>
									</span>
									<span class="kassa-methode__logos"><?php echo kj_betaal_logos( $info['sleutel'] ) ?: kj_svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								</label>
								<?php if ( $gateway->has_fields() ) : ?>
									<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?> kassa-methode__box"><?php $gateway->payment_fields(); ?></div>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<input type="hidden" name="woocommerce_pay" value="1">
			<?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
			<button type="submit" class="kj-button kj-button--primair kassa-knop" id="place_order" value="<?php echo esc_attr( $t( 'acc_knop_betalen' ) ); ?>"><span class="kj-button__label"><?php echo esc_html( $t( 'acc_knop_betalen' ) ); ?></span><span class="kj-button__icoon"><?php echo kj_svg( 'pijl-rechts' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></button>
		</div>
	</div>
</form>
