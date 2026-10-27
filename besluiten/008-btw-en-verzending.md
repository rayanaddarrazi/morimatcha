# Besluit 008: btw en verzending

**Datum:** oktober 2026

## Btw

- Matcha is een voedingsmiddel: 9%. Accessoires (klopper, zeefje, kom) en de startersets: 21%.
- Alle prijzen zijn inclusief btw, de klant ziet dus dezelfde prijzen als voorheen.
- Verzendkosten volgen de producten. Zit er matcha en een accessoire in de mand, dan wordt de btw over de verzending naar verhouding verdeeld (zo doet WooCommerce het met de instelling "op basis van winkelwagen").
- Nog uit te zoeken met de accountant: de startersets bevatten matcha (9%) en accessoires (21%). Nu staan ze op 21%, dat is de veilige kant.

## Verzending

- Nederland, 6,95 inclusief btw, gratis vanaf 40 euro (zoals in het ontwerp).
- PostNL zakelijk, pakket: 7,40 excl. btw bij 100 tot 250 pakketten per jaar. Een brievenbuspakje (3,74 excl. btw) past waarschijnlijk niet door de dikte van het blik.
- `inc/btw.php` zorgt dat de klant altijd precies 6,95 betaalt, ook als de btw per mand verschilt. Is gratis verzending beschikbaar, dan is dat de enige keuze.

## Waar staat het

- Instellingen en tarieven: `scripts/seed/part-10-btw-verzending.php`.
- Code: `inc/btw.php`.
- Aanpassen kan ook in WooCommerce, Instellingen, Belasting en Verzending.
