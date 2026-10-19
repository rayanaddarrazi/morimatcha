<?php
/**
 * Vensters (dialogen) voor pauzeren, maat wijzigen en opzeggen (Figma "Account opzeggen").
 *
 * @var array $abo
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$t       = fn( $k ) => (string) kj_option( $k, '' );
$action  = esc_url( admin_url( 'admin-post.php' ) );
$redenen = (array) kj_option( 'acc_redenen', array() );
$product = wc_get_product( (int) $abo['product'] );
$maten   = $product ? kj_product_sizes( $product ) : array();
$laatste = kj_abo_datum( $abo['laatste'], 'j F' );
?>

<dialog class="dialoog" id="dlg-opzeggen" aria-labelledby="dlg-opzeggen-titel">
	<form method="post" action="<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput ?>" class="dialoog__inhoud">
		<input type="hidden" name="action" value="mori_abo_actie"><input type="hidden" name="actie" value="opzeggen">
		<?php wp_nonce_field( 'mori_abo_actie' ); ?>
		<h2 class="font-serif dialoog__titel" id="dlg-opzeggen-titel"><?php echo esc_html( $t( 'acc_dialog_titel' ) ); ?></h2>
		<p class="dialoog__tekst"><?php echo esc_html( sprintf( $t( 'acc_dialog_tekst' ), $laatste ) ); ?></p>

		<div class="dialoog__pauze">
			<div>
				<p class="dialoog__kop"><?php echo esc_html( $t( 'acc_dialog_pauze_kop' ) ); ?></p>
				<p class="dialoog__sub"><?php echo esc_html( $t( 'acc_dialog_pauze_tekst' ) ); ?></p>
			</div>
			<button type="button" class="kj-button kj-button--secundair" data-dialoog-open="dlg-pauzeren"><span class="kj-button__label"><?php echo esc_html( $t( 'acc_dialog_pauze_knop' ) ); ?></span></button>
		</div>

		<fieldset class="dialoog__redenen">
			<legend class="dialoog__kop"><?php echo esc_html( $t( 'acc_dialog_reden' ) ); ?></legend>
			<?php foreach ( $redenen as $i => $reden ) : ?>
				<label class="optie"><input type="radio" name="reden" value="<?php echo esc_attr( $reden['tekst'] ); ?>"><span class="optie__vink"></span><span><?php echo esc_html( $reden['tekst'] ); ?></span></label>
			<?php endforeach; ?>
		</fieldset>

		<div class="dialoog__knoppen">
			<button type="submit" class="kj-button kj-button--primair"><span class="kj-button__label"><?php echo esc_html( $t( 'acc_dialog_ja' ) ); ?></span></button>
			<button type="button" class="kj-button kj-button--tekstlink" data-dialoog-sluit><span class="kj-button__label"><?php echo esc_html( $t( 'acc_dialog_nee' ) ); ?></span></button>
		</div>
	</form>
</dialog>

<dialog class="dialoog" id="dlg-pauzeren" aria-labelledby="dlg-pauzeren-titel">
	<form method="post" action="<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput ?>" class="dialoog__inhoud">
		<input type="hidden" name="action" value="mori_abo_actie"><input type="hidden" name="actie" value="pauzeren">
		<?php wp_nonce_field( 'mori_abo_actie' ); ?>
		<h2 class="font-serif dialoog__titel" id="dlg-pauzeren-titel"><?php echo esc_html( $t( 'acc_knop_pauzeren' ) ); ?></h2>
		<p class="dialoog__tekst"><?php echo esc_html( $t( 'acc_pauze_tekst' ) ); ?></p>
		<fieldset class="dialoog__redenen">
			<?php foreach ( array( 1, 2, 3 ) as $m ) : ?>
				<label class="optie"><input type="radio" name="maanden" value="<?php echo (int) $m; ?>" <?php checked( 1, $m ); ?>><span class="optie__vink"></span><span><?php echo esc_html( sprintf( $t( 'acc_pauze_maanden' ), $m ) . ( $m > 1 ? 'en' : '' ) ); ?></span></label>
			<?php endforeach; ?>
		</fieldset>
		<div class="dialoog__knoppen">
			<button type="submit" class="kj-button kj-button--primair"><span class="kj-button__label"><?php echo esc_html( $t( 'acc_knop_pauzeren' ) ); ?></span></button>
			<button type="button" class="kj-button kj-button--tekstlink" data-dialoog-sluit><span class="kj-button__label"><?php echo esc_html( $t( 'acc_dialog_nee' ) ); ?></span></button>
		</div>
	</form>
</dialog>

<dialog class="dialoog" id="dlg-maat" aria-labelledby="dlg-maat-titel">
	<form method="post" action="<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput ?>" class="dialoog__inhoud">
		<input type="hidden" name="action" value="mori_abo_actie"><input type="hidden" name="actie" value="maat">
		<?php wp_nonce_field( 'mori_abo_actie' ); ?>
		<h2 class="font-serif dialoog__titel" id="dlg-maat-titel"><?php echo esc_html( $t( 'acc_knop_maat' ) ); ?></h2>
		<fieldset class="dialoog__redenen">
			<?php foreach ( $maten as $maat ) :
				$prijzen = kj_size_prices( $maat );
				?>
				<label class="optie"><input type="radio" name="gram" value="<?php echo (int) $maat['gram']; ?>" <?php checked( (int) $abo['gram'], (int) $maat['gram'] ); ?>><span class="optie__vink"></span><span><?php echo esc_html( (int) $maat['gram'] . ' gram, ' . kj_format_price( $prijzen['maand'] ) ); ?></span></label>
			<?php endforeach; ?>
		</fieldset>
		<div class="dialoog__knoppen">
			<button type="submit" class="kj-button kj-button--primair"><span class="kj-button__label"><?php echo esc_html( $t( 'acc_knop_maat' ) ); ?></span></button>
			<button type="button" class="kj-button kj-button--tekstlink" data-dialoog-sluit><span class="kj-button__label"><?php echo esc_html( $t( 'acc_dialog_nee' ) ); ?></span></button>
		</div>
	</form>
</dialog>
