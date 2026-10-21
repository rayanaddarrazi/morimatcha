<?php
/**
 * Kassa (Figma "Afrekenen"): drie kaarten links, samenvatting rechts.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'Je moet ingelogd zijn om af te rekenen.', 'morimatcha' ) ) );
	return;
}

$velden   = $checkout->get_checkout_fields( 'billing' );
$toon_abo = ! is_user_logged_in();

$veld = function ( $sleutel ) use ( $velden, $checkout ) {
	if ( isset( $velden[ $sleutel ] ) ) {
		woocommerce_form_field( $sleutel, $velden[ $sleutel ], $checkout->get_value( $sleutel ) );
	}
};
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout kassa" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" novalidate>
	<div class="kassa__grid container">

		<div class="kassa__links">
			<h1 class="kassa__titel font-serif"><?php echo esc_html( (string) kj_option( 'afr_titel', get_the_title() ) ); ?></h1>

			<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout"></div>

			<section class="kaart kassa-kaart" aria-labelledby="kassa-contact">
				<h2 class="kassa-kaart__titel" id="kassa-contact"><span class="kassa-kaart__nr">1</span><?php echo esc_html( (string) kj_option( 'afr_contact_titel', '' ) ); ?></h2>
				<div class="kassa-velden">
					<?php $veld( 'billing_email' ); ?>
				</div>
				<?php if ( ! is_user_logged_in() ) : ?>
					<p class="kassa-hulp kassa-inlog"><?php echo esc_html( (string) kj_option( 'afr_login_link', '' ) ); ?> <a href="<?php echo esc_url( add_query_arg( 'terug', rawurlencode( wc_get_checkout_url() ), wc_get_page_permalink( 'myaccount' ) ) ); ?>"><?php echo esc_html( (string) kj_option( 'afr_login_link_knop', '' ) ); ?></a></p>
				<?php endif; ?>
				<?php if ( $toon_abo ) : ?>
					<div class="kassa-melding">
						<?php echo kj_svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<p class="kassa-melding__kop"><?php echo esc_html( (string) kj_option( 'afr_account_kop', '' ) ); ?></p>
						<p class="kassa-melding__tekst"><?php echo esc_html( (string) kj_option( 'afr_account_tekst', '' ) ); ?></p>
					</div>
				<?php endif; ?>
			</section>

			<section class="kaart kassa-kaart" aria-labelledby="kassa-adres">
				<h2 class="kassa-kaart__titel" id="kassa-adres"><span class="kassa-kaart__nr">2</span><?php echo esc_html( (string) kj_option( 'afr_adres_titel', '' ) ); ?></h2>
				<div class="kassa-velden">
					<?php
					$veld( 'billing_first_name' );
					$veld( 'billing_last_name' );
					$veld( 'billing_postcode' );
					$veld( 'billing_huisnummer' );
					$veld( 'billing_address_1' );
					$veld( 'billing_city' );
					$veld( 'billing_country' );
					?>
				</div>
				<p class="kassa-hulp" data-straat-hulp hidden><?php echo esc_html( (string) kj_option( 'afr_straat_hulp', '' ) ); ?></p>
			</section>

			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
			<?php woocommerce_checkout_payment(); ?>
		</div>

		<aside class="kassa__rechts" aria-label="<?php echo esc_attr( (string) kj_option( 'afr_samenvatting', '' ) ); ?>">
			<?php woocommerce_order_review(); ?>
		</aside>

	</div>
</form>
