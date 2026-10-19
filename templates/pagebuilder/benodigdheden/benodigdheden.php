<?php
/**
 * Benodigdheden: producten met korte uitleg (Figma: Bereiden > Wat je nodig hebt)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$titel   = get_sub_field( 'titel' );
$intro   = get_sub_field( 'intro' );
$knop    = kj_link_args( get_sub_field( 'knop' ), 'secundair', array( 'icon' => true ) );
$items   = get_sub_field( 'items' );
$donker  = in_array( get_sub_field( 'achtergrond' ), array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'rijstpapier' ) ); ?> benodigdheden">
	<div class="container benodigdheden__binnen">
		<div class="benodigdheden__kop" data-reveal>
			<div class="benodigdheden__kop-tekst">
				<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
				<h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2>
				<?php if ( $intro ) : ?><p class="text-body lg:text-body-l text-steen"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
			</div>
			<?php if ( $knop ) : ?><div class="benodigdheden__knop"><?php kj_component( 'button', $knop ); ?></div><?php endif; ?>
		</div>

		<?php if ( $items ) : ?>
			<ul class="benodigdheden__lijst">
				<?php foreach ( $items as $i => $item ) :
					$product = ! empty( $item['product'] ) ? wc_get_product( (int) $item['product'] ) : null;
					if ( ! $product ) {
						continue;
					}
					$sizes = $product->is_type( 'variable' ) ? kj_product_sizes( $product ) : array();
					$prijs = $sizes ? min( array_column( $sizes, 'price' ) ) : (float) $product->get_price();
					?>
					<li class="benodigd kaart" data-reveal style="--i: <?php echo (int) $i; ?>">
						<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" class="benodigd__link">
							<?php
							kj_component( 'foto-vlak', array(
								'image'    => (int) $product->get_image_id(),
								'etiket'   => ! empty( $item['toon_etiket'] ),
								'gram'     => '60',
								'class'    => 'benodigd__foto rounded-groot beeld--zoom',
								'vierkant' => true,
							) );
							?>
							<span class="benodigd__info">
								<span class="benodigd__naam">
									<span class="font-serif text-h4"><?php echo esc_html( $product->get_name() ); ?></span>
									<span class="text-klein text-steen"><?php echo esc_html( $item['uitleg'] ); ?></span>
								</span>
								<span class="text-label"><?php echo esc_html( trim( ( $item['prijs_voorvoegsel'] ?? '' ) . ' ' . kj_format_price( $prijs ) ) ); ?></span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
