<?php
/**
 * Bereiding: stappen met grote waarden, per manier een tab (Figma: Bereiding Puur / Met melk).
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow   = get_sub_field( 'eyebrow' );
$titel     = get_sub_field( 'titel' );
$intro     = get_sub_field( 'intro' );
$tip       = get_sub_field( 'tip' );
$varianten = get_sub_field( 'varianten' );
if ( empty( $varianten ) ) {
	return;
}
$uid = 'ber-' . wp_unique_id();
$gestapeld = 'gestapeld' === get_sub_field( 'weergave' );
$tabs_tonen = ! $gestapeld && count( $varianten ) > 1;
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'rijstpapier' ) ); ?> bereiding<?php echo $gestapeld ? ' bereiding--gestapeld' : ''; ?>">
	<div class="container<?php echo $gestapeld ? ' bereiding__stapel' : ''; ?>">
	<?php
	$kaarten = $gestapeld ? array_keys( $varianten ) : array( 0 );
	foreach ( $kaarten as $kaart_index ) :
		$zichtbaar = $gestapeld ? array( $kaart_index => $varianten[ $kaart_index ] ) : $varianten;
		?>
		<div class="bereiding__kaart sectie--mos" <?php echo $tabs_tonen ? 'data-bereiding' : ''; ?> data-reveal>

			<div class="bereiding__kop">
				<div class="bereiding__titel">
					<p class="text-eyebrow font-semibold uppercase bereiding__label"><?php echo esc_html( $eyebrow ); ?></p>
					<h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2>
					<p class="text-body bereiding__intro"><?php echo esc_html( $intro ); ?></p>
				</div>
				<?php if ( $tabs_tonen || ( $gestapeld && count( $varianten ) > 1 ) ) : ?>
					<div class="bereiding__wissel"<?php echo $tabs_tonen ? ' role="tablist"' : ' aria-hidden="true"'; ?> aria-label="<?php echo esc_attr( $titel ); ?>">
						<?php foreach ( $varianten as $i => $variant ) :
							$actief = $gestapeld ? ( $i === $kaart_index ) : ( 0 === $i );
							?>
							<?php if ( $tabs_tonen ) : ?>
								<button type="button" role="tab" class="bereiding__tab<?php echo $actief ? ' is-actief' : ''; ?>" id="<?php echo esc_attr( $uid . '-tab-' . $i ); ?>" aria-selected="<?php echo $actief ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $uid . '-' . $i ); ?>" tabindex="<?php echo $actief ? '0' : '-1'; ?>" data-tab="<?php echo (int) $i; ?>"><?php echo esc_html( $variant['naam'] ); ?></button>
							<?php else : ?>
								<span class="bereiding__tab<?php echo $actief ? ' is-actief' : ''; ?>"><?php echo esc_html( $variant['naam'] ); ?></span>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php foreach ( $zichtbaar as $i => $variant ) : ?>
				<ol class="bereiding__stappen"<?php echo $tabs_tonen ? ' role="tabpanel" id="' . esc_attr( $uid . '-' . $i ) . '" aria-labelledby="' . esc_attr( $uid . '-tab-' . $i ) . '"' : ''; ?><?php echo ( $tabs_tonen && 0 !== $i ) ? ' hidden' : ''; ?>>
					<?php foreach ( (array) $variant['stappen'] as $n => $stap ) : ?>
						<li class="bereiding__stap">
							<div class="bereiding__stap-kop">
								<span class="bereiding__icoon"><?php echo kj_svg( $stap['icoon'] ?: 'lepel' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<span class="text-meta bereiding__nummer"><?php echo esc_html( sprintf( (string) kj_option( 'bereiding_stap', 'Stap %d' ), $n + 1 ) ); ?></span>
							</div>
							<p class="font-serif text-h2 bereiding__waarde"><?php echo esc_html( $stap['waarde'] ); ?></p>
							<div class="bereiding__stap-tekst">
								<p class="text-label bereiding__stap-label"><?php echo esc_html( $stap['label'] ); ?></p>
								<p class="text-klein"><?php echo esc_html( $stap['uitleg'] ); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endforeach; ?>

			<?php if ( $tip ) : ?>
				<p class="bereiding__tip text-klein"><?php echo kj_svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $tip ); ?></span></p>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>
	</div>
</section>
