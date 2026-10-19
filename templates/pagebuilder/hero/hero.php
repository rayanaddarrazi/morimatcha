<?php
/**
 * Hero met foto (Figma: Home > Hero). De header ligt transparant over de foto.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$foto        = get_sub_field( 'foto' );
$foto_mobiel = get_sub_field( 'foto_mobiel' );
$eyebrow     = get_sub_field( 'eyebrow' );
$titel       = (string) get_sub_field( 'titel' );
$groote      = get_sub_field( 'titelgrootte' ) ?: 'display';
$noot        = get_sub_field( 'noot' );
$stijl       = get_sub_field( 'foto_stijl' ) ?: 'vol';
$etiket      = (bool) get_sub_field( 'toon_etiket' );
$hoogte      = (int) get_sub_field( 'hoogte' ) ?: 900;
$zoom        = max( 1, (float) get_sub_field( 'foto_zoom' ) );
$focus       = array( 'midden' => '50% 50%', 'onder' => '50% 100%', 'boven' => '50% 0%', 'links' => '0% 50%', 'rechts' => '100% 50%', 'onder-links' => '0% 100%' )[ get_sub_field( 'foto_focus' ) ?: 'midden' ] ?? '50% 50%';
$tekst       = get_sub_field( 'tekst' );
$knop_1      = kj_link_args( get_sub_field( 'knop_primair' ), 'licht' );
$knop_2      = kj_link_args( get_sub_field( 'knop_secundair' ), 'contour', array( 'icon' => false ) );

$desktop_id = kj_image_id( $foto );
$mobiel_id  = kj_image_id( $foto_mobiel );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="hero hero--<?php echo esc_attr( $stijl ); ?> sectie--mos" style="--hero-h: <?php echo (int) $hoogte; ?>px; --foto-zoom: <?php echo esc_attr( $zoom ); ?>; --foto-focus: <?php echo esc_attr( $focus ); ?>">

	<div class="hero__foto">
		<?php if ( $desktop_id && 'rechts' === $stijl ) : ?>
			<div class="hero__foto-rechts">
				<?php echo wp_get_attachment_image( $desktop_id, 'mori-1920', false, array( 'sizes' => '112vw', 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( $etiket ) { kj_component( 'etiket', array( 'gram' => '60', 'inset' => array( 45.31, 48.2, 40.59, 37.6 ) ) ); } ?>
			</div>
		<?php elseif ( $desktop_id ) : ?>
			<picture>
				<?php if ( $mobiel_id ) : ?>
					<source media="(max-width: 1023px)" srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( $mobiel_id, 'mori-1000' ) ); ?>" sizes="100vw">
				<?php endif; ?>
				<?php
				echo wp_get_attachment_image( $desktop_id, 'mori-1920', false, array( // phpcs:ignore WordPress.Security.EscapeOutput
					'sizes'         => '100vw',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'decoding'      => 'async',
				) );
				?>
			</picture>
		<?php endif; ?>
		<div class="hero__verloop hero__verloop--zijkant"></div>
		<?php if ( 'rechts' === $stijl ) : ?><div class="hero__verloop hero__verloop--donker"></div><?php endif; ?>
		<div class="hero__verloop hero__verloop--boven"></div>
	</div>

	<div class="hero__inhoud container">
		<div class="hero__tekst">
			<?php if ( $eyebrow ) : ?>
				<div data-hero-item style="--i: 0"><?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => 'licht' ) ); ?></div>
			<?php endif; ?>
			<?php if ( $titel ) : ?>
				<h1 class="hero__titel font-serif <?php echo 'h1' === $groote ? 'text-h1' : 'text-display'; ?> text-rijstpapier" data-hero-item style="--i: 1"><?php echo nl2br( esc_html( $titel ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
			<?php endif; ?>
			<?php if ( $tekst ) : ?>
				<p class="hero__intro text-body lg:text-body-l text-gerst" data-hero-item style="--i: 2"><?php echo esc_html( $tekst ); ?></p>
			<?php endif; ?>
			<?php if ( $knop_1 || $knop_2 ) : ?>
				<div class="hero__knoppen" data-hero-item style="--i: 3">
					<?php
					if ( $knop_1 ) {
						$knop_1['class'] = 'kj-button--vol lg:w-auto';
						kj_component( 'button', $knop_1 );
					}
					if ( $knop_2 ) {
						$knop_2['class'] = 'kj-button--vol lg:w-auto';
						kj_component( 'button', $knop_2 );
					}
					?>
				</div>
			<?php endif; ?>
			<?php if ( $noot ) : ?>
				<p class="hero__noot text-klein text-gerst" data-hero-item style="--i: 4"><?php echo esc_html( $noot ); ?></p>
			<?php endif; ?>
		</div>
	</div>

</section>
