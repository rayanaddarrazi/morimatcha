<?php
/**
 * Seed deel 9: Afrekenen, Bevestiging en Mijn account (teksten uit Figma) en WooCommerce-instellingen voor de kassa.
 */

$img = $GLOBALS['mori_img'];

// Kassa en account als klassieke pagina's met shortcode (de blokken van WooCommerce worden niet gebruikt).
wp_update_post( array( 'ID' => wc_get_page_id( 'checkout' ), 'post_content' => '[woocommerce_checkout]' ) );
wp_update_post( array( 'ID' => wc_get_page_id( 'cart' ), 'post_content' => '[woocommerce_cart]' ) );
wp_update_post( array( 'ID' => wc_get_page_id( 'myaccount' ), 'post_content' => '[woocommerce_my_account]' ) );

update_option( 'woocommerce_enable_guest_checkout', 'yes' );
update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' );
update_option( 'woocommerce_enable_myaccount_registration', 'no' );
update_option( 'woocommerce_registration_generate_password', 'yes' );
update_option( 'woocommerce_enable_coupons', 'yes' );
update_option( 'woocommerce_ship_to_destination', 'billing_only' );
update_option( 'woocommerce_specific_allowed_countries', array( 'NL' ) );
update_option( 'woocommerce_allowed_countries', 'specific' );
update_option( 'woocommerce_default_country', 'NL' );

