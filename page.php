<?php
/**
 * De pagina's: de pagebuilder (ACF flexible content). De kassa en Mijn account gebruiken de WooCommerce-templates.
 *
 * @package morimatcha
 */

$kassa = function_exists( 'kj_is_kassa' ) && kj_is_kassa();

if ( $kassa ) {
	get_header( 'checkout' );
} else {
	get_header();
}
?>

<main id="primary" class="site-main<?php echo $kassa ? ' site-main--kassa' : ''; ?><?php echo ( function_exists( 'is_account_page' ) && is_account_page() ) ? ' site-main--account' : ''; ?>">

	<?php
	while ( have_posts() ) :
		the_post();

		if ( $kassa || ( function_exists( 'is_account_page' ) && is_account_page() ) ) {
			the_content();
		} elseif ( function_exists( 'is_cart' ) && is_cart() ) {
			get_template_part( 'templates/components/winkelwagen-leeg' );
		} elseif ( have_rows( 'pagebuilder' ) ) {
			get_template_part( 'templates/pagebuilder/loop' );
		}

	endwhile;
	?>

</main>

<?php
if ( $kassa ) {
	get_footer( 'checkout' );
} else {
	get_footer();
}
