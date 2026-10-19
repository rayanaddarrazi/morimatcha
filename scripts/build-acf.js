/**
 * Genereert de ACF-veldgroepen in acf-json/ uit compacte definities (scripts/acf/*.js).
 * Gebruik:  node scripts/build-acf.js
 *
 * Werkwijze (zoals de boilerplate): elk blok krijgt een eigen groep "Layout -- X" (inactief) die in
 * group_pagebuilder.json via een clone-veld wordt ingeladen. Zet nooit velden direct in de pagebuilder.
 */

const fs = require('fs');
const path = require('path');

const outDir = path.join(__dirname, '..', 'acf-json');
const modified = Math.floor(Date.now() / 1000);

const wrapper = (width) => ({ width: width ? String(width) : '', class: '', id: '' });

const base = (type, name, label, o = {}) => {
    const field = {
        key: '',
        label,
        name,
        aria_label: '',
        type,
        instructions: o.instructions || '',
        required: o.required ? 1 : 0,
        conditional_logic: o.conditional || 0,
        wrapper: wrapper(o.width),
    };
    return Object.assign(field, o.extra || {});
};

const f = {
    tab: (label, o = {}) => base('tab', 'tab_' + label.toLowerCase().replace(/[^a-z0-9]+/g, '_'), label, { ...o, extra: { placement: 'top', endpoint: 0 } }),
    text: (name, label, o = {}) => base('text', name, label, { ...o, extra: { default_value: o.default || '', maxlength: '', placeholder: o.placeholder || '', prepend: '', append: '' } }),
    textarea: (name, label, o = {}) => base('textarea', name, label, { ...o, extra: { default_value: o.default || '', maxlength: '', rows: o.rows || 3, placeholder: '', new_lines: '' } }),
    number: (name, label, o = {}) => base('number', name, label, { ...o, extra: { default_value: o.default !== undefined ? o.default : '', min: o.min !== undefined ? o.min : '', max: '', step: o.step || '', placeholder: '', prepend: '', append: o.append || '' } }),
    email: (name, label, o = {}) => base('email', name, label, { ...o, extra: { default_value: '', placeholder: '', prepend: '', append: '' } }),
    url: (name, label, o = {}) => base('url', name, label, { ...o, extra: { default_value: '', placeholder: '' } }),
    link: (name, label, o = {}) => base('link', name, label, { ...o, extra: { return_format: 'array' } }),
    image: (name, label, o = {}) => base('image', name, label, { ...o, extra: { return_format: 'array', library: 'all', min_width: '', min_height: '', min_size: '', max_width: '', max_height: '', max_size: '', mime_types: '', preview_size: 'medium' } }),
    bool: (name, label, o = {}) => base('true_false', name, label, { ...o, extra: { message: o.message || '', default_value: o.default ? 1 : 0, ui: 1, ui_on_text: 'Aan', ui_off_text: 'Uit' } }),
    select: (name, label, choices, o = {}) => base('select', name, label, { ...o, extra: { choices, default_value: o.default || '', return_format: 'value', multiple: 0, allow_null: o.allowNull ? 1 : 0, ui: 0, ajax: 0, placeholder: '' } }),
    wysiwyg: (name, label, o = {}) => base('wysiwyg', name, label, { ...o, extra: { default_value: '', tabs: 'visual', toolbar: 'basic', media_upload: 0, delay: 0 } }),
    post: (name, label, postType, o = {}) => base('post_object', name, label, { ...o, extra: { post_type: [].concat(postType), post_status: '', taxonomy: '', return_format: 'id', multiple: o.multiple ? 1 : 0, allow_null: 1, bidirectional: 0, ui: 1, bidirectional_target: [] } }),
    repeater: (name, label, sub, o = {}) => base('repeater', name, label, { ...o, extra: { layout: o.layout || 'block', pagination: 0, min: o.min || 0, max: o.max || 0, collapsed: '', button_label: o.button || 'Rij toevoegen', rows_per_page: 20, sub_fields: sub } }),
    group: (name, label, sub, o = {}) => base('group', name, label, { ...o, extra: { layout: 'block', sub_fields: sub } }),
};

/** Kent sleutels toe aan velden (recursief). */
const keyFields = (fields, prefix) => fields.map((field) => {
    const copy = Object.assign({}, field);
    copy.key = 'field_' + prefix + '_' + copy.name;
    if (copy.sub_fields) {
        copy.sub_fields = keyFields(copy.sub_fields, prefix + '_' + copy.name);
    }
    return copy;
});

const stripUnderscoreKeys = (obj) => JSON.parse(JSON.stringify(obj).replace(/"aria_label"/g, '"aria-label"'));

const groups = [];

const defineGroup = (slug, title, fields, location, extra = {}) => {
    const group = Object.assign({
        key: 'group_' + slug,
        title,
        fields: keyFields(fields, slug),
        location,
        menu_order: 0,
        position: 'normal',
        style: 'default',
        label_placement: 'top',
        instruction_placement: 'label',
        hide_on_screen: '',
        active: true,
        description: '',
        show_in_rest: 0,
        modified,
    }, extra);
    groups.push(group);
    return group;
};

