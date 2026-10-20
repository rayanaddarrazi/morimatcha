"""
Knipt het blik uit de echte productfoto (scripts/seed/images/product-mori-matcha.jpg) en maakt er drie PNG's van
met doorzichtige achtergrond: klein (30 g), middel (60 g) en groot (90 g). Deze staan in de maatkiezer.
De hoogtes volgen de derdemachtswortel van de inhoud, dus 30 : 60 : 90 gram wordt 1 : 1,26 : 1,44.

Gebruik:  python3 scripts/build-blik-maten.py
"""
import os
import numpy as np
from PIL import Image, ImageFilter

HIER = os.path.dirname(os.path.abspath(__file__))
THEMA = os.path.dirname(HIER)
FOTO = os.path.join(HIER, 'seed', 'images', 'product-mori-matcha.jpg')
UIT = os.path.join(THEMA, 'assets', 'images')

foto = Image.open(FOTO).convert('RGB')
im = np.array(foto).astype(np.float64)
H, W, _ = im.shape
r, g, b = im[..., 0], im[..., 1], im[..., 2]

XC = 507.0
Y0, Y1 = 232, 872
H_TOP, H_BOT = 243.8, 832.4           # bovenste en onderste ring
A_TOP, A_BOT = 7.2, 28.7              # kromming van die ringen (zie build-blik-textuur.py)

# Rechterrand per rij uit de foto (zelfde methode als bij het 3D-blik)
blik = (((r - b) < 22) | (g > r + 6)) & ((r + g + b) / 3 < 175)
rechts = np.full(Y1 - Y0, np.nan)
for i, y in enumerate(range(Y0, Y1)):
    xs = np.where(blik[y, 480:720])[0]
    if len(xs) > 20:
        rechts[i] = xs.max() + 480
idx = np.arange(len(rechts))
ok = ~np.isnan(rechts)
rechts = np.interp(idx, idx[ok], rechts[ok])
k = 9
pad = np.pad(rechts, k, mode='edge')
straal = np.clip(np.array([np.median(pad[i:i + 2 * k + 1]) for i in range(len(rechts))]) + 1.0 - XC, 160, 170) - 0.8

yy, xx = np.mgrid[0:H, 0:W].astype(np.float64)
R = np.full(H, 165.0)
R[Y0:Y1] = straal
Rr = R[:, None] * np.ones((1, W))
s = np.clip((xx - XC) / Rr, -1, 1)
boog = np.sqrt(1 - s ** 2)
zijkant = np.clip(Rr - np.abs(xx - XC) + 0.5, 0, 1)
boven = np.clip(yy - (H_TOP - A_TOP * boog) + 0.5, 0, 1)
onder = np.clip((H_BOT + A_BOT * boog) - yy + 0.5, 0, 1)
alpha = zijkant * boven * onder

rgba = np.dstack([im, alpha * 255]).clip(0, 255).astype(np.uint8)
uitsnede = Image.fromarray(rgba).crop((int(XC - 172), 228, int(XC + 172), 868))   # 344 x 640

# kleine contactschaduw onder het blik
def maak(hoogte, naam):
    schaal = hoogte / 640
    breed = round(344 * schaal)
    blikje = uitsnede.resize((breed, hoogte), Image.LANCZOS)
    pad_x, pad_b = 14, 14
    doek = Image.new('RGBA', (breed + 2 * pad_x, hoogte + pad_b), (0, 0, 0, 0))
    schaduw = Image.new('L', doek.size, 0)
    sy = hoogte - 10
    ell = Image.new('L', (breed + 2 * pad_x, 2 * pad_b), 0)
    from PIL import ImageDraw
    ImageDraw.Draw(ell).ellipse((pad_x + 4, 6, pad_x + breed - 4, 2 * pad_b - 4), fill=95)
    schaduw.paste(ell, (0, sy - pad_b + 4))
    schaduw = schaduw.filter(ImageFilter.GaussianBlur(4))
    lak = Image.new('RGBA', doek.size, (31, 42, 27, 0))
    lak.putalpha(schaduw)
    doek = Image.alpha_composite(doek, lak)
    doek.alpha_composite(blikje, (pad_x, 0))
    pad = os.path.join(UIT, naam)
    doek.save(pad, optimize=True)
    print(naam, doek.size, os.path.getsize(pad) // 1024, 'kB')

for naam, gram in (('klein', 30), ('middel', 60), ('groot', 90)):
    maak(round(168 * (gram / 30) ** (1 / 3)), f'blik-{naam}.png')
