<?php
/**
 * Header van de kassa: logo, stappen en "Veilig betalen". Geen menu, zodat de bezoeker niet wegdwaalt.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$stappen = (array) kj_option( 'afr_stappen', array() );
$huidig  = kj_kassa_stap();
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php wp_head(); ?>
	</head>
	<body id="page-top" <?php body_class( 'max-w-full overflow-x-hidden kassa-body' ); ?>>
	<?php wp_body_open(); ?>

	<a class="skip-link" href="#primary"><?php esc_html_e( 'Naar de inhoud', 'morimatcha' ); ?></a>

	<header class="kassa-header">
		<div class="kassa-header__inner container">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="kassa-header__logo" aria-label="<?php echo esc_attr( (string) kj_option( 'bedrijfsnaam', get_bloginfo( 'name' ) ) ); ?>">
				<?php echo kj_logo( 'zwart', 'volledig' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>

			<ol class="kassa-stappen" aria-label="<?php esc_attr_e( 'Stappen', 'morimatcha' ); ?>">
				<?php foreach ( $stappen as $i => $stap ) :
					$nr    = $i + 1;
					$staat = $nr < $huidig ? 'klaar' : ( $nr === $huidig ? 'nu' : 'later' );
					?>
					<li class="kassa-stappen__stap is-<?php echo esc_attr( $staat ); ?>"<?php echo 'nu' === $staat ? ' aria-current="step"' : ''; ?>>
						<span class="kassa-stappen__nr"><?php echo 'klaar' === $staat ? kj_svg( 'vinkje' ) : (int) $nr; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="kassa-stappen__naam"><?php echo esc_html( $stap['naam'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>

			<p class="kassa-header__veilig"><?php echo kj_svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( (string) kj_option( 'afr_veilig', '' ) ); ?></span></p>
		</div>
	</header>
