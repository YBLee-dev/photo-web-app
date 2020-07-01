/**
 * Define NODE_ENV variable for global scope
 * And define necessary variable
 */
const NODE_ENV = process.env.NODE_ENV || 'dev';
const webpack = require('webpack');
const path = require('path');

console.info(`now is ${NODE_ENV} mode`);

module.exports = {
    /**
     * Entry points for scripts and library
     */
    entry: {
        script : path.join(__dirname,'./js/script.js'),
        libs : path.join(__dirname,'./js/libs.js')
    },
    /**
     * Output point.
     */
    output: {
        path: path.join(__dirname,'../../../public_html/js/'),
        publicPath: '/js/',
        filename: '[name].js', // for each of entry point create js
    },
    /**
     * For dev mode enable watch
     */
    watch: NODE_ENV == 'dev',
    watchOptions: {
        aggregateTimeout: 100 // what time about run rebuild
    },
    /**
     * Resolving need for js loaders
     */
    resolve: {
        root: [ path.resolve(__dirname, 'node_modules') ],
        modulesDirectories: [ path.resolve(__dirname, 'node_modules') ]
    },
    resolveLoader: {
        root: [ path.resolve(__dirname, 'node_modules') ]
    },
    /**
     * build different souce-map for different mode
     * details [https://webpack.github.io/docs/configuration.html#devtool]
     */
    devtool: NODE_ENV == 'dev' ? "source-map" : "cheap-module-source-map",
    /**
     * Define loaders which transpile and compiling js
     */
    module: {
        loaders: [
            {
                test: /\.js$/,
                loader: 'babel',
                query: {
                    presets: require.resolve('babel-preset-es2015'),
                    plugins: require.resolve('babel-plugin-transform-runtime'),
                },
                exclude:path.resolve(__dirname, "node_modules")
            },
            {
                test: require.resolve('jquery'),
                loader: 'expose?jQuery!expose?$'
            }
        ],
    },
    /**
     * Define necessary plugins
     */
    plugins:[
        new webpack.NoErrorsPlugin(), // Disable build js with error
        new webpack.DefinePlugin({
            NODE_ENV: JSON.stringify(NODE_ENV), // Define NODE_ENV global
        }),
    ]
};
/**
 * For prod mode uglify js
 */
if(NODE_ENV == 'prod'){
    module.exports.plugins.push(
        new webpack.optimize.UglifyJsPlugin({
            warning: false
        })
    );
}