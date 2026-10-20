<?php
/**
 * Bestellingen: dezelfde lijst als "Laatste leveringen", maar dan volledig.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$user   = wp_get_current_user();
$pagina = max( 1, (int) get_query_var( 'orders' ) );
$res    = wc_get_orders( array( 'customer_id' => $user->ID, 'paginate' => true, 'page' => $pagina, 'limit' => 10, 'orderby' => 'date', 'order' => 'DESC', 'status' => array_keys( wc_get_order_statuses() ) ) );
$naam   = '';
foreach ( (array) kj_option( 'acc_menu', array() ) as $rij ) {
	if ( 'orders' === $rij['sleutel'] ) {
		$naam = $rij['naam'];
	}
}
?>
<?php kj_account_kop( $naam ); ?>

<section class="leveringen">
	<ul class="leveringen__lijst leveringen__lijst--los">
		<?php foreach ( $res->orders as $order ) :
			list( $badge, $stijl ) = kj_order_badge( $order );
			?>
			<li class="leveringen__rij">
				<div class="leveringen__tekst">
					<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="leveringen__datum"><?php echo esc_html( wp_date( 'j F Y', $order->get_date_created()->getTimestamp() ) ); ?></a>
					<p class="leveringen__sub"><?php echo esc_html( kj_order_regel( $order ) ); ?></p>
				</div>
				<?php kj_component( 'badge', array( 'text' => $badge, 'stijl' => $stijl, 'punt' => true ) ); ?>
				<span class="leveringen__prijs"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( $res->max_num_pages > 1 ) : ?>
		<nav class="leveringen__pagina">
			<?php if ( $pagina > 1 ) : ?><a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $pagina - 1 ) ); ?>">&larr;</a><?php endif; ?>
			<?php if ( $pagina < $res->max_num_pages ) : ?><a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $pagina + 1 ) ); ?>">&rarr;</a><?php endif; ?>
		</nav>
	<?php endif; ?>
</section>
