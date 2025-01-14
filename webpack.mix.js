const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps();

// Compile assets for the public pages
mix.js('resources/js/public.js', 'public/js/public.js')
    .sass('resources/sass/public.scss', 'public/css/public.css')
    .sourceMaps();

// copy library styles
mix.copy('resources/css/styles.css','public/css')