$opties = array(
    'afr_titel'          => 'Afrekenen',
    'afr_veilig'         => 'Veilig betalen',
    'zoek_label'         => 'Zoeken',
    'zoek_placeholder'   => 'Zoek in de winkel',
    'zoek_snel'          => 'Of kijk direct in',
    'zoek_knop'          => 'Zoeken',
    'zoek_titel'         => 'Zoekresultaten voor "%s"',
    'zoek_aantal'        => '%d producten',
    'zoek_leeg'          => 'We hebben niets gevonden. Probeer een ander woord, of kijk in de hele shop.',
    'zoek_alles'         => 'Bekijk de hele shop',
    'afr_stappen'        => array( array( 'naam' => 'Gegevens' ), array( 'naam' => 'Betalen' ), array( 'naam' => 'Klaar' ) ),
    'afr_contact_titel'  => 'Contact',
    'afr_email_label'    => 'E-mailadres',
    'afr_email_hulp'     => 'We sturen je bestelbevestiging naar dit adres.',
    'afr_account_kop'    => 'Je abonnement krijgt een account',
    'afr_account_tekst'  => 'Zo kun je het zelf pauzeren of opzeggen. Je kiest straks alleen een wachtwoord.',
    'afr_adres_titel'    => 'Bezorgadres',
    'afr_voornaam'       => 'Voornaam',
    'afr_achternaam'     => 'Achternaam',
    'afr_postcode'       => 'Postcode',
    'afr_huisnummer'     => 'Huisnummer',
    'afr_straat'         => 'Straat',
    'afr_plaats'         => 'Plaats',
    'afr_huisnummer_fout' => 'Vul je huisnummer in, bijvoorbeeld 12 of 12A.',
    'afr_straat_hulp'    => 'We vulden dit aan op basis van je postcode.',
    'afr_betaal_titel'   => 'Betaalmethode',
    'afr_betaal_intro'   => 'Je betaalt pas als je op de knop hieronder klikt.',
    'afr_betaalmethoden' => array(
        array( 'sleutel' => 'ideal', 'naam' => 'iDEAL', 'uitleg' => 'Betaal direct via je eigen bank.' ),
        array( 'sleutel' => 'bancontact', 'naam' => 'Bancontact', 'uitleg' => 'Voor Belgische bankpassen.' ),
        array( 'sleutel' => 'creditcard', 'naam' => 'Visa of Mastercard', 'uitleg' => 'Ook voor een abonnement.' ),
        array( 'sleutel' => 'bacs', 'naam' => 'Bankoverschrijving', 'uitleg' => 'Je krijgt de gegevens per mail. We versturen zodra het bedrag binnen is.' ),
    ),
    'afr_abo_toestemming' => 'Voor je abonnement geef je toestemming voor een betaling per levering. Die stopt zodra je opzegt.',
    'afr_voorwaarden'    => 'Ik ga akkoord met de algemene voorwaarden',
    'afr_knop'           => 'Bestelling plaatsen en betalen',
    'afr_noot_abo'       => 'Je betaalt nu %1$s. Daarna %2$s per maand tot je opzegt.',
    'afr_samenvatting'   => 'Je bestelling',
    'afr_kortingscode'   => 'Kortingscode',
    'afr_toepassen'      => 'Toepassen',
    'afr_subtotaal'      => 'Subtotaal',
    'afr_verzending'     => 'Verzending',
    'afr_totaal'         => 'Totaal',
    'afr_btw'            => 'Inclusief btw',
    'afr_abo_eyebrow'    => 'Over je abonnement',
    'afr_abo_nu'         => '%s, inclusief verzending',
    'afr_abo_volgende'   => 'Rond %1$s, %2$s',
    'afr_abo_opzeggen_label' => 'Opzeggen',
    'afr_abo_opzeggen_tekst' => 'Altijd, met één klik in je account',

    'bev_titel'          => 'Bedankt, %s. Je bestelling is binnen.',
    'bev_tekst'          => 'We hebben een bevestiging gestuurd naar %s. Je blik gaat binnen 1 tot 2 werkdagen op weg.',
    'bev_bestelling'     => 'Bestelling %s',
    'bev_tijdlijn'       => array(
        array( 'naam' => 'Besteld', 'tekst' => 'Vandaag' ),
        array( 'naam' => 'Ingepakt', 'tekst' => 'Morgen' ),
        array( 'naam' => 'Verstuurd', 'tekst' => 'Je krijgt een track en trace' ),
        array( 'naam' => 'Bezorgd', 'tekst' => 'Binnen 1 tot 2 werkdagen' ),
    ),
    'bev_kop_label'      => 'Alvast voor als hij binnen is',
    'bev_kop_titel'      => 'Zo maak je je eerste kop',
    'bev_kop_tekst'      => '2 gram, 70 ml water van 80 °C en vijftien seconden kloppen. De hele uitleg staat op de bereidingspagina.',
    'bev_kop_knop'       => array( 'title' => 'Naar de bereiding', 'url' => home_url( '/bereiden/' ), 'target' => '' ),
    'bev_kop_foto'       => $img['ritueel'],
    'bev_overzicht'      => 'Wat je hebt besteld',
    'bev_betaald'        => 'Betaald met',
    'bev_adres'          => 'Bezorgadres',
    'bev_totaal'         => 'Totaal',
    'bev_abo_eyebrow'    => 'Je abonnement staat aan',
    'bev_abo_titel'      => 'Volgende levering rond %s',
    'bev_abo_tekst'      => 'Pauzeren, een andere maat of opzeggen doe je zelf in je account. Je krijgt een mail een paar dagen voor elke levering.',
    'bev_abo_knop'       => 'Naar mijn abonnement',

    'blik3d_aan'         => 1,
    'blik3d_knop'        => 'Draai het blik',
    'blik3d_terug'       => 'Terug naar de foto',
    'blik3d_hint'        => 'Sleep om het blik te draaien',

    'acc_titel'          => 'Mijn account',
    'acc_groet'          => 'Hoi %s',
    'acc_menu'           => array(
        array( 'sleutel' => 'dashboard', 'naam' => 'Overzicht', 'icoon' => 'account' ),
        array( 'sleutel' => 'abonnement', 'naam' => 'Abonnement', 'icoon' => 'herhalen' ),
        array( 'sleutel' => 'orders', 'naam' => 'Bestellingen', 'icoon' => 'tas' ),
        array( 'sleutel' => 'edit-address', 'naam' => 'Adressen', 'icoon' => 'verzending' ),
        array( 'sleutel' => 'edit-account', 'naam' => 'Gegevens', 'icoon' => 'info' ),
    ),
    'acc_gegevens_persoon' => 'Je gegevens',
    'acc_gegevens_wachtwoord' => 'Wachtwoord wijzigen',
    'acc_gegevens_wachtwoord_hulp' => 'Laat deze velden leeg als je je wachtwoord niet wilt wijzigen.',
    'acc_wachtwoord_huidig' => 'Huidig wachtwoord',
    'acc_wachtwoord_nieuw' => 'Nieuw wachtwoord',
    'acc_wachtwoord_herhaal' => 'Herhaal nieuw wachtwoord',
    'acc_opslaan'        => 'Wijzigingen opslaan',
    'acc_annuleren'      => 'Annuleren',
    'acc_adres_kop'      => 'Bezorgadres',
    'acc_adres_hulp'     => 'Dit adres staat standaard ingevuld als je bestelt.',
    'acc_adres_leeg'     => 'Je hebt nog geen adres opgeslagen.',
    'acc_adres_bewerken' => 'Adres wijzigen',
    'acc_adres_toevoegen' => 'Adres toevoegen',
    'acc_adres_straat'   => 'Straat en huisnummer',
    'acc_uitloggen'      => 'Uitloggen',
    'acc_abo_titel'      => 'Je abonnement',
    'acc_abo_sinds'      => 'Klant sinds %s',
    'acc_overzicht_titel' => 'Overzicht',
    'acc_pauze_kop'      => 'Op vakantie?',
    'acc_pauze_tekst'    => 'Pauzeer je abonnement tot je terug bent. Er wordt dan niets verstuurd of afgeschreven.',
    'acc_label'          => 'Abonnement',
    'acc_frequentie'     => 'Elke maand een blik, %d% korting',
    'acc_volgende'       => 'Volgende levering',
    'acc_per_levering'   => 'Per levering',
    'acc_betaalwijze'    => 'Betaalwijze',
    'acc_nog_dagen'      => 'Nog %d dagen tot je volgende blik',
    'acc_verstuurd'      => 'Wordt %s verstuurd',
    'acc_knop_pauzeren'  => 'Pauzeren',
    'acc_knop_maat'      => 'Maat wijzigen',
    'acc_knop_opzeggen'  => 'Abonnement opzeggen',
    'acc_knop_hervatten' => 'Weer aanzetten',
    'acc_knop_betalen'   => 'Nu betalen',
    'acc_knop_opnieuw'   => 'Opnieuw starten',
    'acc_gepauzeerd'     => 'Gepauzeerd',
    'acc_gepauzeerd_tot' => 'Gepauzeerd tot',
    'acc_gepauzeerd_uitleg' => 'Tot %s wordt er niets verstuurd of afgeschreven.',
    'acc_actief'         => 'Actief',
    'acc_betaling_status' => 'Betaling niet binnen',
    'acc_betaling_melding' => 'Je betaling van %s is nog niet binnen.',
    'acc_openstaand'     => 'Openstaand',
    'acc_voor_levering'  => 'Voor levering van',
    'acc_betaling_uitleg' => 'Na 7 dagen zetten we je abonnement vanzelf op pauze. Je betaalt dan niets.',
    'acc_opgezegd_status' => 'Opgezegd',
    'acc_opgezegd_melding_kop' => 'Je abonnement is opgezegd',
    'acc_opgezegd_melding_tekst' => 'We hebben je een bevestiging gemaild. Je wordt niets meer afgeschreven.',
    'acc_laatste_levering' => 'Laatste levering',
    'acc_opgezegd_op'    => 'Opgezegd op',
    'acc_ontvangen'      => 'Ontvangen',
    'acc_ontvangen_waarde' => '%d blikken',
    'acc_opgezegd_uitleg' => 'Je account en bestelgeschiedenis blijven bewaard. Opnieuw starten kan altijd.',
    'acc_leveringen'     => 'Laatste leveringen',
    'acc_alle_bestellingen' => 'Alle bestellingen',
    'acc_geen_abonnement' => 'Je hebt nog geen abonnement.',
    'acc_geen_abonnement_knop' => array( 'title' => 'Bekijk het abonnement', 'url' => home_url( '/abonnement/' ), 'target' => '' ),
    'acc_dialog_titel'   => 'Abonnement opzeggen?',
    'acc_dialog_tekst'   => 'Na opzeggen sturen we niets meer en schrijven we niets meer af. Je laatste levering was op %s. Je account en bestelgeschiedenis blijven bewaard.',
    'acc_dialog_pauze_kop' => 'Alleen even geen matcha nodig?',
    'acc_dialog_pauze_tekst' => 'Je kunt ook 1, 2 of 3 maanden pauzeren.',
    'acc_dialog_pauze_knop' => 'Pauzeren',
    'acc_dialog_reden'   => 'Waarom stop je? Niet verplicht, het helpt ons wel.',
    'acc_redenen'        => array(
        array( 'tekst' => 'Ik heb nog genoeg' ), array( 'tekst' => 'Te duur' ), array( 'tekst' => 'Smaak viel tegen' ),
        array( 'tekst' => 'Ik drink minder matcha' ), array( 'tekst' => 'Andere reden' ),
    ),
    'acc_dialog_ja'      => 'Ja, zeg mijn abonnement op',
    'acc_dialog_nee'     => 'Toch niet',
    'acc_pauze_maanden'  => '%d maand',
);

