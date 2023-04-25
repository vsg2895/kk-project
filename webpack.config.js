//
//  Output
//

let chalk = require('chalk');
let nodeEnvironment = process.env.NODE_ENV;
outputHeaderInformation(nodeEnvironment);


//
//  Webpack
//

let path = require('path');
let assets = './resources/assets/';
let ManifestPlugin = require('webpack-manifest-plugin');
let ExtractTextPlugin = require("extract-text-webpack-plugin");
let ProvidePlugin = require('webpack/lib/ProvidePlugin');
let CopyWebpackPlugin = require('copy-webpack-plugin');
let webpack = require('webpack');

module.exports = {
    entry: {
        base: assets + 'js/app.js',
        main: assets + 'js/custom.js',
    },
    output: {
        filename: '[name].[chunkhash].js',
        path: path.resolve('./public', 'build')
    },
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.esm.js',
            'build': path.join(__dirname, 'resources/assets/js/build'),
            'vendor': path.join(__dirname, 'resources/assets/js/vendor'),
            'services': path.join(__dirname, 'resources/assets/js/services'),
            'components': path.join(__dirname, 'resources/assets/js/components'),
            'vue-pages': path.join(__dirname, 'resources/assets/js/vue', 'pages'),
            'vue-helpers': path.join(__dirname, 'resources/assets/js/vue', 'helpers'),
            'vue-components': path.join(__dirname, 'resources/assets/js/vue', 'components'),
            'BaremetricsCalendar': path.join(__dirname, "node_modules", "BaremetricsCalendar", "public/js/Calendar.js")
        },
        extensions: ['.js', '.vue'],
        modules: [
            'node_modules',
            path.resolve(__dirname, 'resources/assets/js'),
            //path.resolve(__dirname, 'resources/assets/js/vuecomponents')
        ]
    },
    devtool: 'source-map',
    module: {
        rules: [{
            test: /\.css$/,
            use: ['style-loader', 'css-loader']
        },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
                        // Since sass-loader (weirdly) has SCSS as its default parse mode, we map
                        // the "scss" and "sass" values for the lang attribute to the right configs here.
                        // other preprocessors should work out of the box, no loader config like this necessary.
                        'scss': 'vue-style-loader!css-loader!sass-loader',
                        'sass': 'vue-style-loader!css-loader!sass-loader?indentedSyntax',
                        'script': 'babel-loader'
                    }
                    // other vue-loader options go here
                }
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            }, {
                test: /\.(png|jpg|gif)$/,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]?[hash]'
                }
            }, {
                test: /\.svg$/,
                loader: 'svg-inline-loader',
                options: {
                    removeSVGTagAttrs: false
                }
            }, {
                test: /\.(scss|sass)$/,
                use: ExtractTextPlugin.extract({
                    fallback: "style-loader",
                    use: [{
                        loader: "css-loader" // translates CSS into CommonJS
                    }, {
                        loader: "sass-loader" // compiles Sass to CSS
                    }]
                })
            }]
    },
    plugins: [
        new ManifestPlugin({
            fileName: '../mix-manifest.json',
            basePath: '/build/'
        }),
        new ExtractTextPlugin({
            filename: "[name].[contenthash].css"
            // disable: process.env.NODE_ENV === "development"
        }),
        new ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
            "window.jQuery": "jquery",
            Tether: "tether",
            "window.Tether": "tether",
            Alert: "exports-loader?Alert!bootstrap/js/dist/alert",
            Button: "exports-loader?Button!bootstrap/js/dist/button",
            Carousel: "exports-loader?Carousel!bootstrap/js/dist/carousel",
            Collapse: "exports-loader?Collapse!bootstrap/js/dist/collapse",
            Dropdown: "exports-loader?Dropdown!bootstrap/js/dist/dropdown",
            Modal: "exports-loader?Modal!bootstrap/js/dist/modal",
            Popover: "exports-loader?Popover!bootstrap/js/dist/popover",
            Scrollspy: "exports-loader?Scrollspy!bootstrap/js/dist/scrollspy",
            Tab: "exports-loader?Tab!bootstrap/js/dist/tab",
            Tooltip: "exports-loader?Tooltip!bootstrap/js/dist/tooltip",
            Util: "exports-loader?Util!bootstrap/js/dist/util",
        }),
        new CopyWebpackPlugin([
            {from: './resources/assets/img/', to: 'img'},
            {from: './resources/assets/svg/', to: 'svg'}
        ], {copyUnmodified: true}),
        // only load swedish locales for moment.js
        new webpack.ContextReplacementPlugin(/moment[\/\\]locale$/, /^\.\/(sv|en-gb)$/)
    ]
};

if (process.env.NODE_ENV === 'production') {
    module.exports.devtool = '#source-map'
    // http://vue-loader.vuejs.org/en/workflow/production.html
    module.exports.plugins = (module.exports.plugins || []).concat([
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: '"production"'
            }
        }),
        new webpack.optimize.UglifyJsPlugin({
            // sourceMap: true,
            compress: {
                warnings: false
            }
        }),
        new webpack.LoaderOptionsPlugin({
            minimize: true
        })
    ])
}


//
//  Helper Functions
//

function isEnvironment(str) {
    str = str instanceof Array ? str : [str];

    for (var i in str) {
        if (str[i] === nodeEnvironment) {
            return true;
        }
    }

    return false;
}

function isNotEnvironment(str) {
    return !isEnvironment(str);
}

function outputHeaderInformation(nodeEnvironment) {
    process.stdout.write('\n');
    var envNote = ' > ENVIRONMENT: ' + nodeEnvironment + '\n';

    if (isEnvironment(['production', 'staging'])) {
        envNote = chalk.bold.red(envNote);
    } else {
        envNote = chalk.bold.cyan(envNote);
    }

    process.stdout.write(envNote);
    process.stdout.write('\n\n');
}
