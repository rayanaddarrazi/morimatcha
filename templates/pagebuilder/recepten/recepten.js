/**
 * Recepten: voortgangsbalkjes volgen de carrousel op mobiel
 *
 * @package morimatcha
 */
(function() {
    'use strict';

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
