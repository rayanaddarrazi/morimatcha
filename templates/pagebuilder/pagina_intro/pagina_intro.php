<?php
/**
 * Pagina-intro: eyebrow, grote titel en een korte tekst rechts (Figma: Shop > Intro)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$titel   = get_sub_field( 'titel' );
$tekst   = get_sub_field( 'tekst' );
$donker  = in_array( get_sub_field( 'achtergrond' ), array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'rijstpapier' ) ); ?> pagina-intro">
	<div class="container pagina-intro__rij">
		<div class="pagina-intro__kop" data-reveal>
			<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
			<h1 class="font-serif text-h1"><?php echo esc_html( $titel ); ?></h1>
		</div>
		<?php if ( $tekst ) : ?><p class="pagina-intro__tekst text-body lg:text-body-l text-steen" data-reveal style="--i: 1"><?php echo esc_html( $tekst ); ?></p><?php endif; ?>
	</div>
</section>
