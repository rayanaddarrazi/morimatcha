import globals from 'globals';
import js from '@eslint/js';

export default [
    { ignores: ['node_modules/**', 'postcss.config.js', 'tailwind.config.js', 'assets/js/pagebuilder.js'] },
    js.configs.recommended,
    {
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: {
                ...globals.browser,
                ...globals.jquery,
                wp: 'readonly',
                Swiper: 'readonly',
            },
        },
        rules: {
            'no-unused-vars': 'warn',
            'no-console': 'warn',
            'semi': ['error', 'always'],
            'quotes': ['warn', 'single', { avoidEscape: true }],
            'indent': ['warn', 4],
            'no-multiple-empty-lines': ['warn', { max: 2 }],
            'eqeqeq': ['warn', 'always'],
            'curly': ['warn', 'all'],
            'brace-style': ['warn', '1tbs'],
            'comma-dangle': ['warn', 'always-multiline'],
            'no-var': 'error',
            'prefer-const': 'warn',
        },
    },
    {
        files: ['tailwind.config.js', 'scripts/**/*.js'],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'commonjs',
            globals: {
                ...globals.node,
            },
        },
    },
    {
        ignores: [
            'node_modules/',
            'dist/',
            'vendor/',
            '**/*.min.js',
        ],
    },
];
