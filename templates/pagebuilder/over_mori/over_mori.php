<?php
/**
 * Foto met tekst en beloftes (Figma: Home > Over Mori)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$foto     = get_sub_field( 'foto' );
$positie  = get_sub_field( 'foto_positie' ) ?: 'links';
$eyebrow  = get_sub_field( 'eyebrow' );
$titel    = get_sub_field( 'titel' );
$tekst    = get_sub_field( 'tekst' );
$beloftes = get_sub_field( 'beloftes' );
$knop     = kj_link_args( get_sub_field( 'knop' ), 'tekstlink', array( 'icon' => true ) );
$donker   = in_array( get_sub_field( 'achtergrond' ), array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'rijstpapier' ) ); ?> over-mori over-mori--<?php echo esc_attr( $positie ); ?>">
	<div class="container over-mori__rij grid-mori">

		<div class="over-mori__foto" data-reveal>
			<?php kj_component( 'image', array( 'image' => $foto, 'class' => 'over-mori__beeld rounded-groot', 'sizes' => '(min-width: 1024px) 40vw, 92vw', 'size' => 'mori-1000', 'zoom' => true ) ); ?>
		</div>

		<div class="over-mori__tekst">
			<div class="over-mori__kop" data-reveal style="--i: 1">
				<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
				<?php if ( $titel ) : ?><h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2><?php endif; ?>
				<?php if ( $tekst ) : ?><p class="text-body lg:text-body-l text-steen"><?php echo esc_html( $tekst ); ?></p><?php endif; ?>
			</div>

			<?php if ( $beloftes ) : ?>
				<ol class="over-mori__beloftes" data-reveal style="--i: 2">
					<?php foreach ( $beloftes as $i => $belofte ) : ?>
						<li class="over-mori__belofte">
							<span class="over-mori__nummer text-eyebrow font-semibold uppercase"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
							<h3 class="over-mori__belofte-titel font-serif text-h4"><?php echo esc_html( $belofte['titel'] ); ?></h3>
							<p class="over-mori__uitleg text-klein text-steen"><?php echo esc_html( $belofte['uitleg'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>

			<?php if ( $knop ) : ?>
				<div data-reveal style="--i: 3"><?php kj_component( 'button', $knop ); ?></div>
			<?php endif; ?>
		</div>

	</div>
</section>
