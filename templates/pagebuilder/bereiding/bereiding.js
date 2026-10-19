/**
 * Bereiding: tabs (Puur / Met melk) met pijltoetsen
 *
 * @package morimatcha
 */
(function() {
    'use strict';

    document.querySelectorAll('[data-bereiding]').forEach(function(blok) {
        const tabs = Array.from(blok.querySelectorAll('[role="tab"]'));
        const panelen = Array.from(blok.querySelectorAll('[role="tabpanel"]'));
        if (tabs.length < 2) {
            return;
        }

        const kies = function(index, focus) {
            tabs.forEach(function(tab, i) {
                const actief = i === index;
                tab.classList.toggle('is-actief', actief);
                tab.setAttribute('aria-selected', actief ? 'true' : 'false');
                tab.tabIndex = actief ? 0 : -1;
                panelen[i].hidden = !actief;
            });
            if (focus) {
                tabs[index].focus();
            }
        };

        tabs.forEach(function(tab, i) {
            tab.addEventListener('click', function() {
                kies(i, false);
            });
            tab.addEventListener('keydown', function(event) {
                if (event.key === 'ArrowRight') {
                    event.preventDefault();
                    kies((i + 1) % tabs.length, true);
                } else if (event.key === 'ArrowLeft') {
                    event.preventDefault();
                    kies((i - 1 + tabs.length) % tabs.length, true);
                }
            });
        });
    });
})();
