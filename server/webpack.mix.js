const { mix } = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .sass('resources/assets/sass/admin/admin.login.scss', 'public/css')
   .js('resources/assets/js/admin/admin.login.js', 'public/js')
   .combine([
        'resources/assets/js/rsa/Barrett.js',
        'resources/assets/js/rsa/BigInt.js',
        'resources/assets/js/rsa/RSA.js'
   ],'public/js/rsa.js');

if (mix.config.inProduction) {
    mix.version();
}

mix.disableNotifications();