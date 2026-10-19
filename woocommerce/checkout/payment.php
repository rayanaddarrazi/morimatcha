<?php
/**
 * Betaalmethode, voorwaarden en de knop (Figma "Afrekenen", kaart 3). Wordt ververst door WooCommerce.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$gateways = WC()->payment_gateways()->get_available_payment_gateways();
$heeft_abo = kj_cart_has_abo();
?>
<div id="payment" class="woocommerce-checkout-payment kassa-betalen">

	<section class="kaart kassa-kaart" aria-labelledby="kassa-betaal">
		<h2 class="kassa-kaart__titel" id="kassa-betaal"><span class="kassa-kaart__nr">3</span><?php echo esc_html( (string) kj_option( 'afr_betaal_titel', '' ) ); ?></h2>
		<p class="kassa-hulp kassa-hulp--intro"><?php echo esc_html( (string) kj_option( 'afr_betaal_intro', '' ) ); ?></p>

		<?php if ( $gateways ) : ?>
			<ul class="wc_payment_methods payment_methods methods kassa-methodes" role="radiogroup">
				<?php foreach ( $gateways as $gateway ) :
					$info  = kj_betaal_info( $gateway->id );
					$naam  = $info['naam'] ? $info['naam'] : $gateway->get_title();
					$uitleg = $info['uitleg'];
					?>
					<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> kassa-methode">
						<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio kassa-methode__radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?>>
						<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="kassa-methode__label">
							<span class="kassa-methode__vink" aria-hidden="true"></span>
							<span class="kassa-methode__tekst">
								<span class="kassa-methode__naam"><?php echo esc_html( $naam ); ?></span>
								<?php if ( $uitleg ) : ?><span class="kassa-methode__uitleg"><?php echo esc_html( $uitleg ); ?></span><?php endif; ?>
							</span>
							<span class="kassa-methode__logos">
								<?php echo kj_betaal_logos( $info['sleutel'] ) ?: kj_svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</span>
						</label>
						<?php if ( $gateway->has_fields() || $gateway->get_description() && ! $info['uitleg'] ) : ?>
							<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?> kassa-methode__box">
								<?php $gateway->payment_fields(); ?>
							</div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p class="kassa-hulp"><?php echo esc_html( apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? __( 'Er zijn nog geen betaalmethodes beschikbaar.', 'morimatcha' ) : __( 'Vul je gegevens in om de betaalmethodes te zien.', 'morimatcha' ) ) ); ?></p>
		<?php endif; ?>

		<?php if ( $heeft_abo ) : ?>
			<p class="kassa-toestemming"><?php echo kj_svg( 'herhalen' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( (string) kj_option( 'afr_abo_toestemming', '' ) ); ?></span></p>
		<?php endif; ?>
	</section>

	<label class="kassa-akkoord">
		<input type="checkbox" name="terms" id="terms" class="kassa-akkoord__vak" required>
		<input type="hidden" name="terms-field" value="1">
		<span class="kassa-akkoord__tekst"><?php echo esc_html( (string) kj_option( 'afr_voorwaarden', '' ) ); ?></span>
	</label>

	<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

	<?php
	$knop = (string) kj_option( 'afr_knop', 'Bestelling plaatsen en betalen' );
	echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="kj-button kj-button--primair kassa-knop" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $knop ) . '"><span class="kj-button__label">' . esc_html( $knop ) . '</span><span class="kj-button__icoon">' . kj_svg( 'pijl-rechts' ) . '</span></button>' ); // phpcs:ignore WordPress.Security.EscapeOutput
	?>

	<?php if ( $heeft_abo ) : ?>
		<p class="kassa-noot"><?php echo esc_html( sprintf( (string) kj_option( 'afr_noot_abo', '' ), kj_format_price( (float) WC()->cart->get_total( 'edit' ) ), kj_format_price( kj_cart_abo_bedrag() ) ) ); ?></p>
	<?php endif; ?>

	<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

	<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
</div>
