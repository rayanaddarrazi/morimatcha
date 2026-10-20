<?php
/**
 * Shop (Figma: Shop). De pagina "Shop" wordt in het dashboard opgebouwd met blokken; hier worden ze getoond.
 * Categorie-overzichten sturen door naar de shop met een filter.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

if ( is_tax( 'product_cat' ) ) {
	$term = get_queried_object();
	wp_safe_redirect( add_query_arg( 'categorie', $term->slug, wc_get_page_permalink( 'shop' ) ), 301 );
	exit;
}

get_header();

if ( is_search() ) :
	$zoek      = trim( get_search_query( false ) );
	$producten = array_filter( array_map( 'wc_get_product', wp_list_pluck( $GLOBALS['wp_query']->posts, 'ID' ) ), fn( $p ) => $p && 'publish' === $p->get_status() );
	?>
	<main id="primary" class="site-main">
		<section class="bg-rijstpapier productgrid zoekresultaten">
			<div class="container">
				<div class="zoekresultaten__kop">
					<h1 class="font-serif text-h1"><?php echo esc_html( sprintf( (string) kj_option( 'zoek_titel', 'Zoekresultaten voor "%s"' ), $zoek ) ); ?></h1>
					<p class="text-body text-steen"><?php echo esc_html( sprintf( (string) kj_option( 'zoek_aantal', '%d producten' ), count( $producten ) ) ); ?></p>
				</div>
				<?php if ( $producten ) : ?>
					<div class="productgrid__lijst">
						<?php foreach ( array_values( $producten ) as $i => $product ) : ?>
							<div data-reveal style="--i: <?php echo (int) ( $i % 3 ); ?>"><?php kj_component( 'product-card', array( 'product_id' => $product->get_id() ) ); ?></div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="productgrid__leeg text-body text-steen"><?php echo esc_html( (string) kj_option( 'zoek_leeg', '' ) ); ?></p>
					<p><a class="text-label underline" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php echo esc_html( (string) kj_option( 'zoek_alles', 'Bekijk de hele shop' ) ); ?></a></p>
				<?php endif; ?>
			</div>
		</section>
	</main>
	<?php
	get_footer();
	return;
endif;

$shop_id = wc_get_page_id( 'shop' );
$GLOBALS['kj_blocks_field'] = 'pagebuilder';
$GLOBALS['kj_blocks_post']  = $shop_id;
?>

<main id="primary" class="site-main">
	<?php get_template_part( 'templates/pagebuilder/loop' ); ?>
</main>

<?php
unset( $GLOBALS['kj_blocks_field'], $GLOBALS['kj_blocks_post'] );
get_footer();
