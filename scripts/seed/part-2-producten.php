<?php
/**
 * Seed deel 2: WooCommerce-producten uit de Shop en de Productpagina.
 */

$img = $GLOBALS['mori_img'];

function kj_seed_product( $slug, $args ) {
    $found    = get_posts( array( 'post_type' => 'product', 'name' => $slug, 'post_status' => 'any', 'numberposts' => 1 ) );
    $existing = $found ? $found[0] : null;
    $variable = ! empty( $args['maten'] );
    $product  = $existing ? wc_get_product( $existing->ID ) : ( $variable ? new WC_Product_Variable() : new WC_Product_Simple() );

    $product->set_name( $args['naam'] );
    $product->set_slug( $slug );
    $product->set_status( 'publish' );
    $product->set_catalog_visibility( 'visible' );
    $product->set_short_description( $args['kort'] ?? '' );
    $product->set_description( $args['lang'] ?? '' );
    $product->set_image_id( $args['foto'] );
    if ( ! empty( $args['galerij'] ) ) {
        $product->set_gallery_image_ids( $args['galerij'] );
    }
    $product->set_category_ids( array( (int) get_term_by( 'name', $args['categorie'], 'product_cat' )->term_id ) );
    $product->set_menu_order( $args['volgorde'] );
    $product->set_sold_individually( false );

    if ( ! $variable ) {
        $product->set_regular_price( (string) $args['prijs'] );
        $product->set_price( (string) $args['prijs'] );
        $product->set_stock_status( 'instock' );
    } else {
        $attribute = new WC_Product_Attribute();
        $attribute->set_id( 0 );
        $attribute->set_name( 'Maat' );
        $attribute->set_options( array_column( $args['maten'], 'label' ) );
        $attribute->set_position( 0 );
        $attribute->set_visible( true );
        $attribute->set_variation( true );
        $product->set_attributes( array( $attribute ) );
    }

    $id = $product->save();

    if ( $variable ) {
        $bestaand = array();
        foreach ( $product->get_children() as $child_id ) {
            $child = wc_get_product( $child_id );
            $bestaand[ $child->get_attributes()['maat'] ?? '' ] = $child;
        }
        foreach ( $args['maten'] as $maat ) {
            $variation = $bestaand[ $maat['label'] ] ?? new WC_Product_Variation();
            $variation->set_parent_id( $id );
            $variation->set_attributes( array( 'maat' => $maat['label'] ) );
            $variation->set_regular_price( (string) $maat['prijs'] );
            $variation->set_weight( (string) $maat['gram'] );
            $variation->set_stock_status( 'instock' );
            $variation->set_status( 'publish' );
            $variation->save();
        }
        WC_Product_Variable::sync( $id );
        wc_delete_product_transients( $id );
    }

    foreach ( $args['acf'] ?? array() as $naam => $waarde ) {
        update_field( $naam, $waarde, $id );
    }

    WP_CLI::log( 'Product: ' . $args['naam'] . ' (' . $id . ')' );
    return $id;
}

$ids = array();

$ids['matcha'] = kj_seed_product( 'mori-matcha', array(
    'naam'      => 'Mori Matcha',
    'kort'      => 'Zacht en romig, met een frisse groene smaak. Niet bitter, zolang je het water niet te heet maakt. Lekker puur of met melk.',
    'foto'      => $img['productfoto'],
    'galerij'   => array( $img['fijngemalen'], $img['galerij_2'] ),
    'categorie' => 'Matcha',
    'volgorde'  => 1,
    'maten'     => array(
        array( 'label' => '30 gram', 'gram' => 30, 'prijs' => '19.95' ),
        array( 'label' => '60 gram', 'gram' => 60, 'prijs' => '36.95' ),
        array( 'label' => '90 gram', 'gram' => 90, 'prijs' => '49.95' ),
    ),
    'acf'       => array(
        'badge_blad'         => 'Voor beginners',
        'smaakbadges'        => array( array( 'tekst' => 'Zacht' ), array( 'tekst' => 'Romig' ), array( 'tekst' => 'Weinig bitter' ) ),
        'kaart_omschrijving' => 'Zacht en romig, voor puur of met melk.',
        'kaart_label'        => 'vanaf',
        'abonnement_mogelijk' => 1,
        'hulp_maat_tekst'    => 'Hoe lang doe ik ermee?',
        'hulp_maat_link'     => array( 'title' => 'Hoe lang doe ik ermee?', 'url' => '#voor-je-kiest', 'target' => '' ),
        'uitleg_maten'       => 'Uitgaande van 2 gram per kop. Hoe groter het blik, hoe lager de prijs per gram.',
    ),
) );

$ids['klopper'] = kj_seed_product( 'bamboe-klopper', array(
    'naam' => 'Bamboe klopper', 'foto' => $img['klopper'], 'categorie' => 'Gereedschap', 'prijs' => '17.95', 'volgorde' => 4,
    'kort' => 'Voor fijn schuim in vijftien seconden.',
    'acf'  => array( 'kaart_omschrijving' => 'Voor fijn schuim in vijftien seconden.', 'kaart_label' => 'los', 'kaart_meta' => 'Gereedschap' ),
) );
$ids['zeefje'] = kj_seed_product( 'zeefje', array(
    'naam' => 'Zeefje', 'foto' => $img['zeefje'], 'categorie' => 'Gereedschap', 'prijs' => '8.95', 'volgorde' => 5,
    'kort' => 'Geen klontjes meer in je kom.',
    'acf'  => array( 'kaart_omschrijving' => 'Geen klontjes meer in je kom.', 'kaart_label' => 'los', 'kaart_meta' => 'Gereedschap' ),
) );
$ids['kom'] = kj_seed_product( 'matchakom', array(
    'naam' => 'Matchakom', 'foto' => $img['kom'], 'categorie' => 'Gereedschap', 'prijs' => '29.95', 'volgorde' => 6,
    'kort' => 'Handgemaakt, breed genoeg om goed te kloppen.',
    'acf'  => array( 'kaart_omschrijving' => 'Handgemaakt, breed genoeg om goed te kloppen.', 'kaart_label' => 'los', 'kaart_meta' => 'Gereedschap' ),
) );
$ids['basis'] = kj_seed_product( 'basisstarterset', array(
    'naam' => 'Basisstarterset', 'foto' => $img['starterset'], 'categorie' => 'Startersets', 'prijs' => '39.95', 'volgorde' => 2,
    'kort' => 'Matcha, klopper en zeefje. Alles voor je eerste kop.',
    'acf'  => array( 'kaart_omschrijving' => 'Matcha, klopper en zeefje. Alles voor je eerste kop.', 'kaart_label' => 'set', 'kaart_meta' => '3 delen' ),
) );
$ids['complete'] = kj_seed_product( 'complete-starterset', array(
    'naam' => 'Complete starterset', 'foto' => $img['complete_starter'], 'categorie' => 'Startersets', 'prijs' => '59.95', 'volgorde' => 3,
    'kort' => 'De basisset met een handgemaakte kom erbij.',
    'acf'  => array( 'kaart_omschrijving' => 'De basisset met een handgemaakte kom erbij.', 'kaart_label' => 'set', 'kaart_meta' => '4 delen' ),
) );

$GLOBALS['mori_products'] = $ids;
