var concat = require('gulp-concat');
var env = process.env.GULP_ENV;
var gulp = require('gulp');
var gulpif = require('gulp-if');
var livereload = require('gulp-livereload');
var merge = require('merge-stream');
var order = require('gulp-order');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var argv = require('yargs').argv;

var rootPath = argv.rootPath;
var adminRootPath = rootPath + 'admin/';
var resourcePath = argv.resourcePath || '';

var paths = {
    admin: {
        js: [
            resourcePath + 'Resources/private/js/**'
        ],
        sass: [
            resourcePath + 'Resources/private/sass/**'
        ],
        css: [
            resourcePath + 'Resources/private/css/**'
        ],
        img: [
            resourcePath + 'Resources/private/img/**'
        ]
    }
};

gulp.task('admin-js', function () {
    return gulp.src(paths.admin.js)
        .pipe(concat('app.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'js/'))
    ;
});

gulp.task('admin-css', function() {
    var cssStream = gulp.src(paths.admin.css)
        .pipe(concat('css-files.css'))
    ;

    var sassStream = gulp.src(paths.admin.sass)
        .pipe(sass())
        .pipe(concat('sass-files.scss'))
    ;

    return merge(cssStream, sassStream)
        .pipe(order(['css-files.css', 'sass-files.scss']))
        .pipe(concat('style.css'))
        .pipe(gulpif(env === 'prod', uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'css/'))
        .pipe(livereload())
    ;
});

gulp.task('admin-img', function() {
    return gulp.src(paths.admin.img)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'img/'))
    ;
});

gulp.task('admin-watch', function() {
    livereload.listen();

    gulp.watch(paths.admin.js, ['admin-js']);
    gulp.watch(paths.admin.sass, ['admin-css']);
    gulp.watch(paths.admin.css, ['admin-css']);
    gulp.watch(paths.admin.img, ['admin-img']);
});

gulp.task('default', ['admin-js', 'admin-css', 'admin-img']);
gulp.task('watch', ['default', 'admin-watch']);
