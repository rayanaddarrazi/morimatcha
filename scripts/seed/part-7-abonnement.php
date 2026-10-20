<?php
/**
 * Seed deel 7: de pagina Abonnement.
 */

$img  = $GLOBALS['mori_img'];
$prod = $GLOBALS['mori_products'];

$id = kj_seed_page( 'abonnement', 'Abonnement' );
$product_url = get_permalink( $prod['matcha'] );

$blokken = array(
    array(
        'acf_fc_layout'  => 'hero',
        'foto'           => $img['abonnement'],
        'eyebrow'        => 'Abonnement',
        'titel'          => "Elke maand een blik.\nJij houdt de regie.",
        'titelgrootte'   => 'h1',
        'tekst'          => 'Je kiest je maat en krijgt elke maand een vers blik, met 10% korting. Pauzeren, een andere maat of stoppen regel je zelf in je account.',
        'knop_primair'   => array( 'title' => 'Kies je maat', 'url' => $product_url, 'target' => '' ),
        'knop_secundair' => array( 'title' => 'Zo zeg je op', 'url' => '#opzeggen', 'target' => '' ),
        'noot'           => 'Opzeggen kan altijd, zonder opzegtermijn.',
        'foto_stijl'     => 'rechts',
        'toon_etiket'    => 1,
    ),
    array(
        'acf_fc_layout' => 'stappen_kaarten',
        'eyebrow'       => 'In drie stappen',
        'titel'         => 'Zo werkt het',
        'stap_label'    => 'Stap',
        'stappen'       => array(
            array( 'foto' => $img['productfoto'], 'toon_etiket' => 1, 'zoom' => 1, 'titel' => 'Kies je maat', 'tekst' => '30, 60 of 90 gram. Je ziet meteen wat je per maand betaalt en hoeveel koppen je eruit haalt.' ),
            array( 'foto' => $img['abonnement'], 'toon_etiket' => 1, 'zoom' => 1.25, 'titel' => 'Elke maand in huis', 'tekst' => 'Rond dezelfde datum, met 10% korting. Je betaalt per levering, niet vooruit.' ),
            array( 'foto' => $img['recept_melk'], 'toon_etiket' => 0, 'zoom' => 1, 'titel' => 'Pauzeren of stoppen', 'tekst' => 'In je account, met één klik. Zonder te bellen, te mailen of uit te leggen waarom.' ),
        ),
        'achtergrond'   => 'wit',
        'ruimte'        => '128',
    ),
    array(
        'acf_fc_layout' => 'prijstabel',
        'eyebrow'       => 'Prijzen',
        'titel'         => 'Wat je betaalt',
        'intro'         => 'Per maand, per blik. Verzending is gratis vanaf € 40.',
        'product'       => $prod['matcha'],
        'koppen'        => array( 'maat' => 'Maat', 'eenmalig' => 'Eenmalig', 'abonnement' => 'Abonnement', 'per_gram' => 'Per gram met abonnement' ),
        'achtergrond'   => 'rijstpapier',
        'ruimte'        => '128',
    ),
    array(
        'acf_fc_layout' => 'opzeggen',
        'eyebrow'       => 'Opzeggen',
        'titel'         => 'Stoppen is net zo makkelijk als starten',
        'intro'         => 'Geen formulier, geen telefoontje en geen vragen waarom.',
        'stappen'       => array(
            array( 'tekst' => 'Log in en ga naar Abonnement' ),
            array( 'tekst' => 'Klik op Abonnement opzeggen' ),
            array( 'tekst' => 'Bevestig, je krijgt meteen een mail' ),
        ),
        'kaart'         => array(
            'label' => 'Abonnement', 'titel' => 'Mori Matcha, 60 gram', 'frequentie' => 'Elke maand een blik, 10% korting', 'status' => 'Actief',
            'volgende_label' => 'Volgende levering', 'volgende_waarde' => '12 november',
            'bedrag_label' => 'Per levering', 'bedrag_waarde' => '€ 33,25',
            'betaal_label' => 'Betaalwijze', 'betaal_waarde' => 'iDEAL',
            'uitleg_links' => 'Nog 12 dagen tot je volgende blik', 'uitleg_rechts' => 'Wordt 10 november verstuurd', 'voortgang' => 60,
            'knop_pauzeren' => 'Pauzeren', 'knop_maat' => 'Maat wijzigen', 'knop_opzeggen' => 'Abonnement opzeggen',
        ),
        'achtergrond'   => 'mos',
        'anker'         => 'opzeggen',
        'ruimte'        => '128',
    ),
    array(
        'acf_fc_layout' => 'vragen',
        'eyebrow'       => 'Vragen',
        'titel'         => 'Nog iets onduidelijk?',
        'intro'         => 'Mail ons gerust. Je krijgt binnen een werkdag antwoord.',
        'toon_contact'  => 0,
        'vragen'        => array(
            array( 'vraag' => 'Wanneer wordt er betaald?', 'antwoord' => 'Bij elke levering. Je betaalt dus nooit vooruit voor blikken die je nog niet hebt.' ),
            array( 'vraag' => 'Kan ik een maand overslaan?', 'antwoord' => 'Ja. Pauzeer je abonnement in je account voor 1, 2 of 3 maanden. In die tijd wordt er niets verstuurd of afgeschreven.' ),
            array( 'vraag' => 'Kan ik later een andere maat kiezen?', 'antwoord' => 'Ja. In je account onder Abonnement kies je een andere maat. Je betaalt dan de prijs van die maat bij de eerstvolgende levering.' ),
            array( 'vraag' => 'Wat als een maat uitverkocht is?', 'antwoord' => 'Een uitverkochte maat kun je niet kiezen, dat zie je meteen bij de maten. Kies dan een andere maat. Een blik dat groter is, is per gram goedkoper.' ),
            array( 'vraag' => 'Kan ik een abonnement afsluiten zonder account?', 'antwoord' => 'Nee, voor een abonnement maak je een account. Dat gaat in de kassa en je kiest alleen een wachtwoord. In je account pauzeer, wijzig of zeg je zelf op.' ),
        ),
        'achtergrond'   => 'wit',
        'ruimte'        => '128',
    ),
);

update_field( 'pagebuilder', $blokken, $id );
$GLOBALS['mori_pages']['abonnement'] = $id;
