var path = require('path');
var utils = require('./utils');
var config = require('../config');
var vueLoaderConfig = require('./vue-loader.conf');
var eslintFriendlyFormatter = require('eslint-friendly-formatter');
var DashboardPlugin = require('webpack-dashboard/plugin');
var WebpackNotifierPlugin = require('webpack-notifier');
var pkg = require('../package.json');
var env = process.env.NODE_ENV;

var plugins = [];

// dev or production, use WebpackNotifier
if(env === 'development' || env === 'production') {
  //面板插件
  plugins.push(
    new DashboardPlugin()
  );
  //提示插件
  plugins.push(
    new WebpackNotifierPlugin({
      title: pkg.name,
      contentImage: path.join(__dirname, '../src/assets/images/logo.png')
    })
  );
}

function resolve (dir) {
  return path.join(__dirname, '..', dir);
}

module.exports = {
  watchOptions: {
    poll: true
  },
  entry: {
    app: './src/main.js'
  },
  output: {
    path: config.build.assetsRoot,
    filename: '[name].js',
    publicPath: env === 'production' ? config.build.assetsPublicPath : config.dev.assetsPublicPath
  },
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    modules: [
      resolve('src'),
      resolve('node_modules')
    ],
    alias: {
      'vue$': 'vue/dist/vue.common.js',
      'md5': 'blueimp-md5/js/md5.js',
      'src': resolve('src'),
      'assets': resolve('src/assets'),
      'components': resolve('src/components')
    }
  },
  module: {
    noParse: [/moment-with-locales/], // http://code.oneapm.com/javascript/2015/07/07/webpack_performance_1/
    rules: [
      {
        test: /\.(js|vue)$/,
        loader: 'eslint-loader',
        enforce: "pre",
        include: [resolve('src'), resolve('test')],
        options: {
          formatter: eslintFriendlyFormatter
        }
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: vueLoaderConfig
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        include: [resolve('src'), resolve('test')]
      },
      {
        test: /\.json$/,
        loader: 'json-loader'
      },
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        loader: 'url-loader',
        query: {
          limit: 10000,
          name: utils.assetsPath('img/[name].[hash:7].[ext]')
        }
      },
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        loader: 'url-loader',
        query: {
          limit: 10000,
          name: utils.assetsPath('fonts/[name].[hash:7].[ext]')
        }
      }
    ]
  },
  plugins: plugins
};
