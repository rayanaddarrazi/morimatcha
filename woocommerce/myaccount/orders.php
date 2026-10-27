<?php
/**
 * Bestellingen: filter (alle, lopend, bezorgd), lijst met pagina's en een lege staat.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$user   = wp_get_current_user();
$pagina = max( 1, (int) get_query_var( 'orders' ) );
$filter = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : 'alle'; // phpcs:ignore WordPress.Security.NonceVerification
$groepen = array(
	'alle'    => array( kj_acc( 'acc_filter_alle', 'Alle' ), array_keys( wc_get_order_statuses() ) ),
	'lopend'  => array( kj_acc( 'acc_filter_lopend', 'Lopend' ), array( 'wc-pending', 'wc-on-hold', 'wc-processing' ) ),
	'bezorgd' => array( kj_acc( 'acc_filter_bezorgd', 'Bezorgd' ), array( 'wc-completed' ) ),
);
$filter = isset( $groepen[ $filter ] ) ? $filter : 'alle';
$res    = wc_get_orders( array( 'customer_id' => $user->ID, 'paginate' => true, 'page' => $pagina, 'limit' => 10, 'orderby' => 'date', 'order' => 'DESC', 'status' => $groepen[ $filter ][1] ) );
$basis  = wc_get_account_endpoint_url( 'orders' );

kj_account_kop( kj_account_tabnaam( 'orders' ) );
?>

<nav class="account-filter" aria-label="<?php echo esc_attr( kj_acc( 'acc_filter_label', 'Filter bestellingen' ) ); ?>">
	<?php foreach ( $groepen as $sleutel => $groep ) : ?>
		<a href="<?php echo esc_url( 'alle' === $sleutel ? $basis : add_query_arg( 'status', $sleutel, $basis ) ); ?>" class="account-filter__knop<?php echo $sleutel === $filter ? ' is-actief' : ''; ?>"<?php echo $sleutel === $filter ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $groep[0] ); ?></a>
	<?php endforeach; ?>
</nav>

<?php if ( $res->orders ) : ?>
	<?php wc_get_template( 'myaccount/bestellingen-lijst.php', array( 'orders' => $res->orders ) ); ?>
	<?php if ( $res->max_num_pages > 1 ) : ?>
		<nav class="account-paginas" aria-label="<?php echo esc_attr( kj_acc( 'acc_paginas', 'Pagina\'s' ) ); ?>">
			<?php if ( $pagina > 1 ) : ?><a href="<?php echo esc_url( add_query_arg( 'status', $filter, wc_get_endpoint_url( 'orders', $pagina - 1 ) ) ); ?>">&larr;</a><?php endif; ?>
			<span><?php echo (int) $pagina; ?> / <?php echo (int) $res->max_num_pages; ?></span>
			<?php if ( $pagina < $res->max_num_pages ) : ?><a href="<?php echo esc_url( add_query_arg( 'status', $filter, wc_get_endpoint_url( 'orders', $pagina + 1 ) ) ); ?>">&rarr;</a><?php endif; ?>
		</nav>
	<?php endif; ?>
<?php else : ?>
	<div class="account-leeg">
		<span class="account-leeg__icoon" aria-hidden="true"><?php echo kj_svg( 'tas' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<p><?php echo esc_html( kj_acc( 'acc_bestel_leeg', 'Je hebt hier nog geen bestellingen.' ) ); ?></p>
		<?php kj_component( 'button', array( 'label' => kj_acc( 'acc_bestel_leeg_knop', 'Naar de shop' ), 'url' => wc_get_page_permalink( 'shop' ) ) ); ?>
	</div>
<?php endif; ?>
