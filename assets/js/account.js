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
