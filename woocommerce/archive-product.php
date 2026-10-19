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
