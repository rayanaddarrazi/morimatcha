<?php
/**
 * Veelgestelde vragen (Figma: Home > Vragen). Accordeon met aria-expanded, hoogte-animatie via grid-template-rows.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$titel   = get_sub_field( 'titel' );
$intro   = get_sub_field( 'intro' );
$contact = get_sub_field( 'toon_contact' );
$knop    = kj_link_args( get_sub_field( 'knop' ), 'secundair', array( 'icon' => true ) );
$vragen  = get_sub_field( 'vragen' );
$donker  = in_array( get_sub_field( 'achtergrond' ), array( 'mos', 'bosgroen' ), true );
$mail    = (string) kj_option( 'contact_mail' );
$reactie = (string) kj_option( 'contact_reactietijd' );
$uid     = 'vraag-' . wp_unique_id();
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'rijstpapier' ) ); ?> vragen">
	<div class="container vragen__rij grid-mori">

		<div class="vragen__links">
			<div class="vragen__kop" data-reveal>
				<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
				<?php if ( $titel ) : ?><h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2><?php endif; ?>
				<?php if ( $intro ) : ?><p class="text-body lg:text-body-l text-steen"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
			</div>
			<?php if ( $knop ) : ?>
				<div data-reveal><?php kj_component( 'button', $knop ); ?></div>
			<?php endif; ?>
			<?php if ( $contact && $mail ) : ?>
				<div class="vragen__contact" data-reveal>
					<a href="mailto:<?php echo esc_attr( $mail ); ?>" class="font-serif text-h4"><?php echo esc_html( $mail ); ?></a>
					<p class="text-klein text-steen"><?php echo esc_html( $reactie ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $vragen ) : ?>
			<div class="vragen__lijst accordeon" data-reveal style="--i: 1">
				<?php foreach ( $vragen as $i => $item ) :
					$open = 0 === $i;
					$id   = $uid . '-' . $i;
					?>
					<div class="accordeon__item<?php echo $open ? ' is-open' : ''; ?>">
						<h3 class="accordeon__kop">
							<button type="button" class="accordeon__knop" data-accordeon aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $id ); ?>">
								<span class="font-serif text-h4"><?php echo esc_html( $item['vraag'] ); ?></span>
								<span class="accordeon__icoon" aria-hidden="true"><i></i><i></i></span>
							</button>
						</h3>
						<div class="accordeon__paneel" id="<?php echo esc_attr( $id ); ?>" role="region">
							<div class="accordeon__binnen"><p class="text-body text-steen"><?php echo esc_html( $item['antwoord'] ); ?></p></div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
