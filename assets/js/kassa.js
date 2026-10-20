/**
 * Kassa: postcode en huisnummer vullen straat en plaats aan (PDOK Locatieserver), huisnummer controleren, kortingscode.
 * Vereist jQuery omdat WooCommerce de kassa daarmee ververst.
 */
/* global wc_checkout_params */
(function ($) {
    'use strict';

    const PDOK = 'https://api.pdok.nl/bzk/locatieserver/search/v3_1/free';
    let timer = null;

    function veld(naam) {
        return $('#' + naam); 
    }
    function rij(naam) {
        return $('#' + naam + '_field'); 
    }

    function fout(naam, tekst) {
        const r = rij(naam);
        r.find('.veld__fout').remove();
        r.removeClass('is-fout');
        if (tekst) {
            r.addClass('is-fout');
            r.append('<span class="veld__fout" role="alert">' + $('<i>').text(tekst).html() + '</span>');
            veld(naam).attr('aria-invalid', 'true');
        } else {
            veld(naam).removeAttr('aria-invalid');
        }
    }

    function huisnummerGeldig(waarde) {
        return /^\d+\s?[a-zA-Z0-9\- /]{0,6}$/.test($.trim(waarde));
    }

    function auto(waarde) {
        const r = rij('billing_address_1').add(rij('billing_city'));
        r.addClass('is-auto');
        veld('billing_address_1').val(waarde.straat).prop('readonly', true);
        veld('billing_city').val(waarde.plaats).prop('readonly', true);
        $('[data-straat-hulp]').prop('hidden', false);
        $(document.body).trigger('update_checkout');
    }

    function vrijgeven() {
        rij('billing_address_1').add(rij('billing_city')).removeClass('is-auto');
        veld('billing_address_1').prop('readonly', false);
        veld('billing_city').prop('readonly', false);
        $('[data-straat-hulp]').prop('hidden', true);
    }

    function zoek() {
        const postcode = $.trim(veld('billing_postcode').val()).replace(/\s+/g, '').toUpperCase();
        const nummer = $.trim(veld('billing_huisnummer').val());
        if (!/^[1-9][0-9]{3}[A-Z]{2}$/.test(postcode)) {
            return;
        }
        const q = nummer && huisnummerGeldig(nummer)
            ? 'postcode:' + postcode + ' and huisnummer:' + parseInt(nummer, 10)
            : postcode;
        const fq = nummer && huisnummerGeldig(nummer) ? 'type:adres' : 'type:postcode';
        $.getJSON(PDOK, { q: q, fq: fq, rows: 1, fl: 'straatnaam,woonplaatsnaam' }).done(function (data) {
            const doc = data && data.response && data.response.docs && data.response.docs[0];
            if (doc && doc.straatnaam) {
                auto({ straat: doc.straatnaam, plaats: doc.woonplaatsnaam });
            } else {
                vrijgeven();
            }
        }).fail(vrijgeven);
    }

    $(function () {
        const form = $('form.checkout');
        if (!form.length) {
            return; 
        }

        form.on('input change', '#billing_postcode, #billing_huisnummer', function () {
            clearTimeout(timer);
            timer = setTimeout(zoek, 350);
        });

        form.on('blur', '#billing_huisnummer', function () {
            const w = $(this).val();
            fout('billing_huisnummer', w && !huisnummerGeldig(w) ? window.moriKassa.huisnummerFout : '');
        });

        form.on('checkout_place_order', function () {
            const w = veld('billing_huisnummer').val();
            if (!huisnummerGeldig(w)) {
                fout('billing_huisnummer', window.moriKassa.huisnummerFout);
                veld('billing_huisnummer').trigger('focus');
                return false;
            }
            fout('billing_huisnummer', '');
            return true;
        });

        // Kortingscode (het veld zit in de samenvatting, die WooCommerce steeds ververst)
        $(document.body).on('click', '[data-korting-knop]', function () {
            const code = $.trim($('#mori-kortingscode').val());
            const melding = $('[data-korting-melding]');
            if (!code) {
                return; 
            }
            $.post(wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'), {
                security: wc_checkout_params.apply_coupon_nonce,
                coupon_code: code,
            }).done(function (html) {
                const tekst = $('<div>').html(html).text().replace(/\s+/g, ' ').trim();
                const ok = /^\s*<div class="woocommerce-message/.test($.trim(html)) || $(html).hasClass('woocommerce-message');
                $(document.body).trigger('update_checkout');
                setTimeout(function () {
                    $('[data-korting-melding]').text(tekst).toggleClass('is-fout', !ok);
                }, 600);
            });
        });
        $(document.body).on('keydown', '#mori-kortingscode', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); $('[data-korting-knop]').trigger('click'); 
            }
        });

        // Bij laden: zijn postcode en huisnummer al ingevuld, vul dan aan
        if (veld('billing_postcode').val()) {
            zoek(); 
        }
    });
})(jQuery);
