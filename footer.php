<?php
/**
 * Footer: nieuwsbrief, kolommen, onderbalk en betaalmethoden. Alle inhoud komt uit Instellingen Mori en de WordPress-menu's.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$kolommen = array(
	array( 'location' => 'footer_shop', 'kop' => (string) kj_option( 'footer_kop_shop' ) ),
	array( 'location' => 'footer_hulp', 'kop' => (string) kj_option( 'footer_kop_hulp' ) ),
	array( 'location' => 'footer_mori', 'kop' => (string) kj_option( 'footer_kop_mori' ), 'mobiel_weg' => true ),
);

$juridisch = get_field( 'footer_juridisch', 'option' );
$betalen   = get_field( 'footer_betaalmethoden', 'option' );
$melding   = isset( $_GET['nieuwsbrief'] ) ? sanitize_key( wp_unslash( $_GET['nieuwsbrief'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
?>

	<?php
	get_template_part( 'templates/components/cart-drawer' );
	?>

	<footer class="site-footer sectie--mos">
		<div class="container site-footer__inner">

			<div class="site-footer__nieuwsbrief">
				<div class="site-footer__nieuwsbrief-tekst">
					<p class="site-footer__nieuwsbrief-kop"><?php echo esc_html( (string) kj_option( 'footer_nieuwsbrief_kop' ) ); ?></p>
					<p class="site-footer__nieuwsbrief-sub"><?php echo esc_html( (string) kj_option( 'footer_nieuwsbrief_sub' ) ); ?></p>
					<?php if ( 'ok' === $melding ) : ?>
						<p class="site-footer__melding" role="status"><?php echo esc_html( (string) kj_option( 'footer_nieuwsbrief_gelukt' ) ); ?></p>
					<?php elseif ( 'fout' === $melding ) : ?>
						<p class="site-footer__melding site-footer__melding--fout" role="alert"><?php echo esc_html( (string) kj_option( 'footer_nieuwsbrief_fout' ) ); ?></p>
					<?php endif; ?>
				</div>
				<form class="site-footer__formulier" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="mori_nieuwsbrief">
					<?php wp_nonce_field( 'mori_nieuwsbrief', 'mori_nonce' ); ?>
					<label class="screen-reader-text" for="nieuwsbrief-mail"><?php echo esc_html( (string) kj_option( 'footer_nieuwsbrief_placeholder' ) ); ?></label>
					<input type="email" id="nieuwsbrief-mail" name="email" required placeholder="<?php echo esc_attr( (string) kj_option( 'footer_nieuwsbrief_placeholder' ) ); ?>" autocomplete="email">
					<?php
					kj_component( 'button', array(
						'label'   => (string) kj_option( 'footer_nieuwsbrief_knop' ),
						'variant' => 'licht',
						'tag'     => 'button',
						'icon'    => false,
						'attrs'   => array( 'type' => 'submit' ),
					) );
					?>
				</form>
			</div>

			<div class="site-footer__kolommen">
				<div class="site-footer__merk">
					<span class="logo logo--footer"><?php echo kj_logo( 'wit', 'volledig' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<p><?php echo esc_html( (string) kj_option( 'footer_merk_tekst' ) ); ?></p>
				</div>
				<div class="site-footer__links">
					<?php foreach ( $kolommen as $kolom ) :
						$items = kj_menu_items( $kolom['location'] );
						if ( ! $items ) {
							continue;
						}
						?>
						<nav class="site-footer__kolom<?php echo ! empty( $kolom['mobiel_weg'] ) ? ' max-lg:hidden' : ''; ?>" aria-label="<?php echo esc_attr( $kolom['kop'] ); ?>">
							<p class="site-footer__kop"><?php echo esc_html( $kolom['kop'] ); ?></p>
							<ul>
								<?php foreach ( $items as $item ) : ?>
									<li class="<?php echo esc_attr( $item['klassen'] ); ?>"><a href="<?php echo esc_url( $item['url'] ); ?>" class="site-footer__link"><?php echo kj_rt( $item['naam'], $item['kort'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="site-footer__onder">
				<div class="site-footer__legal">
					<p class="site-footer__copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( (string) kj_option( 'footer_copyright', 'Mori Matcha' ) ); ?></p>
					<ul class="site-footer__juridisch">
						<?php foreach ( (array) $juridisch as $link ) :
							if ( empty( $link['link']['title'] ) ) {
								continue;
							}
							?>
							<li><a href="<?php echo esc_url( $link['link']['url'] ); ?>" class="site-footer__link"><?php echo kj_rt( $link['link']['title'], $link['korte_tekst'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<ul class="site-footer__betalen">
					<?php foreach ( (array) $betalen as $methode ) :
						$id = kj_image_id( $methode['logo'] ?? null );
						if ( ! $id ) {
							continue;
						}
						?>
						<li><?php echo wp_get_attachment_image( $id, 'full', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
					<?php endforeach; ?>
				</ul>
			</div>

		</div>
	</footer>

	<?php wp_footer(); ?>

</body>
</html>
