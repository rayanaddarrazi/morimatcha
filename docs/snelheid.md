# Snelheid van de productpagina

## Doel (vooraf bepaald)
- Lighthouse mobiel: prestaties 90 of hoger, toegankelijkheid 95 of hoger.
- LCP onder 2,5 s, CLS onder 0,1, TBT onder 200 ms.
- Geen jQuery en geen WooCommerce-scripts op pagina's die ze niet nodig hebben.
- 3D-blik laadt niet mee met de pagina.

## Maatregelen
- WooCommerce-scripts en jQuery Migrate alleen laden op kassa en account (`inc/woocommerce.php`).
- Fraunces ingekort (vaste SOFT en WONK, gewicht 300 tot 400): 120 kB naar 58 kB.
- Foto's als WebP met `srcset`, `loading="lazy"` onder de vouw, eerste foto `fetchpriority="high"`.
- Eén CSS-bestand (110 kB, 19 kB gzip), scripts met `defer`.
- 3D-blik als apart bestand, pas bij een klik geladen.

## Metingen (lokaal, 19 oktober 2026)
| Pagina | Mobiel gesimuleerd (trage 4G, zonder compressie) | Mobiel zonder vertraging |
|---|---|---|
| Home | prestaties 85, LCP 4,0 s | 100, LCP 0,4 s |
| Product | prestaties 80, LCP 4,6 s | 100, LCP 0,4 s |
| Shop | prestaties 87, LCP 3,9 s | 100, LCP 0,3 s |

Toegankelijkheid 94 tot 100, best practices 100. De gesimuleerde score is lager omdat de lokale MAMP-server geen compressie en geen cache-headers stuurt: het CSS-bestand gaat daar ongecomprimeerd (110 kB) over de simulatielijn. Op de livesite moeten gzip of brotli en caching aan staan (zie hieronder). Meet daarna opnieuw.

## 3D-blik, voor en na
| | Voor de klik | Na de klik |
|---|---|---|
| Overdracht | 665 kB | +541 kB (ongecomprimeerd, 132 kB gzip) |
| Tijd tot het blik draait | n.v.t. | ca. 370 tot 440 ms (lokaal, software-rendering) |

De pagina zelf wordt dus niet trager. Alleen wie het blik opent betaalt de extra 132 kB.

## Instellingen voor de server
Zet `mod_deflate` en `mod_expires` aan (in MAMP staan ze uitgeschakeld in `httpd.conf`). Voorbeeld voor `.htaccess`:

```
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/css application/javascript image/svg+xml
</IfModule>
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType text/css "access plus 1 year"
  ExpiresByType application/javascript "access plus 1 year"
  ExpiresByType font/woff2 "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
</IfModule>
```
