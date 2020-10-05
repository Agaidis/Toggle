const mix = require('laravel-mix');
<<<<<<< HEAD
mix.autoload({
    jquery: ['$', 'jquery', 'window.jQuery',"jQuery","window.$","jquery","window.jquery"]
});
=======

>>>>>>> 74ba0951e6b64f358c0d3b230295efb4db24237a
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
    'resources/js/bootstrap.js',
    'vendor/select2/select2/dist/js/select2.min.js',
<<<<<<< HEAD
    'resources/js/jquery-dp-ui.min.js',
=======
    'resources//js/jquery-dp-ui.min.js',
>>>>>>> 74ba0951e6b64f358c0d3b230295efb4db24237a
    'resources/js/leasePage.js',
    'resources/js/wellbore.js',
    'resources/js/permits.js',
    'resources/js/admin.js',
    'resources/js/phoneNumberPush.js',
    'resources/js/owner.js',
    'resources/js/datatables.min.js',
    'resources/js/permitStorage.js'

], 'public/js/app.js').version()
<<<<<<< HEAD
    // .sass('resources/sass/app.scss', 'public/css')
=======
    .sass('resources/sass/app.scss', 'public/css')
>>>>>>> 74ba0951e6b64f358c0d3b230295efb4db24237a
    .styles([
        'public/css/app.css',
        'resources/css/mmp.css',
        'resources/css/datatables.min.css',
        'resources/css/jquery-dp-ui.min.css',
        'resources/css/jquery-dp-ui.structure.min.css',
        'resources/css/jquery-dp-ui.theme.min.css',
        'vendor/select2/select2/dist/css/select2.min.css'


    ], 'public/css/app.css').version();