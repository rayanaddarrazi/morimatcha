# Plan vooraf, per blok en functie

Elk onderdeel heeft acceptatiecriteria. Na het bouwen is elk punt gecontroleerd (zie `papier/review-acceptatiecriteria.md`).

## Blokken (pagebuilder)
| Blok | Doel | Acceptatiecriteria |
|---|---|---|
| hero | Eerste indruk met foto, titel en knoppen | Titel, tekst, foto (desktop en mobiel), knoppen en etiket bewerkbaar. Header transparant over de foto |
| kenmerken, foto_kenmerken, smaakprofiel | Smaak en eigenschappen | Items toevoegen, verwijderen, verslepen |
| over_mori, herkomst, foto_tekst, fotoband | Verhaal | Foto, tekst en volgorde bewerkbaar |
| recepten, productsplit, productgrid | Producten tonen | Kiest producten uit WooCommerce, filters en sortering werken zonder jQuery |
| starterset, abonnement, prijstabel, opzeggen | Aanbod en abonnement | Prijzen uit WooCommerce, korting uit instellingen |
| vragen | Veelgestelde vragen | Accordeon met `aria-expanded`, toetsenbord werkt |
| bereiding, feiten, benodigdheden, fouten, stappen_kaarten, keuzehulp, pagina_intro, beeldband | Bereiden en uitleg | Tabs of gestapeld, alles bewerkbaar |

## Functies
| Functie | Acceptatiecriteria |
|---|---|
| Maten en prijs per gram | 3 maten, prijs per gram zichtbaar, voordeligste maat gemarkeerd |
| Aankoopkeuze | "Eenmalig" of "Elke maand een blik", niets voorgeselecteerd, knop uitgeschakeld tot een keuze, uitleg opzeggen naast de keuze |
| Winkelwagenpaneel | Schuift in, focus blijft binnen het paneel, Esc sluit, aantal en verwijderen werken |
| Kassa | Straat en plaats vullen zich via postcode (PDOK), huisnummer wordt gecontroleerd met inline foutmelding, account bij abonnement |
| Bevestiging | Tijdlijn, overzicht en abonnementsblok |
| Mijn account | Vier statussen van het abonnement, pauzeren 1 tot 3 maanden, maat wijzigen, opzeggen met reden, opnieuw starten |
| Blik in 3D | Draaien met muis, vinger en pijltjestoetsen, foto als terugval, laadt pas na klik |
| Bereidingsinstructie | Eigen veld per product en blok Bereiding |
| Tweede taal | Alle teksten staan in velden en `__()`-functies met tekstdomein `morimatcha`, dus vertaalbaar (bijvoorbeeld met een vertaalplugin) |
