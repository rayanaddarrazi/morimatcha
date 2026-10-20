<?php
/**
 * Wachtwoord vergeten, in dezelfde stijl als inloggen.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$t = fn( $k, $d = '' ) => (string) kj_option( $k, $d );
wc_print_notices();
?>
<div class="inlog inlog--smal container">
	<div class="inlog__kaart inlog__kaart--enkel">
		<div class="inlog__formulier">
			<h1 class="inlog__titel font-serif"><?php echo esc_html( $t( 'acc_vergeten_titel' ) ); ?></h1>
			<p class="inlog__sub"><?php echo esc_html( $t( 'acc_vergeten_sub' ) ); ?></p>
			<form method="post" class="woocommerce-ResetPassword lost_reset_password">
				<p class="inlog__veld">
					<label for="user_login"><?php echo esc_html( $t( 'afr_email_label', 'E-mailadres' ) ); ?></label>
					<input type="text" name="user_login" id="user_login" autocomplete="username" inputmode="email" required>
				</p>
				<?php do_action( 'woocommerce_lostpassword_form' ); ?>
				<input type="hidden" name="wc_reset_password" value="true">
				<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
				<button type="submit" class="inlog__knop"><?php echo esc_html( $t( 'acc_vergeten_knop', 'Link versturen' ) ); ?></button>
			</form>
			<p class="inlog__nieuw"><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo esc_html( $t( 'acc_vergeten_terug', 'Terug naar inloggen' ) ); ?></a></p>
		</div>
	</div>
</div>
