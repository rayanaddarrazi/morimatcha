<?php

defined( 'ABSPATH' ) || exit;

// Eenmalige site-defaults, alleen bij de eerste keer laden van het thema.
if ( ! get_option( 'kj_theme_settings' ) ) {

    update_option( 'start_of_week', 1 );
    update_option( 'date_format', 'j F Y' );
    update_option( 'time_format', 'H:i' );
    update_option( 'timezone_string', 'Europe/Amsterdam' );
    update_option( 'permalink_structure', '/%postname%/' );
    update_option( 'WPLANG', 'nl_NL' );

    update_option( 'kj_theme_settings', true );
}
