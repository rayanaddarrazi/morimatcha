<?php
/**
 * Foto met tekst en knop (Figma: Shop > Waarom één matcha)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$foto    = get_sub_field( 'foto' );
$positie = get_sub_field( 'foto_positie' ) ?: 'links';
$eyebrow = get_sub_field( 'eyebrow' );
$titel   = get_sub_field( 'titel' );
$tekst   = get_sub_field( 'tekst' );
$knop    = kj_link_args( get_sub_field( 'knop' ), 'primair' );
$donker  = in_array( get_sub_field( 'achtergrond' ), array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'gerst' ) ); ?> foto-tekst foto-tekst--<?php echo esc_attr( $positie ); ?>">
	<div class="foto-tekst__foto" data-reveal>
		<?php kj_component( 'image', array( 'image' => $foto, 'class' => 'foto-tekst__beeld', 'sizes' => '(min-width: 1024px) 50vw, 100vw', 'size' => 'mori-1440' ) ); ?>
	</div>
	<div class="foto-tekst__tekst">
		<div class="foto-tekst__inhoud">
			<div data-reveal><?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?></div>
			<?php if ( $titel ) : ?><h2 class="font-serif text-h2" data-reveal style="--i: 1"><?php echo esc_html( $titel ); ?></h2><?php endif; ?>
			<?php if ( $tekst ) : ?><p class="text-body lg:text-body-l text-steen" data-reveal style="--i: 2"><?php echo esc_html( $tekst ); ?></p><?php endif; ?>
			<?php if ( $knop ) : ?><div data-reveal style="--i: 3"><?php kj_component( 'button', $knop ); ?></div><?php endif; ?>
		</div>
	</div>
</section>
