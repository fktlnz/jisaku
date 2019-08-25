
var gulp = require('gulp');
var minifycss = require('gulp-clean-css');
var sass = require('gulp-sass');
var imagemin = require('gulp-imagemin');
var changed = require('gulp-changed');
var packageImporter = require('node-sass-package-importer');

//タスクの作成
//gulp.task()
//第一引数に任意のタスク名、第二引数に実行したい処理をfunction関数で渡してあげる


//cssファイルの圧縮
gulp.task('minify-css', function (done) {
    gulp.src("src/css/*.css")
        .pipe(minifycss())
        .pipe(gulp.dest("dist/css/"));
    done()
});

//sassをコンパイル
gulp.task('sass', function (done) {
    gulp.src("../scss/app.scss")
        .pipe(sass({
            outputStyle: 'expanded',
            importer: packageImporter({
                extensions: ['.scss', '.css']
            })
        }))
        .pipe(gulp.dest("../scss/"));
    done()
});

//ローカルサーバー起動、自動更新タスク
const browserSync = require('browser-sync').create()

const browserSyncOption = {
    proxy: "localhost:8888/jisaku-app-git/",
    
    files: [
        "../scss/app.css"
    ],

}
gulp.task('serve', (done) => {
    browserSync.init(browserSyncOption)
    done()
})

//画像の圧縮
var paths = {
    srcDir: 'src',
    dstDir: 'dist'
};
gulp.task('watch', (done) => {
    
    //
    gulp.watch('../scss/foundation/**/*.css', gulp.task('sass'));
    gulp.watch('../scss/layout/**/*.css', gulp.task('sass'));
    gulp.watch('../scss/object/**/*.css', gulp.task('sass'));

    //ブラウザ更新
    const browserReload = (done) => {
        browserSync.reload()
        done()
    }
    gulp.watch('../scss/app.css', browserReload)
    gulp.watch('../*.php', browserReload)
})

//画像圧縮タスク
gulp.task('imagemin', function (done) {
    var srcGlob = paths.srcDir + '/**/*.+(jpg|jpeg|png|gif)';
    var dstGlob = paths.dstDir;
    gulp.src(srcGlob)
        .pipe(changed(dstGlob))
        .pipe(imagemin([
            imagemin.gifsicle({ interlaced: true }),
            imagemin.jpegtran({ progressive: true }),
            imagemin.optipng({ optimizationLevel: 5 }),
            
        ]))
        .pipe(gulp.dest(dstGlob));
    done()
});

// gulp.task('watch', function () {
//     return gulp.watch(paths.srcDir + '/**/*', gulp.task('sass'));
// });

//defaultのタスクを設定する
//defaultで設定しておくとgulpコマンドだけでタスクが実行される
//書き方は第二引数に配列でタスクを指定する
//gulp.task('default, ['タスク名','タスク名','タスク名','タスク名'])
//gulp.task('default', ['minify-css']);
gulp.task('default', gulp.series('serve', 'sass', 'watch'));