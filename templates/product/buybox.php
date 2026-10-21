<?php
/**
 * Productgalerij en aankoopkeuze: maten met prijs per gram, eenmalig of abonnement (niet voorgeselecteerd), aantal.
 *
 * @param array $args { @type WC_Product $product }
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;
if ( ! $product ) {
	return;
}

$id          = $product->get_id();
$badges      = kj_product_badges( $id );
$sizes       = kj_product_sizes( $product );
$is_variable = $product->is_type( 'variable' ) && $sizes;
$abonnement  = (bool) get_field( 'abonnement_mogelijk', $id );
$goedkoopste = kj_cheapest_size_id( $sizes );
$default_idx = $is_variable ? min( 1, count( $sizes ) - 1 ) : 0;
$selected    = $sizes[ $default_idx ] ?? null;
$korting     = (float) kj_option( 'abonnement_korting', 10 );

$galerij_ids = array_slice( $product->get_gallery_image_ids(), 0, 2 );
$hoofd_id    = (int) $product->get_image_id();

$zekerheden = get_field( 'zekerheden', $id );
if ( empty( $zekerheden ) ) {
	$zekerheden = get_field( 'zekerheden', 'option' );
}

$hulp = get_field( 'hulp_maat_link', $id );

// Gegevens voor product.js
$config = array(
	'productId'  => $id,
	'variable'   => $is_variable,
	'abonnement' => $abonnement,
	'etiket'     => (string) kj_option( 'etiket_regel', 'MATCHA  {gram} G' ),
	'sizes'      => array_map( function ( $s ) {
		$p = kj_size_prices( $s );
		return array(
			'id'       => $s['id'],
			'label'    => $s['label'],
			'gram'     => (int) $s['gram'],
			'perGram'  => kj_format_price( $s['gram'] ? $s['price'] / $s['gram'] : 0 ),
			'eenmalig' => kj_format_price( $p['eenmalig'] ),
			'maand'    => kj_format_price( $p['maand'] ),
		);
	}, $sizes ),
	'tekst'      => array(
		'gekozen'      => (string) kj_option( 'maat_gekozen', '%s gekozen' ),
		'perGram'      => (string) kj_option( 'per_gram_regel', '€ %s per gram' ),
		'prijsEenmalig' => (string) kj_option( 'aankoop_prijs_eenmalig', '%1$s, %2$s' ),
		'prijsMaand'   => (string) kj_option( 'aankoop_prijs_maand', 'per maand, %1$s. Los %2$s' ),
		'knop'         => (string) kj_option( 'aankoop_knop', 'In winkelwagen' ),
		'knopAbo'      => (string) kj_option( 'aankoop_knop_abo', 'Start abonnement, %s per maand' ),
		'hint'         => (string) kj_option( 'aankoop_hint' ),
		'hintAbo'      => (string) kj_option( 'aankoop_hint_abo' ),
		'toelichting'  => (string) kj_option( 'aankoop_abo_toelichting', '' ),
	),
);
?>

<section class="product container">
	<div class="product__rij grid-mori">

		<div class="product__galerij">
			<?php
			// Het 3D-blik is gemaakt van deze ene productfoto. Is de foto vervangen, dan blijft alleen de foto staan.
			$foto_sleutel = $hoofd_id ? get_post_meta( $hoofd_id, '_kj_seed_key', true ) : '';
			$blik3d       = $is_variable && kj_option( 'blik3d_aan', true ) && 'product-mori-matcha' === $foto_sleutel && file_exists( get_template_directory() . '/assets/images/blik-textuur.webp' ) && file_exists( get_template_directory() . '/assets/images/blik-achtergrond.webp' );
			$foto_url     = $blik3d ? wp_get_attachment_image_url( $hoofd_id, 'mori-1440' ) : '';
			?>
			<div class="product__hoofd"<?php echo $blik3d ? ' data-blik3d data-module="' . esc_url( get_template_directory_uri() . '/assets/js/blik3d.js?v=' . filemtime( get_template_directory() . '/assets/js/blik3d.js' ) ) . '" data-textuur="' . esc_url( get_template_directory_uri() . '/assets/images/blik-textuur.webp?v=' . filemtime( get_template_directory() . '/assets/images/blik-textuur.webp' ) ) . '" data-glans="' . esc_url( get_template_directory_uri() . '/assets/images/blik-glans.webp?v=' . filemtime( get_template_directory() . '/assets/images/blik-glans.webp' ) ) . '" data-teken="' . esc_attr( (string) kj_option( 'etiket_teken', '森' ) ) . '" data-naam="' . esc_attr( (string) kj_option( 'etiket_naam', 'MORI' ) ) . '" data-etiket="' . esc_attr( wp_json_encode( array(
				'achterKop'   => (string) kj_option( 'etiket_achter_kop', '' ),
				'achterRegels' => array_values( array_filter( array_map( fn( $r ) => (string) ( $r['tekst'] ?? '' ), (array) kj_option( 'etiket_achter_regels', array() ) ) ) ),
				'achterKop2'  => (string) kj_option( 'etiket_achter_kop2', '' ),
				'achterRegels2' => array_values( array_filter( array_map( fn( $r ) => (string) ( $r['tekst'] ?? '' ), (array) kj_option( 'etiket_achter_regels2', array() ) ) ) ),
			) ) ) . '"' : ''; ?>>
			<?php
			kj_component( 'foto-vlak', array(
				'image'  => $hoofd_id,
				'etiket' => $is_variable,
				'gram'   => $selected ? (string) (int) $selected['gram'] : '60',
				'class'  => 'product__hoofdbeeld',
				'eager'  => true,
				'vierkant' => true,
			) );
			?>
			<?php if ( $blik3d ) : ?>
				<div class="blik3d" data-blik3d-stage style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/blik-achtergrond.webp?v=' . filemtime( get_template_directory() . '/assets/images/blik-achtergrond.webp' ) ); ?>')" aria-hidden="true"></div>
			<?php endif; ?>
			</div>
			<?php if ( $galerij_ids ) : ?>
				<div class="product__beelden">
					<?php foreach ( $galerij_ids as $gid ) : ?>
						<?php kj_component( 'image', array( 'image' => $gid, 'class' => 'product__foto rounded-groot', 'sizes' => '(min-width: 1024px) 24vw, 46vw', 'size' => 'mori-720', 'zoom' => true ) ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<form class="product__info buybox" method="post" enctype="multipart/form-data" data-buybox data-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>" action="<?php echo esc_url( get_permalink( $id ) ); ?>">

			<div class="buybox__kop">
				<?php if ( $badges['blad'] || $badges['smaken'] ) : ?>
					<ul class="buybox__labels">
						<?php if ( $badges['blad'] ) : ?><li><?php kj_component( 'badge', array( 'text' => $badges['blad'], 'stijl' => 'blad' ) ); ?></li><?php endif; ?>
						<?php foreach ( $badges['smaken'] as $smaak ) : ?><li><?php kj_component( 'badge', array( 'text' => $smaak, 'stijl' => 'lijn', 'punt' => true ) ); ?></li><?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<h1 class="font-serif text-h1"><?php echo esc_html( $product->get_name() ); ?></h1>
				<?php if ( $product->get_short_description() ) : ?>
					<p class="text-body lg:text-body-l text-steen"><?php echo esc_html( wp_strip_all_tags( $product->get_short_description() ) ); ?></p>
				<?php endif; ?>
				<p class="buybox__prijs">
					<span class="font-serif text-h3" data-prijs><?php echo esc_html( kj_format_price( $selected ? $selected['price'] : (float) $product->get_price() ) ); ?></span>
					<?php if ( $selected && $is_variable ) : ?>
						<span class="text-klein text-steen" data-prijs-regel><?php echo esc_html( sprintf( (string) kj_option( 'aankoop_prijs_eenmalig', '%1$s, %2$s' ), $selected['label'], sprintf( (string) kj_option( 'per_gram_regel', '€ %s per gram' ), $selected['per_gram'] ) ) ); ?></span>
					<?php endif; ?>
				</p>
			</div>

			<?php if ( $is_variable ) : ?>
				<fieldset class="maatkiezer">
					<legend class="screen-reader-text"><?php echo esc_html( (string) kj_option( 'maat_label', 'Maat' ) ); ?></legend>
					<div class="maatkiezer__kop">
						<p class="maatkiezer__titel"><span class="text-label"><?php echo esc_html( (string) kj_option( 'maat_label', 'Maat' ) ); ?></span> <span class="text-klein text-steen" data-maat-gekozen><?php echo esc_html( sprintf( (string) kj_option( 'maat_gekozen', '%s gekozen' ), $selected['label'] ) ); ?></span></p>
						<?php if ( is_array( $hulp ) && ! empty( $hulp['title'] ) ) : ?>
							<a class="maatkiezer__hulp text-klein text-steen" href="<?php echo esc_url( $hulp['url'] ); ?>"><?php echo kj_svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $hulp['title'] ); ?></span></a>
						<?php endif; ?>
					</div>
					<div class="maatkiezer__maten">
						<?php foreach ( $sizes as $i => $size ) :
							$aantal  = count( $sizes );
							$rang    = $aantal > 1 ? $i / ( $aantal - 1 ) : 0.5;
							$maat    = $rang < 0.34 ? 'klein' : ( $rang > 0.66 ? 'groot' : 'middel' );
							$blik_h  = array( 'klein' => 70, 'middel' => 88, 'groot' => 100 )[ $maat ];
							$actief  = $i === $default_idx;
							$op      = ! $size['in_stock'];
							?>
							<label class="maatoptie<?php echo $actief ? ' is-geselecteerd' : ''; ?><?php echo $op ? ' is-uitverkocht' : ''; ?>">
								<input type="radio" name="variation_id" value="<?php echo (int) $size['id']; ?>" data-maat="<?php echo (int) $size['id']; ?>"<?php echo $actief ? ' checked' : ''; ?><?php echo $op ? ' disabled' : ''; ?> required>
								<span class="maatoptie__beeld">
									<?php kj_component( 'blik', array( 'maat' => $maat, 'hoogte' => $blik_h, 'gram' => (int) $size['gram'] ) ); ?>
									<span class="maatoptie__radio" aria-hidden="true"><?php echo kj_svg( 'vinkje' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<?php if ( $op ) : ?>
										<span class="maatoptie__badge"><?php kj_component( 'badge', array( 'text' => (string) kj_option( 'badge_uitverkocht', 'Uitverkocht' ), 'stijl' => 'lijn' ) ); ?></span>
									<?php elseif ( $size['id'] === $goedkoopste && count( $sizes ) > 1 ) : ?>
										<span class="maatoptie__badge"><?php kj_component( 'badge', array( 'text' => (string) kj_option( 'badge_voordeligst', 'Voordeligst' ), 'stijl' => 'blad' ) ); ?></span>
									<?php endif; ?>
								</span>
								<span class="maatoptie__tekst">
									<span class="maatoptie__maat"><span class="font-serif text-h3"><?php echo esc_html( (string) (int) $size['gram'] ); ?></span> <span class="text-klein text-steen"><?php echo esc_html( preg_replace( '/^[0-9.,\s]+/', '', $size['label'] ) ?: 'gram' ); ?></span></span>
									<span class="text-klein text-steen"><?php echo esc_html( sprintf( (string) kj_option( 'koppen_regel', '± %d koppen' ), $size['koppen'] ) ); ?></span>
								</span>
								<span class="maatoptie__prijs">
									<span class="text-prijs"><?php echo esc_html( kj_format_price( $size['price'] ) ); ?></span>
									<span class="text-meta"><?php echo $op ? esc_html( (string) kj_option( 'uitverkocht_regel', 'Tijdelijk op' ) ) : esc_html( sprintf( (string) kj_option( 'per_gram_regel', '€ %s per gram' ), $size['per_gram'] ) ); ?></span>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
					<?php if ( get_field( 'uitleg_maten', $id ) ) : ?>
						<p class="maatkiezer__uitleg text-klein"><?php echo kj_svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( (string) get_field( 'uitleg_maten', $id ) ); ?></span></p>
					<?php endif; ?>
				</fieldset>
			<?php endif; ?>
			<input type="hidden" name="add-to-cart" value="<?php echo (int) $id; ?>">

			<?php if ( $abonnement && $selected ) :
				$prijzen = kj_size_prices( $selected );
				?>
				<fieldset class="aankoopkeuze">
					<legend class="screen-reader-text"><?php esc_html_e( 'Aankoopkeuze', 'morimatcha' ); ?></legend>

					<label class="aankoopkeuze__optie">
						<input type="radio" name="mori_levering" value="eenmalig" data-levering="eenmalig">
						<span class="aankoopkeuze__radio" aria-hidden="true"></span>
						<span class="aankoopkeuze__inhoud">
							<span class="aankoopkeuze__kop">
								<span class="font-serif text-h4"><?php echo esc_html( (string) kj_option( 'aankoop_eenmalig_titel', 'Eenmalig' ) ); ?></span>
								<span class="text-prijs" data-prijs-eenmalig><?php echo esc_html( kj_format_price( $prijzen['eenmalig'] ) ); ?></span>
							</span>
							<span class="aankoopkeuze__onder text-klein text-steen">
								<span><?php echo esc_html( (string) kj_option( 'aankoop_eenmalig_uitleg' ) ); ?></span>
								<span class="text-meta"><?php echo esc_html( (string) kj_option( 'aankoop_eenmalig_eenheid' ) ); ?></span>
							</span>
						</span>
					</label>

					<label class="aankoopkeuze__optie aankoopkeuze__optie--abonnement">
						<input type="radio" name="mori_levering" value="maand" data-levering="maand">
						<span class="aankoopkeuze__radio" aria-hidden="true"></span>
						<span class="aankoopkeuze__inhoud">
							<span class="aankoopkeuze__kop">
								<span class="aankoopkeuze__titel">
									<span class="font-serif text-h4"><?php echo esc_html( (string) kj_option( 'aankoop_abo_titel', 'Elke maand een blik' ) ); ?></span>
									<?php kj_component( 'badge', array( 'text' => sprintf( (string) kj_option( 'aankoop_abo_badge', 'Bespaar %d%%' ), $korting ), 'stijl' => 'mos' ) ); ?>
								</span>
								<span class="aankoopkeuze__prijzen">
									<span class="aankoopkeuze__oud text-klein text-steen" data-prijs-oud><?php echo esc_html( kj_format_price( $prijzen['eenmalig'] ) ); ?></span>
									<span class="text-prijs" data-prijs-maand><?php echo esc_html( kj_format_price( $prijzen['maand'] ) ); ?></span>
								</span>
							</span>
							<span class="aankoopkeuze__onder text-klein text-steen">
								<span><?php echo esc_html( (string) kj_option( 'aankoop_abo_uitleg' ) ); ?></span>
								<span class="text-meta"><?php echo esc_html( (string) kj_option( 'aankoop_abo_eenheid' ) ); ?></span>
							</span>
							<span class="aankoopkeuze__details">
								<span class="aankoopkeuze__details-binnen">
									<?php foreach ( (array) get_field( 'aankoop_abo_voordelen', 'option' ) as $voordeel ) : ?>
										<span class="aankoopkeuze__voordeel"><span class="aankoopkeuze__vink"><?php echo kj_svg( 'vinkje' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo esc_html( $voordeel['tekst'] ); ?></span>
									<?php endforeach; ?>
									<span class="aankoopkeuze__toelichting text-klein"><?php echo kj_svg( 'herhalen' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span data-abo-toelichting><?php echo esc_html( sprintf( (string) kj_option( 'aankoop_abo_toelichting' ), str_replace( '€ ', '', kj_format_price( $prijzen['maand'] ) ) ) ); ?></span></span>
									<?php $opzeg = get_field( 'aankoop_abo_opzeglink', 'option' ); if ( is_array( $opzeg ) && ! empty( $opzeg['title'] ) ) : ?>
										<a class="aankoopkeuze__link text-klein" href="<?php echo esc_url( $opzeg['url'] ); ?>"><?php echo esc_html( $opzeg['title'] ); ?></a>
									<?php endif; ?>
								</span>
							</span>
						</span>
					</label>
				</fieldset>
			<?php endif; ?>

			<div class="buybox__kopen">
				<div class="buybox__rij">
					<div class="aantal aantal--groot" role="group" aria-label="<?php esc_attr_e( 'Aantal', 'morimatcha' ); ?>">
						<button type="button" data-qty="-1" aria-label="<?php esc_attr_e( 'Minder', 'morimatcha' ); ?>" disabled><?php echo kj_svg( 'min' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
						<input type="number" class="aantal__waarde" name="quantity" value="1" min="1" max="99" inputmode="numeric" aria-label="<?php esc_attr_e( 'Aantal', 'morimatcha' ); ?>" data-aantal>
						<button type="button" data-qty="1" aria-label="<?php esc_attr_e( 'Meer', 'morimatcha' ); ?>"><?php echo kj_svg( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					</div>
					<?php
					$wacht = $abonnement; // eerst kiezen tussen eenmalig en abonnement
					kj_component( 'button', array(
						'label'    => (string) kj_option( 'aankoop_knop', 'In winkelwagen' ),
						'variant'  => 'primair',
						'tag'      => 'button',
						'class'    => 'buybox__knop',
						'disabled' => $wacht,
						'attrs'    => array( 'type' => 'submit', 'data-toevoegen' => '1' ),
					) );
					?>
				</div>
				<p class="buybox__hint text-klein text-steen" data-hint<?php echo $wacht ? '' : ' hidden'; ?>>
					<?php echo kj_svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span data-hint-tekst><?php echo esc_html( (string) kj_option( 'aankoop_hint' ) ); ?></span>
				</p>
				<p class="buybox__melding text-klein" role="alert" data-melding hidden></p>
			</div>

			<?php if ( $zekerheden ) : ?>
				<ul class="buybox__zekerheden">
					<?php foreach ( $zekerheden as $regel ) : ?>
						<li><?php echo kj_svg( $regel['icoon'] ?? 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="text-body"><?php echo esc_html( $regel['tekst'] ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</form>
	</div>
</section>
