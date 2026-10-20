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
uniform vec4 uGlansRect;
uniform vec2 uStage;
uniform float uScale;
uniform float uRot;
uniform float uXc, uA0, uK, uH0, uY0, uN, uHTop, uHBot, uHL0, uHL1;
const float PI = 3.14159265;

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
    // Glans en randlicht: vast op het scherm, het draait niet mee met het blik
    vec2 g = (f - uGlansRect.xy) / uGlansRect.zw;
    if (g.x >= 0.0 && g.x <= 1.0 && g.y >= 0.0 && g.y <= 1.0) {
        kleur += (texture2D(uGlans, vec2(g.x, 1.0 - g.y)).rgb - 0.5) * 2.0;
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

    const albedo = new CanvasTexture(canvas);
    albedo.wrapS = RepeatWrapping;
    albedo.wrapT = ClampToEdgeWrapping;
    albedo.minFilter = LinearFilter;          // geen mipmaps: bij de naad van de textuur geven die streepjes
    albedo.generateMipmaps = false;
    albedo.magFilter = LinearFilter;
    albedo.anisotropy = Math.min(4, renderer.capabilities.getMaxAnisotropy());

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

    // Draaien: slepen (muis, vinger, pen) en pijltjestoetsen, met uitloop
    let hoek = 0;
    let snelheid = 0;
    let sleept = false;
    let laatsteX = 0;
    let bezig = false;
    let zichtbaar = true;

    function teken() {
        materiaal.uniforms.uRot.value = -hoek;
        renderer.render(scene, camera);
    }

    function lus() {
        if (!zichtbaar || document.hidden) { bezig = false; return; }
        if (!sleept) {
            hoek += snelheid;
            snelheid *= 0.93;
        }
        teken();
        if (sleept || Math.abs(snelheid) > 0.0004) {
            requestAnimationFrame(lus);
        } else {
            snelheid = 0;
            bezig = false;
        }
    }
    function start_() {
        if (!bezig) { bezig = true; requestAnimationFrame(lus); }
    }

    const el = renderer.domElement;
    el.style.touchAction = 'pan-y';
    el.addEventListener('pointerdown', function(e) { sleept = true; laatsteX = e.clientX; snelheid = 0; el.setPointerCapture(e.pointerId); houder.classList.add('is-sleept'); start_(); });
    el.addEventListener('pointermove', function(e) {
        if (!sleept) { return; }
        const d = (e.clientX - laatsteX) * 0.011;
        hoek += d;
        snelheid = d;
        laatsteX = e.clientX;
    });
    const los = function() { sleept = false; houder.classList.remove('is-sleept'); start_(); };
    el.addEventListener('pointerup', los);
    el.addEventListener('pointercancel', los);
    houder.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') { snelheid = -0.05; start_(); e.preventDefault(); }
        if (e.key === 'ArrowRight') { snelheid = 0.05; start_(); e.preventDefault(); }
    });

    const waarnemer = new IntersectionObserver(function(items) { zichtbaar = items[0].isIntersecting; if (zichtbaar) { teken(); } });
    waarnemer.observe(houder);

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

    return function opruimen() {
        waarnemer.disconnect();
        resize.disconnect();
        zichtbaar = false;
        albedo.dispose();
        glans.dispose();
        renderer.dispose();
        renderer.domElement.remove();
    };
}
