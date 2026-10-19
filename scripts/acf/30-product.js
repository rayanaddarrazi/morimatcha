/**
 * Productvelden en productblokken (smaakprofiel, bereiding, herkomst) per WooCommerce-product.
 */
module.exports = ({ f, defineGroup, defineLayout }) => {

    // Velden direct bij het product
    defineGroup('product_extra', 'Product -- Extra', [
        f.tab('Labels'),
        f.text('badge_blad', 'Badge in lichte kleur', { instructions: 'Bijvoorbeeld "Voor beginners". Staat boven de naam op de productpagina en op de kaart.' }),
        f.bool('badge_op_kaart', 'Badge ook op de kaart in de shop', { default: false }),
        f.repeater('smaakbadges', 'Smaakbadges', [
            f.text('tekst', 'Smaak'),
        ], { layout: 'table', button: 'Smaak toevoegen', instructions: 'Bijvoorbeeld Zacht, Romig, Weinig bitter.' }),
        f.text('kaart_omschrijving', 'Korte omschrijving op de kaart', { instructions: 'Eén regel onder de naam in de shop.' }),
        f.text('kaart_label', 'Label bij de prijs op de kaart', { instructions: 'Bijvoorbeeld "vanaf", "set" of "los".' }),
        f.text('kaart_meta', 'Onderregel op de kaart', { instructions: 'Voor producten zonder maten, bijvoorbeeld "3 delen" of "Gereedschap".' }),
        f.tab('Kopen'),
        f.bool('abonnement_mogelijk', 'Abonnement mogelijk', { default: false, instructions: 'Toont de keuze "Eenmalig" of "Elke maand een blik" op de productpagina.' }),
        f.text('hulp_maat_tekst', 'Tekst "Hoe lang doe ik ermee?"'),
        f.link('hulp_maat_link', 'Link "Hoe lang doe ik ermee?"'),
        f.text('uitleg_maten', 'Uitleg onder de maten', { instructions: 'Regel in het grijze vlak onder de maten.' }),
        f.tab('Zekerheden'),
        f.repeater('zekerheden', 'Zekerheden onder de knop', [
            f.select('icoon', 'Icoon', { verzending: 'Verzending', herhalen: 'Herhalen', info: 'Info', klok: 'Klok', pauze: 'Pauze' }, { width: 25 }),
            f.text('tekst', 'Tekst', { width: 75 }),
        ], { layout: 'table', button: 'Regel toevoegen', instructions: 'Leeg = de zekerheden uit Instellingen Mori.' }),
    ], [[{ param: 'post_type', operator: '==', value: 'product' }]], { menu_order: 5 });

    defineLayout('foto_kenmerken', 'Foto met kenmerken (herkomst)', [
        f.tab('Inhoud'),
        f.image('foto', 'Foto', { required: true }),
        f.select('foto_positie', 'Foto links of rechts', { links: 'Links', rechts: 'Rechts' }, { default: 'links' }),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('intro', 'Intro'),
        f.repeater('kenmerken', 'Kenmerken', [
            f.text('titel', 'Titel', { width: 35 }),
            f.text('uitleg', 'Uitleg', { width: 65 }),
        ], { layout: 'table', button: 'Kenmerk toevoegen' }),
    ], { achtergrondDefault: 'mos', where: ['pagina', 'product'] });

    defineLayout('smaakprofiel', 'Smaakprofiel en vragen', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('intro', 'Intro'),
        f.group('profiel', 'Smaakprofiel', [
            f.text('label', 'Label boven de kop', { default: 'Smaak' }),
            f.text('kop', 'Kop'),
            f.textarea('tekst', 'Tekst'),
            f.repeater('schalen', 'Schalen', [
                f.text('kenmerk', 'Kenmerk', { width: 25 }),
                f.text('links', 'Linkerlabel', { width: 25 }),
                f.text('rechts', 'Rechterlabel', { width: 25 }),
                f.number('waarde', 'Waarde (1 tot 5)', { width: 25, min: 1, default: 3 }),
            ], { layout: 'table', button: 'Schaal toevoegen' }),
            f.repeater('feiten', 'Kenmerken', [
                f.text('label', 'Label', { width: 40 }),
                f.text('waarde', 'Waarde', { width: 60 }),
            ], { layout: 'table', button: 'Kenmerk toevoegen', instructions: 'Bijvoorbeeld Lekker als, Per kop, Water, Bewaren.' }),
        ]),
        f.repeater('vragen', 'Vragen', [
            f.text('vraag', 'Vraag', { width: 35 }),
            f.textarea('antwoord', 'Antwoord', { width: 65, rows: 3 }),
        ], { layout: 'table', button: 'Vraag toevoegen', instructions: 'De eerste vraag staat open.' }),
    ], { achtergrondDefault: 'wit', where: ['pagina', 'product'] });

    defineLayout('bereiding', 'Bereiding (stappen)', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow', { default: 'Bereiden' }),
        f.text('titel', 'Titel'),
        f.textarea('intro', 'Intro'),
        f.select('weergave', 'Weergave', { tabs: 'Tabs (Puur / Met melk)', gestapeld: 'Onder elkaar' }, { default: 'tabs' }),
        f.text('tip', 'Tip onderaan', { instructions: 'Leeg = geen tip.' }),
        f.repeater('varianten', 'Manieren van bereiden', [
            f.text('naam', 'Naam van het tabblad', { width: 30, instructions: 'Bijvoorbeeld Puur of Met melk.' }),
            f.repeater('stappen', 'Stappen', [
                f.select('icoon', 'Icoon', { lepel: 'Lepel', temperatuur: 'Temperatuur', druppel: 'Druppel', klok: 'Klok' }, { width: 15 }),
                f.text('waarde', 'Grote waarde', { width: 20, instructions: 'Bijvoorbeeld "2 g" of "80 °C".' }),
                f.text('label', 'Label', { width: 25 }),
                f.textarea('uitleg', 'Uitleg', { width: 40, rows: 3 }),
            ], { layout: 'table', button: 'Stap toevoegen' }),
        ], { layout: 'block', button: 'Manier toevoegen', instructions: 'Eén tabblad per manier. De stappen worden genummerd.' }),
    ], { achtergrond: false, where: ['pagina', 'product'] });

};
