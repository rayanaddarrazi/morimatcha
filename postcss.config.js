/**
 * Het ontwerp is 1440 px breed (desktop) en 390 px (mobiel). Lengtes blijven in px:
 * koppen schalen met clamp() en de container volgt het grid (marge 110, 12 kolommen).
 */
module.exports = ({ env }) => ({
    plugins: [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
        ...(env === 'production' ? [require('cssnano')({ preset: 'default' })] : []),
    ],
});