foreach ( $opties as $naam => $waarde ) {
    update_field( $naam, $waarde, 'option' );
}

// Verzending: € 4,95, gratis vanaf € 40 (Nederland).
$zones = WC_Data_Store::load( 'shipping-zone' )->get_zones();
if ( ! $zones ) {
    $zone = new WC_Shipping_Zone();
    $zone->set_zone_name( 'Nederland' );
    $zone->add_location( 'NL', 'country' );
    $zone->save();
    $flat = $zone->add_shipping_method( 'flat_rate' );
    $free = $zone->add_shipping_method( 'free_shipping' );
    update_option( 'woocommerce_flat_rate_' . $flat . '_settings', array( 'title' => 'Verzending', 'tax_status' => 'none', 'cost' => '4.95' ) );
    update_option( 'woocommerce_free_shipping_' . $free . '_settings', array( 'title' => 'Gratis verzending', 'requires' => 'min_amount', 'min_amount' => '40', 'ignore_discounts' => 'no' ) );
}

// Links onderaan de kassa
update_field( 'afr_footer_links', array(
    array( 'link' => array( 'title' => 'Verzending en retour', 'url' => '#', 'target' => '' ) ),
    array( 'link' => array( 'title' => 'Algemene voorwaarden', 'url' => '#', 'target' => '' ) ),
    array( 'link' => array( 'title' => 'Privacyverklaring', 'url' => '#', 'target' => '' ) ),
    array( 'link' => array( 'title' => 'Contact', 'url' => home_url( '/contact/' ), 'target' => '' ) ),
), 'option' );

