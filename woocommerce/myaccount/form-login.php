<?php
/**
 * Inloggen: foto links, formulier rechts. Klanten krijgen een account bij hun eerste bestelling.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$t    = fn( $k, $d = '' ) => (string) kj_option( $k, $d );
$foto = kj_option( 'acc_login_foto' );
do_action( 'woocommerce_before_customer_login_form' );
?>
<div class="inlog container">
	<div class="inlog__kaart">

		<div class="inlog__beeld">
			<?php if ( $foto ) { kj_component( 'image', array( 'image' => $foto, 'class' => 'inlog__foto', 'sizes' => '(min-width: 1024px) 560px, 100vw', 'eager' => true ) ); } ?>
			<div class="inlog__beeld-tekst">
				<p class="inlog__beeld-kop font-serif"><?php echo esc_html( $t( 'acc_login_beeld_kop' ) ); ?></p>
				<ul>
					<?php foreach ( (array) kj_option( 'acc_login_punten', array() ) as $punt ) : ?>
						<li><?php echo kj_svg( 'vinkje' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $punt['tekst'] ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="inlog__formulier">
			<h1 class="inlog__titel font-serif"><?php echo esc_html( $t( 'acc_login_titel', 'Inloggen' ) ); ?></h1>
			<p class="inlog__sub"><?php echo esc_html( $t( 'acc_login_sub' ) ); ?></p>

			<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>
				<?php do_action( 'woocommerce_login_form_start' ); ?>
				<p class="inlog__veld">
					<label for="username"><?php echo esc_html( $t( 'afr_email_label', 'E-mailadres' ) ); ?></label>
					<input type="text" name="username" id="username" autocomplete="username" inputmode="email" value="<?php echo ! empty( $_POST['username'] ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore WordPress.Security ?>" required>
				</p>
				<p class="inlog__veld">
					<span class="inlog__label-rij">
						<label for="password"><?php echo esc_html( $t( 'acc_login_wachtwoord', 'Wachtwoord' ) ); ?></label>
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="inlog__vergeten"><?php echo esc_html( $t( 'acc_login_vergeten', 'Wachtwoord vergeten?' ) ); ?></a>
					</span>
					<span class="inlog__wachtwoord">
						<input type="password" name="password" id="password" autocomplete="current-password" required>
						<button type="button" class="inlog__oog" data-toon-wachtwoord aria-pressed="false"><?php echo esc_html( $t( 'acc_login_toon', 'Toon' ) ); ?></button>
					</span>
				</p>
				<?php do_action( 'woocommerce_login_form' ); ?>
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="inlog__knop" name="login" value="<?php echo esc_attr( $t( 'acc_login_knop', 'Inloggen' ) ); ?>"><?php echo esc_html( $t( 'acc_login_knop', 'Inloggen' ) ); ?></button>
				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>

			<p class="inlog__nieuw"><?php echo esc_html( $t( 'acc_login_nieuw' ) ); ?></p>
		</div>

	</div>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
