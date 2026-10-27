/**
 * Pagebuilder Blocks JavaScript
 * Auto-generated file - Do not edit manually
 * 
 * @package morimatcha
 * @version 1.0.0
 * @author Kay Jilesen
 * 
 * This file combines all pagebuilder block JavaScript files.
 * Generated: 2026-10-21T10:12:07.543Z
 */

(function() {
    'use strict';

    // bereiding block
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

    // recepten block
    document.querySelectorAll('[data-carrousel]').forEach(function(lijst) {
            const voortgang = lijst.parentElement.querySelector('.recepten__voortgang');
            if (!voortgang) {
                return;
            }
            const dots = voortgang.querySelectorAll('span');
            lijst.addEventListener('scroll', function() {
                const kaart = lijst.querySelector('.recept');
                if (!kaart) {
                    return;
                }
                const index = Math.round(lijst.scrollLeft / (kaart.offsetWidth + 16));
                dots.forEach(function(dot, i) {
                    dot.classList.toggle('is-actief', i === index);
                });
            }, { passive: true });
        });

})();
