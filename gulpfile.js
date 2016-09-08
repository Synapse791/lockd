var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    // Installation
    mix.sass('install.scss', 'public/css/install.min.css');
    mix.babel([
        'services/00-quest.js',
        'install.js'
    ], 'public/js/install.min.js');

    // Login
    mix.sass('login.scss', 'public/css/login.min.css');

    // Dashboard
    mix.sass('dashboard.scss', 'public/css/dashboard.min.css');
    mix.scripts([
        'services/00-quest.js',
        'dashboard/passwords.js',
        'dashboard/search.js',
        'dashboard/app.js'
    ], 'public/js/dashboard.min.js');
});
