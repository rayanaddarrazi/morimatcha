<?php
/**
 * Productsplit: foto met etiket naast naam, smaakbadges, maten met prijs per gram (Figma: Home > Uitgelicht).
 * Naam, maten en prijzen komen uit WooCommerce.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$product_id = (int) get_sub_field( 'product' );
$product    = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
if ( ! $product ) {
	return;
}

$foto        = get_sub_field( 'foto' );
$foto_id     = kj_image_id( $foto ) ?: (int) $product->get_image_id();
$etiket      = (bool) get_sub_field( 'toon_etiket' );
$positie     = get_sub_field( 'foto_positie' ) ?: 'links';
$eyebrow     = get_sub_field( 'eyebrow' );
$tekst       = get_sub_field( 'tekst' ) ?: wp_strip_all_tags( $product->get_short_description() );
$tekst_mob   = get_sub_field( 'tekst_mobiel' );
$knoptekst   = get_sub_field( 'knoptekst' );
$tekstlink   = kj_link_args( get_sub_field( 'knop_tekstlink' ), 'tekstlink', array( 'icon' => false ) );
$smaken      = get_field( 'smaakbadges', $product_id );
$sizes       = kj_product_sizes( $product );
$goedkoopste = kj_cheapest_size_id( $sizes );
$etiket_gram = $sizes ? (string) (int) $sizes[ min( 1, count( $sizes ) - 1 ) ]['gram'] : '60';
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'wit' ) ); ?> productsplit productsplit--<?php echo esc_attr( $positie ); ?>">

	<div class="productsplit__foto" data-reveal>
		<?php
		kj_component( 'foto-vlak', array(
			'image'  => $foto_id,
			'etiket' => $etiket,
			'gram'   => $etiket_gram,
			'class'  => 'productsplit__vlak',
		) );
		?>
	</div>

	<div class="productsplit__tekst">
		<div class="productsplit__inhoud">
			<div data-reveal><?php kj_component( 'eyebrow', array( 'text' => $eyebrow ) ); ?></div>

			<div class="productsplit__kop" data-reveal style="--i: 1">
				<h1 class="font-serif text-h1"><?php echo esc_html( $product->get_name() ); ?></h1>
				<?php if ( ! empty( $smaken ) ) : ?>
					<ul class="productsplit__smaak">
						<?php foreach ( $smaken as $smaak ) : ?>
							<li><?php kj_component( 'badge', array( 'text' => $smaak['tekst'] ?? '', 'stijl' => 'lijn', 'punt' => true ) ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<?php if ( $tekst ) : ?><p class="text-body lg:text-body-l text-steen" data-reveal style="--i: 2"><?php echo kj_rt( $tekst, $tekst_mob ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>

			<?php if ( $sizes ) : ?>
				<ul class="productsplit__maten" data-reveal style="--i: 3">
					<?php foreach ( $sizes as $size ) : ?>
						<li class="productsplit__maat">
							<div class="productsplit__maat-naam">
								<span class="font-serif text-h4"><?php echo esc_html( $size['label'] ); ?></span>
								<span class="productsplit__koppen text-klein text-steen"><?php echo esc_html( sprintf( (string) kj_option( 'koppen_regel', '± %d koppen' ), $size['koppen'] ) ); ?></span>
								<?php if ( $size['id'] === $goedkoopste && count( $sizes ) > 1 ) : ?>
									<?php kj_component( 'badge', array( 'text' => (string) kj_option( 'badge_voordeligst', 'Voordeligst' ), 'stijl' => 'blad' ) ); ?>
								<?php endif; ?>
							</div>
							<div class="productsplit__prijs">
								<span class="text-meta text-matcha"><?php echo esc_html( sprintf( (string) kj_option( 'per_gram_regel', '€ %s per gram' ), $size['per_gram'] ) ); ?></span>
								<span class="text-prijs"><?php echo esc_html( kj_format_price( $size['price'] ) ); ?></span>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="productsplit__knoppen" data-reveal style="--i: 4">
				<?php
				kj_component( 'button', array(
					'label'   => $knoptekst,
					'url'     => get_permalink( $product_id ),
					'variant' => 'primair',
					'class'   => 'kj-button--vol lg:w-auto',
				) );
				if ( $tekstlink ) {
					kj_component( 'button', $tekstlink );
				}
				?>
			</div>
		</div>
	</div>

</section>
