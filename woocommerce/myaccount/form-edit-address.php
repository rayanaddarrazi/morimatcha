<?php
/**
 * Adres wijzigen.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$t = fn( $k, $d = '' ) => (string) kj_option( $k, $d );
kj_account_kop( kj_account_tabnaam( 'edit-address' ) );
do_action( 'woocommerce_before_edit_account_address_form' );
?>
<form class="account-formulier" method="post">
	<section class="account-kaart">
		<h2 class="account-kaart__titel font-serif"><?php echo esc_html( $t( 'acc_adres_kop', 'Bezorgadres' ) ); ?></h2>
		<div class="woocommerce-address-fields">
			<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>
			<div class="woocommerce-address-fields__field-wrapper">
				<?php foreach ( $address as $key => $field ) {
					woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
				} ?>
			</div>
			<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>
		</div>
	</section>
	<div class="account-formulier__acties">
		<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
		<button type="submit" class="account-knop" name="save_address" value="<?php esc_attr_e( 'Opslaan', 'morimatcha' ); ?>"><?php echo esc_html( $t( 'acc_opslaan', 'Wijzigingen opslaan' ) ); ?></button>
		<a class="account-knop account-knop--licht" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>"><?php echo esc_html( $t( 'acc_annuleren', 'Annuleren' ) ); ?></a>
		<input type="hidden" name="action" value="edit_address">
	</div>
</form>
<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
