<?php
/**
 * Productpagina (Figma: Productpagina). Foto's en aankoopkeuze, daarna de blokken (herkomst, smaak, bereiding, vragen)
 * die per product in ACF staan (veld "Blokken onder de aankoop").
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		continue;
	}

	?>

	<main id="primary" class="site-main">

		<?php get_template_part( 'templates/product/buybox', null, array( 'product' => $product ) ); ?>

		<?php
		$GLOBALS['kj_blocks_field'] = 'productblokken';
		$GLOBALS['kj_blocks_post']  = $product->get_id();
		get_template_part( 'templates/pagebuilder/loop' );
		unset( $GLOBALS['kj_blocks_field'], $GLOBALS['kj_blocks_post'] );
		?>

	</main>

<?php endwhile;

get_footer();
