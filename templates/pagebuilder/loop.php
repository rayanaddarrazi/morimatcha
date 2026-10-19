<?php
/**
 * Pagebuilder Loop
 *
 * Rendert de blokken (ACF flexible content). Standaard het veld 'pagebuilder' van de huidige pagina;
 * met $GLOBALS['kj_blocks_field'] en $GLOBALS['kj_blocks_post'] kan een ander veld of een andere pagina worden gebruikt
 * (bijvoorbeeld 'productblokken' op een product).
 *
 * @package morimatcha
 * @version 1.0.0
 * @author Kay Jilesen
 */

defined( 'ABSPATH' ) || exit;

$kj_field = $GLOBALS['kj_blocks_field'] ?? 'pagebuilder';
$kj_post  = $GLOBALS['kj_blocks_post'] ?? false;

if ( have_rows( $kj_field, $kj_post ) ) :
    while ( have_rows( $kj_field, $kj_post ) ) : the_row();

        $layout = get_row_layout();

        $template_path = get_stylesheet_directory() . '/templates/pagebuilder/' . $layout . '/' . $layout . '.php';

        if ( file_exists( $template_path ) ) {
            include $template_path;
        }

    endwhile;
endif;
