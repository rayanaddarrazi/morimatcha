/**
 * Blokken voor de pagina Abonnement: Zo werkt het (stappen met foto), Prijstabel, Opzeggen (met voorbeeldkaart).
 */
module.exports = ({ f, defineLayout }) => {

    defineLayout('stappen_kaarten', 'Stappen met foto (zo werkt het)', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.text('stap_label', 'Label voor het stapnummer', { default: 'Stap', instructions: 'Wordt "Stap 01", "Stap 02", ...' }),
        f.repeater('stappen', 'Stappen', [
            f.image('foto', 'Foto', { width: 25 }),
            f.bool('toon_etiket', 'Etiket op het blik', { width: 10, default: false }),
            f.number('zoom', 'Zoom', { width: 10, default: 1, step: 0.05, min: 1, instructions: '1 = geen zoom.' }),
            f.text('titel', 'Titel', { width: 25 }),
            f.textarea('tekst', 'Tekst', { width: 35, rows: 3 }),
        ], { layout: 'block', button: 'Stap toevoegen' }),
    ], { achtergrondDefault: 'wit', where: ['pagina'] });

    defineLayout('prijstabel', 'Prijstabel per maat', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('intro', 'Intro'),
        f.post('product', 'Product', 'product', { required: true, instructions: 'De maten en prijzen komen uit WooCommerce, de abonnementsprijs uit Instellingen Mori > Aankoop.' }),
        f.group('koppen', 'Kolomkoppen', [
            f.text('maat', 'Kolom 1', { default: 'Maat', width: 25 }),
            f.text('eenmalig', 'Kolom 2', { default: 'Eenmalig', width: 25 }),
            f.text('abonnement', 'Kolom 3', { default: 'Abonnement', width: 25 }),
            f.text('per_gram', 'Kolom 4', { default: 'Per gram met abonnement', width: 25 }),
        ]),
    ], { achtergrondDefault: 'rijstpapier', where: ['pagina'] });

    defineLayout('opzeggen', 'Opzeggen (stappen en voorbeeldkaart)', [
        f.tab('Inhoud'),
        f.text('eyebrow', 'Eyebrow'),
        f.text('titel', 'Titel'),
        f.textarea('intro', 'Intro'),
        f.repeater('stappen', 'Stappen', [
            f.text('tekst', 'Tekst'),
        ], { layout: 'table', button: 'Stap toevoegen' }),
        f.group('kaart', 'Voorbeeldkaart van een abonnement', [
            f.text('label', 'Label', { default: 'Abonnement', width: 25 }),
            f.text('titel', 'Titel', { width: 25 }),
            f.text('frequentie', 'Frequentie', { width: 25 }),
            f.text('status', 'Status', { default: 'Actief', width: 25 }),
            f.text('volgende_label', 'Volgende levering: label', { width: 33 }),
            f.text('volgende_waarde', 'Volgende levering: waarde', { width: 33 }),
            f.text('bedrag_label', 'Bedrag: label', { width: 33 }),
            f.text('bedrag_waarde', 'Bedrag: waarde', { width: 33 }),
            f.text('betaal_label', 'Betaalwijze: label', { width: 33 }),
            f.text('betaal_waarde', 'Betaalwijze: waarde', { width: 33 }),
            f.text('uitleg_links', 'Regel links'),
            f.text('uitleg_rechts', 'Regel rechts'),
            f.number('voortgang', 'Voortgang (procent)', { default: 60 }),
            f.text('knop_pauzeren', 'Knop pauzeren', { width: 33 }),
            f.text('knop_maat', 'Knop maat wijzigen', { width: 33 }),
            f.text('knop_opzeggen', 'Knop opzeggen', { width: 33 }),
        ]),
    ], { achtergrondDefault: 'mos', where: ['pagina'] });

};
