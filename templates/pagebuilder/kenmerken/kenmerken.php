<?php
/**
 * Kenmerkenstrook onder de hero (Figma: Home > Kenmerken). Op mobiel een rustige marquee.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$items = get_sub_field( 'items' );
if ( empty( $items ) ) {
	return;
}
$teken = (string) kj_option( 'etiket_teken', '森' );
?>

<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'mos' ) ); ?> kenmerken" aria-label="<?php esc_attr_e( 'Kenmerken', 'morimatcha' ); ?>">
	<div class="kenmerken__spoor">
		<?php for ( $kopie = 0; $kopie < 2; $kopie++ ) : ?>
			<ul class="kenmerken__lijst container" <?php echo $kopie ? 'aria-hidden="true"' : ''; ?>>
				<?php foreach ( $items as $i => $item ) : ?>
					<?php if ( $i > 0 ) : ?>
						<li class="kenmerken__teken" aria-hidden="true"><?php echo esc_html( $teken ); ?></li>
					<?php endif; ?>
					<li class="kenmerken__item"><?php echo esc_html( $item['tekst'] ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endfor; ?>
	</div>
</section>
