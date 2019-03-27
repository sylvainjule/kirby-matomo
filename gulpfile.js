const { src, dest } = require('gulp');
const autoprefixer = require('gulp-autoprefixer');

function prefix() {
    return src('./index.css')
               .pipe(autoprefixer())
               .pipe(dest('./'));
}

// Default Task
exports.default = prefix;
