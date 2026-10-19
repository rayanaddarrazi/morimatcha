<?php
/**
 * Stappen met foto: "Zo werkt het" (Figma: Abonnement > Zo werkt het)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$titel   = get_sub_field( 'titel' );
$label   = (string) get_sub_field( 'stap_label' ) ?: 'Stap';
$stappen = get_sub_field( 'stappen' );
$donker  = in_array( get_sub_field( 'achtergrond' ), array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'wit' ) ); ?> stappen-kaarten">
	<div class="container stappen-kaarten__binnen">
		<div class="stappen-kaarten__kop" data-reveal>
			<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
			<h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2>
		</div>
		<?php if ( $stappen ) : ?>
			<ol class="stappen-kaarten__lijst">
				<?php foreach ( $stappen as $i => $stap ) : ?>
					<li class="stap-kaart" data-reveal style="--i: <?php echo (int) $i; ?>">
						<?php
						if ( ! empty( $stap['toon_etiket'] ) ) {
							$meta  = wp_get_attachment_metadata( kj_image_id( $stap['foto'] ) );
							$breed = ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) ? $meta['width'] / $meta['height'] > 1.2 : false;
							kj_component( 'foto-vlak', array(
								'image'    => $stap['foto'],
								'etiket'   => true,
								'gram'     => '60',
								'inset'    => $breed ? array( 45.31, 48.2, 40.59, 37.6 ) : array( 45.83, 34.67, 33.33, 34.08 ),
								'class'    => 'stap-kaart__beeld rounded-groot',
								'vierkant' => ! $breed,
								'zoom'     => max( 1, (float) ( $stap['zoom'] ?? 1 ) ),
							) );
						} else {
							kj_component( 'image', array( 'image' => $stap['foto'], 'class' => 'stap-kaart__beeld rounded-groot', 'sizes' => '(min-width: 1024px) 30vw, 92vw', 'size' => 'mori-1000', 'zoom' => true ) );
						}
						?>
						<p class="stap-kaart__nummer text-eyebrow font-semibold uppercase"><?php echo esc_html( strtoupper( $label ) . ' ' . sprintf( '%02d', $i + 1 ) ); ?></p>
						<h3 class="font-serif text-h3"><?php echo esc_html( $stap['titel'] ); ?></h3>
						<p class="text-body text-steen"><?php echo esc_html( $stap['tekst'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
