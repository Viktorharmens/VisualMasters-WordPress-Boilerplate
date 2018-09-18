/**
 * Requires
 */
var gulp            = require('gulp');
var gutil           = require('gulp-util');

// SASS
var sass            = require('gulp-sass');
var prefix          = require('gulp-autoprefixer');
var crass           = require('gulp-crass');

// JS
var uglify          = require('gulp-uglify');

// IMG
var imagemin        = require('gulp-imagemin');

// Sync
var browserSync     = require('browser-sync');

// Optimization
var cache           = require('gulp-cache');
var del             = require('del');
var runSequence     = require('run-sequence');

// Deployment
var ftp             = require( 'vinyl-ftp' );

/**
 * Gulp tasks
 */

// Start browserSync server
gulp.task('browserSync', function() {
    browserSync.init({
        proxy: "localhost:8000",
        ghostMode: {
            clicks: false,
            location: false,
            scroll: false
        }
    })
})

// SASS
gulp.task('sass', function (){
    gulp.src(['./dev/sass/styles.scss'])
        .pipe(sass({
            includePaths: ['./dev/sass','./dev/sass/base','./dev/sass/layout','./dev/sass/sections'],
            outputStyle: 'expanded'
        }))
        .on('error', swallowError)
        .pipe(prefix(
            "last 1 version", "> 1%", "ie 8", "ie 7"
        ))
        .pipe(gulp.dest('./dev/css'))
        .pipe(crass())
        .pipe(gulp.dest('./dist/css'))
        .pipe(browserSync.reload({ // Reloading with Browser Sync
            stream: true
        }));
});

// JS
gulp.task('uglify', function(){
    gulp.src('./dev/js/*.js')
        .pipe(uglify())
        .pipe(gulp.dest('./dist/js'));
});

// IMG
gulp.task('images', function() {
    return gulp.src('./dev/img/**/*.+(png|jpg|jpeg|gif|svg)')
    // Caching images that ran through imagemin
        .pipe(cache(imagemin({
            interlaced: true,
        })))
        .pipe(gulp.dest('./dist/img'))
});

// Cleaning
gulp.task('clean', function() {
    return del.sync('dist').then(function(cb) {
        return cache.clearAll(cb);
    });
})

gulp.task('clean:dist', function() {
    return del.sync(['./dist/**/*', '!./dist/img', '!./dist/img/**/*']);
});

// Watcher
gulp.task('watch', function() {
    gulp.watch('./dev/sass/**/*.scss', ['sass']);
    gulp.watch('./dev/js/**/*.js', ['uglify']);
    gulp.watch('./dist/js/*.js', browserSync.reload);
    gulp.watch('./*.php', browserSync.reload);
    gulp.watch('./**/*.php', browserSync.reload);
    gulp.watch('./dev/img/*', ['images']);
});

// Prevent errors from breaking watch
function swallowError (error) {

    // If you want details of the error in the console
    console.log(error.toString())

    this.emit('end')
}

/**
 * Watch, Build & Deploy
 */

// Default function (for dev)
gulp.task('default', function(callback) {
    runSequence(['sass', 'uglify', 'browserSync'], 'watch',
        callback
    )
})

// Build for production
gulp.task('build', function(callback) {
    runSequence(
        'clean:dist',
        'sass',
        ['uglify', 'images'],
        callback
    )
})

gulp.task( 'deploy', function () {

    // Set connection
    var conn = ftp.create( {
        host:     'visualtest.nl',
        user:     'visualtest',
        password: 'VisualMasters@109',
        parallel: 10,
        log:      gutil.log
    } );

    var globs = [
        'inc/**',
        'dist/**',
        'layout/**',
        '*.css',
        '*.php'
    ];

    // using base = '.' will transfer everything to /public_html correctly
    // turn off buffering in gulp.src for best performance

    return gulp.src( globs, { base: '.', buffer: false } )
        .pipe( conn.newer( '/' ) ) // only upload newer files
        .pipe( conn.dest( '/domains/nsvleden.visualtest.nl/public_html/' ) );

} );