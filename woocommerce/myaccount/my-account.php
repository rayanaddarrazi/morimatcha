<?php
/**
 * Mijn account (Figma "Account"): menu links, inhoud rechts.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$user    = wp_get_current_user();
$naam    = $user->first_name ? $user->first_name : $user->display_name;
$menu    = kj_account_menu();
?>
<div class="account container">
	<div class="account__grid">

		<aside class="account__zij">
			<p class="account__label"><?php echo esc_html( (string) kj_option( 'acc_titel', '' ) ); ?></p>
			<p class="account__groet font-serif"><?php echo esc_html( sprintf( (string) kj_option( 'acc_groet', '%s' ), $naam ) ); ?></p>
			<nav class="account__menu" aria-label="<?php echo esc_attr( (string) kj_option( 'acc_titel', '' ) ); ?>">
				<ul>
					<?php foreach ( $menu as $item ) : ?>
						<li><a href="<?php echo esc_url( $item['url'] ); ?>" class="account__link<?php echo $item['actief'] ? ' is-actief' : ''; ?>"<?php echo $item['actief'] ? ' aria-current="page"' : ''; ?>><?php echo kj_svg( $item['icoon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $item['naam'] ); ?></span></a></li>
					<?php endforeach; ?>
				</ul>
				<a href="<?php echo esc_url( wc_logout_url() ); ?>" class="account__uit"><?php echo esc_html( (string) kj_option( 'acc_uitloggen', '' ) ); ?></a>
			</nav>
		</aside>

		<div class="account__inhoud">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>

	</div>
</div>
