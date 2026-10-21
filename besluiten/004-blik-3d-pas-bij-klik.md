# 004 Blik in 3D laadt pas na een klik

**Besluit:** het 3D-blik (Three.js, alleen de gebruikte onderdelen gebundeld) laadt pas als de bezoeker op "Draai het blik" klikt. Zonder WebGL of bij een fout blijft de foto staan.

**Waarom:** de productpagina moet snel blijven. Het script is 529 kB (134 kB gzip), dat hoort niet in de eerste lading.

**Meting:** zie `docs/snelheid.md`.

**Uiterlijk en aanpak:** een los 3D-model zag er nooit hetzelfde uit als de productfoto, dus het blik wordt uit de foto zelf gemaakt. `scripts/build-blik-textuur.py` rolt de foto af tot een textuur (het blik is een cilinder), met de kromming van de ringen en een aparte glanslaag voor het licht. Een shader in Three.js rolt die textuur weer om het blik, op precies dezelfde plek en maat als op de foto. De foto blijft het achterbeeld, dus achtergrond, schaduw en linnen zijn hetzelfde. Stilstaand verschilt het beeld gemiddeld 2 van de 255 kleurwaarden van de foto. Het etiket is echte tekst in dezelfde opmaak als op de foto en draait mee.

**Let op:** vervang je de productfoto, dan klopt de textuur niet meer. De knop verdwijnt dan vanzelf (het blik werkt alleen met de foto `product-mori-matcha`) en alleen de foto blijft staan. Draai `python3 scripts/build-blik-textuur.py` opnieuw met een nieuwe foto en pas de maten bovenin het script aan.

**Draaien van het hele blik:** het groene lak heeft in de foto geen kenmerken, dus alleen het etiket liet zien dat het blik draaide. Daarom staat er in de shader een tone-op-tone merk (het teken zes keer rond, boven en onder het etiket) en een naad. Die komen geleidelijk in beeld zodra het blik uit de beginstand draait en verdwijnen als het weer recht staat, dus de stilstaande weergave blijft gelijk aan de foto.

**De naad:** de twee zilveren lijntjes links op het lak zijn de naad van het blik. Die zaten eerst in de glanslaag, die op het scherm blijft staan. `scripts/build-blik-textuur.py` haalt ze nu uit de foto en zet ze in de textuur, op ongeveer -71 graden, dus ze draaien mee.

**Update (automatisch draaien):** de knop "Draai het blik" en het slepen zijn weggehaald. Het blik maakt nu vanzelf één volledige draai (5,4 seconden, een cubic-bezier met zachte start en lange uitloop) zodra de foto in beeld is en het script klaar is, en daarna wordt de 3D-laag weer weggehaald zodat alleen de foto overblijft. Het script laadt dus niet meer bij een klik maar als de pagina klaar is (requestIdleCallback), en niet bij "minder beweging" in het apparaat. Het etiket heeft nu ook tekst op de achterkant: ingrediënten (matcha, fijngemalen groene thee; geen suiker, geen smaakstoffen) en hoe je hem maakt (2 gram matcha, 70 ml water van 80 °C, 15 seconden kloppen). De zijkanten van het etiket zijn leeg. De teksten staan in Instellingen Mori.

**De naad, definitief:** de twee zilveren lijntjes staan in een eigen laag (`assets/images/blik-naad.webp`) met een eigen zachte lichtfactor, en zijn weggehaald uit de glanslaag. Op het scherm blijft op die plek alleen het randlicht van het lak staan. Daardoor draaien de lijntjes volledig mee en blijft er niets achter aan de linkerrand.

**De naad, laatste versie:** de twee zilveren lijntjes met het donkere randje ertussen worden in de shader getekend, met een vaste dikte op het scherm (ongeveer 3 pixels, zoals op de foto) en op een vaste straal. Ze staan in rust op dezelfde plek en in dezelfde kleur als op de productfoto, en blijven bij het draaien dunne, rechte, schone lijntjes die met het blik meeschuiven. Eerder werden de pixels uit de foto zelf om het blik gezet, maar die rekken bij het draaien uit en werden vlekkerig. Er is dus geen aparte naad-afbeelding meer.

**Verfijning:** rond de twee lijntjes zat een stuk glanslaag dat vast op het scherm bleef staan, met een harde grens. Dat is vervangen door een zachte overgang van de rand naar het lak, zodat er niets meer blijft hangen. De rand van het blik is gladgestreken (sub-pixel, geen trapjes meer). De draai heeft een zachte start en een lange vloeiende uitloop.

**Achtergrondplaat en juiste maat (oktober 2026):** de twee lijntjes bleven achter omdat de foto zelf, die achter het 3D-blik ligt, langs de linkerrand nog een donkergrijs randje en de lijntjes heeft. Het 3D-blik ligt daarom nu op `assets/images/blik-achtergrond.webp`: dezelfde foto met een smalle strook rond het blik vervangen door de achtergrond. Ook de maat klopte niet helemaal: het midden is 509,4 (niet 507) en de straal 168,1 (niet 160 tot 164), gemeten aan de randen van het etiket. Daardoor staat de rand van het blik nu recht en op de goede plek. `scripts/build-blik-textuur.py` maakt de plaat en de maten.

**Deksel en voet (oktober 2026):** de bovenrand van het deksel is afgeschuind (de halve breedte groeit in tien rijen van 154 naar 168), en dat zat niet in het model, waardoor bij de bovenhoeken van het deksel losse pixels van het blik buiten de foto-rand stonden. De straalrij volgt nu die afschuining en de iets bredere voet. De achtergrondplaat vervangt alleen nog een smalle strook langs de zijranden (niet meer het hele blik), zodat er onder het blik geen lichte balk meer in beeld komt. Stilstaand is het gemiddelde verschil met de foto 1,4 op 255.
