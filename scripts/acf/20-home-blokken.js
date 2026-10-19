/**
 * Blokken van de Home: Hero, Kenmerken, Over Mori, Productsplit, Herkomst, Recepten, Fotoband,
 * Starterset, Abonnement en Veelgestelde vragen. Ze zijn bruikbaar op elke pagina.
 */
module.exports = ({ f, defineLayout }) => {

    defineLayout('hero', 'Hero met foto', [
        f.tab('Inhoud'),
        f.image('foto', 'Foto (desktop)', { required: true, instructions: 'Liggende foto, vult het hele scherm. De header wordt op deze pagina automatisch transparant.' }),
        f.image('foto_mobiel', 'Foto (mobiel)', { instructions: 'Optioneel. Staande foto voor smalle schermen; leeg = de desktopfoto.' }),
        f.text('eyebrow', 'Eyebrow'),
        f.textarea('titel', 'Titel', { required: true, rows: 2, instructions: 'Een nieuwe regel in dit veld wordt een nieuwe regel in de titel.' }),
        f.select('titelgrootte', 'Grootte van de titel', { display: 'Display (112)', h1: 'H1 (72)' }, { default: 'display' }),
        f.textarea('tekst', 'Tekst'),
        f.link('knop_primair', 'Eerste knop', { instructions: 'Lichte knop met pijl.' }),
        f.link('knop_secundair', 'Tweede knop', { instructions: 'Knop met contour.' }),
        f.text('noot', 'Kleine regel onder de knoppen', { instructions: 'Optioneel.' }),
        f.tab('Foto-opmaak'),
        f.number('hoogte', 'Hoogte op desktop (px)', { default: 900, min: 480, instructions: 'In het ontwerp 900, 860 of 820.' }),
        f.number('foto_zoom', 'Foto inzoomen', { default: 1, step: 0.05, min: 1, instructions: '1 = geen zoom.' }),
        f.select('foto_focus', 'Welk deel van de foto blijft zichtbaar?', { midden: 'Midden', onder: 'Onder', boven: 'Boven', links: 'Links', rechts: 'Rechts', 'onder-links': 'Onder links' }, { default: 'midden' }),
        f.select('foto_stijl', 'Foto', { vol: 'Over het hele vlak', rechts: 'Rechts, met donker vlak links' }, { default: 'vol' }),
        f.bool('toon_etiket', 'Etiket op het blik tonen', { default: false, instructions: 'Alleen bij een foto van het Mori-blik met een leeg etiket (stijl "Rechts").' }),
    ], { achtergrond: false });

    defineLayout('kenmerken', 'Kenmerkenstrook', [
        f.tab('Inhoud'),
        f.repeater('items', 'Kenmerken', [
            f.text('tekst', 'Tekst'),
        ], { layout: 'table', button: 'Kenmerk toevoegen', instructions: 'Op mobiel loopt de strook langzaam door, op desktop staat hij stil.' }),
    ], { achtergrondDefault: 'mos' });

    defineLayout('over_mori', 'Foto met tekst en beloftes', [
        f.tab('Inhoud'),
        f.image('foto', 'Foto', { required: true }),
        f.select('foto_positie', 'Foto links of rechts', { links: 'Links', rechts: 'Rechts' }, { default: 'links' }),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('tekst', 'Tekst'),
        f.repeater('beloftes', 'Beloftes', [
            f.text('titel', 'Titel', { width: 35 }),
            f.textarea('uitleg', 'Uitleg', { width: 65, rows: 2 }),
        ], { layout: 'table', button: 'Belofte toevoegen', instructions: 'De nummers (01, 02, ...) komen vanzelf.' }),
        f.link('knop', 'Tekstlink onderaan'),
    ]);

    defineLayout('productsplit', 'Productsplit (foto en maten)', [
        f.tab('Inhoud'),
        f.post('product', 'Product', 'product', { required: true, instructions: 'Naam, maten en prijzen komen uit WooCommerce.' }),
        f.image('foto', 'Foto', { instructions: 'Leeg = de productfoto uit WooCommerce.' }),
        f.bool('toon_etiket', 'Etiket op het blik tonen', { default: true, instructions: 'Het etiket is tekst (zie Instellingen Mori > Algemeen).' }),
        f.select('foto_positie', 'Foto links of rechts', { links: 'Links', rechts: 'Rechts' }, { default: 'links' }),
        f.text('eyebrow', 'Eyebrow'),
        f.textarea('tekst', 'Tekst', { instructions: 'Leeg = de korte omschrijving uit WooCommerce.' }),
        f.textarea('tekst_mobiel', 'Tekst op mobiel', { instructions: 'Optioneel, kortere tekst voor smalle schermen. Leeg = dezelfde tekst.' }),
        f.text('knoptekst', 'Tekst eerste knop'),
        f.link('knop_tekstlink', 'Tekstlink'),
    ], { achtergrondDefault: 'wit' });

    defineLayout('herkomst', 'Herkomst (stappen en foto\'s)', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('intro', 'Intro'),
        f.repeater('stappen', 'Stappen', [
            f.text('titel', 'Titel', { width: 35 }),
            f.textarea('uitleg', 'Uitleg', { width: 65, rows: 3 }),
        ], { layout: 'table', button: 'Stap toevoegen' }),
        f.image('foto_groot', 'Grote foto'),
        f.repeater('fotos', 'Kleine foto\'s met bijschrift', [
            f.image('foto', 'Foto', { width: 40 }),
            f.text('bijschrift', 'Bijschrift', { width: 60 }),
        ], { layout: 'table', max: 2, button: 'Foto toevoegen' }),
    ], { achtergrondDefault: 'mos' });

    defineLayout('recepten', 'Recepten (kaarten)', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('intro', 'Intro'),
        f.text('intro_mobiel', 'Intro op mobiel', { instructions: 'Optioneel, kortere tekst voor smalle schermen.' }),
        f.link('knop', 'Tekstlink rechtsboven', { instructions: 'Alleen zichtbaar op desktop.' }),
        f.number('foto_hoogte', 'Hoogte van de foto\'s op desktop (px)', { default: 500, min: 200 }),
        f.repeater('recepten', 'Recepten', [
            f.image('foto', 'Foto', { width: 30 }),
            f.text('tijd', 'Tijd', { width: 20 }),
            f.text('titel', 'Titel', { width: 20 }),
            f.textarea('uitleg', 'Uitleg', { width: 30, rows: 3 }),
            f.text('uitleg_mobiel', 'Uitleg op mobiel', { width: 30, instructions: 'Optioneel, korter.' }),
            f.link('link', 'Link', { instructions: 'Alleen zichtbaar op desktop.' }),
        ], { layout: 'block', button: 'Recept toevoegen', instructions: 'Op mobiel staan de kaarten in een zijwaartse carrousel.' }),
    ]);

    defineLayout('fotoband', 'Fotoband met tekst', [
        f.tab('Inhoud'),
        f.image('foto', 'Foto', { required: true }),
        f.select('uitsnede_mobiel', 'Uitsnede op mobiel', { links: 'Links', midden: 'Midden', rechts: 'Rechts' }, { default: 'rechts', instructions: 'Welk deel van de foto zichtbaar is op een smal scherm.' }),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('tekst', 'Tekst'),
        f.textarea('tekst_mobiel', 'Tekst op mobiel', { instructions: 'Optioneel, kortere tekst voor smalle schermen.' }),
        f.link('knop', 'Knop', { instructions: 'Knop met contour en pijl.' }),
        f.bool('parallax', 'Foto beweegt licht mee bij scrollen', { default: true, instructions: 'Alleen op desktop, maximaal 40 px.' }),
    ], { achtergrond: false });

    defineLayout('starterset', 'Starterset (bundel)', [
        f.tab('Inhoud'),
        f.post('product', 'Bundelproduct', 'product', { required: true, instructions: 'De starterset in WooCommerce. De setprijs komt uit dit product.' }),
        f.image('foto', 'Foto', { instructions: 'Leeg = de productfoto uit WooCommerce.' }),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel', { instructions: 'Leeg = de naam van het product.' }),
        f.textarea('intro', 'Intro'),
        f.text('intro_mobiel', 'Intro op mobiel', { instructions: 'Optioneel, kortere tekst voor smalle schermen.' }),
        f.repeater('onderdelen', 'In deze set', [
            f.post('product', 'Product', 'product', { width: 40 }),
            f.text('detail', 'Detailregel', { width: 60 }),
        ], { layout: 'table', button: 'Onderdeel toevoegen', instructions: 'De prijzen en de besparing worden berekend uit WooCommerce.' }),
        f.bool('compact', 'Lagere foto', { default: false, instructions: 'Gebruikt op de productpagina (foto 640 hoog in plaats van 680).' }),
        f.text('knoptekst', 'Tekst eerste knop'),
        f.select('knop_actie', 'Wat doet de eerste knop?', { product: 'Naar de productpagina', winkelwagen: 'Zet de set in de winkelwagen' }, { default: 'product' }),
        f.link('knop_tekstlink', 'Tekstlink'),
    ], { achtergrondDefault: 'gerst' });

    defineLayout('abonnement', 'Abonnement (uitleg in stappen)', [
        f.tab('Inhoud'),
        f.image('foto', 'Foto', { instructions: 'Etiket-tekst staat in Instellingen Mori.' }),
        f.bool('toon_etiket', 'Etiket op het blik tonen', { default: true }),
        f.select('foto_positie', 'Foto links of rechts', { links: 'Links', rechts: 'Rechts' }, { default: 'rechts' }),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('intro', 'Intro'),
        f.repeater('stappen', 'Stappen', [
            f.text('titel', 'Titel', { width: 25 }),
            f.text('uitleg', 'Uitleg', { width: 40 }),
            f.text('uitleg_mobiel', 'Uitleg op mobiel', { width: 35, instructions: 'Optioneel, korter.' }),
        ], { layout: 'table', button: 'Stap toevoegen' }),
        f.link('knop_primair', 'Eerste knop'),
        f.link('knop_secundair', 'Tweede knop'),
    ], { achtergrondDefault: 'mos' });

    defineLayout('vragen', 'Veelgestelde vragen', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('intro', 'Intro'),
        f.bool('toon_contact', 'Contactgegevens tonen', { default: true, instructions: 'Neemt e-mailadres en reactietijd uit Instellingen Mori > Contact.' }),
        f.link('knop', 'Knop onder de tekst', { instructions: 'Optioneel, knop met contour.' }),
        f.repeater('vragen', 'Vragen', [
            f.text('vraag', 'Vraag', { width: 35 }),
            f.textarea('antwoord', 'Antwoord', { width: 65, rows: 3 }),
        ], { layout: 'table', button: 'Vraag toevoegen', instructions: 'De eerste vraag staat open.' }),
    ]);

};
