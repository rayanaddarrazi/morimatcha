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
