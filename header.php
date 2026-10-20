<?php
/**
 * Header: mededelingsbalk, menu, logo en acties. Alles komt uit Instellingen Mori en de WordPress-menu's.
 * Thema "Op foto" (transparant over een foto) of "Standaard"; na 80 px scrollen wordt de header vast (zie script.js).
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$menu         = kj_menu_items( 'hoofdmenu' );
$over_foto    = kj_header_over_photo();
$mededelingen = array_values( array_filter( (array) kj_option( 'mededelingen', array() ), fn( $r ) => ! empty( $r['tekst'] ) ) );
$toon_balk    = $mededelingen && kj_option( 'mededeling_toon', true );
$account_url  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
$shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$cart_url     = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
$cart_count   = function_exists( 'kj_cart_count' ) ? kj_cart_count() : 0;
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php wp_head(); ?>
	</head>
	<body id="page-top" <?php body_class( 'max-w-full overflow-x-hidden' ); ?>>
	<?php wp_body_open(); ?>

	<a class="skip-link" href="#primary"><?php esc_html_e( 'Naar de inhoud', 'morimatcha' ); ?></a>

	<header id="masthead" class="site-header <?php echo $over_foto ? 'site-header--foto' : 'site-header--standaard'; ?>" data-header>

		<?php if ( $toon_balk ) : ?>
			<div class="site-header__aankondiging" data-aankondiging>
				<ul>
					<?php foreach ( $mededelingen as $i => $rij ) : ?>
						<li<?php echo 0 === $i ? ' class="is-actief"' : ' aria-hidden="true"'; ?>>
							<?php echo kj_svg( 'verzending' === $rij['icoon'] ? 'verzending' : 'vinkje' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<p><?php echo esc_html( $rij['tekst'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="site-header__balk">
			<div class="site-header__inner">

				<button type="button" class="site-header__menu-knop" id="menu-toggle" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?php esc_attr_e( 'Menu openen', 'morimatcha' ); ?>">
					<?php echo kj_svg( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>

				<nav class="site-header__menu" aria-label="<?php esc_attr_e( 'Hoofdmenu', 'morimatcha' ); ?>">
					<ul>
						<?php foreach ( $menu as $item ) : ?>
							<li><a href="<?php echo esc_url( $item['url'] ); ?>" class="site-header__link<?php echo $item['actief'] ? ' is-actief' : ''; ?>"><?php echo esc_html( $item['naam'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>

				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo" aria-label="<?php echo esc_attr( (string) kj_option( 'bedrijfsnaam', get_bloginfo( 'name' ) ) ); ?>">
					<span class="logo logo--volledig">
						<?php echo kj_logo( 'wit', 'volledig', 'logo__wit' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo kj_logo( 'zwart', 'volledig', 'logo__zwart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</span>
					<span class="logo logo--woordmerk">
						<?php echo kj_logo( 'wit', 'woordmerk', 'logo__wit' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo kj_logo( 'zwart', 'woordmerk', 'logo__zwart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</span>
				</a>

				<div class="site-header__acties">
					<a href="<?php echo esc_url( $shop_url . '?zoeken=1' ); ?>" class="site-header__actie site-header__zoeken" aria-label="<?php esc_attr_e( 'Zoeken', 'morimatcha' ); ?>"><?php echo kj_svg( 'zoeken' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<a href="<?php echo esc_url( $account_url ); ?>" class="site-header__actie site-header__account" aria-label="<?php esc_attr_e( 'Mijn account', 'morimatcha' ); ?>"><?php echo kj_svg( 'account' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<a href="<?php echo esc_url( $cart_url ); ?>" class="site-header__winkelwagen" data-cart-open aria-haspopup="dialog" aria-controls="cart-drawer" data-cart-label="<?php esc_attr_e( 'Winkelwagen openen, %d artikelen', 'morimatcha' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Winkelwagen openen, %d artikelen', 'morimatcha' ), (int) $cart_count ) ); ?>">
						<?php echo kj_svg( 'tas' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="site-header__teller" data-cart-count><?php echo (int) $cart_count; ?></span>
					</a>
				</div>

			</div>
		</div>
	</header>

	<?php if ( ! $over_foto ) : ?>
		<div class="site-header__spacer" aria-hidden="true"></div>
	<?php endif; ?>

	<div id="mobile-menu" class="mobile-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'morimatcha' ); ?>" aria-hidden="true" inert>
		<div class="mobile-menu__overlay" data-menu-close></div>
		<div class="mobile-menu__paneel">
			<div class="mobile-menu__kop">
				<span class="logo logo--woordmerk-vast"><?php echo kj_logo( 'zwart', 'woordmerk' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<button type="button" class="mobile-menu__sluiten" aria-label="<?php esc_attr_e( 'Menu sluiten', 'morimatcha' ); ?>" data-menu-close><?php echo kj_svg( 'sluiten' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			</div>
			<nav aria-label="<?php esc_attr_e( 'Hoofdmenu', 'morimatcha' ); ?>">
				<ul>
					<?php foreach ( $menu as $i => $item ) : ?>
						<li style="--i: <?php echo (int) $i; ?>"><a href="<?php echo esc_url( $item['url'] ); ?>" data-menu-close><?php echo esc_html( $item['naam'] ); ?></a></li>
					<?php endforeach; ?>
					<li style="--i: <?php echo (int) count( $menu ); ?>"><a href="<?php echo esc_url( $account_url ); ?>" data-menu-close><?php esc_html_e( 'Mijn account', 'morimatcha' ); ?></a></li>
				</ul>
			</nav>
		</div>
	</div>
