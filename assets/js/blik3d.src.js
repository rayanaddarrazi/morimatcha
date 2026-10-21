/**
 * Mori-blik in 3D (Three.js). Wordt pas geladen als de bezoeker op "Draai het blik" klikt,
 * zodat de productpagina zelf niet zwaarder wordt.
 *
 * Hoe het werkt: het blik is een cilinder. De productfoto is "afgerold" tot een textuur (zie
 * scripts/build-blik-textuur.py). Een shader rolt die textuur weer om het blik, op precies dezelfde plek en in
 * dezelfde maat als op de foto. De foto zelf blijft het achterbeeld (met schaduw en linnen), alleen het blik
 * zelf wordt getekend. Daardoor is het stilstaande beeld gelijk aan de foto en draait het blik echt rond, met
 * het licht dat op zijn plek blijft. Het etiket is echte tekst in dezelfde opmaak als het etiket op de foto.
 */
import {
    WebGLRenderer, Scene, Camera, Mesh, PlaneGeometry, ShaderMaterial, TextureLoader, DataTexture,
    CanvasTexture, Texture, NoBlending, RepeatWrapping, ClampToEdgeWrapping, LinearFilter,
    RGBAFormat, UnsignedByteType,
} from 'three';
import data from './blik-data.json';

const FOTO = data.foto;               // de productfoto is 1024 bij 1024
const N = data.straal.length;
const H_TOP = data.hTop;
const H_BOT = data.hBot;
const [H_LAB0, H_LAB1] = data.etiketRingen;
const kromming = function(h) { return data.a0 + data.k * (h - data.h0); };

const VERTEX = `
varying vec2 vUv;
void main() {
    vUv = uv;
    gl_Position = vec4(position.xy, 0.0, 1.0);
}`;

