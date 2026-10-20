# 004 Blik in 3D laadt pas na een klik

**Besluit:** het 3D-blik (Three.js, alleen de gebruikte onderdelen gebundeld) laadt pas als de bezoeker op "Draai het blik" klikt. Zonder WebGL of bij een fout blijft de foto staan.

**Waarom:** de productpagina moet snel blijven. Het bestand is 528 kB (132 kB gzip), dat hoort niet in de eerste lading.

**Gevolg:** meting in `docs/snelheid.md`. Het blik is volledig in code getekend, het etiket is echte tekst.
