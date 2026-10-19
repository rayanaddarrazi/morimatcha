/**
 * Blokken voor de Shop en andere pagina's: Pagina-intro, Beeldband, Productgrid, Keuzehulp, Foto met tekst.
 */
module.exports = ({ f, defineLayout }) => {

    defineLayout('pagina_intro', 'Pagina-intro (titel en tekst)', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel', { required: true }),
        f.textarea('tekst', 'Tekst rechts', { instructions: 'Korte inleiding naast de titel.' }),
    ], { achtergrondDefault: 'rijstpapier', where: ['pagina'] });

    defineLayout('beeldband', 'Beeldband (brede foto)', [
        f.tab('Inhoud'),
        f.image('foto', 'Foto', { required: true }),
        f.number('hoogte', 'Hoogte op desktop (px)', { default: 520, min: 200 }),
    ], { achtergrond: false, where: ['pagina'] });

    defineLayout('productgrid', 'Producten met filters', [
        f.tab('Inhoud'),
        f.text('label_alles', 'Filter "Alles"', { default: 'Alles' }),
        f.text('label_aantal', 'Tekst aantal producten', { default: '%d producten', instructions: '%d wordt het aantal.' }),
        f.text('label_sorteren', 'Tekst bij het sorteren', { default: 'Sorteer op' }),
        f.repeater('sorteringen', 'Sorteeropties', [
            f.select('sleutel', 'Sortering', { menu_order: 'Aanbevolen (volgorde)', price: 'Prijs, laag naar hoog', 'price-desc': 'Prijs, hoog naar laag', date: 'Nieuwste eerst' }, { width: 40 }),
            f.text('naam', 'Naam in het menu', { width: 60 }),
        ], { layout: 'table', button: 'Optie toevoegen' }),
        f.text('leeg', 'Tekst als er geen producten zijn'),
    ], { achtergrondDefault: 'rijstpapier', where: ['pagina'] });

    defineLayout('keuzehulp', 'Keuzehulp (drie keuzes)', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.repeater('keuzes', 'Keuzes', [
            f.select('icoon', 'Icoon', { lepel: 'Lepel', druppel: 'Druppel', herhalen: 'Herhalen', klok: 'Klok', info: 'Info', temperatuur: 'Temperatuur' }, { width: 15 }),
            f.text('titel', 'Titel', { width: 25 }),
            f.textarea('tekst', 'Tekst', { width: 35, rows: 3 }),
            f.link('knop', 'Knop', { width: 25 }),
        ], { layout: 'block', button: 'Keuze toevoegen' }),
    ], { achtergrondDefault: 'mos', where: ['pagina'] });

    defineLayout('foto_tekst', 'Foto met tekst en knop', [
        f.tab('Inhoud'),
        f.image('foto', 'Foto', { required: true }),
        f.select('foto_positie', 'Foto links of rechts', { links: 'Links', rechts: 'Rechts' }, { default: 'links' }),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('tekst', 'Tekst'),
        f.link('knop', 'Knop'),
    ], { achtergrondDefault: 'gerst', where: ['pagina'] });

};
