# Besluit 007: accounts en beveiliging

**Datum:** oktober 2026

## Wat er besloten is

Klanten zijn gewone WordPress-gebruikers met de rol Klant. Ze staan in de database in de tabellen `wp_users` en `wp_usermeta` (te zien in phpMyAdmin). Het wachtwoord staat daar nooit leesbaar: WordPress slaat alleen een hash op.

Er zijn twee manieren om een account te krijgen:

1. **Account maken op de accountpagina.** Voornaam, achternaam, e-mailadres, eigen wachtwoord (minimaal 10 tekens) en akkoord met de voorwaarden. Daarna ben je meteen ingelogd en kun je later gewoon inloggen.
2. **Bij het afrekenen.** Gasten bestellen niet meer zonder account. Je vult je e-mailadres in en het account wordt automatisch gemaakt. Je krijgt een mail om zelf een wachtwoord te kiezen. Wie al een account heeft, ziet een link om in te loggen.

## Beveiliging

- Maximaal 5 foute pogingen per account per apparaat in 15 minuten, daarna is inloggen even geblokkeerd, ook met het juiste wachtwoord.
- De foutmelding bij inloggen is altijd dezelfde, dus je kunt niet zien of een e-mailadres bij ons bekend is.
- Account maken heeft een verborgen veld tegen bots en maximaal 5 pogingen per uur per apparaat.
- Registreren via wp-login.php staat uit, XML-RPC staat uit, de gebruikerslijst in de REST-API is niet openbaar en `?author=1` stuurt door naar de homepage.
- Het bewerken van thema en plug-in bestanden in het dashboard staat uit. Klanten komen niet in het dashboard.
- Beveiligingsheaders: nosniff, X-Frame-Options, Referrer-Policy en Permissions-Policy.

## Waar staat het

- Code: `inc/toegang.php`.
- Formulier: `woocommerce/myaccount/form-login.php`.
- Teksten: Instellingen Mori, tabblad Inloggen.
- WooCommerce-instellingen worden gezet in `scripts/seed/part-9-winkel.php`.

## Let op

- Mails (account aangemaakt, wachtwoord kiezen) gaan via `wp_mail`. Op de live server moet SMTP ingesteld zijn, anders komen ze niet aan.
- Testgebruiker `sanne` en andere testaccounts moeten voor livegang worden verwijderd.
