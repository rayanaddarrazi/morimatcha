/**
 * Blokken voor de pagina Bereiden: Feiten, Benodigdheden, Veelgemaakte fouten.
 */
module.exports = ({ f, defineLayout }) => {

    defineLayout('feiten', 'Feiten (grote waarden op een rij)', [
        f.tab('Inhoud'),
        f.repeater('items', 'Feiten', [
            f.text('waarde', 'Grote waarde', { width: 40, instructions: 'Bijvoorbeeld "80 °C".' }),
            f.text('uitleg', 'Uitleg', { width: 60 }),
        ], { layout: 'table', button: 'Feit toevoegen' }),
    ], { achtergrondDefault: 'mos', where: ['pagina'] });

    defineLayout('benodigdheden', 'Benodigdheden (producten met korte uitleg)', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('intro', 'Intro'),
        f.link('knop', 'Knop rechts', { instructions: 'Knop met contour en pijl.' }),
        f.repeater('items', 'Producten', [
            f.post('product', 'Product', 'product', { width: 30 }),
            f.text('uitleg', 'Korte uitleg', { width: 40 }),
            f.text('prijs_voorvoegsel', 'Voor de prijs', { width: 15, instructions: 'Bijvoorbeeld "vanaf".' }),
            f.bool('toon_etiket', 'Etiket', { width: 15, default: false }),
        ], { layout: 'table', button: 'Product toevoegen', instructions: 'Naam, foto en prijs komen uit WooCommerce.' }),
    ], { achtergrondDefault: 'rijstpapier', where: ['pagina'] });

    defineLayout('fouten', 'Veelgemaakte fouten (oorzaak en oplossing)', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('intro', 'Intro'),
        f.image('foto', 'Foto'),
        f.text('label_oorzaak', 'Label oorzaak', { default: 'Oorzaak', width: 50 }),
        f.text('label_oplossing', 'Label oplossing', { default: 'Zo los je het op', width: 50 }),
        f.repeater('fouten', 'Fouten', [
            f.text('probleem', 'Probleem', { width: 30 }),
            f.text('oorzaak', 'Oorzaak', { width: 30 }),
            f.text('oplossing', 'Oplossing', { width: 40 }),
        ], { layout: 'table', button: 'Fout toevoegen' }),
    ], { achtergrondDefault: 'rijstpapier', where: ['pagina'] });

};
