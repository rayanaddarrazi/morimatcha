<?php
/**
 * Veelgemaakte fouten: probleem, oorzaak en oplossing (Figma: Bereiden > Veelgemaakte fouten)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$titel   = get_sub_field( 'titel' );
$intro   = get_sub_field( 'intro' );
$foto    = get_sub_field( 'foto' );
$l_oor   = (string) get_sub_field( 'label_oorzaak' ) ?: 'Oorzaak';
$l_op    = (string) get_sub_field( 'label_oplossing' ) ?: 'Zo los je het op';
$fouten  = get_sub_field( 'fouten' );
$donker  = in_array( get_sub_field( 'achtergrond' ), array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'rijstpapier' ) ); ?> fouten">
	<div class="container fouten__rij grid-mori">

		<div class="fouten__links" data-reveal>
			<div class="fouten__kop">
				<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
				<h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2>
				<?php if ( $intro ) : ?><p class="text-body lg:text-body-l text-steen"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
			</div>
			<?php if ( $foto ) { kj_component( 'image', array( 'image' => $foto, 'class' => 'fouten__foto rounded-groot', 'sizes' => '(min-width: 1024px) 40vw, 92vw', 'size' => 'mori-1000', 'zoom' => true ) ); } ?>
		</div>

		<?php if ( $fouten ) : ?>
			<ol class="fouten__lijst" data-reveal style="--i: 1">
				<?php foreach ( $fouten as $i => $fout ) : ?>
					<li class="fout">
						<div class="fout__kop">
							<span class="text-eyebrow font-semibold uppercase fout__nummer"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
							<h3 class="font-serif text-h3"><?php echo esc_html( $fout['probleem'] ); ?></h3>
						</div>
						<div class="fout__uitleg">
							<div>
								<p class="text-eyebrow font-semibold uppercase fout__label"><?php echo esc_html( $l_oor ); ?></p>
								<p class="text-body"><?php echo esc_html( $fout['oorzaak'] ); ?></p>
							</div>
							<div>
								<p class="text-eyebrow font-semibold uppercase fout__label fout__label--oplossing"><?php echo esc_html( $l_op ); ?></p>
								<p class="text-body"><?php echo esc_html( $fout['oplossing'] ); ?></p>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

	</div>
</section>
