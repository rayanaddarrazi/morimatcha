<?php
/**
 * Starterset (bundel): foto, onderdelen met prijs, setprijs en besparing (Figma: Home > Starterset).
 * Prijzen komen uit WooCommerce; de besparing wordt berekend.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$product_id = (int) get_sub_field( 'product' );
$product    = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
if ( ! $product ) {
	return;
}

$foto       = get_sub_field( 'foto' );
$foto_id    = kj_image_id( $foto ) ?: (int) $product->get_image_id();
$eyebrow    = get_sub_field( 'eyebrow' );
$titel      = get_sub_field( 'titel' ) ?: $product->get_name();
$intro      = get_sub_field( 'intro' );
$intro_m    = get_sub_field( 'intro_mobiel' );
$onderdelen = get_sub_field( 'onderdelen' );
$knoptekst  = get_sub_field( 'knoptekst' );
$tekstlink  = kj_link_args( get_sub_field( 'knop_tekstlink' ), 'tekstlink', array( 'icon' => false ) );

$som = 0;
$rijen = array();
foreach ( (array) $onderdelen as $onderdeel ) {
	$p = ! empty( $onderdeel['product'] ) ? wc_get_product( (int) $onderdeel['product'] ) : null;
	if ( ! $p ) {
		continue;
	}
	$prijs = (float) $p->get_price();
	if ( $p->is_type( 'variable' ) ) {
		$sizes = kj_product_sizes( $p );
		$prijs = $sizes ? $sizes[0]['price'] : $prijs;
	}
	$som    += $prijs;
	$rijen[] = array( 'naam' => $p->get_name(), 'detail' => $onderdeel['detail'] ?? '', 'prijs' => $prijs );
}

$setprijs = (float) $product->get_price();
$was      = $som > $setprijs ? $som : (float) $product->get_regular_price();
$bespaar  = $was > $setprijs ? $was - $setprijs : 0;
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'gerst' ) ); ?> starterset<?php echo get_sub_field( 'compact' ) ? ' starterset--compact' : ''; ?>">
	<div class="container starterset__rij grid-mori">

		<div class="starterset__foto" data-reveal>
			<?php kj_component( 'image', array( 'image' => $foto_id, 'class' => 'starterset__beeld rounded-groot', 'sizes' => '(min-width: 1024px) 48vw, 92vw', 'size' => 'mori-1000', 'zoom' => true ) ); ?>
		</div>

		<div class="starterset__tekst">
			<div class="starterset__kop" data-reveal style="--i: 1">
				<?php kj_component( 'eyebrow', array( 'text' => $eyebrow ) ); ?>
				<h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2>
				<?php if ( $intro ) : ?><p class="text-body lg:text-body-l text-steen"><?php echo kj_rt( $intro, $intro_m ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
			</div>

			<?php if ( $rijen ) : ?>
				<ul class="starterset__set" data-reveal style="--i: 2">
					<?php foreach ( $rijen as $rij ) : ?>
						<li class="starterset__item">
							<div>
								<p class="font-serif text-h4"><?php echo esc_html( $rij['naam'] ); ?></p>
								<p class="text-klein text-steen"><?php echo esc_html( $rij['detail'] ); ?></p>
							</div>
							<p class="text-klein text-steen"><?php echo esc_html( kj_format_price( $rij['prijs'] ) ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="starterset__prijs" data-reveal style="--i: 3">
				<span class="font-serif text-h3"><?php echo esc_html( kj_format_price( $setprijs ) ); ?></span>
				<?php if ( $bespaar > 0 ) : ?>
					<span class="starterset__was text-body text-steen"><?php echo esc_html( kj_format_price( $was ) ); ?></span>
					<?php kj_component( 'badge', array( 'text' => sprintf( (string) kj_option( 'bespaar_regel', 'Je bespaart %s' ), kj_format_price( $bespaar ) ), 'stijl' => 'blad' ) ); ?>
				<?php endif; ?>
			</div>

			<div class="starterset__knoppen" data-reveal style="--i: 4">
				<?php
				$knop_actie = get_sub_field( 'knop_actie' ) ?: 'product';
				kj_component( 'button', array(
					'label'   => $knoptekst,
					'url'     => get_permalink( $product_id ),
					'variant' => 'primair',
					'class'   => 'kj-button--vol lg:w-auto',
					'tag'     => 'winkelwagen' === $knop_actie ? 'button' : 'a',
					'attrs'   => 'winkelwagen' === $knop_actie ? array( 'data-snel-toevoegen' => (string) $product_id ) : array(),
				) );
				if ( $tekstlink ) {
					$tekstlink['class'] = 'hidden lg:inline-flex';
					kj_component( 'button', $tekstlink );
				}
				?>
			</div>
		</div>

	</div>
</section>
