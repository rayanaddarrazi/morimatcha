<?php
/**
 * Seed deel 3: Instellingen Mori (alle teksten uit Figma die op meerdere pagina's terugkomen).
 */

$img = $GLOBALS['mori_img'];

$opties = array(
    'bedrijfsnaam'          => 'Mori Matcha',
    'logo_volledig_zwart'   => $img['logo_volledig_zwart'],
    'logo_volledig_wit'     => $img['logo_volledig_wit'],
    'logo_woordmerk_zwart'  => $img['logo_woordmerk_zwart'],
    'logo_woordmerk_wit'    => $img['logo_woordmerk_wit'],
    'etiket_teken'          => '森',
    'etiket_naam'           => 'MORI',
    'etiket_regel'          => 'MATCHA  {gram} G',

    'mededeling_toon'       => 1,
    'mededelingen'          => array(
        array( 'tekst' => 'Gratis verzending vanaf € 40', 'icoon' => 'verzending' ),
        array( 'tekst' => 'In 1 tot 2 werkdagen verstuurd', 'icoon' => 'vinkje' ),
        array( 'tekst' => 'Alleen matcha, verder niets', 'icoon' => 'vinkje' ),
    ),

    'footer_nieuwsbrief_kop'         => 'Eén mail per maand. Over nieuwe oogsten en hoe je betere matcha zet.',
    'footer_nieuwsbrief_sub'         => 'Afmelden kan met één klik onderaan elke mail.',
    'footer_nieuwsbrief_placeholder' => 'Je e-mailadres',
    'footer_nieuwsbrief_knop'        => 'Aanmelden',
    'footer_nieuwsbrief_gelukt'      => 'Gelukt, je bent aangemeld.',
    'footer_nieuwsbrief_fout'        => 'Dat is niet gelukt. Controleer je e-mailadres.',
    'footer_merk_tekst'              => 'Matcha voor thuis, met duidelijke smaakinfo en een abonnement dat je zelf beheert.',
    'footer_kop_shop'                => 'Shop',
    'footer_kop_hulp'                => 'Hulp',
    'footer_kop_mori'                => 'Mori',
    'footer_copyright'               => 'Mori Matcha',
    'footer_juridisch'               => array(
        array( 'link' => array( 'title' => 'Algemene voorwaarden', 'url' => '#', 'target' => '' ), 'korte_tekst' => 'Voorwaarden' ),
        array( 'link' => array( 'title' => 'Privacyverklaring', 'url' => '#', 'target' => '' ), 'korte_tekst' => 'Privacy' ),
        array( 'link' => array( 'title' => 'Cookies', 'url' => '#', 'target' => '' ) ),
    ),
    'footer_betaalmethoden'          => array(
        array( 'naam' => 'iDEAL', 'logo' => $img['betaal_ideal'] ),
        array( 'naam' => 'Bancontact', 'logo' => $img['betaal_bancontact'] ),
        array( 'naam' => 'Visa', 'logo' => $img['betaal_visa'] ),
        array( 'naam' => 'Mastercard', 'logo' => $img['betaal_mastercard'] ),
    ),

    'gratis_verzending_vanaf'        => 40,
    'cart_titel'                     => 'Je winkelwagen',
    'cart_verzending_start'          => 'Gratis verzending vanaf € %s.',
    'cart_verzending_bijna'          => 'Nog € %s tot gratis verzending.',
    'cart_verzending_gratis'         => 'Je bestelling wordt gratis verzonden.',
    'cart_verzending_gratis_kort'    => 'Gratis',
    'cart_verzending_later'          => 'Bij het afrekenen',
    'cart_leeg'                      => 'Je winkelwagen is nog leeg.',
    'cart_leeg_knop'                 => 'Naar de shop',
    'cart_verwijder'                 => 'Verwijder',
    'cart_afrekenen'                 => 'Afrekenen',
    'cart_noot'                      => 'Je abonnement kun je daarna altijd zelf pauzeren of opzeggen in je account.',

    'gram_per_kop'                   => 2,
    'koppen_regel'                   => '± %d koppen',
    'per_gram_regel'                 => '€ %s per gram',
    'badge_voordeligst'              => 'Voordeligst',
    'badge_uitverkocht'              => 'Uitverkocht',
    'bespaar_regel'                  => 'Je bespaart %s',

    'abonnement_korting'             => 10,
    'maat_label'                     => 'Maat',
    'maat_gekozen'                   => '%s gekozen',
    'aankoop_eenmalig_titel'         => 'Eenmalig',
    'aankoop_eenmalig_uitleg'        => 'Eén blik, je zit nergens aan vast.',
    'aankoop_eenmalig_eenheid'       => 'per blik',
    'aankoop_abo_titel'              => 'Elke maand een blik',
    'aankoop_abo_badge'              => 'Bespaar %d%%',
    'aankoop_abo_uitleg'             => 'Pauzeren of opzeggen wanneer je wilt.',
    'aankoop_abo_eenheid'            => 'per maand',
    'aankoop_abo_voordelen'          => array(
        array( 'tekst' => '10% korting op elke levering' ),
        array( 'tekst' => 'Elke maand rond dezelfde datum in huis' ),
        array( 'tekst' => 'Pauzeren of opzeggen in je account, met één klik' ),
    ),
    'aankoop_abo_toelichting'        => 'Je betaalt per levering, € %s per maand. Stoppen kost geen geld en geen moeite.',
    'aankoop_abo_opzeglink'          => array( 'title' => 'Zo werkt opzeggen', 'url' => '#over-het-abonnement', 'target' => '' ),
    'aankoop_prijs_eenmalig'         => '%1$s, %2$s',
    'aankoop_prijs_maand'            => 'per maand, %1$s. Los %2$s',
    'aankoop_knop'                   => 'In winkelwagen',
    'aankoop_knop_abo'               => 'Start abonnement, %s per maand',
    'aankoop_hint_abo'               => 'Eerste levering in 1 tot 2 werkdagen. Daarna elke maand rond dezelfde datum.',
    'uitverkocht_regel'              => 'Tijdelijk op',
    'aankoop_hint'                   => 'Kies eerst of je eenmalig koopt of een abonnement wilt.',
    'zekerheden'                     => array(
        array( 'icoon' => 'verzending', 'tekst' => 'In 1 tot 2 werkdagen verstuurd, gratis vanaf € 40' ),
        array( 'icoon' => 'herhalen', 'tekst' => 'Abonnement pauzeren of opzeggen doe je zelf in je account' ),
        array( 'icoon' => 'info', 'tekst' => 'Betalen met iDEAL, Bancontact of creditcard' ),
    ),
    'levering_label_eenmalig'        => 'eenmalig',
    'levering_label_maand'           => 'elke maand',

    'contact_mail'                   => 'hallo@morimatcha.nl',
    'contact_telefoon'               => '',
    'contact_reactietijd'            => 'Je krijgt binnen een werkdag antwoord.',
);

foreach ( $opties as $naam => $waarde ) {
    update_field( $naam, $waarde, 'option' );
}
