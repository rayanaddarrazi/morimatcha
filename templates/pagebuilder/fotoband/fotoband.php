<?php
/**
 * Fotoband met tekst (Figma: Home > Ritueel). Desktop: tekst over de foto; mobiel: foto met de tekst eronder.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$foto     = get_sub_field( 'foto' );
$eyebrow  = get_sub_field( 'eyebrow' );
$titel    = get_sub_field( 'titel' );
$tekst    = get_sub_field( 'tekst' );
$tekst_m  = get_sub_field( 'tekst_mobiel' );
$uitsnede = get_sub_field( 'uitsnede_mobiel' ) ?: 'rechts';
$pos_klasse = array( 'links' => 'max-lg:object-left', 'midden' => '', 'rechts' => 'max-lg:object-[84%_50%]' )[ $uitsnede ] ?? '';
$knop     = kj_link_args( get_sub_field( 'knop' ), 'contour', array( 'icon' => true ) );
$parallax = get_sub_field( 'parallax' );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="fotoband sectie--mos">

	<div class="fotoband__foto"<?php echo $parallax ? ' data-parallax' : ''; ?>>
		<?php kj_component( 'image', array( 'image' => $foto, 'class' => 'fotoband__beeld', 'sizes' => '100vw', 'size' => 'mori-1920', 'position' => $pos_klasse ) ); ?>
		<div class="fotoband__verloop"></div>
	</div>

	<div class="fotoband__inhoud">
		<div class="container">
			<div class="fotoband__tekst" data-reveal>
				<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => 'licht' ) ); ?>
				<?php if ( $titel ) : ?><h2 class="font-serif text-h1 text-rijstpapier"><?php echo esc_html( $titel ); ?></h2><?php endif; ?>
				<?php if ( $tekst ) : ?><p class="text-body lg:text-body-l text-gerst"><?php echo kj_rt( $tekst, $tekst_m ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
				<?php if ( $knop ) : ?>
					<div class="fotoband__knop"><?php $knop['class'] = 'kj-button--vol lg:w-auto'; kj_component( 'button', $knop ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>

</section>
