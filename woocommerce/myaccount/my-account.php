<?php
/**
 * Mijn account: kop met groet, menu met tabbladen en daaronder de inhoud van de gekozen pagina.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$user   = wp_get_current_user();
$naam   = $user->first_name ? $user->first_name : $user->display_name;
$menu   = kj_account_menu();
$letter = function_exists( 'mb_substr' ) ? mb_strtoupper( mb_substr( $naam, 0, 1 ) ) : strtoupper( substr( $naam, 0, 1 ) );
$sinds  = sprintf( kj_acc( 'acc_abo_sinds', 'Klant sinds %s' ), strtolower( kj_account_sinds( $user ) ) );
?>
<div class="account container">

	<header class="account-hoofd">
		<span class="account-hoofd__avatar" aria-hidden="true"><?php echo esc_html( $letter ); ?></span>
		<div class="account-hoofd__wie">
			<p class="account-hoofd__groet font-serif"><?php echo esc_html( sprintf( kj_acc( 'acc_groet', 'Hoi %s' ), $naam ) ); ?></p>
			<p class="account-hoofd__meta"><span><?php echo esc_html( $user->user_email ); ?></span><span aria-hidden="true">·</span><span><?php echo esc_html( $sinds ); ?></span></p>
		</div>
		<a href="<?php echo esc_url( wc_logout_url() ); ?>" class="account-hoofd__uit"><?php echo kj_svg( 'uitloggen' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( kj_acc( 'acc_uitloggen', 'Uitloggen' ) ); ?></span></a>
	</header>

	<nav class="account-nav" aria-label="<?php echo esc_attr( kj_acc( 'acc_menu_label', 'Mijn account' ) ); ?>">
		<ul>
			<?php foreach ( $menu as $item ) : ?>
				<li>
					<a href="<?php echo esc_url( $item['url'] ); ?>" class="account-nav__link<?php echo $item['actief'] ? ' is-actief' : ''; ?>"<?php echo $item['actief'] ? ' aria-current="page"' : ''; ?>>
						<?php echo kj_svg( $item['icoon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php echo esc_html( $item['naam'] ); ?></span>
						<?php if ( $item['teller'] ) : ?><span class="account-nav__teller"><?php echo (int) $item['teller']; ?></span><?php endif; ?>
						<?php if ( $item['punt'] ) : ?><span class="account-nav__punt account-nav__punt--<?php echo esc_attr( $item['punt'] ); ?>" aria-hidden="true"></span><?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<div class="account__inhoud">
		<?php do_action( 'woocommerce_account_content' ); ?>
	</div>

</div>
