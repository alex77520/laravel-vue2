var path = require('path');
var utils = require('./utils');
var webpack = require('webpack');
var config = require('../config');
var merge = require('webpack-merge');
var baseWebpackConfig = require('./webpack.base.conf');
var HtmlWebpackPlugin = require('html-webpack-plugin');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var StatsPlugin = require('stats-webpack-plugin');
var env = process.env.NODE_ENV === 'testing' ? require('../config/test.env') : config.build.env;

var args = process.argv;
var debug = args.indexOf('--debug') > -1;
console.log('debug:' + debug);

var plugins = [
  // http://vuejs.github.io/vue-loader/en/workflow/production.html
  new webpack.DefinePlugin({
    'process.env': env
  }),
  // extract css into its own file
  new ExtractTextPlugin(debug ? utils.assetsPath('css/[name].css') : utils.assetsPath('css/[name].[contenthash].css')),
  // generate dist index.html with correct asset hash for caching.
  // you can customize output by editing /index.html
  // see https://github.com/ampedandwired/html-webpack-plugin
  new HtmlWebpackPlugin({
    filename: process.env.NODE_ENV === 'testing'
      ? 'index.html'
      : config.build.index,
    template: 'index.html',
    inject: true,
    minify: {
      removeComments: true,
      //collapseWhitespace: true,
      //removeAttributeQuotes: true
      // more options:
      // https://github.com/kangax/html-minifier#options-quick-reference
    },
    // necessary to consistently work with multiple chunks via CommonsChunkPlugin
    chunksSortMode: 'dependency'
  }),
  // split vendor js into its own file
  new webpack.optimize.CommonsChunkPlugin({
    name: 'vendor',
    minChunks: function (module, count) {
      // any required modules inside node_modules are extracted to vendor
      return (
        module.resource &&
        /\.js$/.test(module.resource) &&
        module.resource.indexOf(
          path.join(__dirname, '../node_modules')
        ) === 0
      );
    }
  }),
  // extract webpack runtime and module manifest to its own file in order to
  // prevent vendor hash from being updated whenever app bundle is updated
  new webpack.optimize.CommonsChunkPlugin({
    name: 'manifest',
    chunks: ['vendor']
  })
];

if(debug === false) {
  plugins.push(
    new webpack.optimize.UglifyJsPlugin({
      beautify: false,
      comments: false,
      compress: {
        warnings: false,
        drop_console: true,
        collapse_vars: true,
        reduce_vars: true,
        screw_ie8: true
      },
      sourceMap: config.build.productionSourceMap,
      mangle: {
        except: [],
        screw_ie8: true,
        keep_fnames: true
      }
    })
  );
  // https://github.com/unindented/stats-webpack-plugin
  plugins.push(
    new StatsPlugin('stats.json', {
      chunkModules: true,
      chunks: true,
      assets: true,
      modules: true,
      children: true,
      chunksSort: true,
      assetsSort: true
    })
  );
} else {
  // debug 开启 watch
  baseWebpackConfig.watch = true;
}

var webpackConfig = merge(baseWebpackConfig, {
  module: {
    rules: utils.styleLoaders({
      sourceMap: config.build.productionSourceMap,
      extract: true
    })
  },
  devtool: (debug === false && config.build.productionSourceMap) ? '#source-map' : false,
  output: {
    path: config.build.assetsRoot,
    filename: debug ? utils.assetsPath('js/[name].js') : utils.assetsPath('js/[name].[chunkhash].js'),
    chunkFilename: debug ? utils.assetsPath('js/[id].js') : utils.assetsPath('js/[id].[chunkhash].js')
  },
  plugins: plugins
});

if (config.build.productionGzip) {
  var CompressionWebpackPlugin = require('compression-webpack-plugin');

  webpackConfig.plugins.push(
    new CompressionWebpackPlugin({
      asset: '[path].gz[query]',
      algorithm: 'gzip',
      test: new RegExp(
        '\\.(' +
        config.build.productionGzipExtensions.join('|') +
        ')$'
      ),
      threshold: 10240,
      minRatio: 0.8
    })
  );
}

if (config.build.bundleAnalyzerReport) {
  var BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
  webpackConfig.plugins.push(new BundleAnalyzerPlugin());
}

module.exports = webpackConfig;
