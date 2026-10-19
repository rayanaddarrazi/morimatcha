<?php
/**
 * Inloggen (zonder registratie: klanten krijgen een account bij hun eerste bestelling).
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );
?>
<div class="account-login container">
	<h2 class="font-serif"><?php esc_html_e( 'Inloggen', 'morimatcha' ); ?></h2>
	<form class="woocommerce-form woocommerce-form-login login" method="post">
		<?php do_action( 'woocommerce_login_form_start' ); ?>
		<p class="form-row">
			<label for="username"><?php esc_html_e( 'E-mailadres', 'morimatcha' ); ?></label>
			<input type="text" name="username" id="username" autocomplete="username" value="<?php echo ! empty( $_POST['username'] ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore WordPress.Security ?>" required>
		</p>
		<p class="form-row">
			<label for="password"><?php esc_html_e( 'Wachtwoord', 'morimatcha' ); ?></label>
			<input type="password" name="password" id="password" autocomplete="current-password" required>
		</p>
		<?php do_action( 'woocommerce_login_form' ); ?>
		<p class="form-row">
			<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
			<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Inloggen', 'morimatcha' ); ?>"><?php esc_html_e( 'Inloggen', 'morimatcha' ); ?></button>
		</p>
		<p class="form-row"><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="account__uit" style="margin:0"><?php esc_html_e( 'Wachtwoord vergeten?', 'morimatcha' ); ?></a></p>
		<?php do_action( 'woocommerce_login_form_end' ); ?>
	</form>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
