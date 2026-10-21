/**
 * Mijn account: vensters (dialogen) voor pauzeren, maat wijzigen en opzeggen.
 * Gebruikt het ingebouwde <dialog>: Esc sluit, focus blijft in het venster en keert terug naar de knop.
 */
(function () {
    'use strict';

    document.addEventListener('click', function (e) {
        const open = e.target.closest('[data-dialoog-open]');
        if (open) {
            const dlg = document.getElementById(open.getAttribute('data-dialoog-open'));
            if (dlg && typeof dlg.showModal === 'function') {
                // een venster in een venster: sluit het eerste
                document.querySelectorAll('dialog[open]').forEach(function (d) {
                    if (d !== dlg) {
                        d.close(); 
                    } 
                });
                dlg.showModal();
            }
            return;
        }
        if (e.target.closest('[data-dialoog-sluit]')) {
            const d = e.target.closest('dialog');
            if (d) {
                d.close(); 
            }
            return;
        }
        // klik op de achtergrond sluit
        if (e.target.tagName === 'DIALOG' && e.target.classList.contains('dialoog')) {
            e.target.close();
        }
    });
})();

/* Inloggen: wachtwoord tonen of verbergen */
document.querySelectorAll('[data-toon-wachtwoord]').forEach((knop) => {
    const veld = knop.parentElement.querySelector('input');
    const toon = knop.textContent;
    knop.addEventListener('click', () => {
        const zichtbaar = veld.type === 'text';
        veld.type = zichtbaar ? 'password' : 'text';
        knop.setAttribute('aria-pressed', zichtbaar ? 'false' : 'true');
        knop.textContent = zichtbaar ? toon : (knop.dataset.verberg || 'Verberg');
    });
});

/* Inloggen en account maken: twee tabbladen in dezelfde kaart. Zonder JavaScript staan ze onder elkaar. */
document.querySelectorAll('[data-inlog]').forEach((blok) => {
    const tabs = blok.querySelectorAll('.inlog__tabs [role="tab"]');
    const panelen = blok.querySelectorAll('[data-inlog-paneel]');
    const toon = (naam, focus) => {
        panelen.forEach((p) => { p.hidden = p.dataset.inlogPaneel !== naam; });
        tabs.forEach((t) => t.setAttribute('aria-selected', t.dataset.inlogTab === naam ? 'true' : 'false'));
        if (focus) {
            const eerste = blok.querySelector('[data-inlog-paneel="' + naam + '"] input:not([type="hidden"])');
            if (eerste) { eerste.focus(); }
        }
    };
    const start = blok.querySelector('.inlog__tabs [aria-selected="true"]');
    toon(start ? start.dataset.inlogTab : 'inloggen', false);
    blok.addEventListener('click', (e) => {
        const knop = e.target.closest('[data-inlog-tab]');
        if (knop) { toon(knop.dataset.inlogTab, true); }
    });
});

/* Inloggen en account maken: melding direct onder het veld. De server controleert dezelfde regels nog een keer. */
document.querySelectorAll('[data-inlog]').forEach((blok) => {
    let m = {};
    try { m = JSON.parse(blok.dataset.meldingen || '{}'); } catch (e) { m = {}; }
    const min = parseInt(blok.dataset.min || '10', 10);
    const hoofd = /\p{Lu}/u;
    const naamTekens = /^\p{L}[\p{L}' -]*$/u;
    const email = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    const regels = {
        voornaam: (v) => (!v ? m.voornaam_leeg : !naamTekens.test(v) ? m.naam_tekens : !/^\p{Lu}/u.test(v) ? m.voornaam_hoofdletter : ''),
        achternaam: (v) => (!v ? m.achternaam_leeg : !naamTekens.test(v) ? m.naam_tekens : !hoofd.test(v) ? m.achternaam_hoofdletter : ''),
        email: (v) => (!v ? m.email_leeg : !email.test(v) && v.indexOf('@') > -1 ? m.email : ''),
        'email-streng': (v) => (!v ? m.email_leeg : !email.test(v) ? m.email : ''),
        'wachtwoord-leeg': (v) => (!v ? m.wachtwoord_leeg : ''),
        wachtwoord: (v, form) => {
            if (!v) { return m.wachtwoord_leeg; }
            if (v.length < min) { return m.wachtwoord_kort; }
            if (!hoofd.test(v)) { return m.wachtwoord_hoofdletter; }
            if (!/\d/.test(v)) { return m.wachtwoord_cijfer; }
            const mail = form.querySelector('[data-regel="email-streng"]');
            return mail && mail.value.trim().toLowerCase() === v.toLowerCase() ? m.wachtwoord_email : '';
        },
        akkoord: (v, form, veld) => (veld.checked ? '' : m.voorwaarden),
    };

    const toon = (veld, tekst) => {
        const fout = document.getElementById(veld.id + '-fout');
        veld.classList.toggle('is-fout', !!tekst);
        if (tekst) { veld.setAttribute('aria-invalid', 'true'); } else { veld.removeAttribute('aria-invalid'); }
        if (fout) { fout.textContent = tekst || ''; fout.hidden = !tekst; }
    };
    const controleer = (veld, form) => {
        const regel = regels[veld.dataset.regel];
        if (!regel) { return ''; }
        const waarde = veld.type === 'checkbox' ? '' : veld.value.trim();
        const tekst = regel(veld.type === 'password' ? veld.value : waarde, form, veld) || '';
        toon(veld, tekst);
        return tekst;
    };

    blok.querySelectorAll('form').forEach((form) => {
        const velden = [...form.querySelectorAll('[data-regel]')];
        velden.forEach((veld) => {
            const gedaan = () => { veld.dataset.geraakt = '1'; controleer(veld, form); };
            veld.addEventListener('blur', gedaan);
            veld.addEventListener('input', () => { if (veld.dataset.geraakt) { controleer(veld, form); } });
            veld.addEventListener('change', () => { if (veld.type === 'checkbox') { veld.dataset.geraakt = '1'; controleer(veld, form); } });
        });
        form.addEventListener('submit', (e) => {
            let eerste = null;
            velden.forEach((veld) => {
                veld.dataset.geraakt = '1';
                if (controleer(veld, form) && !eerste) { eerste = veld; }
            });
            if (eerste) { e.preventDefault(); eerste.focus(); }
        });
    });
});
