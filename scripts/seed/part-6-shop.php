<?php
/**
 * Seed deel 6: de pagina Shop (blokken).
 */

$img  = $GLOBALS['mori_img'];
$prod = $GLOBALS['mori_products'];

$shop_id = wc_get_page_id( 'shop' );

$blokken = array(
    array(
        'acf_fc_layout' => 'pagina_intro',
        'eyebrow'       => 'Shop',
        'titel'         => 'Alles voor matcha thuis',
        'tekst'         => 'Eén matcha in drie maten, twee startersets en het gereedschap om hem te maken. Niet meer, niet minder.',
        'achtergrond'   => 'rijstpapier',
    ),
    array(
        'acf_fc_layout' => 'beeldband',
        'foto'          => $img['shop_hero'],
        'hoogte'        => 520,
    ),
    array(
        'acf_fc_layout' => 'productgrid',
        'label_alles'   => 'Alles',
        'label_aantal'  => '%d producten',
        'label_sorteren' => 'Sorteer op',
        'sorteringen'   => array(
            array( 'sleutel' => 'menu_order', 'naam' => 'Aanbevolen' ),
            array( 'sleutel' => 'price', 'naam' => 'Prijs, laag naar hoog' ),
            array( 'sleutel' => 'price-desc', 'naam' => 'Prijs, hoog naar laag' ),
            array( 'sleutel' => 'date', 'naam' => 'Nieuwste eerst' ),
        ),
        'leeg'          => 'Geen producten gevonden.',
        'achtergrond'   => 'rijstpapier',
    ),
    array(
        'acf_fc_layout' => 'keuzehulp',
        'eyebrow'       => 'Keuzehulp',
        'titel'         => 'Weet je niet wat je moet kiezen?',
        'keuzes'        => array(
            array( 'icoon' => 'lepel', 'titel' => 'Ik heb nog nooit matcha gemaakt', 'tekst' => 'Begin met de basisstarterset. Daar zit alles in voor je eerste kop.', 'knop' => array( 'title' => 'Naar de starterset', 'url' => get_permalink( $prod['basis'] ), 'target' => '' ) ),
            array( 'icoon' => 'druppel', 'titel' => 'Ik drink het al vaker', 'tekst' => 'Kies 60 of 90 gram. Hoe groter het blik, hoe lager de prijs per gram.', 'knop' => array( 'title' => 'Naar Mori Matcha', 'url' => get_permalink( $prod['matcha'] ), 'target' => '' ) ),
            array( 'icoon' => 'herhalen', 'titel' => 'Ik wil niet steeds bestellen', 'tekst' => 'Neem een abonnement met 10% korting en stop wanneer je wilt.', 'knop' => array( 'title' => 'Zo werkt het abonnement', 'url' => home_url( '/abonnement/' ), 'target' => '' ) ),
        ),
        'achtergrond'   => 'mos',
    ),
    array(
        'acf_fc_layout' => 'foto_tekst',
        'foto'          => $img['fijngemalen'],
        'foto_positie'  => 'links',
        'eyebrow'       => 'Waarom maar één',
        'titel'         => 'Liever één goede dan tien die op elkaar lijken',
        'tekst'         => 'Je hoeft niet te kiezen tussen soorten die je niet kent. Onze matcha is zacht genoeg om puur te drinken en sterk genoeg voor een latte.',
        'knop'          => array( 'title' => 'Bekijk Mori Matcha', 'url' => get_permalink( $prod['matcha'] ), 'target' => '' ),
        'achtergrond'   => 'gerst',
    ),
);

update_field( 'pagebuilder', $blokken, $shop_id );
