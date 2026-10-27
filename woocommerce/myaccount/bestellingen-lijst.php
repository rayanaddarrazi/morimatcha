<?php
/**
 * Lijst met bestellingen: miniatuur, bestelnummer en datum, status en totaal. Een rij is één link naar de bestelling.
 *
 * @var WC_Order[] $orders
 * @var string     $titel  Kop boven de lijst (leeg = geen kop).
 * @var string     $link   Tekst van de link naar alle bestellingen (leeg = geen link).
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$titel = $titel ?? '';
$link  = $link ?? '';
?>
<section class="bestellingen">
	<?php if ( $titel || $link ) : ?>
		<div class="bestellingen__kop">
			<h2 class="font-serif"><?php echo esc_html( $titel ); ?></h2>
			<?php if ( $link ) { kj_component( 'button', array( 'label' => $link, 'url' => wc_get_account_endpoint_url( 'orders' ), 'variant' => 'tekstlink', 'icon' => true ) ); } ?>
		</div>
	<?php endif; ?>
	<ul class="bestellingen__lijst">
		<?php foreach ( $orders as $order ) :
			list( $badge, $stijl )   = kj_order_badge( $order );
			list( $fotos, $meer )    = kj_order_miniaturen( $order, 3 );
			$datum                   = wp_date( 'j F Y', $order->get_date_created()->getTimestamp() );
			?>
			<li>
				<a class="bestelling-rij" href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
					<span class="bestelling-rij__fotos" aria-hidden="true">
						<?php foreach ( $fotos as $foto ) : ?><span class="bestelling-rij__foto"><?php echo $foto; // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php endforeach; ?>
						<?php if ( $meer ) : ?><span class="bestelling-rij__foto bestelling-rij__foto--meer">+<?php echo (int) $meer; ?></span><?php endif; ?>
					</span>
					<span class="bestelling-rij__tekst">
						<span class="bestelling-rij__nummer"><?php echo esc_html( sprintf( kj_acc( 'acc_bestel_nummer', 'Bestelling %s' ), $order->get_order_number() ) ); ?></span>
						<span class="bestelling-rij__sub"><?php echo esc_html( $datum . ' · ' . kj_order_namen( $order ) ); ?></span>
					</span>
					<?php kj_component( 'badge', array( 'text' => $badge, 'stijl' => $stijl, 'punt' => true ) ); ?>
					<span class="bestelling-rij__prijs"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
					<span class="bestelling-rij__pijl" aria-hidden="true"><?php echo kj_svg( 'pijl-rechts' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
