<?php
/**
 * Lege winkelwagen (pagina Winkelwagen): de melding en een knop terug naar de shop.
 * Is de winkelwagen gevuld, dan stuurt inc/kassa.php door naar de shop met het paneel open.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;
?>

<section class="<?php echo esc_attr( kj_section_bg( 'rijstpapier' ) ); ?> winkelwagen-leeg">
	<div class="container flex flex-col items-start gap-6 py-20 lg:py-32">
		<h1 class="font-serif text-h1"><?php echo esc_html( get_the_title() ); ?></h1>
		<p class="text-body lg:text-body-l text-steen"><?php echo esc_html( (string) kj_option( 'cart_leeg', 'Je winkelwagen is nog leeg.' ) ); ?></p>
		<?php
		kj_component( 'button', array(
			'label'   => (string) kj_option( 'cart_leeg_knop', 'Naar de shop' ),
			'url'     => wc_get_page_permalink( 'shop' ),
			'variant' => 'primair',
		) );
		?>
	</div>
</section>
