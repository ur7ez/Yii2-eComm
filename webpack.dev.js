const { merge } = require('webpack-merge');
const common = require('./webpack.common.js');

module.exports = merge(common, {
    mode: 'development',
    watch: true,
    watchOptions: {
        ignored: ['vendor/**', 'node_modules/**', 'frontend/web/**'],
        aggregateTimeout: 600,
        poll: 1000,
    },
    devtool: "source-map",
});