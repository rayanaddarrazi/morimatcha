<?php
/**
 * Recepten: drie kaarten (Figma: Home > Drie manieren). Op mobiel een zijwaartse carrousel.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow  = get_sub_field( 'eyebrow' );
$titel    = get_sub_field( 'titel' );
$intro    = get_sub_field( 'intro' );
$intro_m  = get_sub_field( 'intro_mobiel' );
$knop     = kj_link_args( get_sub_field( 'knop' ), 'tekstlink', array( 'icon' => true ) );
$recepten = get_sub_field( 'recepten' );
$donker   = in_array( get_sub_field( 'achtergrond' ), array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'rijstpapier' ) ); ?> recepten" style="--foto-h: <?php echo (int) ( get_sub_field( 'foto_hoogte' ) ?: 500 ); ?>px">
	<div class="container recepten__binnen">

		<div class="recepten__kop" data-reveal>
			<div class="recepten__kop-tekst">
				<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
				<?php if ( $titel ) : ?><h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2><?php endif; ?>
				<?php if ( $intro ) : ?><p class="text-body lg:text-body-l text-steen"><?php echo kj_rt( $intro, $intro_m ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
			</div>
			<?php if ( $knop ) : ?><div class="recepten__knop"><?php kj_component( 'button', $knop ); ?></div><?php endif; ?>
		</div>

		<?php if ( $recepten ) : ?>
			<div class="recepten__lijst" data-carrousel>
				<?php foreach ( $recepten as $i => $recept ) :
					$link = $recept['link'] ?? null;
					$url  = is_array( $link ) ? $link['url'] : '';
					?>
					<article class="recept kaart" data-reveal style="--i: <?php echo (int) $i; ?>">
						<?php kj_component( 'image', array( 'image' => $recept['foto'], 'class' => 'recept__foto rounded-groot', 'sizes' => '(min-width: 1024px) 30vw, 270px', 'size' => 'mori-720', 'zoom' => true ) ); ?>
						<div class="recept__tekst">
							<p class="recept__tijd text-eyebrow font-semibold uppercase"><?php echo esc_html( $recept['tijd'] ); ?></p>
							<h3 class="font-serif text-h4 lg:text-h3"><?php echo esc_html( $recept['titel'] ); ?></h3>
							<p class="text-klein lg:text-body text-steen"><?php echo kj_rt( $recept['uitleg'], $recept['uitleg_mobiel'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
							<?php if ( is_array( $link ) && ! empty( $link['title'] ) ) : ?>
								<?php kj_component( 'button', array( 'label' => $link['title'], 'url' => $url, 'variant' => 'tekstlink', 'icon' => true, 'class' => 'hidden lg:inline-flex' ) ); ?>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="recepten__voortgang" aria-hidden="true">
				<?php foreach ( $recepten as $i => $recept ) : ?><span<?php echo 0 === $i ? ' class="is-actief"' : ''; ?>></span><?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
