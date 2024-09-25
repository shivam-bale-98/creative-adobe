const webpack = require('webpack');
const path = require('path');
const glob = require("glob");
const TerserPlugin = require("terser-webpack-plugin");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const RtlCssPlugin = require("rtlcss-webpack-plugin");
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const CompressionPlugin = require("compression-webpack-plugin");
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
// const { PurgeCSSPlugin } = require("purgecss-webpack-plugin");
const version = require("../../config/version.json");


module.exports = function (env, argv) {
    /*
     * boolean variable for development mode
     */
    const devMode = argv.mode === 'development';

    let mods = {
        watch: devMode,
        devtool: devMode ? 'source-map' : 'cheap-module-source-map',
        entry: {
            app: ['./src/js/app.js', './src/scss/app.scss'],
            login: ['./src/scss/pages/login.scss'],
            page_not_found: ['./src/scss/pages/page_not_found.scss'],
        },
        output: {
            path: path.resolve(__dirname, 'dist'),
            filename: `js/[name]-${version.version}.min.js`,
        },
        module: {
            rules: [
                /*
                 * Handle ES6 transpilation
                 */
                {
                    test: /\.js$/,
                    exclude: /(node_modules|bower_components)/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env'],
                            plugins: ['@babel/plugin-syntax-dynamic-import']
                        }
                    },
                },
                /*
                 * Handle SCSS transpilation
                 */
                {
                    test: /.s?css$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: "css-loader",
                            options: {
                              url: false,
                            },
                        },
                        "postcss-loader",
                         'css-unicode-loader', // Convert double-byte character to unicode encoding.
                        "sass-loader"
                    ],
                },

            ],
        },
        plugins: [
            new RemoveEmptyScriptsPlugin(),
            /*
             * Automatically load jquery instead of having to import it everywhere
             */
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery',
                'window.jQuery': 'jquery',
            }),
            /*
             * Extract app CSS and npm package CSS into two separate files
             */
            new MiniCssExtractPlugin({
                filename: 'css/[name].min.css',
            }),

            /* https://rtlcss.com/learn/usage-guide/control-directives/ */
            /*!rtl:ignore*/
            new RtlCssPlugin("css/[name].rtl.min.css"),

            new CleanWebpackPlugin(),
        ],
    };
    /*
     * Minimize CSS if not devMode
     */
    if (!devMode) {
        mods = {
            ...mods,
            plugins: [
                ...mods.plugins,
                /*
                 * For safelisting selectors follow the steps in the link: https://purgecss.com/safelisting.html
                 * OR use inline comments like the following
                 */
                /*! purgecss start ignore */
                // You css
                /*! purgecss end ignore */
                // new PurgeCSSPlugin({
                //     paths: glob.sync(`./**/*.php`, { nodir: true }),
                //     safelist: {
                //         standard: [
                //             'collapse',
                //             'collapsing',
                //             'collapsed',
                //             'show',
                //             'hide',
                //             'fade',
                //             'open',
                //             'close',
                //             'enter',
                //             'active',
                //             'top',
                //             'sticky',
                //             'loaded',
                //             'loading',
                //             'ts-line',
                //             'line',
                //             'modal-backdrop',
                //             'fieldset',
                //             'dark',
                //             'light',
                //             'stop',
                //             'blockquote',
                //         ],
                //         greedy: [
                //             /^ci-/,
                //             /^iti-/,
                //             /^intl-/,
                //             /^btn-/,
                //             /^slide-/,
                //         ],
                //     }
                // }),
                new CompressionPlugin() //GZ compression
            ],
            optimization: {
                minimize: true,
                minimizer: [
                    new TerserPlugin(),
                    new CssMinimizerPlugin()
                ],
                concatenateModules: true,
            }
        }
    }
    return mods;
};

