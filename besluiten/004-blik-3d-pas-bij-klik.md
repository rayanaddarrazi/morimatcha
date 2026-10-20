# 004 Blik in 3D laadt pas na een klik

**Besluit:** het 3D-blik (Three.js, alleen de gebruikte onderdelen gebundeld) laadt pas als de bezoeker op "Draai het blik" klikt. Zonder WebGL of bij een fout blijft de foto staan.

**Waarom:** de productpagina moet snel blijven. Het script is 529 kB (134 kB gzip), dat hoort niet in de eerste lading.

**Meting:** zie `docs/snelheid.md`.

**Uiterlijk en aanpak:** een los 3D-model zag er nooit hetzelfde uit als de productfoto, dus het blik wordt uit de foto zelf gemaakt. `scripts/build-blik-textuur.py` rolt de foto af tot een textuur (het blik is een cilinder), met de kromming van de ringen en een aparte glanslaag voor het licht. Een shader in Three.js rolt die textuur weer om het blik, op precies dezelfde plek en maat als op de foto. De foto blijft het achterbeeld, dus achtergrond, schaduw en linnen zijn hetzelfde. Stilstaand verschilt het beeld gemiddeld 2 van de 255 kleurwaarden van de foto. Het etiket is echte tekst in dezelfde opmaak als op de foto en draait mee.

**Let op:** vervang je de productfoto, dan klopt de textuur niet meer. De knop verdwijnt dan vanzelf (het blik werkt alleen met de foto `product-mori-matcha`) en alleen de foto blijft staan. Draai `python3 scripts/build-blik-textuur.py` opnieuw met een nieuwe foto en pas de maten bovenin het script aan.
