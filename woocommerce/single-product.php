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

	$shop_url  = wc_get_page_permalink( 'shop' );
	$shop_naam = get_the_title( wc_get_page_id( 'shop' ) );
	$terms     = get_the_terms( $product->get_id(), 'product_cat' );
	$term      = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
	?>

	<main id="primary" class="site-main">

		<nav class="kruimelpad container" aria-label="<?php esc_attr_e( 'Kruimelpad', 'morimatcha' ); ?>">
			<ol>
				<li><a href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html( $shop_naam ); ?></a></li>
				<?php if ( $term ) : ?>
					<li><a href="<?php echo esc_url( add_query_arg( 'categorie', $term->slug, $shop_url ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
				<?php endif; ?>
				<li aria-current="page"><?php echo esc_html( $product->get_name() ); ?></li>
			</ol>
		</nav>

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
