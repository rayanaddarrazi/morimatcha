<?php
/**
 * Smaakprofiel met vragen (Figma: Productpagina > Voor je kiest)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$titel   = get_sub_field( 'titel' );
$intro   = get_sub_field( 'intro' );
$profiel = get_sub_field( 'profiel' );
$vragen  = get_sub_field( 'vragen' );
$donker  = in_array( get_sub_field( 'achtergrond' ), array( 'mos', 'bosgroen' ), true );
$uid     = 'sv-' . wp_unique_id();
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'wit' ) ); ?> smaakprofiel">
	<div class="container smaakprofiel__binnen">

		<div class="smaakprofiel__kop" data-reveal>
			<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
			<?php if ( $titel ) : ?><h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2><?php endif; ?>
			<?php if ( $intro ) : ?><p class="text-body lg:text-body-l text-steen"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
		</div>

		<div class="smaakprofiel__rij grid-mori">

			<?php if ( is_array( $profiel ) ) : ?>
				<div class="profiel" data-reveal>
					<div class="profiel__kop">
						<p class="text-eyebrow font-semibold uppercase profiel__label"><?php echo esc_html( $profiel['label'] ?? '' ); ?></p>
						<h3 class="font-serif text-h3"><?php echo esc_html( $profiel['kop'] ?? '' ); ?></h3>
						<p class="text-body text-steen"><?php echo esc_html( $profiel['tekst'] ?? '' ); ?></p>
					</div>
					<?php if ( ! empty( $profiel['schalen'] ) ) : ?>
						<div class="profiel__schalen">
							<?php foreach ( $profiel['schalen'] as $schaal ) :
								$waarde = max( 1, min( 5, (int) $schaal['waarde'] ) );
								?>
								<div class="schaal" style="--w: <?php echo (int) $waarde; ?>">
									<p class="schaal__kenmerk text-label"><?php echo esc_html( $schaal['kenmerk'] ); ?></p>
									<div class="schaal__rechts">
										<div class="schaal__spoor" role="img" aria-label="<?php echo esc_attr( sprintf( '%s: %d van 5', $schaal['kenmerk'], $waarde ) ); ?>">
											<span class="schaal__lijn"></span>
											<span class="schaal__gevuld"></span>
											<?php for ( $i = 1; $i <= 5; $i++ ) : ?><span class="schaal__stap<?php echo $i <= $waarde ? ' is-gevuld' : ''; ?>" style="--p: <?php echo (int) $i; ?>"></span><?php endfor; ?>
											<span class="schaal__markering"></span>
										</div>
										<div class="schaal__labels text-meta"><span><?php echo esc_html( $schaal['links'] ); ?></span><span><?php echo esc_html( $schaal['rechts'] ); ?></span></div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $profiel['feiten'] ) ) : ?>
						<dl class="profiel__feiten">
							<?php foreach ( $profiel['feiten'] as $feit ) : ?>
								<div><dt class="text-meta text-steen"><?php echo esc_html( $feit['label'] ); ?></dt><dd class="text-label"><?php echo esc_html( $feit['waarde'] ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $vragen ) : ?>
				<div class="smaakprofiel__vragen accordeon" data-reveal style="--i: 1">
					<?php foreach ( $vragen as $i => $item ) :
						$open = 0 === $i;
						$id   = $uid . '-' . $i;
						?>
						<div class="accordeon__item<?php echo $open ? ' is-open' : ''; ?>">
							<h3 class="accordeon__kop">
								<button type="button" class="accordeon__knop" data-accordeon aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $id ); ?>">
									<span class="font-serif text-h4"><?php echo esc_html( $item['vraag'] ); ?></span>
									<span class="accordeon__icoon" aria-hidden="true"><i></i><i></i></span>
								</button>
							</h3>
							<div class="accordeon__paneel" id="<?php echo esc_attr( $id ); ?>" role="region">
								<div class="accordeon__binnen"><p class="text-body text-steen"><?php echo esc_html( $item['antwoord'] ); ?></p></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
