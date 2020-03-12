const elixir = require('laravel-elixir');


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
    mix.less([
    'bootstrap.css',
    'elements.less',
    '_core.less',
    '_pages.less',
    '_components.less',
    '_responsive.less',
    'style.less',
    'custom.less',
    'vendor/angular-toggle-switch.less',
    'vendor/angular-toggle-switch-bootstrap.less'
    ]);
});