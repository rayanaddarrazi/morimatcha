<?php
/**
 * Inloggen: foto links, formulier rechts. Klanten krijgen een account bij hun eerste bestelling.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$t    = fn( $k, $d = '' ) => (string) kj_option( $k, $d );
$foto = kj_option( 'acc_login_foto' );
$registreren = ! empty( $_POST['register'] ) || ! empty( $_GET['registreren'] ); // phpcs:ignore WordPress.Security.NonceVerification
$terug       = isset( $_GET['terug'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_GET['terug'] ) ), '' ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
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

		<div class="inlog__formulier" data-inlog>
			<div class="inlog__tabs" role="tablist" aria-label="<?php echo esc_attr( $t( 'acc_login_titel', 'Inloggen' ) ); ?>">
				<button type="button" role="tab" id="tab-inloggen" aria-controls="paneel-inloggen" aria-selected="<?php echo $registreren ? 'false' : 'true'; ?>" data-inlog-tab="inloggen"><?php echo esc_html( $t( 'acc_login_tab_inloggen', 'Inloggen' ) ); ?></button>
				<button type="button" role="tab" id="tab-registreren" aria-controls="paneel-registreren" aria-selected="<?php echo $registreren ? 'true' : 'false'; ?>" data-inlog-tab="registreren"><?php echo esc_html( $t( 'acc_login_tab_registreren', 'Account maken' ) ); ?></button>
			</div>

			<div class="inlog__paneel" id="paneel-inloggen" role="tabpanel" aria-labelledby="tab-inloggen" data-inlog-paneel="inloggen">
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
							<button type="button" class="inlog__oog" data-toon-wachtwoord aria-pressed="false" data-verberg="<?php echo esc_attr( $t( 'acc_login_verberg', 'Verberg' ) ); ?>"><?php echo esc_html( $t( 'acc_login_toon', 'Toon' ) ); ?></button>
						</span>
					</p>
					<label class="inlog__vink"><input type="checkbox" name="rememberme" value="forever"><span><?php echo esc_html( $t( 'acc_login_onthoud', 'Houd me ingelogd' ) ); ?></span></label>
					<input type="hidden" name="redirect" value="<?php echo esc_url( $terug ); ?>">
					<?php do_action( 'woocommerce_login_form' ); ?>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="inlog__knop" name="login" value="<?php echo esc_attr( $t( 'acc_login_knop', 'Inloggen' ) ); ?>"><?php echo esc_html( $t( 'acc_login_knop', 'Inloggen' ) ); ?></button>
					<?php do_action( 'woocommerce_login_form_end' ); ?>
				</form>
				<p class="inlog__nieuw"><?php echo esc_html( $t( 'acc_login_nieuw' ) ); ?> <button type="button" class="inlog__wissel" data-inlog-tab="registreren"><?php echo esc_html( $t( 'acc_login_nieuw_link', 'Account maken' ) ); ?></button></p>
			</div>

			<div class="inlog__paneel" id="paneel-registreren" role="tabpanel" aria-labelledby="tab-registreren" data-inlog-paneel="registreren">
				<h2 class="inlog__titel font-serif"><?php echo esc_html( $t( 'acc_reg_titel', 'Account maken' ) ); ?></h2>
				<p class="inlog__sub"><?php echo esc_html( $t( 'acc_reg_sub' ) ); ?></p>

				<form method="post" class="woocommerce-form woocommerce-form-register register" novalidate>
					<?php do_action( 'woocommerce_register_form_start' ); ?>
					<div class="inlog__rij">
						<p class="inlog__veld">
							<label for="reg_voornaam"><?php echo esc_html( $t( 'afr_voornaam', 'Voornaam' ) ); ?></label>
							<input type="text" name="voornaam" id="reg_voornaam" autocomplete="given-name" value="<?php echo ! empty( $_POST['voornaam'] ) ? esc_attr( wp_unslash( $_POST['voornaam'] ) ) : ''; // phpcs:ignore WordPress.Security ?>" required>
						</p>
						<p class="inlog__veld">
							<label for="reg_achternaam"><?php echo esc_html( $t( 'afr_achternaam', 'Achternaam' ) ); ?></label>
							<input type="text" name="achternaam" id="reg_achternaam" autocomplete="family-name" value="<?php echo ! empty( $_POST['achternaam'] ) ? esc_attr( wp_unslash( $_POST['achternaam'] ) ) : ''; // phpcs:ignore WordPress.Security ?>" required>
						</p>
					</div>
					<p class="inlog__veld">
						<label for="reg_email"><?php echo esc_html( $t( 'afr_email_label', 'E-mailadres' ) ); ?></label>
						<input type="email" name="email" id="reg_email" autocomplete="email" value="<?php echo ! empty( $_POST['email'] ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore WordPress.Security ?>" required>
					</p>
					<p class="inlog__veld">
						<label for="reg_wachtwoord"><?php echo esc_html( $t( 'acc_login_wachtwoord', 'Wachtwoord' ) ); ?></label>
						<span class="inlog__wachtwoord">
							<input type="password" name="password" id="reg_wachtwoord" autocomplete="new-password" minlength="<?php echo (int) KJ_WACHTWOORD_MIN; ?>" aria-describedby="reg_hulp" required>
							<button type="button" class="inlog__oog" data-toon-wachtwoord aria-pressed="false" data-verberg="<?php echo esc_attr( $t( 'acc_login_verberg', 'Verberg' ) ); ?>"><?php echo esc_html( $t( 'acc_login_toon', 'Toon' ) ); ?></button>
						</span>
						<span class="inlog__hulp" id="reg_hulp"><?php echo esc_html( sprintf( $t( 'acc_reg_wachtwoord_hulp', 'Minimaal %d tekens.' ), KJ_WACHTWOORD_MIN ) ); ?></span>
					</p>
					<p class="inlog__bot" aria-hidden="true"><label>Website<input type="text" name="kj_website" tabindex="-1" autocomplete="off"></label></p>
					<label class="inlog__vink"><input type="checkbox" name="voorwaarden" value="1" required><span><?php echo esc_html( $t( 'afr_voorwaarden', 'Ik ga akkoord met de algemene voorwaarden' ) ); ?></span></label>
					<?php do_action( 'woocommerce_register_form' ); ?>
					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="inlog__knop" name="register" value="<?php echo esc_attr( $t( 'acc_reg_knop', 'Account maken' ) ); ?>"><?php echo esc_html( $t( 'acc_reg_knop', 'Account maken' ) ); ?></button>
					<?php do_action( 'woocommerce_register_form_end' ); ?>
				</form>
				<p class="inlog__nieuw"><?php echo esc_html( $t( 'acc_reg_heb_account', 'Heb je al een account?' ) ); ?> <button type="button" class="inlog__wissel" data-inlog-tab="inloggen"><?php echo esc_html( $t( 'acc_login_titel', 'Inloggen' ) ); ?></button></p>
			</div>
		</div>

	</div>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
