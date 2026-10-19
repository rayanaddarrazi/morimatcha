<?php
/**
 * Footer van de kassa: alleen de links en betaalmethoden.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$links  = (array) kj_option( 'afr_footer_links', array() );
$betalen = (array) kj_option( 'footer_betaalmethoden', array() );
?>
	<footer class="kassa-footer">
		<div class="kassa-footer__inner container">
			<ul class="kassa-footer__links">
				<?php foreach ( $links as $rij ) :
					if ( empty( $rij['link']['title'] ) ) {
						continue;
					}
					?>
					<li><a href="<?php echo esc_url( $rij['link']['url'] ); ?>"><?php echo esc_html( $rij['link']['title'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<ul class="kassa-footer__betalen">
				<?php foreach ( $betalen as $methode ) :
					$id = kj_image_id( $methode['logo'] ?? null );
					if ( ! $id ) {
						continue;
					}
					?>
					<li><?php echo wp_get_attachment_image( $id, 'full', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</footer>

	<?php wp_footer(); ?>

</body>
</html>
