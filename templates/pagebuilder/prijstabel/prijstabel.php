<?php
/**
 * Prijstabel per maat: eenmalig, abonnement en prijs per gram (Figma: Abonnement > Prijzen)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$titel   = get_sub_field( 'titel' );
$intro   = get_sub_field( 'intro' );
$koppen  = get_sub_field( 'koppen' );
$product = (int) get_sub_field( 'product' );
$sizes   = $product ? kj_product_sizes( $product ) : array();
if ( ! $sizes ) {
	return;
}
$goedkoopste = kj_cheapest_size_id( $sizes );
$donker      = in_array( get_sub_field( 'achtergrond' ), array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'rijstpapier' ) ); ?> prijstabel">
	<div class="container prijstabel__binnen">
		<div class="prijstabel__kop" data-reveal>
			<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
			<h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2>
			<?php if ( $intro ) : ?><p class="text-body lg:text-body-l text-steen"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
		</div>

		<table class="prijstabel__tabel" data-reveal style="--i: 1">
			<thead>
				<tr class="text-meta">
					<th scope="col"><?php echo esc_html( $koppen['maat'] ?? 'Maat' ); ?></th>
					<th scope="col"><?php echo esc_html( $koppen['eenmalig'] ?? 'Eenmalig' ); ?></th>
					<th scope="col"><?php echo esc_html( $koppen['abonnement'] ?? 'Abonnement' ); ?></th>
					<th scope="col"><?php echo esc_html( $koppen['per_gram'] ?? 'Per gram met abonnement' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $sizes as $size ) :
					$prijzen = kj_size_prices( $size );
					?>
					<tr>
						<th scope="row" class="font-serif text-h4" data-label="<?php echo esc_attr( $koppen['maat'] ?? 'Maat' ); ?>"><?php echo esc_html( (int) $size['gram'] . ' gram, ' . sprintf( (string) kj_option( 'koppen_regel', '± %d koppen' ), $size['koppen'] ) ); ?></th>
						<td class="text-body text-steen" data-label="<?php echo esc_attr( $koppen['eenmalig'] ?? 'Eenmalig' ); ?>"><?php echo esc_html( kj_format_price( $prijzen['eenmalig'] ) ); ?></td>
						<td data-label="<?php echo esc_attr( $koppen['abonnement'] ?? 'Abonnement' ); ?>">
							<span class="prijstabel__abo">
								<span class="text-prijs"><?php echo esc_html( kj_format_price( $prijzen['maand'] ) ); ?></span>
								<?php if ( $size['id'] === $goedkoopste && count( $sizes ) > 1 ) { kj_component( 'badge', array( 'text' => (string) kj_option( 'badge_voordeligst', 'Voordeligst' ), 'stijl' => 'blad' ) ); } ?>
							</span>
						</td>
						<td class="text-body text-matcha" data-label="<?php echo esc_attr( $koppen['per_gram'] ?? 'Per gram' ); ?>"><?php echo esc_html( kj_format_price( $size['gram'] ? $prijzen['maand'] / $size['gram'] : 0 ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
