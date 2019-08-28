const path = require('path')
const UglifyJsPlugin = require('uglifyjs-webpack-plugin')

const rootLoyalty = path.join(__dirname, '/public/loyalty/')

module.exports = {
  context: rootLoyalty + 'js',
  entry: {
      app: './app'
  },
  output: {
      path: rootLoyalty + 'js',
      filename: '[name].min.js',
      publicPath: '/loyalty/js/',
      library: '[name]'
  },
  externals: {
      'jquery': '$',
      'Tipped': 'Tipped'
  },
  resolve: {
      modules: [
          path.resolve(rootLoyalty + 'blocks'),
          path.resolve(rootLoyalty + 'js'),
          path.resolve(__dirname + '/node_modules'),

      ]
  },
  module: {
      rules: [
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