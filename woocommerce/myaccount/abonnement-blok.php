<?php
/**
 * Het abonnement van de klant: melding, kaart, verborgen formulieren en de vensters voor pauzeren, maat en opzeggen.
 * Wordt gebruikt op het Overzicht en op de pagina Abonnement.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$user = wp_get_current_user();
$abo  = kj_abo_van_klant( $user->ID );
$knop = get_field( 'acc_geen_abonnement_knop', 'option' );
$t    = fn( $k ) => (string) kj_option( $k, '' );
?>
<?php if ( ! $abo ) : ?>
	<div class="account-leeg">
		<span class="account-leeg__icoon" aria-hidden="true"><?php echo kj_svg( 'herhalen' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<p><?php echo esc_html( $t( 'acc_geen_abonnement' ) ); ?></p>
		<?php if ( $knop ) { kj_component( 'button', array( 'label' => $knop['title'], 'url' => $knop['url'] ) ); } ?>
	</div>
<?php else : ?>

	<?php if ( 'actief' === $abo['status'] ) : ?>
		<div class="kassa-melding account-melding">
			<?php echo kj_svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p class="kassa-melding__kop"><?php echo esc_html( $t( 'acc_pauze_kop' ) ); ?></p>
			<p class="kassa-melding__tekst"><?php echo esc_html( $t( 'acc_pauze_tekst' ) ); ?></p>
		</div>
	<?php elseif ( 'opgezegd' === $abo['status'] ) : ?>
		<div class="kassa-melding account-melding account-melding--gelukt">
			<?php echo kj_svg( 'vinkje' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p class="kassa-melding__kop"><?php echo esc_html( $t( 'acc_opgezegd_melding_kop' ) ); ?></p>
			<p class="kassa-melding__tekst"><?php echo esc_html( $t( 'acc_opgezegd_melding_tekst' ) ); ?></p>
		</div>
	<?php endif; ?>

	<?php kj_component( 'abonnement-kaart', kj_abo_kaart_args( $abo ) ); ?>

	<form id="form-hervatten" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="mori_abo_actie"><input type="hidden" name="actie" value="hervatten"><?php wp_nonce_field( 'mori_abo_actie' ); ?></form>
	<form id="form-opnieuw" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="mori_abo_actie"><input type="hidden" name="actie" value="opnieuw"><?php wp_nonce_field( 'mori_abo_actie' ); ?></form>

	<?php if ( 'opgezegd' !== $abo['status'] ) :
		wc_get_template( 'myaccount/abonnement-vensters.php', array( 'abo' => $abo ) );
	endif; ?>

<?php endif; ?>
