const path = require('path')
const UglifyJsPlugin = require('uglifyjs-webpack-plugin')
const VueLoaderPlugin = require('vue-loader/lib/plugin')

const rootAdmin = path.join(__dirname, '/resources/assets/js/admin-app/')
const destAdmin = path.join(__dirname, '/public/admin-app/')

module.exports = {
  context: rootAdmin,
  entry: {
      admin: './admin'
  },

  output: {
      path: destAdmin,
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
              drop_console: true
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