const layoutLocation = [[
    { param: 'post_type', operator: '==', value: 'page' },
    { param: 'page_template', operator: '!=', value: 'default' },
]];

const layouts = [];

/** Een blok (flexible content layout) met eigen veldgroep. */
const defineLayout = (slug, label, fields, opts = {}) => {
    const extra = [
        f.tab('Opmaak'),
    ];
    if (opts.achtergrond !== false) {
        extra.push(f.select('achtergrond', 'Achtergrondkleur', opts.achtergrond || { rijstpapier: 'Rijstpapier', wit: 'Wit', gerst: 'Gerst', mos: 'Mos (donker)', bosgroen: 'Bosgroen (donker)' }, { default: opts.achtergrondDefault || 'rijstpapier', instructions: 'Kleur van het vlak achter dit blok.' }));
    }
    extra.push(f.select('ruimte', 'Ruimte boven en onder (desktop)', { standaard: 'Standaard van dit blok', 80: '80 px', 96: '96 px', 128: '128 px', 144: '144 px', 160: '160 px' }, { default: 'standaard', instructions: 'Past de witruimte aan zonder de mobiele weergave te veranderen.' }));
    extra.push(f.text('anker', 'Anker', { instructions: 'Optioneel. Naam om naar dit blok te linken, bijvoorbeeld "bereiden" voor #bereiden.' }));
    defineGroup('layout_' + slug, 'Layout -- ' + label, fields.concat(extra), layoutLocation, { active: false, menu_order: 9999 });
    layouts.push({ slug, label, where: opts.where || ['pagina', 'product'] });
};

const loadDefinitions = () => {
    const dir = path.join(__dirname, 'acf');
    fs.readdirSync(dir).filter((file) => file.endsWith('.js')).sort().forEach((file) => {
        require(path.join(dir, file))({ f, defineGroup, defineLayout, layoutLocation });
    });
};

loadDefinitions();

// Flexible content (pagebuilder) met clone-velden, per soort inhoud (pagina of product)
const flexibleGroup = (key, fieldName, title, kind, location, instructions) => {
    const list = {};
    layouts.filter((layout) => layout.where.includes(kind)).forEach(({ slug, label }) => {
        list['layout_' + slug] = {
            key: 'layout_' + slug + '_' + kind,
            name: slug,
            label,
            display: 'block',
            sub_fields: [{
                key: 'field_' + slug + '_' + kind + '_clone',
                label,
                name: slug + '_fields',
                aria_label: '',
                type: 'clone',
                instructions: '',
                required: 0,
                conditional_logic: 0,
                wrapper: wrapper(),
                clone: ['group_layout_' + slug],
                display: 'seamless',
                layout: 'block',
                prefix_label: 0,
                prefix_name: 0,
            }],
            min: '',
            max: '',
        };
    });
    groups.push({
        key,
        title,
        fields: [{
            key: 'field_' + key,
            label: 'Blokken',
            name: fieldName,
            aria_label: '',
            type: 'flexible_content',
            instructions,
            required: 0,
            conditional_logic: 0,
            wrapper: wrapper(),
            layouts: list,
            min: '',
            max: '',
            button_label: 'Blok toevoegen',
        }],
        location,
        menu_order: 10,
        position: 'normal',
        style: 'seamless',
        label_placement: 'top',
        instruction_placement: 'label',
        hide_on_screen: kind === 'pagina' ? ['the_content', 'discussion', 'comments', 'slug', 'author', 'format', 'categories', 'tags'] : '',
        active: true,
        description: '',
        show_in_rest: 0,
        modified,
    });
};

flexibleGroup('group_pagebuilder', 'pagebuilder', 'Pagebuilder', 'pagina', [[{ param: 'post_type', operator: '==', value: 'page' }]], 'Bouw de pagina op uit blokken. Voeg toe, verwijder, dupliceer of sleep ze in een andere volgorde.');
flexibleGroup('group_productblokken', 'productblokken', 'Product -- Blokken onder de aankoop', 'product', [[{ param: 'post_type', operator: '==', value: 'product' }]], 'De blokken onder de aankoopkeuze op de productpagina: herkomst, smaak, bereiding, vragen. Sleep ze in een andere volgorde of voeg er toe.');

// Opruimen en schrijven
fs.readdirSync(outDir).filter((file) => file.startsWith('group_') && file.endsWith('.json')).forEach((file) => fs.unlinkSync(path.join(outDir, file)));
groups.forEach((group) => {
    fs.writeFileSync(path.join(outDir, group.key + '.json'), JSON.stringify(stripUnderscoreKeys(group), null, 4) + '\n');
});
console.log('✓ ' + groups.length + ' ACF-groepen geschreven (' + layouts.length + ' blokken)');
