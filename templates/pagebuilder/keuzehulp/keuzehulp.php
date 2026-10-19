<?php
/**
 * Keuzehulp: drie keuzes voor verschillende kopers (Figma: Shop > Keuzehulp)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$titel   = get_sub_field( 'titel' );
$keuzes  = get_sub_field( 'keuzes' );
$donker  = in_array( get_sub_field( 'achtergrond' ) ?: 'mos', array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'mos' ) ); ?> keuzehulp">
	<div class="container keuzehulp__binnen">
		<div class="keuzehulp__kop" data-reveal>
			<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
			<h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2>
		</div>
		<?php if ( $keuzes ) : ?>
			<div class="keuzehulp__keuzes">
				<?php foreach ( $keuzes as $i => $keuze ) :
					$knop = kj_link_args( $keuze['knop'] ?? null, 'contour', array( 'icon' => true ) );
					?>
					<article class="keuze" data-reveal style="--i: <?php echo (int) $i; ?>">
						<span class="keuze__icoon"><?php echo kj_svg( $keuze['icoon'] ?: 'lepel' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<h3 class="font-serif text-h3"><?php echo esc_html( $keuze['titel'] ); ?></h3>
						<p class="text-body"><?php echo esc_html( $keuze['tekst'] ); ?></p>
						<?php if ( $knop ) : ?><div class="keuze__knop"><?php kj_component( 'button', $knop ); ?></div><?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
