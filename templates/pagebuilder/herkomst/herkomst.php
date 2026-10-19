<?php
/**
 * Herkomst: tekst met stappen en foto's (Figma: Home > Herkomst)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$titel   = get_sub_field( 'titel' );
$intro   = get_sub_field( 'intro' );
$stappen = get_sub_field( 'stappen' );
$groot   = get_sub_field( 'foto_groot' );
$fotos   = get_sub_field( 'fotos' );
$donker  = in_array( get_sub_field( 'achtergrond' ) ?: 'mos', array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'mos' ) ); ?> herkomst">
	<div class="container herkomst__rij grid-mori">

		<div class="herkomst__tekst">
			<div class="herkomst__kop" data-reveal>
				<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
				<?php if ( $titel ) : ?><h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2><?php endif; ?>
				<?php if ( $intro ) : ?><p class="text-body lg:text-body-l herkomst__intro"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
			</div>

			<?php if ( $stappen ) : ?>
				<ol class="herkomst__stappen" data-reveal style="--i: 1">
					<?php foreach ( $stappen as $i => $stap ) : ?>
						<li class="herkomst__stap">
							<span class="text-eyebrow font-semibold uppercase herkomst__nummer"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
							<div>
								<h3 class="font-serif text-h4"><?php echo esc_html( $stap['titel'] ); ?></h3>
								<p class="text-klein herkomst__uitleg"><?php echo esc_html( $stap['uitleg'] ); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</div>

		<div class="herkomst__beelden">
			<?php if ( $groot ) : ?>
				<div data-reveal data-parallax class="herkomst__groot">
					<?php kj_component( 'image', array( 'image' => $groot, 'class' => 'rounded-groot herkomst__theeveld', 'sizes' => '(min-width: 1024px) 58vw, 92vw', 'size' => 'mori-1440' ) ); ?>
				</div>
			<?php endif; ?>
			<?php if ( $fotos ) : ?>
				<div class="herkomst__fotos">
					<?php foreach ( $fotos as $i => $item ) : ?>
						<figure data-reveal style="--i: <?php echo (int) $i; ?>">
							<?php kj_component( 'image', array( 'image' => $item['foto'], 'class' => 'rounded-groot herkomst__foto', 'sizes' => '(min-width: 1024px) 28vw, 46vw', 'size' => 'mori-720', 'zoom' => true ) ); ?>
							<figcaption class="text-meta herkomst__bijschrift"><?php echo esc_html( $item['bijschrift'] ); ?></figcaption>
						</figure>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

	</div>
</section>
