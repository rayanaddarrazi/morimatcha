# 001 Basis: KJ Boilerplate

**Besluit:** het thema `morimatcha` is gebouwd op de KJ Boilerplate (Tailwind 3 + PostCSS, ACF Pro flexible content als pagebuilder, `kj_`-functies, `acf-json`). Solea is alleen als voorbeeld van de structuur gebruikt, niets uit Solea is overgenomen of aangepast.

**Waarom:** de boilerplate is al bekend, heeft een vaste mappenstructuur en maakt elk blok onafhankelijk (eigen PHP, CSS en veldgroep).

**Gevolg:** nieuwe blokken maak je met `scripts/acf/*.js` en een map in `templates/pagebuilder/`. De velden worden gegenereerd met `node scripts/build-acf.js`.
