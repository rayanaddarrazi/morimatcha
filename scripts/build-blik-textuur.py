"""
Maakt de textuur voor het 3D-blik uit de productfoto (scripts/seed/images/product-mori-matcha.jpg).
De foto wordt "afgerold": het blik is een cilinder, dus elk punt van de foto hoort bij een hoek om het blik.
Resultaat: assets/images/blik-textuur.webp (afgerolde foto, zonder lichtverloop) en assets/js/blik-data.json
(randen per rij, kromming en lichtverloop). Op de site draait een shader de textuur weer om het blik,
zodat het stilstaande beeld precies gelijk is aan de foto.

Gebruik:  python3 scripts/build-blik-textuur.py
"""
import json
import math
import os
import numpy as np
from PIL import Image

HIER = os.path.dirname(os.path.abspath(__file__))
THEMA = os.path.dirname(HIER)
FOTO = os.path.join(HIER, 'seed', 'images', 'product-mori-matcha.jpg')

im = np.array(Image.open(FOTO).convert('RGB')).astype(np.float64)
H, W, _ = im.shape
r, g, b = im[..., 0], im[..., 1], im[..., 2]

XC = 509.4           # midden van het blik in de foto (px), gemeten aan de randen van het etiket
# Kromming van de ringen door de camerahoek: A(h) = A0 + K * (h - H0). Gemeten aan de naad van het deksel
# (7,2 px) en de boven- en onderrand van het etiket (13,5 en 21,8 px); lager op het blik is de hoek groter.
A0, K, H0 = 7.17, 0.0415, 314.1

def kromming(h):
    return A0 + K * (np.asarray(h) - H0)

Y0, Y1 = 232, 872    # rijen waarin het blik staat
H_TOP = 243.8        # bovenste ring van het deksel (op de hoogte-as)
H_BOT = 832.4        # onderste ring van de voet

# Halve breedte per rij. Gemeten aan de randen van het etiket, dat precies tot aan de rand van het blik loopt:
# links 341,3 en rechts 677,5, dus midden 509,4 en straal 168,1. Het deksel is iets breder (169) en het blik loopt
# onderaan weer iets uit (168,5). Een zachte overgang zodat de rand geen trapjes krijgt.
rijen_y = np.arange(Y0, Y1)
straal = np.full(len(rijen_y), 168.1)
straal[rijen_y >= 254] = 169.0                      # deksel
# De bovenrand van het deksel is afgeschuind: de halve breedte groeit in tien rijen van 154 naar 168 (gemeten aan de foto)
afschuining = {232: 148.0, 238: 152.0, 240: 154.0, 242: 156.5, 244: 157.8, 246: 158.7, 248: 159.3, 250: 160.1, 252: 167.0}
for yy_, r_ in afschuining.items():
    straal[(rijen_y >= yy_) & (rijen_y < 254)] = r_
straal[rijen_y < 232 + 0] = 148.0
straal[(rijen_y >= 318) & (rijen_y <= 826)] = 168.1  # blik
straal[rijen_y > 826] = 169.4                       # voet
_kr = np.exp(-0.5 * (np.arange(-4, 5) / 1.0) ** 2)
_kr /= _kr.sum()
straal = np.convolve(np.pad(straal, 4, mode='edge'), _kr, mode='valid')

# Lichtverloop per materiaal: S(s) = helderheid op breedte s (-1..1) gedeeld door de helderheid in het midden.
# Het lak (groen) heeft een glans links, het papier (etiket) niet, dus ze krijgen elk een eigen verloop.
N_S = 96
H_LAB0, H_LAB1 = 459.6, 668.9   # etiket: bovenste en onderste ring (hoogte-as)
S_MIN, S_MAX = 0.35, 2.6

