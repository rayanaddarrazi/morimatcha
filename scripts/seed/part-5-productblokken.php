<?php
/**
 * Seed deel 5: blokken onder de aankoop op de productpagina van Mori Matcha.
 */

$img  = $GLOBALS['mori_img'];
$prod = $GLOBALS['mori_products'];

$opzeg = 'In je account onder Abonnement. Klik op Abonnement opzeggen en bevestig. Je krijgt meteen een mail. Een levering die al onderweg is, komt nog gewoon aan.';

$stap = function ( $icoon, $waarde, $label, $uitleg ) {
    return array( 'icoon' => $icoon, 'waarde' => $waarde, 'label' => $label, 'uitleg' => $uitleg );
};

$blokken = array(
    array(
        'acf_fc_layout' => 'foto_kenmerken',
        'foto'          => $img['theeveld'],
        'foto_positie'  => 'links',
        'eyebrow'       => 'Wat er in het blik zit',
        'titel'         => 'Alleen matcha. Verder niets.',
        'intro'         => 'Geen suiker, geen smaakstoffen en geen vulmiddel. Alleen jonge theeblaadjes, langzaam gemalen tot poeder.',
        'kenmerken'     => array(
            array( 'titel' => 'Schaduwgekweekt', 'uitleg' => 'Zachter en groener blad, minder bitter' ),
            array( 'titel' => 'Steengemalen', 'uitleg' => 'Fijner dan bloem, lost goed op' ),
            array( 'titel' => 'Luchtdicht verpakt', 'uitleg' => 'Een blik dat licht en vocht tegenhoudt' ),
        ),
        'achtergrond'   => 'mos',
    ),
    array(
        'acf_fc_layout' => 'smaakprofiel',
        'eyebrow'       => 'Voor je kiest',
        'titel'         => 'Wat je wilt weten over deze matcha',
        'intro'         => 'Smaak, gebruik en bewaren. Alles wat mensen ons vragen voordat ze bestellen.',
        'profiel'       => array(
            'label'   => 'Smaak',
            'kop'     => 'Zacht en romig',
            'tekst'   => 'Een frisse, groene smaak met een ronde afdronk. Bitter wordt hij alleen als het water te heet is.',
            'schalen' => array(
                array( 'kenmerk' => 'Intensiteit', 'links' => 'Mild', 'rechts' => 'Krachtig', 'waarde' => 2 ),
                array( 'kenmerk' => 'Textuur', 'links' => 'Romig', 'rechts' => 'Fris en grassig', 'waarde' => 2 ),
                array( 'kenmerk' => 'Bitterheid', 'links' => 'Laag', 'rechts' => 'Hoog', 'waarde' => 1 ),
                array( 'kenmerk' => 'Kleur', 'links' => 'Gedempt', 'rechts' => 'Helder groen', 'waarde' => 4 ),
            ),
            'feiten'  => array(
                array( 'label' => 'Lekker als', 'waarde' => 'Puur of met melk' ),
                array( 'label' => 'Per kop', 'waarde' => '2 gram, 70 ml water' ),
                array( 'label' => 'Water', 'waarde' => '80 °C, niet heter' ),
                array( 'label' => 'Bewaren', 'waarde' => 'Dicht, koel en donker' ),
            ),
        ),
        'vragen'        => array(
            array( 'vraag' => 'Wat zit er in het blik?', 'antwoord' => 'Alleen matcha: fijngemalen groene thee. Geen suiker, geen smaakstoffen, niets anders.' ),
            array( 'vraag' => 'Hoe lang doe ik met een blik?', 'antwoord' => 'Reken op 2 gram per kop. Een blik van 30 gram is dan ongeveer 15 koppen, 60 gram ongeveer 30 koppen en 90 gram ongeveer 45 koppen.' ),
            array( 'vraag' => 'Hoe bewaar ik hem?', 'antwoord' => 'Luchtdicht, droog en uit de zon, dus gewoon in het blik met de dop erop. Zo blijven kleur en smaak het langst goed.' ),
            array( 'vraag' => 'Waarom smaakt matcha soms bitter?', 'antwoord' => 'Meestal door te heet water. Laat gekookt water 5 minuten afkoelen tot ongeveer 80 °C. Ook een te grote hoeveelheid poeder kan bitter smaken, dus begin met 2 gram.' ),
            array( 'vraag' => 'Kan ik hem ook in een latte gebruiken?', 'antwoord' => 'Ja. Klop 2 gram matcha met 30 ml water van 80 °C tot een gladde pasta. Giet er daarna 150 ml warme melk bij. Havermelk of koemelk werkt allebei, laat de melk niet koken.' ),
        ),
        'achtergrond'   => 'wit',
        'anker'         => 'voor-je-kiest',
    ),
    array(
        'acf_fc_layout' => 'bereiding',
        'eyebrow'       => 'Bereiden',
        'titel'         => 'Zo maak je hem',
        'intro'         => 'Klaar in een minuut. Je hebt een kom, een bamboe klopper en een zeefje nodig.',
        'tip'           => 'Te sterk of te slap? Neem de volgende keer iets meer of minder poeder. Er is geen fout antwoord.',
        'varianten'     => array(
            array( 'naam' => 'Puur', 'stappen' => array(
                $stap( 'lepel', '2 g', 'Matcha', 'Eén volle theelepel. Zeef hem in je kom, dan krijg je geen klontjes.' ),
                $stap( 'temperatuur', '80 °C', 'Water', 'Laat gekookt water 5 minuten afkoelen. Heter maakt hem bitter.' ),
                $stap( 'druppel', '70 ml', 'Hoeveelheid', 'Ongeveer een half kopje water op de matcha.' ),
                $stap( 'klok', '15 sec', 'Kloppen', 'Snel in een M-vorm, tot er een laagje fijn schuim op staat.' ),
            ) ),
            array( 'naam' => 'Met melk', 'stappen' => array(
                $stap( 'lepel', '2 g', 'Matcha', 'Eén volle theelepel. Zeef hem in je kom, dan krijg je geen klontjes.' ),
                $stap( 'temperatuur', '30 ml', 'Water van 80 °C', 'Klop er eerst een gladde pasta van, zonder klontjes.' ),
                $stap( 'druppel', '150 ml', 'Warme melk', 'Havermelk of koemelk, allebei werkt goed. Niet laten koken.' ),
                $stap( 'klok', 'Samen', 'Schenken', 'Eerst de melk in je glas, dan de matcha er langzaam over.' ),
            ) ),
        ),
    ),
    array(
        'acf_fc_layout' => 'starterset',
        'product'       => $prod['basis'],
        'foto'          => $img['starterset'],
        'eyebrow'       => 'Nog geen klopper?',
        'titel'         => 'Maak het compleet',
        'intro'         => 'Met een bamboe klopper en een zeefje maak je schuim zonder klontjes. Samen goedkoper dan los.',
        'onderdelen'    => array(
            array( 'product' => $prod['matcha'], 'detail' => '30 gram, ± 15 koppen' ),
            array( 'product' => $prod['klopper'], 'detail' => '100 tanden, met houder' ),
            array( 'product' => $prod['zeefje'], 'detail' => 'Tegen klontjes in je kom' ),
        ),
        'knoptekst'     => 'Set in winkelwagen',
        'knop_actie'    => 'winkelwagen',
        'compact'       => 1,
        'knop_tekstlink' => array( 'title' => 'Alle startersets', 'url' => add_query_arg( 'categorie', 'startersets', wc_get_page_permalink( 'shop' ) ), 'target' => '' ),
        'achtergrond'   => 'gerst',
    ),
    array(
        'acf_fc_layout' => 'vragen',
        'eyebrow'       => 'Abonnement',
        'titel'         => 'Twijfel je over het abonnement?',
        'intro'         => 'Je zit nergens aan vast. Dit zijn de vragen die we het vaakst krijgen.',
        'toon_contact'  => 0,
        'knop'          => array( 'title' => 'Zo werkt het abonnement', 'url' => home_url( '/abonnement/' ), 'target' => '' ),
        'vragen'        => array(
            array( 'vraag' => 'Hoe zeg ik mijn abonnement op?', 'antwoord' => $opzeg ),
            array( 'vraag' => 'Kan ik een maand overslaan?', 'antwoord' => 'Ja. Pauzeer je abonnement in je account voor 1, 2 of 3 maanden. In die tijd wordt er niets verstuurd of afgeschreven.' ),
            array( 'vraag' => 'Kan ik later een andere maat kiezen?', 'antwoord' => 'Ja. In je account onder Abonnement kies je een andere maat. Je betaalt dan de prijs van die maat bij de eerstvolgende levering.' ),
            array( 'vraag' => 'Wanneer wordt er betaald?', 'antwoord' => 'Bij elke levering. Je betaalt dus nooit vooruit voor blikken die je nog niet hebt.' ),
        ),
        'achtergrond'   => 'rijstpapier',
        'anker'         => 'over-het-abonnement',
    ),
);

update_field( 'productblokken', $blokken, $prod['matcha'] );
