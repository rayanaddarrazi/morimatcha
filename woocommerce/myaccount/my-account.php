<?php
/**
 * Mijn account (Figma "Account"): profielkaart met menu links, inhoud rechts.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$user    = wp_get_current_user();
$naam    = $user->first_name ? $user->first_name : $user->display_name;
$menu    = kj_account_menu();
$letter  = function_exists( 'mb_substr' ) ? mb_strtoupper( mb_substr( $naam, 0, 1 ) ) : strtoupper( substr( $naam, 0, 1 ) );
?>
<div class="account container">
	<div class="account__grid">

		<aside class="account__zij">
			<div class="account__profiel">
				<span class="account__avatar" aria-hidden="true"><?php echo esc_html( $letter ); ?></span>
				<div class="account__wie">
					<p class="account__groet font-serif"><?php echo esc_html( sprintf( (string) kj_option( 'acc_groet', '%s' ), $naam ) ); ?></p>
					<p class="account__mail"><?php echo esc_html( $user->user_email ); ?></p>
				</div>
			</div>
			<nav class="account__menu" aria-label="<?php echo esc_attr( (string) kj_option( 'acc_titel', '' ) ); ?>">
				<ul>
					<?php foreach ( $menu as $item ) : ?>
						<li><a href="<?php echo esc_url( $item['url'] ); ?>" class="account__link<?php echo $item['actief'] ? ' is-actief' : ''; ?>"<?php echo $item['actief'] ? ' aria-current="page"' : ''; ?>><?php echo kj_svg( $item['icoon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $item['naam'] ); ?></span></a></li>
					<?php endforeach; ?>
					<li class="account__uit-rij"><a href="<?php echo esc_url( wc_logout_url() ); ?>" class="account__link account__uit"><?php echo kj_svg( 'uitloggen' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( (string) kj_option( 'acc_uitloggen', '' ) ); ?></span></a></li>
				</ul>
			</nav>
		</aside>

		<div class="account__inhoud">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>

	</div>
</div>