// Nederlandse adressen voor de WooCommerce-pagina's
wp_update_post( array( 'ID' => wc_get_page_id( 'checkout' ), 'post_name' => 'afrekenen' ) );
wp_update_post( array( 'ID' => wc_get_page_id( 'cart' ), 'post_name' => 'winkelwagen' ) );
wp_update_post( array( 'ID' => wc_get_page_id( 'myaccount' ), 'post_name' => 'mijn-account' ) );
update_option( 'woocommerce_checkout_pay_endpoint', 'betalen' );
update_option( 'woocommerce_checkout_order_received_endpoint', 'bevestiging' );

// Nederlandse adressen in Mijn account
update_option( 'woocommerce_myaccount_orders_endpoint', 'bestellingen' );
update_option( 'woocommerce_myaccount_view_order_endpoint', 'bestelling' );
update_option( 'woocommerce_myaccount_edit_account_endpoint', 'gegevens' );
update_option( 'woocommerce_myaccount_edit_address_endpoint', 'adressen' );
update_option( 'woocommerce_myaccount_lost_password_endpoint', 'wachtwoord-vergeten' );
update_option( 'woocommerce_logout_endpoint', 'uitloggen' );

update_field( 'seo_beschrijving_standaard', 'Mori Matcha: matcha voor thuis, met duidelijke smaakinfo en een abonnement dat je zelf beheert.', 'option' );
