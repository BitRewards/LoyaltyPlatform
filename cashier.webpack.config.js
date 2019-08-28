const path = require('path')
const UglifyJsPlugin = require('uglifyjs-webpack-plugin')
const VueLoaderPlugin = require('vue-loader/lib/plugin')

const rootCashier = path.join(__dirname, '/resources/assets/js/')
const destCashier = path.join(__dirname, '/public/cashier/')

module.exports = {
  context: rootCashier,
  entry: {
      app: './app'
  },

  output: {
      path: destCashier,
      filename: '[name].js',
      library: '[name]'
  },

  module: {
      rules: [
        {
          test: /\.vue$/,
          loader: 'vue-loader'
        },
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
              loader: 'babel-loader',
              options: {
                  babelrc: true
              },
          }
        },
        {
          test: /\.css$/,
          use: ['style-loader', 'css-loader'],
        },
        {
          test: /vendor\/.+\.(jsx|js)$/,
          use: 'imports-loader?jQuery=jquery,$=jquery,this=>window'
        }
      ]
  },
  plugins: [
    new VueLoaderPlugin()
  ],
  optimization: {
      minimizer: [
        new UglifyJsPlugin({
          sourceMap: true,
          uglifyOptions: {
            compress: {
              inline: false,
              drop_console: false
            },
            output: {
              comments: false
            },
            ecma: 6,
            mangle: true
          },
        }),
      ]
  }
}