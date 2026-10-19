<?php
/**
 * Je abonnement: kaart in vier statussen, laatste leveringen en de vensters voor pauzeren, maat en opzeggen.
 *
 * @var string $titel
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$user    = wp_get_current_user();
$abo     = kj_abo_van_klant( $user->ID );
$sinds   = $abo && $abo['sinds'] ? ucfirst( wp_date( 'F Y', strtotime( $abo['sinds'] ) ) ) : wp_date( 'F Y', strtotime( $user->user_registered ) );
$orders  = kj_laatste_bestellingen( $user->ID, 3 );
$knop    = get_field( 'acc_geen_abonnement_knop', 'option' );
$t       = fn( $k ) => (string) kj_option( $k, '' );
?>
<div class="account-kop">
	<h1 class="account-kop__titel font-serif"><?php echo esc_html( $titel ); ?></h1>
	<p class="account-kop__sinds"><?php echo esc_html( sprintf( $t( 'acc_abo_sinds' ), strtolower( $sinds ) ) ); ?></p>
</div>

<?php if ( ! $abo ) : ?>
	<div class="account-leeg">
		<p><?php echo esc_html( $t( 'acc_geen_abonnement' ) ); ?></p>
		<?php if ( $knop ) { kj_component( 'button', array( 'label' => $knop['title'], 'url' => $knop['url'] ) ); } ?>
	</div>
<?php else : ?>

	<?php if ( 'actief' === $abo['status'] ) : ?>
		<div class="kassa-melding account-melding">
			<?php echo kj_svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p class="kassa-melding__kop"><?php echo esc_html( $t( 'acc_pauze_kop' ) ); ?></p>
			<p class="kassa-melding__tekst"><?php echo esc_html( $t( 'acc_pauze_tekst' ) ); ?></p>
		</div>
	<?php elseif ( 'opgezegd' === $abo['status'] ) : ?>
		<div class="kassa-melding account-melding account-melding--gelukt">
			<?php echo kj_svg( 'vinkje' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p class="kassa-melding__kop"><?php echo esc_html( $t( 'acc_opgezegd_melding_kop' ) ); ?></p>
			<p class="kassa-melding__tekst"><?php echo esc_html( $t( 'acc_opgezegd_melding_tekst' ) ); ?></p>
		</div>
	<?php endif; ?>

	<?php kj_component( 'abonnement-kaart', kj_abo_kaart_args( $abo ) ); ?>

	<form id="form-hervatten" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="mori_abo_actie"><input type="hidden" name="actie" value="hervatten"><?php wp_nonce_field( 'mori_abo_actie' ); ?></form>
	<form id="form-opnieuw" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="mori_abo_actie"><input type="hidden" name="actie" value="opnieuw"><?php wp_nonce_field( 'mori_abo_actie' ); ?></form>

	<?php if ( 'opgezegd' !== $abo['status'] ) :
		wc_get_template( 'myaccount/abonnement-vensters.php', array( 'abo' => $abo ) );
	endif; ?>

<?php endif; ?>

<?php if ( $orders ) : ?>
	<section class="leveringen">
		<div class="leveringen__kop">
			<h2 class="font-serif"><?php echo esc_html( $t( 'acc_leveringen' ) ); ?></h2>
			<?php kj_component( 'button', array( 'label' => $t( 'acc_alle_bestellingen' ), 'url' => wc_get_account_endpoint_url( 'orders' ), 'variant' => 'tekstlink', 'icon' => true ) ); ?>
		</div>
		<ul class="leveringen__lijst">
			<?php foreach ( $orders as $order ) :
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
	</section>
<?php endif; ?>