const FRAGMENT = `
precision highp float;
varying vec2 vUv;
uniform sampler2D uAlbedo;
uniform sampler2D uRadius;
uniform sampler2D uLak;
uniform sampler2D uPapier;
uniform sampler2D uGlans;
uniform sampler2D uMerk;
uniform vec3 uNaadX;
uniform float uMerkSterkte;
uniform vec4 uGlansRect;
uniform vec2 uStage;
uniform float uScale;
uniform float uRot;
uniform float uXc, uA0, uK, uH0, uY0, uN, uHTop, uHBot, uHL0, uHL1;
const float PI = 3.14159265;
const float NAAD_R = 168.1;

void main() {
    vec2 p = vec2(vUv.x, 1.0 - vUv.y) * uStage;                 // vlak in css-px, y naar beneden
    vec2 f = (p - uStage * 0.5) / uScale + vec2(512.0);         // fotopixels (de foto vult het vlak als "cover")
    float idx = f.y - uY0;
    if (idx < 0.0 || idx >= uN) { gl_FragColor = vec4(0.0); return; }
    float R = 150.0 + texture2D(uRadius, vec2((idx + 0.5) / uN, 0.5)).r * 40.0;
    float s = (f.x - uXc) / R;
    float rand = clamp((1.0 - abs(s)) * R * uScale + 0.5, 0.0, 1.0);   // zachte rand van 1 pixel
    if (rand <= 0.0) { gl_FragColor = vec4(0.0); return; }
    s = clamp(s, -0.9995, 0.9995);
    float theta = asin(s);
    float c = cos(theta);
    float h = (f.y - (uA0 - uK * uH0) * c) / (1.0 + uK * c);    // hoogte op de as van het blik
    float boven = clamp((h - uHTop) * uScale + 0.5, 0.0, 1.0);
    float onder = clamp((uHBot - h) * uScale + 0.5, 0.0, 1.0);
    float alpha = min(rand, min(boven, onder));
    if (alpha <= 0.0) { gl_FragColor = vec4(0.0); return; }
    float u = fract((theta + uRot) / (2.0 * PI) + 0.5);
    float v = clamp((h - uHTop) / (uHBot - uHTop), 0.0, 1.0);
    vec3 albedo = texture2D(uAlbedo, vec2(u, 1.0 - v)).rgb;
    float sx = s * 0.5 + 0.5;
    vec3 licht = (h >= uHL0 && h <= uHL1) ? texture2D(uPapier, vec2(sx, 0.5)).rgb : texture2D(uLak, vec2(sx, 0.5)).rgb;
    vec3 kleur = albedo * licht * 3.0;
    // Merk in het lak (alleen buiten het etiket): komt tevoorschijn zodra het blik draait, zodat het hele blik meedraait
    if (h < uHL0 || h > uHL1) {
        float e = texture2D(uMerk, vec2(u, 1.0 - v)).r - 0.5;
        kleur += e * 0.8 * uMerkSterkte;
    }
    // Glans en randlicht: vast op het scherm, het draait niet mee met het blik
    vec2 g = (f - uGlansRect.xy) / uGlansRect.zw;
    if (g.x >= 0.0 && g.x <= 1.0 && g.y >= 0.0 && g.y <= 1.0) {
        kleur += (texture2D(uGlans, vec2(g.x, 1.0 - g.y)).rgb - 0.5) * 2.0;
    }
    kleur = clamp(kleur, 0.0, 1.0);
    // De naad: twee dunne zilveren lijntjes met een donker randje ertussen, links op het lak (boven en onder het etiket).
    // Ze zijn getekend met een vaste dikte op het scherm, zoals op de foto, en schuiven met het blik mee.
    if ((h >= 322.0 && h <= uHL0 - 1.0) || (h >= uHL1 + 1.0 && h <= 826.0)) {
        bool boven = h < uHL0;
        float s1 = (uNaadX.x - uXc) / NAAD_R;
        float s2 = (uNaadX.y - uXc) / NAAD_R;
        float sg = (uNaadX.z - uXc) / NAAD_R;
        float t1 = asin(s1) - uRot;
        float t2 = asin(s2) - uRot;
        float tg = asin(sg) - uRot;
        float x1 = uXc + NAAD_R * sin(t1);
        float x2 = uXc + NAAD_R * sin(t2);
        float xg = uXc + NAAD_R * sin(tg);
        float v1 = smoothstep(0.03, 0.2, cos(t1));
        float v2 = smoothstep(0.03, 0.2, cos(t2));
        float vg = smoothstep(0.03, 0.2, cos(tg));
        float c1 = clamp(0.5 + (1.6 - abs(f.x - x1)) * uScale, 0.0, 1.0) * v1;
        float c2 = clamp(0.5 + (1.5 - abs(f.x - x2)) * uScale, 0.0, 1.0) * v2;
        float cg = clamp(0.5 + (0.8 - abs(f.x - xg)) * uScale, 0.0, 1.0) * vg;
        vec3 zilver1 = boven ? vec3(0.57, 0.57, 0.56) : vec3(0.65, 0.65, 0.64);
        vec3 zilver2 = boven ? vec3(0.50, 0.50, 0.50) : vec3(0.71, 0.71, 0.70);
        vec3 donker = boven ? vec3(0.29, 0.30, 0.30) : vec3(0.37, 0.38, 0.38);
        kleur = mix(kleur, zilver1, c1);
        kleur = mix(kleur, zilver2, c2);
        kleur = mix(kleur, donker, cg);
    }
    gl_FragColor = vec4(clamp(kleur, 0.0, 1.0) * alpha, alpha);
}`;