def verloop(rijen_lijst):
    bins = np.zeros((N_S, 3))
    cnt = np.zeros(N_S)
    for y in rijen_lijst:
        R = straal[y - Y0]
        for x in range(int(XC - R * 0.985), int(XC + R * 0.985) + 1):
            s = (x - XC) / R
            j = min(N_S - 1, max(0, int((s + 1) / 2 * N_S)))
            bins[j] += im[y, x]
            cnt[j] += 1
    v = bins / np.maximum(cnt, 1)[:, None]
    v = np.stack([np.convolve(np.pad(v[:, c], 2, mode='edge'), np.ones(5) / 5, mode='valid') for c in range(3)], axis=1)
    v = v / v[N_S // 2 - 3:N_S // 2 + 3].mean(axis=0)
    return np.clip(v, S_MIN, S_MAX)

S_LAK = verloop(list(range(335, 450)) + list(range(705, 830)))
S_PAPIER = verloop(list(range(480, 670)))
S = S_LAK

def licht_voor(h, s):
    j = min(N_S - 1, max(0, int((s + 1) / 2 * N_S)))
    return S_PAPIER[j] if H_LAB0 <= h <= H_LAB1 else S_LAK[j]

def bilineair(x, y):
    x0 = np.clip(np.floor(x).astype(int), 0, W - 2)
    y0 = np.clip(np.floor(y).astype(int), 0, H - 2)
    fx = (x - x0)[..., None]
    fy = (y - y0)[..., None]
    return (im[y0, x0] * (1 - fx) * (1 - fy) + im[y0, x0 + 1] * fx * (1 - fy)
            + im[y0 + 1, x0] * (1 - fx) * fy + im[y0 + 1, x0 + 1] * fx * fy)

S_RAND = 0.85
TEX_B = 1024
rijen = int(H_BOT - H_TOP)
tex = np.zeros((rijen, TEX_B, 3))
for u in range(TEX_B):
    theta = (u + 0.5) / TEX_B * 2 * math.pi - math.pi   # -pi..pi, 0 = voorkant
    if abs(theta) > math.pi / 2:
        theta = math.copysign(math.pi - abs(theta), theta)   # achterkant = spiegel van de voorkant
    s_ruw = math.sin(theta)
    s = max(-S_RAND, min(S_RAND, s_ruw))   # aan de rand glinstert het blik: dat zit in de 'glans'-laag, niet in de textuur
    c = math.cos(math.asin(s))
    h = H_TOP + np.arange(rijen) + 0.5
    y = h + kromming(h) * c
    i = np.clip((y - Y0).astype(int), 0, len(straal) - 1)
    x = XC + straal[i] * s
    foto = bilineair(x, y)
    sj = np.array([licht_voor(hh, s) for hh in h])
    tex[:, u, :] = foto / sj
# Vlakke textuur: lak en papier hebben overal dezelfde kleur. Glans, korrel en het licht zitten in de glanslaag.
mid = tex[:, int(TEX_B * 0.5 - TEX_B * 0.17):int(TEX_B * 0.5 + TEX_B * 0.17), :]
kleur = np.median(mid, axis=1)
tex = np.repeat(kleur[:, None, :], TEX_B, axis=1)
tex = np.clip(np.round(tex), 0, 255).astype('uint8')
Image.fromarray(tex).save(os.path.join(THEMA, 'assets', 'images', 'blik-textuur.webp'), 'WEBP', lossless=True, method=6)
Image.fromarray(tex).save('/private/tmp/claude-501/-Users-rayan-Sites-morimatcha/7c9e3439-10d2-41d9-b2c8-64c68db4e57e/scratchpad/blik/textuur.png')

# Glanslaag: wat de textuur niet kan (glans en rand), als verschil met de foto, vast op het scherm
dec = np.array(Image.open(os.path.join(THEMA, 'assets', 'images', 'blik-textuur.webp')).convert('RGB')).astype(np.float64)
TH, TW, _ = dec.shape
GX0, GX1, GY0, GY1 = 330, 690, Y0, Y1
res = np.full((GY1 - GY0, GX1 - GX0, 3), 128, dtype=np.uint8)
resf = np.zeros((GY1 - GY0, GX1 - GX0, 3))
def tex_sample(u, v):
    x = u * TW - 0.5
    y = np.clip(v * TH - 0.5, 0, TH - 1.001)
    x0 = np.floor(x).astype(int)
    fx = (x - x0)[..., None]
    y0 = np.floor(y).astype(int)
    fy = (y - y0)[..., None]
    a = dec[y0, x0 % TW]
    b2 = dec[y0, (x0 + 1) % TW]
    c2 = dec[y0 + 1, x0 % TW]
    d = dec[y0 + 1, (x0 + 1) % TW]
    return a * (1 - fx) * (1 - fy) + b2 * fx * (1 - fy) + c2 * (1 - fx) * fy + d * fx * fy
def licht_tabel(t, sx):
    xx = np.clip(sx * N_S - 0.5, 0, N_S - 1.001)
    i0 = np.floor(xx).astype(int)
    f = (xx - i0)[..., None]
    return t[i0] * (1 - f) + t[i0 + 1] * f
RAND_BOVEN = np.median(im[340:445, 344:347].reshape(-1, 3), axis=0)
RAND_ONDER = np.median(im[690:815, 344:347].reshape(-1, 3), axis=0)
for yy in range(GY0, GY1):
    idx = yy - Y0
    R = round((straal[idx] - 150) / 40 * 255) / 255 * 40 + 150   # zoals de shader hem uit de 8-bits tabel leest
    xs = np.arange(GX0, GX1, dtype=np.float64) + 0.5
    sv = (xs - XC) / R
    ok2 = np.abs(sv) < 0.9995
    if not ok2.any():
        continue
    sv_c = np.clip(sv, -0.9995, 0.9995)
    th = np.arcsin(sv_c)
    cc = np.cos(th)
    hh = (yy + 0.5 - (A0 - K * H0) * cc) / (1 + K * cc)
    uu = (th / (2 * math.pi) + 0.5)
    vv = (hh - H_TOP) / (H_BOT - H_TOP)
    alb = tex_sample(uu, vv)
    sx = sv_c * 0.5 + 0.5
    papier = (hh >= H_LAB0) & (hh <= H_LAB1)
    li = np.where(papier[:, None], licht_voor_tabel(S_PAPIER, sx) if False else licht_tabel(S_PAPIER, sx), licht_tabel(S_LAK, sx))
    voorspeld = alb * li
    echt = im[yy, GX0:GX1]
    verschil = np.clip(echt - voorspeld, -48, 48)   # scherpe randen (etiket, deksel) komen uit de textuur zelf, niet uit de glanslaag
    verschil[~ok2] = 0
    # Op de plek van de naad (links op het lak) komt alleen nog beeld uit de textuur: de glanslaag staat vast op het
    # scherm en zou de lijntjes daar laten staan terwijl het blik draait.
    naad_zone = (xs > 336) & (xs < 366) & ~papier & (hh >= 318) & (hh <= 830)
    if naad_zone.any():
        # Het stuk met de twee lijntjes: daar komt een zachte overgang van de rand naar het lak ernaast, zonder lijntjes
        # (die worden in de shader getekend) en zonder harde grens.
        boven_zone = hh[naad_zone] < H_LAB0
        rand = np.where(boven_zone[:, None], RAND_BOVEN, RAND_ONDER)
        binnen = echt[(xs >= 366) & (xs < 374)].mean(axis=0)
        t = np.clip((xs[naad_zone] - 346.0) / 22.0, 0.0, 1.0)
        t = (t * t * (3 - 2 * t))[:, None]
        doel = rand * (1 - t) + binnen * t
        verschil[naad_zone] = np.clip(doel - voorspeld[naad_zone], -48, 48)
    resf[yy - GY0] = verschil
    res[yy - GY0] = np.clip(np.round(verschil / 255 * 127.5 + 127.5), 0, 255).astype(np.uint8)
# (De naad, twee dunne zilveren lijntjes links, wordt in de shader getekend: zie blik3d.src.js.)
# Grover afronden (stappen van 4 niveaus, onzichtbaar) zodat het verliesvrij kleiner wordt
res = (np.round((res.astype(np.int32) - 128) / 4.0) * 4 + 128).clip(0, 255).astype(np.uint8)
Image.fromarray(res).save(os.path.join(THEMA, 'assets', 'images', 'blik-glans.webp'), 'WEBP', lossless=True, method=6)

data = {
    'glans': [GX0, GY0, GX1 - GX0, GY1 - GY0],
    'foto': 1024, 'xc': XC, 'a0': A0, 'k': K, 'h0': H0, 'y0': Y0, 'hTop': H_TOP, 'hBot': H_BOT,
    'straal': [round(float(v), 2) for v in straal],
    'lichtLak': [round(float(v), 4) for v in S_LAK.flatten()],
    'lichtPapier': [round(float(v), 4) for v in S_PAPIER.flatten()],
    'etiketRingen': [H_LAB0, H_LAB1],
    'etiket': {'links': 0.3408 * 1024, 'boven': 0.4583 * 1024, 'breedte': 0.3125 * 1024, 'hoogte': 0.2084 * 1024},
}
with open(os.path.join(THEMA, 'assets', 'js', 'blik-data.json'), 'w') as f:
    json.dump(data, f, separators=(',', ':'))
print('glans', os.path.getsize(os.path.join(THEMA, 'assets', 'images', 'blik-glans.webp')) // 1024, 'kB'); print('textuur', tex.shape, os.path.getsize(os.path.join(THEMA, 'assets', 'images', 'blik-textuur.webp')) // 1024, 'kB')


# Achtergrondplaat: de productfoto, met alleen een smalle strook langs de linker- en rechterrand van het blik vervangen door
# de achtergrond ernaast. Tijdens het draaien ligt het 3D-blik op deze plaat. Op de foto zelf zit langs de rand van het
# blik een donkergrijs randje; dat zou als vaste lijn achter het draaiende blik blijven staan. Het binnenste van het blik
# blijft de foto (daar ligt toch het 3D-blik overheen), zodat boven en onder het blik niets verandert.
plaat = np.array(Image.open(FOTO).convert('RGB')).astype(np.float64)
rng = np.random.default_rng(7)
for yy in range(Y0 + 10, Y1 - 10):
    R = float(straal[yy - Y0])
    l0 = int(math.floor(XC - R - 12))
    l1 = int(math.floor(XC - R + 0.5))
    r0 = int(math.ceil(XC + R - 0.5))
    r1 = int(math.ceil(XC + R + 3))
    links = plaat[yy, l0 - 6:l0 - 1].mean(axis=0)
    rechts = plaat[yy, r1 + 2:r1 + 7].mean(axis=0)
    plaat[yy, l0:l1 + 1] = links + rng.normal(0, 1.6, (l1 - l0 + 1, 1))
    plaat[yy, r0:r1 + 1] = rechts + rng.normal(0, 1.6, (r1 - r0 + 1, 1))
plaat = np.clip(np.round(plaat), 0, 255).astype('uint8')
Image.fromarray(plaat).save(os.path.join(THEMA, 'assets', 'images', 'blik-achtergrond.webp'), 'WEBP', quality=92, method=6)
print('achtergrond', os.path.getsize(os.path.join(THEMA, 'assets', 'images', 'blik-achtergrond.webp')) // 1024, 'kB')
