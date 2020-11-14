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
if(mix.inProduction()){
    mix.setPublicPath('public/dist');
    mix.setResourceRoot('/dist');
    mix.version();
}else{
    mix.setPublicPath('public/build');
    mix.setResourceRoot('/build');
}


mix.js('resources/js/app.js', 'js').extract(['axios','admin-lte','jquery','jquery-pjax']);
mix.sass('resources/sass/admin/app.scss', 'css/admin').sass('resources/sass/admin/vendor.scss', 'css/admin');
