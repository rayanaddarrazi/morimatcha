<?php

defined( 'ABSPATH' ) || exit;

if ( ! defined( '_KAYJILESEN_VERSION' ) ) {
	define( '_KAYJILESEN_VERSION', '1.0.0' );
}

require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/remove.php';
require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/acf.php';
require_once get_template_directory() . '/inc/scripts.php';
require_once get_template_directory() . '/inc/template-functions.php';
require_once get_template_directory() . '/inc/woocommerce.php';
require_once get_template_directory() . '/inc/abonnementen.php';
require_once get_template_directory() . '/inc/kassa.php';
require_once get_template_directory() . '/inc/account.php';
