/**
 * Mori Matcha, algemeen gedrag (vanilla JS, geen jQuery)
 *
 * - header vast na 80 px scrollen
 * - mobiel menu en winkelwagenpaneel (focus trap, Esc, scroll-lock)
 * - inkomende animaties (IntersectionObserver), hero, parallax
 * - accordeon (aria-expanded, hoogte-animatie via grid-template-rows)
 * - winkelwagen via AJAX
 *
 * @package morimatcha
 */

(function() {
    'use strict';

    const root = document.documentElement;
    root.classList.remove('no-js');
    root.classList.add('js');

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Header: vast na 80 px ---------- */
    // mededelingenbalk: teksten wisselen om de 4 seconden, niet bij hover/focus of bij verminderde beweging
    const balk = document.querySelector('[data-aankondiging]');
    if (balk) {
        const items = [...balk.querySelectorAll('li')];
        if (items.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            let nu = 0;
            let pauze = false;
            balk.addEventListener('mouseenter', () => { pauze = true; });
            balk.addEventListener('mouseleave', () => { pauze = false; });
            setInterval(() => {
                if (pauze || document.hidden) return;
                const oud = items[nu];
                nu = (nu + 1) % items.length;
                oud.classList.remove('is-actief');
                oud.classList.add('is-weg');
                oud.setAttribute('aria-hidden', 'true');
                items[nu].classList.remove('is-weg');
                items[nu].classList.add('is-actief');
                items[nu].removeAttribute('aria-hidden');
                setTimeout(() => oud.classList.remove('is-weg'), 450);
            }, 4000);
        }
    }

    const header = document.querySelector('[data-header]');
    if (header) {
        const onScroll = function() {
            header.classList.toggle('is-vast', window.scrollY > 80);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* ---------- Focus trap voor panelen ---------- */
    const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select, textarea, [tabindex]:not([tabindex="-1"])';
    const trapFocus = function(event, container) {
        if (event.key !== 'Tab') {
            return;
        }
        const focusable = Array.from(container.querySelectorAll(focusableSelector)).filter(function(el) {
            return el.offsetParent !== null;
        });
        if (!focusable.length) {
            return;
        }
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    };

    /* ---------- Mobiel menu ---------- */
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && mobileMenu) {
        const setMenu = function(open) {
            menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            mobileMenu.classList.toggle('is-open', open);
            mobileMenu.setAttribute('aria-hidden', open ? 'false' : 'true');
            mobileMenu.inert = !open;
            root.classList.toggle('menu-open', open);
            if (open) {
                const close = mobileMenu.querySelector('.mobile-menu__sluiten');
                if (close) {
                    close.focus({ preventScroll: true });
                }
            } else {
                menuToggle.focus({ preventScroll: true });
            }
        };

        menuToggle.addEventListener('click', function() {
            setMenu(true);
        });
        mobileMenu.addEventListener('click', function(event) {
            if (event.target.closest('[data-menu-close]')) {
                setMenu(false);
            }
        });
        document.addEventListener('keydown', function(event) {
            if (!mobileMenu.classList.contains('is-open')) {
                return;
            }
            if (event.key === 'Escape') {
                setMenu(false);
            } else {
                trapFocus(event, mobileMenu.querySelector('.mobile-menu__paneel'));
            }
        });
        window.matchMedia('(min-width: 1024px)').addEventListener('change', function(event) {
            if (event.matches && mobileMenu.classList.contains('is-open')) {
                setMenu(false);
            }
        });
    }

    /* ---------- Zoeken ---------- */
    const zoekKnop = document.querySelector('[data-zoek-open]');
    const zoekPaneel = document.getElementById('zoek-paneel');
    if (zoekKnop && zoekPaneel) {
        const setZoek = function(open) {
            zoekPaneel.hidden = !open;
            root.classList.toggle('menu-open', open);
            zoekKnop.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open) {
                zoekPaneel.querySelector('input[type="search"]').focus();
            } else {
                zoekKnop.focus({ preventScroll: true });
            }
        };
        zoekKnop.addEventListener('click', function() {
            setZoek(zoekPaneel.hidden);
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !zoekPaneel.hidden) {
                setZoek(false);
            }
        });
        zoekPaneel.addEventListener('click', function(event) {
            if (event.target.closest('[data-zoek-sluit]')) {
                setZoek(false);
            }
        });
    }

    /* ---------- Winkelwagenpaneel ---------- */
    const drawer = document.getElementById('cart-drawer');
    let drawerOpener = null;

    const setDrawer = function(open) {
        if (!drawer) {
            return;
        }
        drawer.classList.toggle('is-open', open);
        drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
        drawer.inert = !open;
        root.classList.toggle('cart-open', open);
        if (open) {
            const close = drawer.querySelector('.cart-drawer__sluiten');
            if (close) {
                close.focus({ preventScroll: true });
            }
        } else if (drawerOpener) {
            drawerOpener.focus({ preventScroll: true });
        }
    };

    // Vanaf de (omgeleide) winkelwagenpagina: open het paneel direct
    if (drawer && /[?&]winkelwagen=open/.test(window.location.search)) {
        setDrawer(true);
    }

    const cartRequest = function(data) {
        if (typeof window.moriCart === 'undefined') {
            return Promise.reject(new Error('Geen winkelwagen'));
        }
        const body = new URLSearchParams(Object.assign({ action: 'mori_cart', nonce: window.moriCart.nonce }, data));
        return fetch(window.moriCart.ajax, { method: 'POST', body: body, credentials: 'same-origin' })
            .then(function(response) {
                return response.json();
            })
            .then(function(json) {
                if (!json.success) {
                    throw new Error((json.data && json.data.melding) || 'Mislukt');
                }
                applyCart(json.data);
                return json.data;
            });
    };

    const applyCart = function(data) {
        const inner = drawer ? drawer.querySelector('.cart-drawer__binnen') : null;
        if (inner && typeof data.drawer === 'string') {
            inner.innerHTML = data.drawer;
        }
        document.querySelectorAll('[data-cart-count]').forEach(function(el) {
            el.textContent = data.count;
            const link = el.closest('[data-cart-label]');
            if (link) {
                link.setAttribute('aria-label', link.getAttribute('data-cart-label').replace('%d', data.count));
            }
        });
    };

    // Globaal beschikbaar voor de productpagina
    window.moriCartApi = {
        add: function(data) {
            return cartRequest(Object.assign({ actie: 'toevoegen' }, data)).then(function() {
                setDrawer(true);
            });
        },
        open: function() {
            setDrawer(true);
        },
    };

    document.addEventListener('click', function(event) {
        const opener = event.target.closest('[data-cart-open]');
        if (opener && drawer) {
            event.preventDefault();
            drawerOpener = opener;
            setDrawer(true);
            return;
        }
        if (event.target.closest('[data-cart-close]')) {
            setDrawer(false);
            return;
        }
        const snel = event.target.closest('[data-snel-toevoegen]');
        if (snel) {
            event.preventDefault();
            snel.disabled = true;
            drawerOpener = snel;
            window.moriCartApi.add({ product_id: snel.getAttribute('data-snel-toevoegen'), mori_levering: 'eenmalig', aantal: 1 }).finally(function() {
                snel.disabled = false;
            });
            return;
        }
        const qty = event.target.closest('[data-cart-qty]');
        if (qty) {
            const regel = qty.closest('.cart-regel');
            const huidig = parseInt(regel.querySelector('.aantal__waarde').textContent, 10) || 1;
            cartRequest({ actie: 'aantal', key: regel.dataset.key, aantal: Math.max(0, huidig + parseInt(qty.dataset.cartQty, 10)) });
            return;
        }
        const remove = event.target.closest('[data-cart-remove]');
        if (remove) {
            cartRequest({ actie: 'verwijderen', key: remove.closest('.cart-regel').dataset.key });
        }
    });

    document.addEventListener('keydown', function(event) {
        if (!drawer || !drawer.classList.contains('is-open')) {
            return;
        }
        if (event.key === 'Escape') {
            setDrawer(false);
        } else {
            trapFocus(event, drawer.querySelector('.cart-drawer__paneel'));
        }
    });

    /* ---------- Accordeon ---------- */
    document.addEventListener('click', function(event) {
        const toggle = event.target.closest('[data-accordeon]');
        if (!toggle) {
            return;
        }
        const item = toggle.closest('.accordeon__item');
        const open = toggle.getAttribute('aria-expanded') !== 'true';
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (item) {
            item.classList.toggle('is-open', open);
        }
    });

    /* ---------- Inkomende animaties ---------- */
    const revealItems = document.querySelectorAll('[data-reveal]');
    if (revealItems.length) {
        if (reduceMotion || !('IntersectionObserver' in window)) {
            revealItems.forEach(function(el) {
                el.classList.add('is-zichtbaar');
            });
        } else {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-zichtbaar');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -5% 0px' });
            revealItems.forEach(function(el) {
                observer.observe(el);
            });
        }
    }

    /* ---------- Hero: laden ---------- */
    document.querySelectorAll('.hero').forEach(function(hero) {
        window.requestAnimationFrame(function() {
            window.requestAnimationFrame(function() {
                hero.classList.add('is-geladen');
            });
        });
    });

    /* ---------- Parallax (alleen desktop, max. 40 px) ---------- */
    const parallaxItems = document.querySelectorAll('[data-parallax]');
    const desktop = window.matchMedia('(min-width: 1024px)');
    if (parallaxItems.length && !reduceMotion) {
        let ticking = false;
        const update = function() {
            ticking = false;
            if (!desktop.matches) {
                parallaxItems.forEach(function(el) {
                    el.style.setProperty('--parallax', '0px');
                });
                return;
            }
            const vh = window.innerHeight;
            parallaxItems.forEach(function(el) {
                const rect = el.getBoundingClientRect();
                if (rect.bottom < 0 || rect.top > vh) {
                    return;
                }
                const progress = (rect.top + rect.height / 2 - vh / 2) / (vh / 2 + rect.height / 2);
                const shift = Math.max(-1, Math.min(1, progress)) * -40;
                el.style.setProperty('--parallax', shift.toFixed(1) + 'px');
            });
        };
        window.addEventListener('scroll', function() {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(update);
            }
        }, { passive: true });
        window.addEventListener('resize', update);
        update();
    }

})();

// Sorteren in de shop: direct toepassen zonder knop
(function() {
    'use strict';
    document.querySelectorAll('select[data-auto-submit]').forEach(function(select) {
        select.addEventListener('change', function() {
            select.form.submit();
        });
    });
})();