function tabel(waarden, kanalen) {
    // Acht-bits tabel van 1 rij. Radius: (straal - 150) / 40. Licht: waarde / 3.
    const breedte = waarden.length / kanalen;
    const px = new Uint8Array(breedte * 4);
    for (let i = 0; i < breedte; i++) {
        for (let k = 0; k < 4; k++) {
            let v = 255;
            if (k < kanalen) {
                const w = waarden[i * kanalen + k];
                v = kanalen === 1 ? (w - 150) / 40 * 255 : w / 3 * 255;
            }
            px[i * 4 + k] = Math.max(0, Math.min(255, Math.round(v)));
        }
    }
    const t = new DataTexture(px, breedte, 1, RGBAFormat, UnsignedByteType);
    t.minFilter = LinearFilter;
    t.magFilter = LinearFilter;
    t.wrapS = ClampToEdgeWrapping;
    t.wrapT = ClampToEdgeWrapping;
    t.needsUpdate = true;
    return t;
}

function laadAfbeelding(url) {
    return new Promise(function(resolve, reject) {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function() { resolve(img); };
        img.onerror = reject;
        img.src = url;
    });
}

/** Het etiket in dezelfde opmaak als op de foto (zie .etiket in foto.css), getekend in fotocoördinaten. */
function etiketLaag(opties) {
    const c = document.createElement('canvas');
    c.width = FOTO;
    c.height = FOTO;
    const g = c.getContext('2d');
    const e = data.etiket;
    const cqw = e.breedte / 100;
    const mid = e.links + e.breedte / 2;
    const blok = function(van, tot) { return e.boven + e.hoogte * (van + tot) / 2; };
    g.textAlign = 'center';
    g.textBaseline = 'middle';

    g.fillStyle = '#1f2a1b';
    g.font = '500 ' + (20 * cqw) + 'px "Shippori Mincho", serif';
    g.fillText(opties.teken, mid, blok(0.18, 0.48) + 5);   // +5: een canvas centreert de tekst iets anders dan CSS

    const naamPx = 5.33 * cqw;
    g.font = '600 ' + naamPx + 'px "Hanken Grotesk", sans-serif';
    spatie(g, opties.naam, mid, blok(0.54, 0.64) + 3.6, 0.45, naamPx);

    g.fillStyle = '#7a9a3f';
    g.fillRect(e.links + e.breedte * 0.4067, e.boven + e.hoogte * 0.70, e.breedte * 0.1866, e.hoogte * 0.01);

    const regelPx = 3.67 * cqw;
    g.fillStyle = '#6a6e61';
    g.font = '500 ' + regelPx + 'px "Hanken Grotesk", sans-serif';
    spatie(g, opties.regel, mid, blok(0.76, 0.84), 0.4, regelPx);
    return g.getImageData(0, 0, FOTO, FOTO);
}

function spatie(g, tekst, x, y, em, px) {
    const tussen = em * px;
    const tekens = [...tekst];
    const breedtes = tekens.map(function(ch) { return g.measureText(ch).width; });
    const totaal = breedtes.reduce(function(a, b) { return a + b; }, 0) + tussen * (tekens.length - 1);
    let cursor = x - totaal / 2;
    tekens.forEach(function(ch, i) {
        g.fillText(ch, cursor + breedtes[i] / 2, y);
        cursor += breedtes[i] + tussen;
    });
}

/**
 * De achterkant van het etiket: ingrediënten en hoe je de matcha maakt. Het etiket gaat helemaal om het blik, dus ook
 * daar staat tekst. Getekend in de afgerolde textuur (1 pixel is ongeveer 1 pixel op het blik), de shader zorgt voor de
 * verkorting. De achterkant ligt op de naad van de textuur, daarom wordt hij links en rechts samen getekend.
 */
