const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

module.exports = {
    mode: 'development',
    entry: {
        style: path.resolve(__dirname, 'frontend/scss/style.scss'),
        app: path.resolve(__dirname, 'frontend/js/app.js'),
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'frontend/web/build'),
    },
    watch: true,
    watchOptions: {
        ignored: ['vendor/**', 'node_modules/**', 'frontend/web/**'],
        aggregateTimeout: 600,
        poll: 1000,
    },
    devtool: "source-map",
    plugins: [
        new RemoveEmptyScriptsPlugin(),
        new MiniCssExtractPlugin(),
    ],
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    // Creates `style` nodes from JS strings
                    // "style-loader",
                    // Translates CSS into CommonJS
                    "css-loader",
                    // Compiles Sass to CSS
                    "sass-loader",
                ],
            },
        ],
    },
};