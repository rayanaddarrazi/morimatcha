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
					<button type="button" class="site-header__actie site-header__zoeken" data-zoek-open aria-expanded="false" aria-controls="zoek-paneel" aria-label="<?php echo esc_attr( (string) kj_option( 'zoek_label', 'Zoeken' ) ); ?>"><?php echo kj_svg( 'zoeken' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					<a href="<?php echo esc_url( $account_url ); ?>" class="site-header__actie site-header__account" aria-label="<?php esc_attr_e( 'Mijn account', 'morimatcha' ); ?>"><?php echo kj_svg( 'account' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<a href="<?php echo esc_url( $cart_url ); ?>" class="site-header__winkelwagen" data-cart-open aria-haspopup="dialog" aria-controls="cart-drawer" data-cart-label="<?php esc_attr_e( 'Winkelwagen openen, %d artikelen', 'morimatcha' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Winkelwagen openen, %d artikelen', 'morimatcha' ), (int) $cart_count ) ); ?>">
						<?php echo kj_svg( 'tas' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="site-header__teller" data-cart-count><?php echo (int) $cart_count; ?></span>
					</a>
				</div>

			</div>
		</div>
	</header>

	<div id="zoek-paneel" class="zoekvenster" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( (string) kj_option( 'zoek_label', 'Zoeken' ) ); ?>" hidden>
		<div class="zoekvenster__achter" data-zoek-sluit></div>
		<div class="zoekvenster__kaart">
			<form class="zoekvenster__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="zoek-veld"><?php echo esc_html( (string) kj_option( 'zoek_label', 'Zoeken' ) ); ?></label>
				<?php echo kj_svg( 'zoeken' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<input type="search" id="zoek-veld" name="s" placeholder="<?php echo esc_attr( (string) kj_option( 'zoek_placeholder', 'Zoek in de winkel' ) ); ?>" autocomplete="off" required>
				<input type="hidden" name="post_type" value="product">
				<button type="button" class="zoekvenster__sluiten" data-zoek-sluit aria-label="<?php esc_attr_e( 'Sluiten', 'morimatcha' ); ?>"><?php echo kj_svg( 'sluiten' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			</form>
			<?php
			$zoek_cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
			if ( ! is_wp_error( $zoek_cats ) && $zoek_cats ) :
				?>
				<div class="zoekvenster__snel">
					<p class="text-eyebrow font-semibold uppercase"><?php echo esc_html( (string) kj_option( 'zoek_snel', 'Of kijk direct in' ) ); ?></p>
					<ul>
						<?php foreach ( $zoek_cats as $cat ) : ?>
							<li><a class="filterpil" href="<?php echo esc_url( add_query_arg( 'categorie', $cat->slug, $shop_url ) ); ?>"><?php echo esc_html( $cat->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( ! $over_foto ) : ?>
		<div class="site-header__spacer" aria-hidden="true"></div>
	<?php endif; ?>

	<div id="mobile-menu" class="mobile-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'morimatcha' ); ?>" aria-hidden="true" inert>
		<div class="mobile-menu__paneel">
			<span class="mobile-menu__teken" aria-hidden="true"><?php echo esc_html( (string) kj_option( 'etiket_teken', '森' ) ); ?></span>
			<div class="mobile-menu__kop">
				<button type="button" class="mobile-menu__sluiten" aria-label="<?php esc_attr_e( 'Menu sluiten', 'morimatcha' ); ?>" data-menu-close><?php echo kj_svg( 'sluiten' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				<span class="logo logo--woordmerk-vast"><?php echo kj_logo( 'zwart', 'woordmerk' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<a href="<?php echo esc_url( $cart_url ); ?>" class="mobile-menu__tas" aria-label="<?php echo esc_attr( sprintf( __( 'Winkelwagen, %d artikelen', 'morimatcha' ), (int) $cart_count ) ); ?>"><?php echo kj_svg( 'tas' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			</div>

			<form class="mobile-menu__zoek" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="zoek-veld-mobiel"><?php echo esc_html( (string) kj_option( 'zoek_label', 'Zoeken' ) ); ?></label>
				<?php echo kj_svg( 'zoeken' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<input type="search" id="zoek-veld-mobiel" name="s" placeholder="<?php echo esc_attr( (string) kj_option( 'zoek_placeholder', 'Zoek in de winkel' ) ); ?>" autocomplete="off" enterkeyhint="search" required>
				<input type="hidden" name="post_type" value="product">
			</form>

			<nav class="mobile-menu__nav" aria-label="<?php esc_attr_e( 'Hoofdmenu', 'morimatcha' ); ?>">
				<ul>
					<?php foreach ( $menu as $i => $item ) : ?>
						<li style="--i: <?php echo (int) $i; ?>"><a href="<?php echo esc_url( $item['url'] ); ?>" data-menu-close><span class="mobile-menu__tekst"><span class="mobile-menu__naam"><?php echo esc_html( $item['naam'] ); ?></span><?php if ( $item['kort'] ) : ?><span class="mobile-menu__kort"><?php echo esc_html( $item['kort'] ); ?></span><?php endif; ?></span><?php echo kj_svg( 'pijl-rechts' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div class="mobile-menu__voet">
				<?php
				$menu_product = function_exists( 'wc_get_products' ) ? wc_get_products( array( 'type' => 'variable', 'status' => 'publish', 'limit' => 1, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) : array();
				$menu_product = $menu_product ? $menu_product[0] : null;
				if ( $menu_product ) :
					$menu_maten = kj_product_sizes( $menu_product );
					$menu_vanaf = $menu_maten ? min( array_column( $menu_maten, 'price' ) ) : (float) $menu_product->get_price();
					?>
					<a href="<?php echo esc_url( get_permalink( $menu_product->get_id() ) ); ?>" class="mobile-menu__product" data-menu-close>
						<span class="mobile-menu__blik"><?php kj_component( 'blik', array( 'maat' => 'middel', 'hoogte' => 84, 'gram' => 60 ) ); ?></span>
						<span class="mobile-menu__product-tekst">
							<span class="mobile-menu__product-naam font-serif"><?php echo esc_html( $menu_product->get_name() ); ?></span>
							<span class="mobile-menu__product-sub"><?php echo esc_html( (string) get_field( 'kaart_omschrijving', $menu_product->get_id() ) ); ?></span>
							<span class="mobile-menu__product-prijs"><?php echo esc_html( trim( (string) get_field( 'kaart_label', $menu_product->get_id() ) . ' ' . kj_format_price( $menu_vanaf ) ) ); ?></span>
						</span>
						<?php echo kj_svg( 'pijl-rechts' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				<?php endif; ?>
				<div class="mobile-menu__onder">
					<a href="<?php echo esc_url( $account_url ); ?>" class="mobile-menu__account" data-menu-close><?php echo kj_svg( 'account' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( is_user_logged_in() ? (string) kj_option( 'acc_titel', 'Mijn account' ) : (string) kj_option( 'acc_login_titel', 'Inloggen' ) ); ?></span></a>
					<?php if ( ! empty( $mededelingen[0]['tekst'] ) ) : ?>
						<p class="mobile-menu__usp"><?php echo kj_svg( 'verzending' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $mededelingen[0]['tekst'] ); ?></span></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	</div>