function etiketOmheen(canvas, e) {
    if (!e) { return; }
    const g = canvas.getContext('2d');
    const B = canvas.width;
    const y0 = H_LAB0 - H_TOP;
    g.save();
    g.globalCompositeOperation = 'multiply';
    g.textAlign = 'center';
    g.textBaseline = 'middle';

    const blok = function(x, y, kop, regels) {
        if (!kop && !(regels || []).length) { return y; }
        g.fillStyle = '#1f2a1b';
        g.font = '600 10.5px "Hanken Grotesk", sans-serif';
        spatie(g, (kop || '').toUpperCase(), x, y, 0.35, 10.5);
        g.fillStyle = '#7a9a3f';
        g.fillRect(x - 12, y + 11, 24, 1.5);
        g.fillStyle = '#3f4a3a';
        g.font = '500 12px "Hanken Grotesk", sans-serif';
        (regels || []).slice(0, 3).forEach(function(regel, i) {
            g.fillText(regel, x, y + 29 + i * 18);
        });
        return y + 29 + Math.max(1, (regels || []).length) * 18;
    };
    const achter = function(x) {
        g.fillStyle = '#1f2a1b';
        g.font = '500 28px "Shippori Mincho", serif';
        g.fillText(e.teken || '', x, y0 + 32);
        const y = blok(x, y0 + 68, e.achterKop, e.achterRegels);
        blok(x, y + 6, e.achterKop2, e.achterRegels2);
    };
    achter(0);
    achter(B);
    g.restore();
}

/** Zet het etiket (multiply, zoals op de foto) in de afgerolde textuur, op de voorkant van het blik. */
function etiketInTextuur(canvas, laag) {
    const g = canvas.getContext('2d');
    const beeld = g.getImageData(0, 0, canvas.width, canvas.height);
    const px = beeld.data;
    const L = laag.data;
    const B = canvas.width;
    const e = data.etiket;
    for (let v = 0; v < canvas.height; v++) {
        const h = H_TOP + v + 0.5;
        if (h < H_LAB0 || h > H_LAB1) { continue; }
        const A = kromming(h);
        for (let ux = B / 4; ux < B * 3 / 4; ux++) {
            const theta = (ux + 0.5) / B * 2 * Math.PI - Math.PI;
            const y = h + A * Math.cos(theta);
            const i = Math.max(0, Math.min(N - 1, Math.floor(y - data.y0)));
            const x = data.xc + data.straal[i] * Math.sin(theta);
            if (x < e.links - 2 || x > e.links + e.breedte + 2 || y < e.boven - 2 || y > e.boven + e.hoogte + 2) { continue; }
            const x0 = Math.floor(x);
            const y0 = Math.floor(y);
            const fx = x - x0;
            const fy = y - y0;
            let a = 0;
            let r = 0;
            let gg = 0;
            let b = 0;
            for (let dy = 0; dy < 2; dy++) {
                for (let dx = 0; dx < 2; dx++) {
                    const w = (dx ? fx : 1 - fx) * (dy ? fy : 1 - fy);
                    const o = ((y0 + dy) * FOTO + (x0 + dx)) * 4;
                    const al = L[o + 3] / 255;
                    a += w * al;
                    r += w * al * L[o];
                    gg += w * al * L[o + 1];
                    b += w * al * L[o + 2];
                }
            }
            if (a < 0.004) { continue; }
            const o2 = (v * B + ux) * 4;
            px[o2] = px[o2] * ((1 - a) + (r / a) / 255 * a);
            px[o2 + 1] = px[o2 + 1] * ((1 - a) + (gg / a) / 255 * a);
            px[o2 + 2] = px[o2 + 2] * ((1 - a) + (b / a) / 255 * a);
        }
    }
    g.putImageData(beeld, 0, 0);
}

/**
 * Tone-op-tone merk op het lak: het teken zes keer rond. Dit staat in een eigen laag (0,5 = geen verandering)
 * en wordt pas zichtbaar als het blik draait, dus de stilstaande weergave blijft gelijk aan de foto.
 */
