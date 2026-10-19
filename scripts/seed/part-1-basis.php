<?php
/**
 * Seed deel 1: site- en WooCommerce-instellingen, afbeeldingen, categorieën.
 */

// Algemeen
update_option( 'blogname', 'Mori Matcha' );
update_option( 'blogdescription', 'Matcha voor thuis' );
update_option( 'woocommerce_currency', 'EUR' );
update_option( 'woocommerce_weight_unit', 'g' );
update_option( 'woocommerce_default_country', 'NL' );
update_option( 'woocommerce_price_decimal_sep', ',' );
update_option( 'woocommerce_price_thousand_sep', '.' );
update_option( 'woocommerce_calc_taxes', 'no' );
update_option( 'woocommerce_prices_include_tax', 'yes' );
update_option( 'woocommerce_enable_reviews', 'no' );
update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' );
update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
update_option( 'woocommerce_cart_redirect_after_add', 'no' );
update_option( 'woocommerce_enable_ajax_add_to_cart', 'no' );
update_option( 'users_can_register', 1 );
update_option( 'blog_public', 0 );
update_option( 'woocommerce_coming_soon', 'no' );
update_option( 'woocommerce_store_pages_only', 'no' );

// Standaardinhoud weg
foreach ( array( 1, 2 ) as $default_id ) {
    $post = get_post( $default_id );
    if ( $post && in_array( $post->post_title, array( 'Hello world!', 'Sample Page', 'Voorbeeldpagina' ), true ) ) {
        wp_delete_post( $default_id, true );
    }
}

// Categorieën
foreach ( array( 'Matcha', 'Startersets', 'Gereedschap' ) as $naam ) {
    if ( ! term_exists( $naam, 'product_cat' ) ) {
        wp_insert_term( $naam, 'product_cat' );
    }
}
$uncat = get_term_by( 'slug', 'uncategorized', 'product_cat' );
if ( $uncat ) {
    wp_delete_term( $uncat->term_id, 'product_cat' );
}

// Afbeeldingen (inhoud)
$GLOBALS['mori_img'] = array(
    'hero'              => kj_seed_image( 'hero-desktop', 'Een kom matcha met een bamboe klopper op een houten tafel bij een raam' ),
    'hero_mobiel'       => kj_seed_image( 'hero-mobiel', 'Een kom matcha op een houten tafel, staand bijgesneden' ),
    'over_mori'         => kj_seed_image( 'over-mori', 'Een hand schept matcha met een bamboe lepeltje uit een groen blik naast een kom' ),
    'productfoto'       => kj_seed_image( 'product-mori-matcha', 'Groen blik Mori Matcha op een linnen doek' ),
    'theeveld'          => kj_seed_image( 'herkomst-theeveld', 'Theevelden met schaduwdoeken in de bergen, in de ochtendmist' ),
    'jong_blad'         => kj_seed_image( 'herkomst-jong-blad', 'Jong theeblad vlak voor de oogst' ),
    'fijngemalen'       => kj_seed_image( 'herkomst-fijngemalen', 'Fijngemalen matcha op een donker schaaltje met een bamboe lepel' ),
    'recept_puur'       => kj_seed_image( 'recept-puur', 'Matcha puur in een kom met een bamboe klopper' ),
    'recept_melk'       => kj_seed_image( 'recept-warme-melk', 'Matcha met warme melk' ),
    'recept_ijs'        => kj_seed_image( 'recept-ijskoud', 'IJskoude matcha latte in een glas' ),
    'ritueel'           => kj_seed_image( 'ritueel', 'Matcha klaarmaken met een bamboe klopper in een kom' ),
    'starterset'        => kj_seed_image( 'product-basisstarterset', 'Klopper, zeefje en kom met matcha, de basisstarterset' ),
    'abonnement'        => kj_seed_image( 'abonnement', 'Een blik Mori Matcha op een tafel, klaar om elke maand te bezorgen' ),
    'shop_hero'         => kj_seed_image( 'shop-hero', 'Matchagereedschap op een houten plank' ),
    'complete_starter'  => kj_seed_image( 'product-complete-starterset', 'De complete starterset met handgemaakte kom' ),
    'klopper'           => kj_seed_image( 'product-klopper', 'Bamboe matchaklopper' ),
    'zeefje'            => kj_seed_image( 'product-zeefje', 'Zeefje voor matcha' ),
    'kom'               => kj_seed_image( 'product-matchakom', 'Handgemaakte matchakom' ),
    'galerij_2'         => kj_seed_image( 'product-galerij-2', 'Matcha in een kom naast een blik' ),
);

// Logo's en betaallogo's (themabeelden, te vervangen in Instellingen Mori)
$GLOBALS['mori_img']['logo_volledig_zwart'] = kj_seed_theme_image( 'logo-zwart.png', 'Mori Matcha' );
$GLOBALS['mori_img']['logo_volledig_wit']   = kj_seed_theme_image( 'logo-wit.png', 'Mori Matcha' );
$GLOBALS['mori_img']['logo_woordmerk_zwart'] = kj_seed_theme_image( 'logo-woordmerk-zwart.png', 'Mori Matcha' );
$GLOBALS['mori_img']['logo_woordmerk_wit']   = kj_seed_theme_image( 'logo-woordmerk-wit.png', 'Mori Matcha' );
$GLOBALS['mori_img']['betaal_ideal']       = kj_seed_theme_image( 'betaal-ideal.png', 'iDEAL' );
$GLOBALS['mori_img']['betaal_bancontact']  = kj_seed_theme_image( 'betaal-bancontact.png', 'Bancontact' );
$GLOBALS['mori_img']['betaal_visa']        = kj_seed_theme_image( 'betaal-visa.png', 'Visa' );
$GLOBALS['mori_img']['betaal_mastercard']  = kj_seed_theme_image( 'betaal-mastercard.png', 'Mastercard' );

// Site-icoon: het teken 森 komt als tekst op het etiket; voor het favicon een eenvoudig SVG-icoon uit het thema is niet nodig.
