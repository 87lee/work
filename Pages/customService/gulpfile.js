var gulp = require('gulp'),
    livereload = require('gulp-livereload');

gulp.task('watch', function () {
    var server = livereload();
        livereload.listen();
       
        gulp.watch('index.html', function (file) {
        	livereload.changed(file.path);
        });
});
