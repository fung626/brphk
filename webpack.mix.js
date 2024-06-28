const path = require("path");
const mix = require("laravel-mix");
const VuetifyLoaderPlugin = require("vuetify-loader/lib/plugin");

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

mix.options({
    // extractVueStyles: true
}).webpackConfig({
    resolve: {
        extensions: ["*", ".js", ".json", ".vue"],
        modules: [path.resolve("./node_modules")],
        alias: {
            "@": path.join(__dirname, "./resources/js")
        }
    },
    module: {
        rules: [
            {
                test: /\.js?$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: "babel-loader"
                        // options: mix.config.babel()
                    }
                ]
            }
        ]
    },
    output: {
        chunkFilename: "js/vuejs_code_split/[name].js"
    }
});

mix.extend(
    "vuetify",
    new (class {
        webpackConfig(config) {
            config.plugins.push(new VuetifyLoaderPlugin());
        }
    })()
);
mix.vuetify();

// mix.copyDirectory("resources/assets/fonts", "public/fonts");

mix.js("resources/js/app.js", "public/js")
    .sass("resources/sass/app.scss", "public/css")
    .vue()
    .sourceMaps();

mix.copy("resources/assets/images", "public/images", false);
