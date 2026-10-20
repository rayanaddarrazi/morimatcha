<?php
/**
 * Gegevens: naam en e-mailadres, en een eigen kaart voor het wachtwoord.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$user = wp_get_current_user();
$t    = fn( $k, $d = '' ) => (string) kj_option( $k, $d );
kj_account_kop( kj_account_tabnaam( 'edit-account' ) );
do_action( 'woocommerce_before_edit_account_form' );
?>
<form class="account-formulier woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>
	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<section class="account-kaart">
		<h2 class="account-kaart__titel font-serif"><?php echo esc_html( $t( 'acc_gegevens_persoon', 'Je gegevens' ) ); ?></h2>
		<div class="account-kaart__velden">
			<p class="veld veld--half"><label for="account_first_name"><?php echo esc_html( $t( 'afr_voornaam', 'Voornaam' ) ); ?></label><input type="text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" required></p>
			<p class="veld veld--half"><label for="account_last_name"><?php echo esc_html( $t( 'afr_achternaam', 'Achternaam' ) ); ?></label><input type="text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" required></p>
			<p class="veld"><label for="account_email"><?php echo esc_html( $t( 'afr_email_label', 'E-mailadres' ) ); ?></label><input type="email" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" required></p>
		</div>
	</section>

	<section class="account-kaart">
		<h2 class="account-kaart__titel font-serif"><?php echo esc_html( $t( 'acc_gegevens_wachtwoord', 'Wachtwoord wijzigen' ) ); ?></h2>
		<p class="account-kaart__hulp"><?php echo esc_html( $t( 'acc_gegevens_wachtwoord_hulp', '' ) ); ?></p>
		<div class="account-kaart__velden">
			<p class="veld"><label for="password_current"><?php echo esc_html( $t( 'acc_wachtwoord_huidig', 'Huidig wachtwoord' ) ); ?></label><input type="password" name="password_current" id="password_current" autocomplete="current-password"></p>
			<p class="veld veld--half"><label for="password_1"><?php echo esc_html( $t( 'acc_wachtwoord_nieuw', 'Nieuw wachtwoord' ) ); ?></label><input type="password" name="password_1" id="password_1" autocomplete="new-password"></p>
			<p class="veld veld--half"><label for="password_2"><?php echo esc_html( $t( 'acc_wachtwoord_herhaal', 'Herhaal nieuw wachtwoord' ) ); ?></label><input type="password" name="password_2" id="password_2" autocomplete="new-password"></p>
		</div>
	</section>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<div class="account-formulier__acties">
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="account-knop" name="save_account_details" value="<?php esc_attr_e( 'Opslaan', 'morimatcha' ); ?>"><?php echo esc_html( $t( 'acc_opslaan', 'Wijzigingen opslaan' ) ); ?></button>
		<input type="hidden" name="action" value="save_account_details">
	</div>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>
<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
