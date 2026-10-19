<?php
/**
 * Foto met kenmerken (Figma: Productpagina > Herkomst "Wat er in het blik zit")
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$foto      = get_sub_field( 'foto' );
$positie   = get_sub_field( 'foto_positie' ) ?: 'links';
$eyebrow   = get_sub_field( 'eyebrow' );
$titel     = get_sub_field( 'titel' );
$intro     = get_sub_field( 'intro' );
$kenmerken = get_sub_field( 'kenmerken' );
$donker    = in_array( get_sub_field( 'achtergrond' ) ?: 'mos', array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'mos' ) ); ?> foto-kenmerken foto-kenmerken--<?php echo esc_attr( $positie ); ?>">
	<div class="foto-kenmerken__foto" data-reveal>
		<?php kj_component( 'image', array( 'image' => $foto, 'class' => 'foto-kenmerken__beeld', 'sizes' => '(min-width: 1024px) 50vw, 100vw', 'size' => 'mori-1440' ) ); ?>
	</div>
	<div class="foto-kenmerken__tekst">
		<div class="foto-kenmerken__inhoud">
			<div class="foto-kenmerken__kop" data-reveal>
				<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
				<?php if ( $titel ) : ?><h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2><?php endif; ?>
				<?php if ( $intro ) : ?><p class="text-body lg:text-body-l foto-kenmerken__intro"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
			</div>
			<?php if ( $kenmerken ) : ?>
				<ul class="foto-kenmerken__lijst" data-reveal style="--i: 1">
					<?php foreach ( $kenmerken as $kenmerk ) : ?>
						<li>
							<h3 class="font-serif text-h4"><?php echo esc_html( $kenmerk['titel'] ); ?></h3>
							<p class="text-klein foto-kenmerken__uitleg"><?php echo esc_html( $kenmerk['uitleg'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
