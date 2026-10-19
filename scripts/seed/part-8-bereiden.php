<?php
/**
 * Seed deel 8: de pagina Bereiden.
 */

$img  = $GLOBALS['mori_img'];
$prod = $GLOBALS['mori_products'];

$id = kj_seed_page( 'bereiden', 'Bereiden' );
$bereiden_url = home_url( '/bereiden/' );

$stap = function ( $icoon, $waarde, $label, $uitleg ) {
    return array( 'icoon' => $icoon, 'waarde' => $waarde, 'label' => $label, 'uitleg' => $uitleg );
};

$blokken = array(
    array(
        'acf_fc_layout'  => 'hero',
        'foto'           => $img['galerij_2'],
        'eyebrow'        => 'Bereiden',
        'titel'          => 'Zo maak je matcha',
        'titelgrootte'   => 'display',
        'tekst'          => 'Je hebt geen ervaring nodig. Wel een kom, een klopper, een zeefje en water dat net niet kookt.',
        'knop_primair'   => array( 'title' => 'Naar de stappen', 'url' => '#stappen', 'target' => '' ),
        'knop_secundair' => array( 'title' => 'Wat je nodig hebt', 'url' => '#nodig', 'target' => '' ),
        'hoogte'         => 820,
        'foto_zoom'      => 1.39,
        'foto_focus'     => 'onder-links',
        'foto_stijl'     => 'vol',
    ),
    array(
        'acf_fc_layout' => 'feiten',
        'items'         => array(
            array( 'waarde' => '1 minuut', 'uitleg' => 'van poeder tot kop' ),
            array( 'waarde' => '80 °C', 'uitleg' => 'niet heter, anders bitter' ),
            array( 'waarde' => '2 gram', 'uitleg' => 'één volle theelepel' ),
            array( 'waarde' => '70 ml', 'uitleg' => 'ongeveer een half kopje' ),
        ),
        'achtergrond'   => 'mos',
    ),
    array(
        'acf_fc_layout' => 'bereiding',
        'eyebrow'       => 'Bereiden',
        'titel'         => 'Zo maak je hem',
        'intro'         => 'Klaar in een minuut. Je hebt een kom, een bamboe klopper en een zeefje nodig.',
        'tip'           => 'Te sterk of te slap? Neem de volgende keer iets meer of minder poeder. Er is geen fout antwoord.',
        'weergave'      => 'gestapeld',
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
        'achtergrond'   => 'rijstpapier',
        'anker'         => 'stappen',
    ),
    array(
        'acf_fc_layout' => 'benodigdheden',
        'eyebrow'       => 'Gereedschap',
        'titel'         => 'Wat je nodig hebt',
        'intro'         => 'Alles los te koop, of samen in de basisstarterset voor € 39,95.',
        'knop'          => array( 'title' => 'Bekijk de starterset', 'url' => get_permalink( $prod['basis'] ), 'target' => '' ),
        'items'         => array(
            array( 'product' => $prod['matcha'], 'uitleg' => '2 gram per kop', 'prijs_voorvoegsel' => 'vanaf', 'toon_etiket' => 1 ),
            array( 'product' => $prod['klopper'], 'uitleg' => 'Voor fijn schuim', 'prijs_voorvoegsel' => '', 'toon_etiket' => 0 ),
            array( 'product' => $prod['zeefje'], 'uitleg' => 'Tegen klontjes', 'prijs_voorvoegsel' => '', 'toon_etiket' => 0 ),
            array( 'product' => $prod['kom'], 'uitleg' => 'Breed genoeg om te kloppen', 'prijs_voorvoegsel' => '', 'toon_etiket' => 0 ),
        ),
        'achtergrond'   => 'rijstpapier',
        'anker'         => 'nodig',
    ),
    array(
        'acf_fc_layout'   => 'fouten',
        'eyebrow'         => 'Als het niet lukt',
        'titel'           => 'Drie dingen die vaak misgaan',
        'intro'           => 'En hoe je het de volgende keer goed doet.',
        'foto'            => $img['zeefje'],
        'label_oorzaak'   => 'Oorzaak',
        'label_oplossing' => 'Zo los je het op',
        'fouten'          => array(
            array( 'probleem' => 'Hij smaakt bitter', 'oorzaak' => 'Het water was te heet.', 'oplossing' => 'Laat gekookt water 5 minuten afkoelen, tot ongeveer 80 °C.' ),
            array( 'probleem' => 'Er zitten klontjes in', 'oorzaak' => 'Het poeder ging er los in.', 'oplossing' => 'Zeef de matcha eerst in je kom. Dat duurt vijf seconden.' ),
            array( 'probleem' => 'Er komt geen schuim', 'oorzaak' => 'Er werd te rustig geroerd.', 'oplossing' => 'Klop snel heen en weer in een M-vorm, niet in rondjes.' ),
        ),
        'achtergrond'     => 'rijstpapier',
    ),
    array(
        'acf_fc_layout' => 'recepten',
        'eyebrow'       => 'Recepten',
        'titel'         => 'Drie manieren om hem te drinken',
        'intro'         => 'Elk recept begint met dezelfde basis. Daarna kies je zelf.',
        'foto_hoogte'   => 480,
        'recepten'      => array(
            array( 'foto' => $img['recept_puur'], 'tijd' => '1 minuut', 'titel' => 'Puur', 'uitleg' => 'Twee gram matcha en 70 ml water van 80 °C. Kloppen tot er een laagje schuim op staat.', 'link' => array( 'title' => 'Bekijk recept', 'url' => '#stappen', 'target' => '' ) ),
            array( 'foto' => $img['recept_melk'], 'tijd' => '3 minuten', 'titel' => 'Met warme melk', 'uitleg' => 'Klop eerst een pasta met een scheutje water. Schenk er daarna 200 ml warme melk bij.', 'link' => array( 'title' => 'Bekijk recept', 'url' => '#stappen', 'target' => '' ) ),
            array( 'foto' => $img['recept_ijs'], 'tijd' => '2 minuten', 'titel' => 'IJskoud', 'uitleg' => 'Klop de matcha los in koud water en schenk hem over melk met veel ijs.', 'link' => array( 'title' => 'Bekijk recept', 'url' => '#stappen', 'target' => '' ) ),
        ),
        'achtergrond'   => 'gerst',
        'ruimte'        => '144',
    ),
);

update_field( 'pagebuilder', $blokken, $id );
$GLOBALS['mori_pages']['bereiden'] = $id;
