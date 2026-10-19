<?php
/**
 * Producten met filters (Figma: Shop > Filters en Producten). Producten komen uit WooCommerce.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}

$label_alles = (string) get_sub_field( 'label_alles' ) ?: 'Alles';
$label_aantal = (string) get_sub_field( 'label_aantal' ) ?: '%d producten';
$label_sorteer = (string) get_sub_field( 'label_sorteren' ) ?: 'Sorteer op';
$sorteringen = get_sub_field( 'sorteringen' );
$leeg        = (string) get_sub_field( 'leeg' );

$actief = isset( $_GET['categorie'] ) ? sanitize_title( wp_unslash( $_GET['categorie'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$orderby = isset( $_GET['sorteer'] ) ? sanitize_key( wp_unslash( $_GET['sorteer'] ) ) : 'menu_order'; // phpcs:ignore WordPress.Security.NonceVerification

$cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'orderby' => 'menu_order' ) );
$cats = is_wp_error( $cats ) ? array() : $cats;
usort( $cats, fn( $a, $b ) => $a->term_id <=> $b->term_id );

$args = array( 'status' => 'publish', 'limit' => -1, 'visibility' => 'catalog' );
switch ( $orderby ) {
	case 'price':
		$args['orderby'] = 'price';
		$args['order']   = 'ASC';
		break;
	case 'price-desc':
		$args['orderby'] = 'price';
		$args['order']   = 'DESC';
		break;
	case 'date':
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';
		break;
	default:
		$args['orderby'] = 'menu_order';
		$args['order']   = 'ASC';
}
if ( $actief ) {
	$args['category'] = array( $actief );
}
$producten = wc_get_products( $args );
$totaal    = wp_count_posts( 'product' )->publish;
$shop_url  = wc_get_page_permalink( 'shop' );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'rijstpapier' ) ); ?> productgrid">
	<div class="container">

		<div class="productgrid__filters">
			<ul class="productgrid__categorieen">
				<li><a href="<?php echo esc_url( $shop_url ); ?>" class="filterpil<?php echo '' === $actief ? ' is-actief' : ''; ?>"<?php echo '' === $actief ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $label_alles . ' (' . (int) $totaal . ')' ); ?></a></li>
				<?php foreach ( $cats as $cat ) : ?>
					<li><a href="<?php echo esc_url( add_query_arg( 'categorie', $cat->slug, $shop_url ) ); ?>" class="filterpil<?php echo $actief === $cat->slug ? ' is-actief' : ''; ?>"<?php echo $actief === $cat->slug ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $cat->name . ' (' . (int) $cat->count . ')' ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<form class="productgrid__sorteren text-klein" method="get" action="<?php echo esc_url( $shop_url ); ?>">
				<span class="text-steen"><?php echo esc_html( sprintf( $label_aantal, count( $producten ) ) ); ?></span>
				<span class="productgrid__punt" aria-hidden="true">&middot;</span>
				<?php if ( $actief ) : ?><input type="hidden" name="categorie" value="<?php echo esc_attr( $actief ); ?>"><?php endif; ?>
				<?php if ( $sorteringen ) : ?>
					<label class="text-steen" for="sorteer"><?php echo esc_html( $label_sorteer ); ?></label>
					<span class="productgrid__select">
						<select id="sorteer" name="sorteer" class="text-label" data-auto-submit>
							<?php foreach ( $sorteringen as $optie ) : ?>
								<option value="<?php echo esc_attr( $optie['sleutel'] ); ?>"<?php selected( $orderby, $optie['sleutel'] ); ?>><?php echo esc_html( $optie['naam'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo kj_svg( 'chevron-omlaag' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</span>
					<noscript><button type="submit" class="text-label">OK</button></noscript>
				<?php endif; ?>
			</form>
		</div>

		<?php if ( $producten ) : ?>
			<div class="productgrid__lijst">
				<?php foreach ( $producten as $i => $product ) : ?>
					<div data-reveal style="--i: <?php echo (int) ( $i % 3 ); ?>"><?php kj_component( 'product-card', array( 'product_id' => $product->get_id() ) ); ?></div>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $leeg ) : ?>
			<p class="productgrid__leeg text-body text-steen"><?php echo esc_html( $leeg ); ?></p>
		<?php endif; ?>

	</div>
</section>
