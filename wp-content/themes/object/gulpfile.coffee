# dependencies
gulp = require('gulp')
gutil = require('gulp-util')
stylus = require('gulp-stylus')
concat = require('gulp-concat')
connect = require('gulp-connect')
imagemin = require('gulp-imagemin')
uglify = require('gulp-uglify')
minifyCSS = require('gulp-minify-css')
kraken = require('gulp-kraken')
clean = require('gulp-clean')
nib = require('nib')
rupture = require('rupture')
poststylus = require('poststylus')
autoprefixer = require('autoprefixer')
sourcemaps = require('gulp-sourcemaps')
lost = require('lost')
mqpacker = require('css-mqpacker')
spritesmith = require('gulp.spritesmith')
coffee = require('gulp-coffee')
gulpif = require('gulp-if')
jshint = require('gulp-jshint')
coffeelint = require('gulp-coffeelint')
stylish = require('jshint-stylish')
changed = require('gulp-changed')
# hash for cache busting
makeHash = (path) ->
  text = ''
  possible = 'abcdefghijklmnopqrstuvwxyz0123456789'
  i = 0
  while i < 5
    text += possible.charAt(Math.floor(Math.random() * possible.length))
    i++
  text
# Browserlist to use with autoprefixer
autoPrefixBrowserList = [
  'last 2 version'
  'safari 7'
  'ie 9'
  'opera 15'
  'ios 7'
  'android 4.4'
]
# LiveReaload server
gulp.task 'connect', ->
  connect.server
    root: '.'
    livereload: true
    port: 6969
  return
#linter
gulp.task 'lint', ->
  gulp.src('./assets/js/app/**/*.coffee')
  .pipe coffeelint()
  .pipe coffeelint.reporter()
  gulp.src(['./assets/js/app/**/*.js'])
  .pipe jshint()
  .pipe jshint.reporter(stylish)
  return
gulp.task 'sprites', ->
  spriteData = gulp.src('assets/images/sprites/*.png').pipe(spritesmith(
    imgName: 'sprite.png'
    imgPath: '../../dist/images/build/sprite.png'
    cssName: 'sprites.styl'
    retinaSrcFilter: ['assets/images/sprites/*@2x.png']
    retinaImgName: 'sprite@2x.png'
    retinaImgPath: '../../dist/images/build/sprite@2x.png'
  ))
  spriteData.img.pipe gulp.dest('dist/images/build/')
  spriteData.css.pipe gulp.dest('assets/stylus/elements')
gulp.task 'sprites-deploy', ['delete-old-sprites'], ->
  filename = makeHash()
  spriteData = gulp.src('assets/images/sprites/*.png')
  .pipe(spritesmith(
    imgName: 'sprite-' + filename + '.png'
    imgPath: '../../dist/images/build/sprite-' + filename + '.png'
    cssName: 'sprites.styl'
    retinaSrcFilter: ['assets/images/sprites/*@2x.png']
    retinaImgName: 'sprite-' + filename + '@2x.png'
    retinaImgPath: '../../dist/images/build/sprite-' + filename + '@2x.png'
  ))
  spriteData.img.pipe gulp.dest('dist/images/build/')
  spriteData.css.pipe gulp.dest('assets/stylus/elements')
# Scripts compiling
gulp.task "scripts", ['lint'], ->
  gulp.src(["assets/js/vendor/**/*.js", "assets/js/app/**/*.{js,coffee}"])
  .pipe gulpif(/[.]coffee$/, coffee())
  .pipe(concat("app.js"))
  .on("error", gutil.log)
  .pipe(gulp.dest("assets/js"))
  .pipe connect.reload()
#compiling our Javascripts for deployment
gulp.task "scripts-deploy", ->
  gulp.src(["assets/js/vendor/**/*.js", "assets/js/app/**/*.{js,coffee}"])
  .pipe gulpif(/[.]coffee$/, coffee())
  .pipe(concat("app.js"))
  .pipe(uglify())
  .pipe gulp.dest("dist/js")
# Compiling the Stylus files
gulp.task 'styles', ['sprites'], (done) ->
#The master (main.styl) will have everything
  gulp.src("assets/stylus/main.styl")
  .pipe(sourcemaps.init())
  .pipe(stylus(
    use: [
      nib(),
      rupture(),
      poststylus([
        autoprefixer({browsers: ['last 2 versions']})
        'lost'
        'css-mqpacker'
      ]),
    ]
  )).on("error", (error) ->
    done error
    return
  )
  .pipe(sourcemaps.write())
  .pipe(concat("main.css"))
  .pipe(gulp.dest("assets/css"))
  .pipe connect.reload()
# Compiling the Stylus files for Deployment
gulp.task 'styles-deploy', ['sprites-deploy'], ->
  gulp.src("assets/stylus/main.styl")
  .pipe(stylus(
    compress: true
    use: [
      nib(),
      rupture(),
      poststylus([
        'lost'
        'css-mqpacker'
        autoprefixer({browsers: ['last 2 versions']})
      ]),
    ]
  ))
  .pipe(concat("main.css"))
  .pipe(minifyCSS())
  .pipe gulp.dest("dist/css")
# Clean the sprites dir
gulp.task 'delete-old-sprites', ->
  gulp.src(['dist/images/build/*.png', '!dist/images/build/sprite.png', '!dist/images/build/sprite@2x.png'],
    read: false)
  .pipe clean()
# Compress images
gulp.task "images", ->
  gulp.src(['assets/images/**/*', '!assets/images/sprites/**', '!assets/images/sprites/'])
  .pipe(changed("dist/images"))
  .pipe(imagemin(
    progressive: true
    optimizationLevel: 0
    svgoPlugins: [removeViewBox: false]
    interlaced: true
  ))
  .pipe gulp.dest("dist/images")
gulp.task "reload", ->
  gulp.src(['index.html'])
  .pipe connect.reload()
gulp.task 'default', [
  'connect'
  'styles'
  'scripts'
  'images'
], ->
  gulp.watch 'assets/js/**/*', ['scripts']
  gulp.watch 'assets/stylus/**/*', ['styles']
  gulp.watch 'assets/images/**/*', ['styles']
  gulp.watch '*.html', ['reload']
  return
gulp.task 'deploy', [
  'images'
  'sprites-deploy'
  'kraken'
  'styles-deploy'
  'scripts-deploy'
], ->
  console.log("deployed!")
  return
gulp.task 'provision-dev', [
  'styles'
  'scripts'
  'images'
], ->
  console.log('ENV Provisioned')
  return
