let mix = require("laravel-mix");
let path = require("path");

require("./mix");

mix.setPublicPath("dist")
    .js("resources/js/field.js", "dist/js/nova-advanced-image.js")
    .sass("resources/sass/field.scss", "dist/css/nova-advanced-image.css")
    .vue({ version: 3 })
    .nova("marshmallow/nova-advanced-image");
