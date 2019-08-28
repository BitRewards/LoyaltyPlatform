'use strict';

const loyaltyConfig = require('./loyalty.webpack.config')
const adminConfig = require('./admin.webpack.config')
const cashierConfig = require('./cashier.webpack.config')

const elixir = require('laravel-elixir');
var fs = require('fs');
var argv = require('yargs').argv;

var gulp = require('gulp'),
    path = require('path'),
    webpack = require('webpack');

var root = path.join(__dirname, '/public/'),
    rootLoyalty = path.join(__dirname, '/public/loyalty/'),
    rootResources = path.join(__dirname, '/resources/');

gulp.task('loyalty-styles', function() {
    var postcss    = require('gulp-postcss'),
        sourcemaps = require('gulp-sourcemaps'),
        atImport = require("postcss-import");

    var timestamp = new Date().getTime();

    var rewriteConfig = {
    imports: true,
    properties: [ 'background-image' ],
    rules: [
            { from: 'icons-common.png', to: '/loyalty/images/sprite/icons-common.png?' + timestamp },
            { from: 'icons-common@2x.png', to: '/loyalty/images/sprite/icons-common@2x.png?' + timestamp },
        ]
    };

    var processors = [
        atImport(),
        require("postcss-cssnext")(),
        require('postcss-short'),
        require('postcss-center'),
        require('postcss-triangle'),
        require('postcss-custom-media'),
        require('postcss-easysprites')({
            imagePath: rootLoyalty + 'images/sprite', 
            spritePath: rootLoyalty + 'images/sprite',
            stylesheetPath: rootLoyalty + 'images/sprite'
        }),
        require('postcss-urlrewrite')(rewriteConfig),
        require('postcss-inline-svg')({
            path: rootLoyalty + 'images/svg/'
        }),
        require('postcss-mixins'),
        require('postcss-pxtorem'),
        require('postcss-svgo'),
        require("css-mqpacker")(),
        require('postcss-discard-comments'),
        require('autoprefixer'),
        require('precss'),
        require('cssnano')({safe: true})
    ];

    return gulp.src(rootLoyalty + 'styles/styles.css')
        .pipe( postcss( processors ) )
        .pipe( gulp.dest(rootLoyalty + 'css/') )
        // .pipe(shell(['cd .. && ./yiic static minifyPngIcons']));
});

function scripts(config) {
    return new Promise(resolve => webpack(config, (err, stats) => {

        if (err) console.log('Webpack', err)

        console.log(stats.toString())

        resolve()
    }))
}


gulp.task('loyalty-scripts', () => scripts(loyaltyConfig))

gulp.task('watch-loyalty', function(callback) {
  gulp.watch(rootLoyalty + 'blocks/**/*.css', ['loyalty-styles']);
  gulp.watch([rootLoyalty + '/**/*.js', '!' + rootLoyalty + 'js/app.min.js'], ['loyalty-scripts']);
  callback();
})

gulp.task('admin', () => {
    makeTranslationFile('resources/lang/i18n/ru_RU/LC_MESSAGES/default.json', rootResources + "assets/js/admin-app/translation/ru.js");
    makeTranslationFile('resources/lang/i18n/tr_TR/LC_MESSAGES/default.json', rootResources + "assets/js/admin-app/translation/tr.js");
    makeTranslationFile('resources/lang/i18n/zh_CN/LC_MESSAGES/default.json', rootResources + "assets/js/admin-app/translation/cn.js");


    scripts(adminConfig)
})

gulp.task('cashier', () => {
    makeTranslationFile('resources/lang/i18n/ru_RU/LC_MESSAGES/default.json', rootResources + "assets/js/translation/ru.js");
    makeTranslationFile('resources/lang/i18n/tr_TR/LC_MESSAGES/default.json', rootResources + "assets/js/translation/tr.js");
    makeTranslationFile('resources/lang/i18n/zh_CN/LC_MESSAGES/default.json', rootResources + "assets/js/translation/cn.js");

    elixir(function(mix) {
        mix.sass('app.scss', 'public/cashier/style.css');
    });

    scripts(cashierConfig)
})

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

gulp.task('loyalty', ['loyalty-styles', 'loyalty-scripts']);

function makeTranslationFile(translationFileName, path)
{
    let translation = fs.readFileSync(translationFileName, 'utf8');
    fs.writeFileSync(path, "module.exports = " + translation);
}

elixir(function(mix) {
    let all = [];

    let styles = JSON.parse(fs.readFileSync('config/assets/styles.json', 'utf8'));
    Object.keys(styles).forEach(function(name) {
        var minFile = 'public/css/minified/' + name + '.min.css';
        all.push(minFile);

        mix.styles(styles[name], minFile, 'public');
    });

    let scripts = JSON.parse(fs.readFileSync('config/assets/scripts.json', 'utf8'));
    Object.keys(scripts).forEach(function(name) {
        let minFile = 'public/js/minified/' + name + '.min.js';
        all.push(minFile);

        mix.scripts(scripts[name], minFile, 'public');
    });

    mix.version(all);
});


gulp.task('poeditToJson', function(callback){
    let poeditToJson = require('gulp-poedit-to-json');
    poeditToJson({ path: rootResources + 'lang/i18n/ru_RU/LC_MESSAGES'});
    poeditToJson({ path: rootResources + 'lang/i18n/tr_TR/LC_MESSAGES'});
    poeditToJson({ path: rootResources + 'lang/i18n/zh_CN/LC_MESSAGES'});
    callback();
});
