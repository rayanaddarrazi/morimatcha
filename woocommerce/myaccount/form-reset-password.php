<?php
/**
 * Wachtwoord opnieuw instellen, in dezelfde stijl als inloggen.
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
			<h1 class="inlog__titel font-serif"><?php echo esc_html( $t( 'acc_reset_titel' ) ); ?></h1>
			<p class="inlog__sub"><?php echo esc_html( $t( 'acc_reset_sub' ) ); ?></p>
			<form method="post" class="woocommerce-ResetPassword lost_reset_password">
				<p class="inlog__veld">
					<label for="password_1"><?php echo esc_html( $t( 'acc_wachtwoord_nieuw', 'Nieuw wachtwoord' ) ); ?></label>
					<input type="password" name="password_1" id="password_1" autocomplete="new-password" required>
				</p>
				<p class="inlog__veld">
					<label for="password_2"><?php echo esc_html( $t( 'acc_wachtwoord_herhaal', 'Herhaal nieuw wachtwoord' ) ); ?></label>
					<input type="password" name="password_2" id="password_2" autocomplete="new-password" required>
				</p>
				<input type="hidden" name="wc_reset_password" value="true">
				<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>">
				<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>">
				<?php do_action( 'woocommerce_resetpassword_form' ); ?>
				<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>
				<button type="submit" class="inlog__knop"><?php echo esc_html( $t( 'acc_reset_knop', 'Wachtwoord opslaan' ) ); ?></button>
			</form>
			
		</div>
	</div>
</div>