function merkLaag(breedte, hoogte, teken) {
    const c = document.createElement('canvas');
    c.width = breedte;
    c.height = hoogte;
    const g = c.getContext('2d');
    g.fillStyle = 'rgb(128,128,128)';
    g.fillRect(0, 0, breedte, hoogte);
    g.textAlign = 'center';
    g.textBaseline = 'middle';
    g.font = '500 58px "Shippori Mincho", serif';
    const zones = [(H_LAB0 + 330) / 2 - H_TOP, (H_LAB1 + H_BOT) / 2 - H_TOP];   // boven en onder het etiket
    for (let k = 0; k < 6; k++) {
        const x = ((k + 0.5) / 6) * breedte;
        zones.forEach(function(y) {
            g.fillStyle = 'rgb(106,106,106)';
            g.fillText(teken, x + 1.2, y + 1.2);
            g.fillStyle = 'rgb(152,152,152)';
            g.fillText(teken, x, y);
        });
    }
    return c;
}

export async function start(houder, opties) {
    const breedte = houder.clientWidth;
    const hoogte = houder.clientHeight;

    const renderer = new WebGLRenderer({ antialias: false, alpha: true, premultipliedAlpha: true, powerPreference: 'default' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setSize(breedte, hoogte);
    renderer.setClearColor(0x000000, 0);
    renderer.domElement.className = 'blik3d__canvas';
    renderer.domElement.setAttribute('aria-hidden', 'true');

    if (document.fonts && document.fonts.load) {
        await Promise.all([
            document.fonts.load('500 64px "Shippori Mincho"', opties.teken),
            document.fonts.load('600 17px "Hanken Grotesk"'),
            document.fonts.load('500 12px "Hanken Grotesk"'),
        ]).catch(function() {});
    }

    // Textuur laden, etiket erin zetten
    const [afbeelding, glansBeeld] = await Promise.all([laadAfbeelding(opties.textuur), laadAfbeelding(opties.glans)]);
    const glans = new Texture(glansBeeld);
    glans.minFilter = LinearFilter;
    glans.magFilter = LinearFilter;
    glans.generateMipmaps = false;
    glans.needsUpdate = true;
    const canvas = document.createElement('canvas');
    canvas.width = afbeelding.naturalWidth;
    canvas.height = afbeelding.naturalHeight;
    canvas.getContext('2d').drawImage(afbeelding, 0, 0);
    etiketInTextuur(canvas, etiketLaag(opties));
    etiketOmheen(canvas, Object.assign({ teken: opties.teken }, opties.etiket));

    const albedo = new CanvasTexture(canvas);
    albedo.wrapS = RepeatWrapping;
    albedo.wrapT = ClampToEdgeWrapping;
    albedo.minFilter = LinearFilter;          // geen mipmaps: bij de naad van de textuur geven die streepjes
    albedo.generateMipmaps = false;
    albedo.magFilter = LinearFilter;
    albedo.anisotropy = Math.min(4, renderer.capabilities.getMaxAnisotropy());

    const merkCanvas = merkLaag(canvas.width, canvas.height, opties.teken);
    const merk = new CanvasTexture(merkCanvas);
    merk.wrapS = RepeatWrapping;
    merk.wrapT = ClampToEdgeWrapping;
    merk.minFilter = LinearFilter;
    merk.magFilter = LinearFilter;
    merk.generateMipmaps = false;

    const materiaal = new ShaderMaterial({
        vertexShader: VERTEX,
        fragmentShader: FRAGMENT,
        blending: NoBlending,
        depthTest: false,
        uniforms: {
            uAlbedo: { value: albedo },
            uRadius: { value: tabel(data.straal, 1) },
            uLak: { value: tabel(data.lichtLak, 3) },
            uPapier: { value: tabel(data.lichtPapier, 3) },
            uGlans: { value: glans },
            uMerk: { value: merk },
            uNaadX: { value: [349.6, 355.0, 351.8] },
            uMerkSterkte: { value: 0 },
            uGlansRect: { value: data.glans },
            uStage: { value: [breedte, hoogte] },
            uScale: { value: Math.max(breedte, hoogte) / FOTO },
            uRot: { value: 0 },
            uXc: { value: data.xc }, uA0: { value: data.a0 }, uK: { value: data.k }, uH0: { value: data.h0 },
            uY0: { value: data.y0 }, uN: { value: N }, uHTop: { value: H_TOP }, uHBot: { value: H_BOT },
            uHL0: { value: H_LAB0 }, uHL1: { value: H_LAB1 },
        },
    });
    // Alles rekent in dezelfde (sRGB-)waarden als de foto: geen omzetting, dus het stilstaande beeld klopt met de foto
    const vlak = new Mesh(new PlaneGeometry(2, 2), materiaal);
    vlak.frustumCulled = false;
    const scene = new Scene();
    scene.add(vlak);
    const camera = new Camera();
    houder.appendChild(renderer.domElement);

    // Eén volledige draai, vanzelf: rustig op gang, rustig tot stilstand. Bezoekers kunnen er niet aan draaien.
    let hoek = 0;

    function teken() {
        materiaal.uniforms.uRot.value = -hoek;
        // Hoe ver het blik van de beginstand af staat (0 tot 3,14): het merk komt geleidelijk in beeld
        const rond = ((hoek % (2 * Math.PI)) + 2 * Math.PI) % (2 * Math.PI);
        const af = Math.min(rond, 2 * Math.PI - rond);
        const t = Math.max(0, Math.min(1, (af - 0.03) / 0.45));
        materiaal.uniforms.uMerkSterkte.value = t * t * (3 - 2 * t);
        renderer.render(scene, camera);
    }

    // Rustige draaitafel: zacht op gang, snelheid in het midden en een lange, vloeiende uitloop. Dit is een cubic-bezier
    // (0,55 / 0 / 0,1 / 1), zoals bij een CSS-overgang, zodat het blik niet "klapt" aan het begin of het eind.
    function bezier(x1, y1, x2, y2) {
        const cx = 3 * x1;
        const bx = 3 * (x2 - x1) - cx;
        const ax = 1 - cx - bx;
        const cy = 3 * y1;
        const by = 3 * (y2 - y1) - cy;
        const ay = 1 - cy - by;
        const px = function(t) { return ((ax * t + bx) * t + cx) * t; };
        const py = function(t) { return ((ay * t + by) * t + cy) * t; };
        const dx = function(t) { return (3 * ax * t + 2 * bx) * t + cx; };
        return function(x) {
            let t = x;
            for (let i = 0; i < 8; i++) {
                const f = px(t) - x;
                const d = dx(t);
                if (Math.abs(f) < 1e-5 || Math.abs(d) < 1e-6) { break; }
                t -= f / d;
            }
            return py(Math.max(0, Math.min(1, t)));
        };
    }
    const ease = bezier(0.55, 0, 0.1, 1);

    function speel(duur) {
        return new Promise(function(klaar) {
            let t0 = null;
            function stap(nu) {
                if (t0 === null) { t0 = nu; }
                const x = Math.min(1, (nu - t0) / duur);
                hoek = ease(x) * 2 * Math.PI;
                teken();
                if (x < 1) { requestAnimationFrame(stap); } else { hoek = 0; teken(); klaar(); }
            }
            requestAnimationFrame(stap);
        });
    }

    const resize = new ResizeObserver(function() {
        const b = houder.clientWidth;
        const h = houder.clientHeight;
        renderer.setSize(b, h);
        materiaal.uniforms.uStage.value = [b, h];
        materiaal.uniforms.uScale.value = Math.max(b, h) / FOTO;
        teken();
    });
    resize.observe(houder);

    teken();

    return {
        speel: speel,
        zet: function(h) { hoek = h; teken(); },
        opruimen: function() {
        resize.disconnect();
        albedo.dispose();
        merk.dispose();
        glans.dispose();
        renderer.dispose();
        renderer.domElement.remove();
        },
    };
}
