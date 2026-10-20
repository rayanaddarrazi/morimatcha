/**
 * Productpagina: maatkiezer, aankoopkeuze (eenmalig of abonnement, niet voorgeselecteerd), aantal en toevoegen.
 *
 * @package morimatcha
 */

(function() {
    'use strict';

    const form = document.querySelector('[data-buybox]');
    if (!form) {
        return;
    }

    let config = {};
    try {
        config = JSON.parse(form.getAttribute('data-config'));
    } catch (e) {
        return;
    }

    const t = config.tekst;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Simpele sprintf: %s, %1$s, %2$s, %d
    const fmt = function(template) {
        const args = Array.prototype.slice.call(arguments, 1);
        let i = 0;
        return String(template).replace(/%(\d+)\$s|%s|%d/g, function(match, pos) {
            const value = pos ? args[parseInt(pos, 10) - 1] : args[i++];
            return value === undefined ? '' : value;
        });
    };

    const els = {
        prijs: form.querySelector('[data-prijs]'),
        prijsRegel: form.querySelector('[data-prijs-regel]'),
        gekozen: form.querySelector('[data-maat-gekozen]'),
        knop: form.querySelector('[data-toevoegen]'),
        hint: form.querySelector('[data-hint]'),
        hintTekst: form.querySelector('[data-hint-tekst]'),
        aantal: form.querySelector('[data-aantal]'),
        melding: form.querySelector('[data-melding]'),
        prijsEenmalig: form.querySelector('[data-prijs-eenmalig]'),
        prijsOud: form.querySelector('[data-prijs-oud]'),
        prijsMaand: form.querySelector('[data-prijs-maand]'),
        toelichting: form.querySelector('[data-abo-toelichting]'),
        etiket: document.querySelector('.product__hoofdbeeld .etiket__regel'),
    };

    const maten = Array.from(form.querySelectorAll('.maatoptie'));
    const keuzes = Array.from(form.querySelectorAll('[data-levering]'));

    const state = {
        sizeId: null,
        levering: null,
    };

    const checked = form.querySelector('[data-maat]:checked');
    state.sizeId = checked ? parseInt(checked.value, 10) : null;

    const sizeById = function(id) {
        return config.sizes.find(function(s) {
            return s.id === id; 
        });
    };

    // Korte crossfade bij prijswissel
    const wissel = function(callback) {
        if (reduceMotion) {
            callback();
            return;
        }
        form.classList.add('is-wisselt');
        window.setTimeout(function() {
            callback();
            form.classList.remove('is-wisselt');
        }, 140);
    };

    const knopLabel = function(tekst) {
        const label = els.knop.querySelector('.kj-button__label');
        if (label) {
            label.textContent = tekst;
        }
    };

    const render = function(animatie) {
        const size = sizeById(state.sizeId);
        const abo = state.levering === 'maand';

        const paint = function() {
            if (size) {
                if (els.gekozen) {
                    els.gekozen.textContent = fmt(t.gekozen, size.label);
                }
                if (els.prijs) {
                    els.prijs.textContent = abo ? size.maand : size.eenmalig;
                }
                if (els.prijsRegel) {
                    els.prijsRegel.textContent = abo
                        ? fmt(t.prijsMaand, size.label, size.eenmalig)
                        : fmt(t.prijsEenmalig, size.label, fmt(t.perGram, size.perGram.replace('€ ', '')));
                }
                if (els.prijsEenmalig) {
                    els.prijsEenmalig.textContent = size.eenmalig;
                }
                if (els.prijsOud) {
                    els.prijsOud.textContent = size.eenmalig;
                }
                if (els.prijsMaand) {
                    els.prijsMaand.textContent = size.maand;
                }
                if (els.toelichting) {
                    els.toelichting.textContent = fmt(t.toelichting, size.maand.replace('€ ', ''));
                }
                if (els.etiket) {
                    els.etiket.textContent = config.etiket.replace('{gram}', size.gram);
                }
            }

            // Knop en hint
            if (config.abonnement) {
                els.knop.disabled = !state.levering;
                knopLabel(abo && size ? fmt(t.knopAbo, size.maand.replace('€ ', '€ ')) : t.knop);
                if (els.hint) {
                    const toon = !state.levering || abo;
                    els.hint.hidden = !toon;
                    if (els.hintTekst) {
                        els.hintTekst.textContent = abo ? t.hintAbo : t.hint;
                    }
                }
            }
        };

        if (animatie) {
            wissel(paint);
        } else {
            paint();
        }

        maten.forEach(function(maat) {
            const input = maat.querySelector('input');
            maat.classList.toggle('is-geselecteerd', input.checked);
        });
    };

    form.addEventListener('change', function(event) {
        const maat = event.target.closest('[data-maat]');
        if (maat) {
            state.sizeId = parseInt(maat.value, 10);
            render(true);
            return;
        }
        const levering = event.target.closest('[data-levering]');
        if (levering) {
            state.levering = levering.value;
            render(true);
        }
    });

    // Aantal
    form.addEventListener('click', function(event) {
        const knop = event.target.closest('[data-qty]');
        if (!knop) {
            return;
        }
        const waarde = Math.max(1, Math.min(99, (parseInt(els.aantal.value, 10) || 1) + parseInt(knop.dataset.qty, 10)));
        els.aantal.value = waarde;
        form.querySelector('[data-qty="-1"]').disabled = waarde <= 1;
    });
    els.aantal.addEventListener('change', function() {
        const waarde = Math.max(1, Math.min(99, parseInt(els.aantal.value, 10) || 1));
        els.aantal.value = waarde;
        form.querySelector('[data-qty="-1"]').disabled = waarde <= 1;
    });

    // Toevoegen via AJAX, met formulier als terugval
    form.addEventListener('submit', function(event) {
        if (!window.moriCartApi) {
            return;
        }
        event.preventDefault();
        if (config.abonnement && !state.levering) {
            return;
        }
        if (config.variable && !state.sizeId) {
            return;
        }
        if (els.melding) {
            els.melding.hidden = true;
        }
        els.knop.disabled = true;
        window.moriCartApi.add({
            product_id: config.productId,
            variation_id: config.variable ? state.sizeId : 0,
            mori_levering: state.levering || 'eenmalig',
            aantal: parseInt(els.aantal.value, 10) || 1,
        }).catch(function(error) {
            if (els.melding) {
                els.melding.textContent = error.message;
                els.melding.hidden = false;
            }
        }).finally(function() {
            els.knop.disabled = config.abonnement && !state.levering;
        });
    });

    render(false);

    /* ---------- Blik in 3D: laadt Three.js pas na een klik, met de foto als terugval ---------- */
    const hoofd = document.querySelector('[data-blik3d]');
    if (hoofd) {
        const knop = hoofd.querySelector('[data-blik3d-knop]');
        const podium = hoofd.querySelector('[data-blik3d-stage]');
        const hint = hoofd.querySelector('[data-blik3d-hint]');
        let opruimen = null;
        let laden = false;

        const webgl = function() {
            try {
                const c = document.createElement('canvas');
                return !!(window.WebGLRenderingContext && (c.getContext('webgl2') || c.getContext('webgl')));
            } catch (e) {
                return false;
            }
        };
        if (!webgl()) {
            knop.hidden = true;
        }

        const sluiten = function() {
            if (opruimen) {
                opruimen(); opruimen = null; 
            }
            podium.hidden = true;
            if (hint) {
                hint.hidden = true; 
            }
            hoofd.classList.remove('is-3d');
            knop.setAttribute('aria-pressed', 'false');
            knop.querySelector('span').textContent = knop.getAttribute('data-label-open');
        };

        knop.addEventListener('click', function() {
            if (hoofd.classList.contains('is-3d')) {
                sluiten();
                return;
            }
            if (laden) {
                return; 
            }
            laden = true;
            const t0 = performance.now();
            const regelEl = document.querySelector('.product__hoofdbeeld .etiket__regel');
            podium.hidden = false;
            hoofd.classList.add('is-3d');
            import(hoofd.getAttribute('data-module')).then(function(mod) {
                return mod.start(podium, {
                    teken: hoofd.getAttribute('data-teken'),
                    naam: hoofd.getAttribute('data-naam'),
                    regel: regelEl ? regelEl.textContent : '',
                    textuur: hoofd.getAttribute('data-textuur'),
                    glans: hoofd.getAttribute('data-glans'),
                    reduceMotion: reduceMotion,
                });
            }).then(function(stop) {
                opruimen = stop;
                knop.setAttribute('aria-pressed', 'true');
                knop.querySelector('span').textContent = knop.getAttribute('data-label-dicht');
                if (hint) {
                    hint.hidden = false; 
                }
                podium.focus({ preventScroll: true });
                const ms = Math.round(performance.now() - t0);
                hoofd.setAttribute('data-laadtijd', String(ms));
                if (window.console && console.info) {
                    console.info('Blik 3D geladen in ' + ms + ' ms'); 
                }
            }).catch(function() {
                // terugval: de foto blijft staan
                sluiten();
                knop.hidden = true;
            }).finally(function() {
                laden = false; 
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && hoofd.classList.contains('is-3d')) {
                sluiten(); knop.focus(); 
            }
        });
    }
})();
