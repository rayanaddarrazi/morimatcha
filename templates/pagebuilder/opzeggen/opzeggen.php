<?php
/**
 * Opzeggen: stappen en een voorbeeld van de abonnementskaart (Figma: Abonnement > Opzeggen)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$titel   = get_sub_field( 'titel' );
$intro   = get_sub_field( 'intro' );
$stappen = get_sub_field( 'stappen' );
$kaart   = get_sub_field( 'kaart' );
$donker  = in_array( get_sub_field( 'achtergrond' ) ?: 'mos', array( 'mos', 'bosgroen' ), true );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'mos' ) ); ?> opzeggen">
	<div class="container opzeggen__rij grid-mori">

		<div class="opzeggen__tekst" data-reveal>
			<div class="opzeggen__kop">
				<?php kj_component( 'eyebrow', array( 'text' => $eyebrow, 'variant' => $donker ? 'licht' : 'donker' ) ); ?>
				<h2 class="font-serif text-h2"><?php echo esc_html( $titel ); ?></h2>
				<?php if ( $intro ) : ?><p class="text-body lg:text-body-l opzeggen__intro"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
			</div>
			<?php if ( $stappen ) : ?>
				<ol class="opzeggen__stappen">
					<?php foreach ( $stappen as $i => $stap ) : ?>
						<li><span class="opzeggen__nummer text-label"><?php echo (int) ( $i + 1 ); ?></span><span class="text-body"><?php echo esc_html( $stap['tekst'] ); ?></span></li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</div>

		<?php if ( is_array( $kaart ) ) : ?>
			<div class="opzeggen__kaart" data-reveal style="--i: 1">
				<?php
				kj_component( 'abonnement-kaart', array(
					'status'        => 'actief',
					'label'         => $kaart['label'] ?? '',
					'titel'         => $kaart['titel'] ?? '',
					'frequentie'    => $kaart['frequentie'] ?? '',
					'status_tekst'  => $kaart['status'] ?? '',
					'gegevens'      => array(
						array( $kaart['volgende_label'] ?? '', $kaart['volgende_waarde'] ?? '' ),
						array( $kaart['bedrag_label'] ?? '', $kaart['bedrag_waarde'] ?? '' ),
						array( $kaart['betaal_label'] ?? '', $kaart['betaal_waarde'] ?? '' ),
					),
					'uitleg_links'  => $kaart['uitleg_links'] ?? '',
					'uitleg_rechts' => $kaart['uitleg_rechts'] ?? '',
					'voortgang'     => (int) ( $kaart['voortgang'] ?? 0 ),
					'acties'        => array(
						array( 'label' => $kaart['knop_pauzeren'] ?? '', 'attrs' => array( 'type' => 'button', 'tabindex' => '-1', 'aria-hidden' => 'true' ) ),
						array( 'label' => $kaart['knop_maat'] ?? '', 'attrs' => array( 'type' => 'button', 'tabindex' => '-1', 'aria-hidden' => 'true' ) ),
						array( 'label' => $kaart['knop_opzeggen'] ?? '', 'type' => 'opzeggen', 'attrs' => array( 'type' => 'button', 'tabindex' => '-1', 'aria-hidden' => 'true' ) ),
					),
				) );
				?>
			</div>
		<?php endif; ?>

	</div>
</section>
