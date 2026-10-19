<?php
/**
 * Feiten: grote waarden op een rij (Figma: Bereiden > Feiten)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$items = get_sub_field( 'items' );
if ( empty( $items ) ) {
	return;
}
?>
<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="<?php echo esc_attr( kj_section_bg( 'mos' ) ); ?> feiten">
	<div class="container">
		<ul class="feiten__rij">
			<?php foreach ( $items as $i => $item ) : ?>
				<li class="feit" data-reveal style="--i: <?php echo (int) $i; ?>">
					<p class="font-serif text-h2 feit__waarde"><?php echo esc_html( $item['waarde'] ); ?></p>
					<p class="text-klein feit__uitleg"><?php echo esc_html( $item['uitleg'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
