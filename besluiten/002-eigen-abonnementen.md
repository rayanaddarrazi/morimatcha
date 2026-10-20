# 002 Abonnementen zonder betaalde plugin

**Besluit:** de abonnementslogica is eigen code (`inc/abonnementen.php`): een eigen berichttype `abonnement`, een dagelijkse taak voor de volgende levering en acties voor pauzeren, maat wijzigen, opzeggen en opnieuw starten.

**Waarom:** de opdracht vraagt om gratis of eigen logica. De keuze "Eenmalig" of "Elke maand een blik" zit als regelgegeven (`mori_levering`) in de winkelwagen, de korting komt uit Instellingen Mori.

**Gevolg:** een nieuwe levering is een gewone WooCommerce-bestelling met betaallink. Blijft die 7 dagen onbetaald, dan pauzeert het abonnement vanzelf. Automatisch afschrijven zonder klikken (Mollie-mandaat) is bewust niet gebouwd.
