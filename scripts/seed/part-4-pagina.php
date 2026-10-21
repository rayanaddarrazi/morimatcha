<?php
/**
 * Seed deel 4: pagina's (Home), menu's en WooCommerce-pagina's.
 */

$img  = $GLOBALS['mori_img'];
$prod = $GLOBALS['mori_products'];

/** Maak of vind een pagina op slug. */
function kj_seed_page( $slug, $titel, $extra = array() ) {
    $found = get_posts( array( 'post_type' => 'page', 'name' => $slug, 'post_status' => 'any', 'numberposts' => 1 ) );
    $args  = array_merge( array( 'post_title' => $titel, 'post_name' => $slug, 'post_status' => 'publish', 'post_type' => 'page', 'post_content' => '' ), $extra );
    if ( $found ) {
        $args['ID'] = $found[0]->ID;
        wp_update_post( $args );
        return $found[0]->ID;
    }
    return wp_insert_post( $args );
}

// WooCommerce-pagina's: Nederlandse titels
foreach ( array( 'shop' => 'Shop', 'cart' => 'Winkelwagen', 'checkout' => 'Afrekenen', 'my-account' => 'Mijn account', 'refund_returns' => 'Verzending en retour' ) as $slug => $titel ) {
    $found = get_posts( array( 'post_type' => 'page', 'name' => $slug, 'post_status' => 'any', 'numberposts' => 1 ) );
    if ( $found ) {
        wp_update_post( array( 'ID' => $found[0]->ID, 'post_title' => $titel ) );
    }
}
$privacy = get_posts( array( 'post_type' => 'page', 'name' => 'privacy-policy', 'post_status' => 'any', 'numberposts' => 1 ) );
if ( $privacy ) {
    wp_delete_post( $privacy[0]->ID, true );
}

$shop_url = wc_get_page_permalink( 'shop' );
$product_url = get_permalink( $prod['matcha'] );

// Home
$home_id = kj_seed_page( 'home', 'Home' );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );

$kenmerken = array( 'Schaduwgekweekt', 'Steengemalen', 'Alleen matcha, verder niets', 'Gratis verzending vanaf € 40', 'Stoppen wanneer je wilt', 'In 1 tot 2 werkdagen verstuurd' );

