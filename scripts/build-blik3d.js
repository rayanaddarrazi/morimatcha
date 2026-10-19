/**
 * Bundelt het 3D-blik (Three.js) tot één klein module-bestand: assets/js/blik3d.js
 * Alleen de onderdelen die we gebruiken komen mee (tree-shaking), en het bestand wordt pas bij een klik geladen.
 */
const path = require('path');
const esbuild = require('esbuild');

esbuild.build({
    entryPoints: [path.join(__dirname, '..', 'assets', 'js', 'blik3d.src.js')],
    outfile: path.join(__dirname, '..', 'assets', 'js', 'blik3d.js'),
    bundle: true,
    format: 'esm',
    minify: true,
    target: 'es2020',
    legalComments: 'none',
}).then(() => {
    const size = require('fs').statSync(path.join(__dirname, '..', 'assets', 'js', 'blik3d.js')).size;
    console.log('✓ Built blik3d.js (' + Math.round(size / 1024) + ' kB, wordt pas bij een klik geladen)');
});
