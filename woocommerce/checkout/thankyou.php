<?php
/**
 * Bevestiging (Figma "Bevestiging").
 *
 * @var WC_Order $order
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
	return;
}

$tijdlijn  = (array) kj_option( 'bev_tijdlijn', array() );
$foto      = kj_image_id( get_field( 'bev_kop_foto', 'option' ) );
$knop      = get_field( 'bev_kop_knop', 'option' );
$abo_id    = (int) $order->get_meta( '_abo_id' );
$abo       = $abo_id ? kj_abo( $abo_id ) : null;
$volgende  = $abo && $abo['volgende'] ? kj_abo_datum( $abo['volgende'] ) : '';
$heeft_abo = (bool) $abo;
if ( ! $heeft_abo ) {
	foreach ( $order->get_items() as $item ) {
		if ( 'maand' === $item->get_meta( '_mori_levering' ) ) {
			$heeft_abo = true;
			$volgende  = wp_date( 'j F', strtotime( '+1 month' ) );
		}
	}
}
$voornaam = $order->get_billing_first_name();
?>
<div class="bevestiging container">
	<div class="bevestiging__grid">

		<div class="bevestiging__links">
			<div>
				<span class="bevestiging__vink" aria-hidden="true"><?php echo kj_svg( 'vinkje' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<h1 class="bevestiging__titel font-serif"><?php echo esc_html( sprintf( (string) kj_option( 'bev_titel', '%s' ), $voornaam ) ); ?></h1>
			</div>
			<p class="bevestiging__tekst text-body-l"><?php echo esc_html( sprintf( (string) kj_option( 'bev_tekst', '%s' ), $order->get_billing_email() ) ); ?></p>

			<?php if ( 'bacs' === $order->get_payment_method() ) : ?>
				<div class="bevestiging__bank"><?php do_action( 'woocommerce_thankyou_bacs', $order->get_id() ); ?></div>
			<?php endif; ?>

			<section class="tijdlijn-kaart">
				<h2 class="font-serif"><?php echo esc_html( sprintf( (string) kj_option( 'bev_bestelling', '%s' ), $order->get_order_number() ) ); ?></h2>
				<ol class="tijdlijn">
					<?php foreach ( $tijdlijn as $i => $stap ) : ?>
						<li class="tijdlijn__stap<?php echo 0 === $i ? ' is-klaar' : ''; ?>">
							<span class="tijdlijn__punt"><?php echo 0 === $i ? kj_svg( 'vinkje' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="tijdlijn__naam"><?php echo esc_html( $stap['naam'] ); ?></span>
							<span class="tijdlijn__tekst"><?php echo esc_html( $stap['tekst'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>

			<section class="bereid-kaart">
				<?php if ( $foto ) : ?>
					<div class="bereid-kaart__foto"><?php echo wp_get_attachment_image( $foto, 'mori-480', false, array( 'loading' => 'lazy', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php endif; ?>
				<div>
					<p class="bereid-kaart__label"><?php echo esc_html( (string) kj_option( 'bev_kop_label', '' ) ); ?></p>
					<h3 class="font-serif"><?php echo esc_html( (string) kj_option( 'bev_kop_titel', '' ) ); ?></h3>
					<p class="bereid-kaart__tekst"><?php echo esc_html( (string) kj_option( 'bev_kop_tekst', '' ) ); ?></p>
					<?php if ( $knop ) { kj_component( 'button', array( 'label' => $knop['title'], 'url' => $knop['url'], 'variant' => 'tekstlink', 'icon' => true ) ); } ?>
				</div>
			</section>
		</div>

		<div class="bevestiging__rechts">
			<section class="overzicht-kaart">
				<h2 class="font-serif text-h3"><?php echo esc_html( (string) kj_option( 'bev_overzicht', '' ) ); ?></h2>
				<ul class="kassa-regels">
					<?php foreach ( $order->get_items() as $item ) :
						$product = $item->get_product();
						$maand   = 'maand' === $item->get_meta( '_mori_levering' );
						$gram    = $product ? kj_variation_gram( $product ) : '';
						$naam    = $product && $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() )->get_name() : $item->get_name();
						$meta    = array();
						if ( $gram ) {
							$meta[] = (int) $gram . ' gram';
						}
						if ( $maand ) {
							$meta[] = (string) kj_option( 'levering_label_maand', 'elke maand' );
						}
						?>
						<li class="kassa-regel">
							<span class="kassa-regel__foto"><?php echo $product ? kj_cart_thumb( $product ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="kassa-regel__tekst">
								<span class="kassa-regel__naam"><?php echo esc_html( $naam ); ?></span>
								<?php if ( $meta ) : ?><span class="kassa-regel__meta"><?php echo esc_html( implode( ', ', $meta ) ); ?></span><?php endif; ?>
							</span>
							<span class="kassa-regel__prijs"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
				<dl class="overzicht-rijen">
					<div><dt><?php echo esc_html( (string) kj_option( 'bev_betaald', '' ) ); ?></dt><dd><?php echo esc_html( $order->get_payment_method_title() ); ?></dd></div>
					<div><dt><?php echo esc_html( (string) kj_option( 'bev_adres', '' ) ); ?></dt><dd><?php echo esc_html( trim( $order->get_billing_address_1() . ', ' . $order->get_billing_city(), ', ' ) ); ?></dd></div>
					<div><dt><?php echo esc_html( (string) kj_option( 'bev_totaal', '' ) ); ?></dt><dd><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd></div>
				</dl>
			</section>

			<?php if ( $heeft_abo ) : ?>
				<section class="abo-donker">
					<p class="eyebrow eyebrow--licht"><span class="eyebrow__lijn"></span><span><?php echo esc_html( (string) kj_option( 'bev_abo_eyebrow', '' ) ); ?></span></p>
					<h2 class="font-serif"><?php echo esc_html( sprintf( (string) kj_option( 'bev_abo_titel', '%s' ), $volgende ) ); ?></h2>
					<p class="text-body"><?php echo esc_html( (string) kj_option( 'bev_abo_tekst', '' ) ); ?></p>
					<?php kj_component( 'button', array( 'label' => (string) kj_option( 'bev_abo_knop', '' ), 'url' => wc_get_account_endpoint_url( 'abonnement' ), 'variant' => 'licht' ) ); ?>
				</section>
			<?php endif; ?>
		</div>

	</div>
</div>