$home = array(
    array(
        'acf_fc_layout'  => 'hero',
        'foto'           => $img['hero'],
        'foto_mobiel'    => $img['hero_mobiel'],
        'eyebrow'        => 'Matcha voor thuis',
        'titel'          => 'Matcha zonder gedoe.',
        'tekst'          => 'Eén theelepel, 70 ml water en vijftien seconden kloppen. Zo maak je thuis een kop die niet bitter smaakt.',
        'knop_primair'   => array( 'title' => 'Kies je matcha', 'url' => $product_url, 'target' => '' ),
        'knop_secundair' => array( 'title' => 'Zo maak je hem', 'url' => home_url( '/bereiden/' ), 'target' => '' ),
        'anker'          => '',
    ),
    array(
        'acf_fc_layout' => 'kenmerken',
        'items'         => array_map( fn( $t ) => array( 'tekst' => $t ), $kenmerken ),
        'achtergrond'   => 'mos',
        'anker'         => '',
    ),
    array(
        'acf_fc_layout' => 'over_mori',
        'foto'          => $img['over_mori'],
        'foto_positie'  => 'links',
        'eyebrow'       => 'Over Mori',
        'titel'         => 'Eén matcha, goed gekozen en helder uitgelegd.',
        'tekst'         => 'Matcha thuis valt vaak tegen. Te bitter, vol klontjes of veel te duur. Daarom kozen we één soort die zacht genoeg is om puur te drinken, en leggen we stap voor stap uit hoe je hem maakt.',
        'beloftes'      => array(
            array( 'titel' => 'Zacht van smaak', 'uitleg' => 'Schaduwgekweekt blad. Niet bitter, zolang het water niet te heet is.' ),
            array( 'titel' => 'Eerlijk geprijsd', 'uitleg' => 'Je ziet altijd de prijs per gram. Een groter blik is voordeliger.' ),
            array( 'titel' => 'Nergens aan vast', 'uitleg' => 'Een abonnement pauzeer of stop je zelf, met één klik.' ),
        ),
        'knop'          => array( 'title' => 'Lees hoe we werken', 'url' => home_url( '/bereiden/' ), 'target' => '' ),
        'achtergrond'   => 'rijstpapier',
        'anker'         => 'over-mori',
    ),
    array(
        'acf_fc_layout' => 'productsplit',
        'product'       => $prod['matcha'],
        'foto'          => $img['productfoto'],
        'toon_etiket'   => 1,
        'foto_positie'  => 'links',
        'eyebrow'       => 'Onze matcha',
        'tekst'         => 'Een frisse groene smaak met een romige afdronk. Lekker puur, met warme melk of op ijs. Eén soort, in drie maten.',
        'tekst_mobiel'  => 'Een frisse groene smaak met een romige afdronk. Lekker puur, met warme melk of op ijs.',
        'knoptekst'     => 'Kies je maat',
        'knop_tekstlink' => array( 'title' => 'Of neem een abonnement', 'url' => '#abonnement', 'target' => '' ),
        'achtergrond'   => 'wit',
        'anker'         => 'matcha',
    ),
    array(
        'acf_fc_layout' => 'herkomst',
        'eyebrow'       => 'Herkomst',
        'titel'         => 'Van de struik tot je kom',
        'intro'         => 'Matcha is geen gewone groene thee. Het hele blad wordt gemalen, dus je drinkt alles mee.',
        'stappen'       => array(
            array( 'titel' => 'In de schaduw', 'uitleg' => 'De laatste weken voor de oogst staan de struiken onder doeken. Het blad wordt daardoor zachter en groener.' ),
            array( 'titel' => 'Gestoomd en gedroogd', 'uitleg' => 'Direct na het plukken wordt het blad kort gestoomd. Daarna gaan de nerven en steeltjes eruit.' ),
            array( 'titel' => 'Langzaam gemalen', 'uitleg' => 'Tussen stenen wordt het blad gemalen tot een poeder dat fijner is dan bloem.' ),
        ),
        'foto_groot'    => $img['theeveld'],
        'fotos'         => array(
            array( 'foto' => $img['jong_blad'], 'bijschrift' => 'Jong blad, vlak voor de oogst' ),
            array( 'foto' => $img['fijngemalen'], 'bijschrift' => 'Fijngemalen, verder niets toegevoegd' ),
        ),
        'achtergrond'   => 'mos',
        'anker'         => 'herkomst',
    ),
    array(
        'acf_fc_layout' => 'recepten',
        'eyebrow'       => 'Drie manieren',
        'titel'         => 'Puur, warm of op ijs',
        'intro'         => 'Dezelfde matcha, drie keer anders. Elk recept is klaar in een paar minuten.',
        'intro_mobiel'  => 'Dezelfde matcha, drie keer anders.',
        'knop'          => array( 'title' => 'Alle recepten', 'url' => home_url( '/bereiden/' ), 'target' => '' ),
        'recepten'      => array(
            array( 'foto' => $img['recept_puur'], 'tijd' => '1 minuut', 'titel' => 'Puur', 'uitleg' => 'Twee gram matcha en 70 ml water van 80 °C. Kloppen tot er een laagje schuim op staat.', 'uitleg_mobiel' => '2 gram matcha en 70 ml water van 80 °C.', 'link' => array( 'title' => 'Bekijk recept', 'url' => home_url( '/bereiden/' ), 'target' => '' ) ),
            array( 'foto' => $img['recept_melk'], 'tijd' => '3 minuten', 'titel' => 'Met warme melk', 'uitleg' => 'Klop eerst een pasta met een scheutje water. Schenk er daarna 200 ml warme melk bij.', 'uitleg_mobiel' => 'Een pasta kloppen, daarna warme melk erbij.', 'link' => array( 'title' => 'Bekijk recept', 'url' => home_url( '/bereiden/' ), 'target' => '' ) ),
            array( 'foto' => $img['recept_ijs'], 'tijd' => '2 minuten', 'titel' => 'IJskoud', 'uitleg' => 'Klop de matcha los in koud water en schenk hem over melk met veel ijs.', 'uitleg_mobiel' => 'Over melk met veel ijs.', 'link' => array( 'title' => 'Bekijk recept', 'url' => home_url( '/bereiden/' ), 'target' => '' ) ),
        ),
        'achtergrond'   => 'rijstpapier',
        'anker'         => 'recepten',
    ),
    array(
        'acf_fc_layout' => 'fotoband',
        'foto'          => $img['ritueel'],
        'eyebrow'       => 'Elke ochtend',
        'titel'         => 'Eén minuut voor jezelf.',
        'tekst'         => 'Afmeten, kloppen, drinken. Zo wordt matcha een vast moment op je dag in plaats van nog een klusje.',
        'tekst_mobiel'  => 'Afmeten, kloppen, drinken. Zo wordt matcha een vast moment op je dag.',
        'uitsnede_mobiel' => 'rechts',
        'knop'          => array( 'title' => 'Zo maak je hem', 'url' => home_url( '/bereiden/' ), 'target' => '' ),
        'parallax'      => 1,
        'anker'         => '',
    ),
    array(
        'acf_fc_layout' => 'starterset',
        'product'       => $prod['basis'],
        'foto'          => $img['starterset'],
        'eyebrow'       => 'Nieuw met matcha',
        'titel'         => 'De starterset',
        'intro'         => 'Nog nooit matcha gemaakt? In de basisstarterset zit precies wat je nodig hebt, en niets meer.',
        'intro_mobiel'  => 'Nog nooit matcha gemaakt? Hierin zit precies wat je nodig hebt, en niets meer.',
        'onderdelen'    => array(
            array( 'product' => $prod['matcha'], 'detail' => '30 gram, ± 15 koppen' ),
            array( 'product' => $prod['klopper'], 'detail' => '100 tanden, met houder' ),
            array( 'product' => $prod['zeefje'], 'detail' => 'Tegen klontjes in je kom' ),
        ),
        'knoptekst'     => 'Bekijk de starterset',
        'knop_tekstlink' => array( 'title' => 'Alle startersets', 'url' => add_query_arg( 'categorie', 'startersets', $shop_url ), 'target' => '' ),
        'achtergrond'   => 'gerst',
        'anker'         => 'startersets',
    ),
    array(
        'acf_fc_layout' => 'abonnement',
        'foto'          => $img['abonnement'],
        'toon_etiket'   => 1,
        'foto_positie'  => 'rechts',
        'eyebrow'       => 'Abonnement',
        'titel'         => 'Elke maand een blik. Stoppen kan altijd.',
        'intro'         => 'Je hoeft niet meer te onthouden wanneer je bijna op bent. Je betaalt per levering en regelt alles zelf.',
        'stappen'       => array(
            array( 'titel' => 'Kies je maat', 'uitleg' => '30, 60 of 90 gram, met 10% korting op elke levering', 'uitleg_mobiel' => '30, 60 of 90 gram, met 10% korting' ),
            array( 'titel' => 'Elke maand in huis', 'uitleg' => 'Rond dezelfde datum, je betaalt pas bij verzending', 'uitleg_mobiel' => 'Je betaalt pas bij verzending' ),
            array( 'titel' => 'Pauzeren of stoppen', 'uitleg' => 'In je account, met één klik. Zonder opzegtermijn', 'uitleg_mobiel' => 'In je account, zonder opzegtermijn' ),
        ),
        'knop_primair'  => array( 'title' => 'Start een abonnement', 'url' => $product_url, 'target' => '' ),
        'knop_secundair' => array( 'title' => 'Zo zeg je op', 'url' => home_url( '/abonnement/' ), 'target' => '' ),
        'achtergrond'   => 'mos',
        'anker'         => 'abonnement',
    ),
    array(
        'acf_fc_layout' => 'vragen',
        'eyebrow'       => 'Vragen',
        'titel'         => 'Vragen die we vaak krijgen',
        'intro'         => 'Staat je vraag er niet bij? Mail ons gerust.',
        'toon_contact'  => 1,
        'vragen'        => array(
            array( 'vraag' => 'Hoe zeg ik mijn abonnement op?', 'antwoord' => 'In je account onder Abonnement. Klik op Abonnement opzeggen en bevestig. Je krijgt meteen een mail. Een levering die al onderweg is, komt nog gewoon aan.' ),
            array( 'vraag' => 'Smaakt matcha bitter?', 'antwoord' => 'Deze niet snel. Mori Matcha is zacht en romig en heeft weinig bitter. Bitter wordt hij alleen als het water te heet is, dus laat gekookt water 5 minuten afkoelen tot ongeveer 80 °C.' ),
            array( 'vraag' => 'Hoeveel matcha gebruik ik per kop?', 'antwoord' => 'Twee gram, dat is één volle theelepel. Daar gaat ongeveer 70 ml water bij, een half kopje. Is hij te sterk of te slap? Neem de volgende keer iets minder of meer poeder.' ),
            array( 'vraag' => 'Hoe lang blijft een blik goed?', 'antwoord' => 'De houdbaarheidsdatum staat op de verpakking. Bewaar hem luchtdicht, droog en uit de zon, dan blijven kleur en smaak het langst goed.' ),
            array( 'vraag' => 'Kan ik eerst een klein blik proberen?', 'antwoord' => 'Ja, dat kan. Het kleinste blik is 30 gram voor € 19,95, goed voor ongeveer 15 koppen. Wil je meteen alles hebben? In de basisstarterset zitten matcha, klopper en zeefje.' ),
            array( 'vraag' => 'Hoe kan ik betalen?', 'antwoord' => 'Met iDEAL, Bancontact, Visa of Mastercard. Betaal je liever per bankoverschrijving? Dan krijg je de gegevens per mail en versturen we zodra het bedrag binnen is.' ),
        ),
        'achtergrond'   => 'rijstpapier',
        'anker'         => 'vragen',
    ),
);

