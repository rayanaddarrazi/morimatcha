<?php
/**
 * Overzicht: het abonnement en de laatste leveringen.
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

wc_get_template( 'myaccount/abonnement.php', array( 'titel' => (string) kj_option( 'acc_overzicht_titel', '' ) ) );
