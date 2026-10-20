/**
 * Mori-blik in 3D (Three.js). Wordt pas geladen als de bezoeker op "Draai het blik" klikt,
 * zodat de productpagina zelf niet zwaarder wordt. Het blik is volledig getekend in code:
 * geen model- of beeldbestand, het etiket is echte tekst op een canvas.
 */
import {
    WebGLRenderer, Scene, PerspectiveCamera, CylinderGeometry, TorusGeometry, MeshStandardMaterial,
    Mesh, Group, CanvasTexture, SRGBColorSpace, HemisphereLight, DirectionalLight, Color, CircleGeometry,
    MeshBasicMaterial,
} from 'three';

const KLEUR_ROMP = 0x1f2a1b;
const KLEUR_DEKSEL = 0x2a3a24;
const KLEUR_PAPIER = '#f4f1e8';
const KLEUR_MOS = '#1f2a1b';

function etiketTextuur(opties) {
    const canvas = document.createElement('canvas');
    canvas.width = 1024;
    canvas.height = 512;
    const c = canvas.getContext('2d');
    c.fillStyle = KLEUR_PAPIER;
    c.fillRect(0, 0, 1024, 512);
    c.fillStyle = KLEUR_MOS;
    c.textAlign = 'center';
    c.textBaseline = 'middle';
    // het teken staat midden voorop (u = 0.25 van de omtrek ligt recht voor de camera)
    const x = 512;
    c.font = '190px "Shippori Mincho", serif';
    c.fillText(opties.teken, x, 140);
    c.font = '500 74px "Fraunces", serif';
    c.letterSpacing = '14px';
    c.fillText(opties.naam, x, 295);
    c.fillRect(x - 60, 350, 120, 3);
    c.font = '500 34px "Hanken Grotesk", sans-serif';
    c.letterSpacing = '8px';
    c.fillText(opties.regel, x, 405);
    const textuur = new CanvasTexture(canvas);
    textuur.colorSpace = SRGBColorSpace;
    textuur.anisotropy = 4;
    return textuur;
}

export async function start(houder, opties) {
    const breedte = houder.clientWidth;
    const hoogte = houder.clientHeight;

    const renderer = new WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'low-power' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setSize(breedte, hoogte);
    renderer.domElement.className = 'blik3d__canvas';
    renderer.domElement.setAttribute('aria-hidden', 'true');
    houder.appendChild(renderer.domElement);

    // lettertypes zijn nodig voor het etiket
    if (document.fonts && document.fonts.load) {
        await Promise.all([
            document.fonts.load('170px "Shippori Mincho"', opties.teken),
            document.fonts.load('500 74px "Fraunces"'),
            document.fonts.load('500 34px "Hanken Grotesk"'),
        ]).catch(function() {});
    }

    const scene = new Scene();
    const camera = new PerspectiveCamera(32, breedte / hoogte, 0.1, 50);
    camera.position.set(0, 0.5, 7.8);
    camera.lookAt(0, 0, 0);

    scene.add(new HemisphereLight(0xffffff, 0xcfc6b0, 1.35));
    const zon = new DirectionalLight(0xfff3dc, 2.2);
    zon.position.set(3, 4, 5);
    scene.add(zon);
    const tegen = new DirectionalLight(0xdfe9d0, 0.7);
    tegen.position.set(-4, 1, -3);
    scene.add(tegen);

    const blik = new Group();
    const romp = new Mesh(new CylinderGeometry(1, 1, 2.0, 96, 1, true), new MeshStandardMaterial({ color: KLEUR_ROMP, roughness: 0.42, metalness: 0.25 }));
    const bodem = new Mesh(new CircleGeometry(1, 64), new MeshStandardMaterial({ color: KLEUR_ROMP, roughness: 0.5, metalness: 0.25 }));
    bodem.rotation.x = Math.PI / 2;
    bodem.position.y = -1.0;
    const etiket = new Mesh(new CylinderGeometry(1.012, 1.012, 1.05, 96, 1, true), new MeshStandardMaterial({ map: etiketTextuur(opties), roughness: 0.8, metalness: 0 }));
    etiket.position.y = -0.12;
    etiket.rotation.y = Math.PI;
    const deksel = new Mesh(new CylinderGeometry(1.04, 1.04, 0.28, 96), new MeshStandardMaterial({ color: KLEUR_DEKSEL, roughness: 0.38, metalness: 0.4 }));
    deksel.position.y = 1.12;
    const rand = new Mesh(new TorusGeometry(1.04, 0.025, 12, 96), new MeshStandardMaterial({ color: KLEUR_DEKSEL, roughness: 0.3, metalness: 0.55 }));
    rand.rotation.x = Math.PI / 2;
    rand.position.y = 1.26;
    const schaduw = new Mesh(new CircleGeometry(1.3, 48), new MeshBasicMaterial({ color: new Color(0x1f2a1b), transparent: true, opacity: 0.12 }));
    schaduw.rotation.x = -Math.PI / 2;
    schaduw.position.y = -1.02;
    blik.add(romp, bodem, etiket, deksel, rand);
    scene.add(blik, schaduw);
    blik.rotation.x = 0.12;

    // slepen om te draaien (muis, vinger, pen) en met de pijltjestoetsen
    let doel = 0.0;
    let hoek = 0.0;
    let sleept = false;
    let laatsteX = 0;
    let rust = !opties.reduceMotion;

    const el = renderer.domElement;
    el.style.touchAction = 'pan-y';
    el.addEventListener('pointerdown', function(e) {
        sleept = true; laatsteX = e.clientX; rust = false; el.setPointerCapture(e.pointerId); houder.classList.add('is-sleept'); 
    });
    el.addEventListener('pointermove', function(e) {
        if (sleept) {
            doel += (e.clientX - laatsteX) * 0.012; laatsteX = e.clientX; 
        } 
    });
    const los = function() {
        sleept = false; houder.classList.remove('is-sleept'); 
    };
    el.addEventListener('pointerup', los);
    el.addEventListener('pointercancel', los);
    houder.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            doel -= 0.3; rust = false; e.preventDefault(); 
        }
        if (e.key === 'ArrowRight') {
            doel += 0.3; rust = false; e.preventDefault(); 
        }
    });

    let zichtbaar = true;
    const waarnemer = new IntersectionObserver(function(items) {
        zichtbaar = items[0].isIntersecting; if (zichtbaar) {
            lus(); 
        } 
    });
    waarnemer.observe(houder);

    let bezig = false;
    let vorige = performance.now();
    function lus(nu) {
        if (bezig && nu === undefined) {
            return; 
        }
        bezig = true;
        if (!zichtbaar || document.hidden) {
            bezig = false; return; 
        }
        const dt = Math.min(0.05, ((nu || performance.now()) - vorige) / 1000);
        vorige = nu || performance.now();
        if (rust) {
            doel += dt * 0.35; 
        }
        hoek += (doel - hoek) * Math.min(1, dt * 8);
        blik.rotation.y = hoek;
        renderer.render(scene, camera);
        requestAnimationFrame(lus);
    }
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            bezig = false; lus(); 
        } 
    });
    lus();

    const resize = new ResizeObserver(function() {
        const b = houder.clientWidth;
        const h = houder.clientHeight;
        renderer.setSize(b, h);
        camera.aspect = b / h;
        camera.updateProjectionMatrix();
    });
    resize.observe(houder);

    return function opruimen() {
        waarnemer.disconnect();
        resize.disconnect();
        zichtbaar = false;
        renderer.dispose();
        renderer.domElement.remove();
    };
}
