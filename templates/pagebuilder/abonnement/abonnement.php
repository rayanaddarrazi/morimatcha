<?php
/**
 * Abonnement: uitleg in drie stappen met foto (Figma: Home > Abonnement)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$foto      = get_sub_field( 'foto' );
$etiket    = (bool) get_sub_field( 'toon_etiket' );
$positie   = get_sub_field( 'foto_positie' ) ?: 'rechts';
$eyebrow   = get_sub_field( 'eyebrow' );
$titel     = get_sub_field( 'titel' );
$intro     = get_sub_field( 'intro' );
$stappen   = get_sub_field( 'stappen' );
$knop_1    = kj_link_args( get_sub_field( 'knop_primair' ), 'licht' );
$knop_2    = kj_link_args( get_sub_field( 'knop_secundair' ), 'contour', array( 'icon' => false ) );
$donker    = in_array( get_sub_field( 'achtergrond' ) ?: 'mos', array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'mos' ) ); ?> abonnement abonnement--<?php echo esc_attr( $positie ); ?>">

	<div class="abonnement__tekst">
		<div class="abonnement__inhoud">
			<div class="abonnement__kop" data-reveal>
				<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
				<?php if ( $titel ) : ?><h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2><?php endif; ?>
				<?php if ( $intro ) : ?><p class="text-body lg:text-body-l abonnement__intro"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
			</div>

			<?php if ( $stappen ) : ?>
				<ol class="abonnement__stappen" data-reveal style="--i: 1">
					<?php foreach ( $stappen as $i => $stap ) : ?>
						<li class="abonnement__stap">
							<span class="text-eyebrow font-semibold uppercase abonnement__nummer"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
							<div>
								<h3 class="font-serif text-h4"><?php echo esc_html( $stap['titel'] ); ?></h3>
								<p class="text-klein abonnement__uitleg"><?php echo kj_rt( $stap['uitleg'], $stap['uitleg_mobiel'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>

			<?php if ( $knop_1 || $knop_2 ) : ?>
				<div class="abonnement__knoppen" data-reveal style="--i: 2">
					<?php
					if ( $knop_1 ) { $knop_1['class'] = 'kj-button--vol lg:w-auto'; kj_component( 'button', $knop_1 ); }
					if ( $knop_2 ) { $knop_2['class'] = 'kj-button--vol lg:w-auto'; kj_component( 'button', $knop_2 ); }
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="abonnement__foto">
		<?php
		kj_component( 'foto-vlak', array(
			'image'  => $foto,
			'etiket' => $etiket,
			'gram'   => '60',
			'inset'  => array( 45.31, 48.2, 40.59, 37.6 ),
			'class'  => 'abonnement__vlak',
		) );
		?>
	</div>

</section>