update_field( 'pagebuilder', $home, $home_id );

// Menu's
function kj_seed_menu( $naam, $items ) {
    // item: [titel, url, korte titel (mobiel), css-klasse]
    $menu = wp_get_nav_menu_object( $naam );
    $id   = $menu ? $menu->term_id : wp_create_nav_menu( $naam );
    foreach ( (array) wp_get_nav_menu_items( $id ) as $oud ) {
        wp_delete_post( $oud->ID, true );
    }
    foreach ( $items as $i => $item ) {
        wp_update_nav_menu_item( $id, 0, array(
            'menu-item-title'  => $item[0],
            'menu-item-url'    => $item[1],
            'menu-item-description' => $item[2] ?? '',
            'menu-item-classes' => $item[3] ?? '',
            'menu-item-status' => 'publish',
            'menu-item-type'   => 'custom',
            'menu-item-position' => $i + 1,
        ) );
    }
    return $id;
}

$locations = array(
    'hoofdmenu'   => kj_seed_menu( 'Hoofdmenu', array(
        array( 'Shop', $shop_url, 'Matcha, startersets en gereedschap' ),
        array( 'Matcha', $product_url, 'Zacht en romig, voor puur of met melk' ),
        array( 'Abonnement', home_url( '/abonnement/' ), 'Elke maand een blik, 10% korting' ),
        array( 'Bereiden', home_url( '/bereiden/' ), 'Klaar in een minuut' ),
    ) ),
    'footer_shop' => kj_seed_menu( 'Footer: Shop', array(
        array( 'Matcha', $product_url ),
        array( 'Startersets', add_query_arg( 'categorie', 'startersets', $shop_url ) ),
        array( 'Accessoires', add_query_arg( 'categorie', 'gereedschap', $shop_url ), '', 'max-lg:hidden' ),
        array( 'Abonnement', home_url( '/abonnement/' ) ),
    ) ),
    'footer_hulp' => kj_seed_menu( 'Footer: Hulp', array(
        array( 'Bereiden', home_url( '/bereiden/' ) ),
        array( 'Verzending en retour', home_url( '/refund_returns/' ), 'Verzending' ),
        array( 'Abonnement beheren', wc_get_page_permalink( 'myaccount' ), '', 'max-lg:hidden' ),
        array( 'Contact', 'mailto:' . get_field( 'contact_mail', 'option' ) ),
    ) ),
    'footer_mori' => kj_seed_menu( 'Footer: Mori', array(
        array( 'Over Mori', home_url( '/#over-mori' ) ),
        array( 'Herkomst', home_url( '/#herkomst' ) ),
        array( 'Veelgestelde vragen', home_url( '/#vragen' ) ),
    ) ),
);
set_theme_mod( 'nav_menu_locations', $locations );

$GLOBALS['mori_pages'] = array( 'home' => $home_id );
