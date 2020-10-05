const mix = require('laravel-mix');
mix.autoload({
    jquery: ['$', 'window.jQuery',"jQuery","window.$","jquery","window.jquery"]
});
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

mix.js([
    'resources/assets/js/bootstrap.js',
    'vendor/select2/select2/dist/js/select2.min.js',
    'resources/assets/js/jquery-dp-ui.min.js',
    'resources/assets/js/leasePage.js',
    'resources/assets/js/wellbore.js',
    'resources/assets/js/permits.js',
    'resources/assets/js/admin.js',
    'resources/assets/js/phoneNumberPush.js',
    'resources/assets/js/owner.js',
    'resources/assets/js/datatables.min.js',
    'resources/assets/js/permitStorage.js'

], 'public/js/app.js').version()
    .sass('resources/assets/sass/app.scss', 'public/css')
    .styles([
        'public/css/app.css',
        'resources/assets/css/mmp.css',
        'resources/assets/css/datatables.min.css',
        'resources/assets/css/jquery-dp-ui.min.css',
        'resources/assets/css/jquery-dp-ui.structure.min.css',
        'resources/assets/css/jquery-dp-ui.theme.min.css',
        'vendor/select2/select2/dist/css/select2.min.css'


    ], 'public/css/app.css').version();