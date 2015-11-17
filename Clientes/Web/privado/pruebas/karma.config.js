// Karma configuration
// Generated on Sun Oct 04 2015 22:58:46 GMT-0500 (Hora de verano central (México))

module.exports = function(config) {
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',


    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['jasmine'],


    // list of files / patterns to load in the browser
    files: [
      '../scripts/angular.js',
      '../scripts/angular-mocks.js',
      '../scripts/angular-route.min.js',
      '../scripts/loading-bar.js',
      '../scripts/angular-google-maps.js',
      '../scripts/ng-tags-input.js',
      '../scripts/angular-simple-logger.min.js',
      '../scripts/lodash.min.js',
      '../scripts/ui-bootstrap-tpls-0.14.3.js',
      '../*.js',
      '../controladores/*.js',
      '../servicios/*.js',
      'servicios/*.tests.js',
      'controladores/*.tests.js'
    ],


    // list of files to exclude
    exclude: [
    ],


    // preprocess matching files before serving them to the browser
    // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
    preprocessors: {
    },


    // test results reporter to use
    // possible values: 'dots', 'progress'
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: ['progress'],


    // web server port
    port: 9876,


    // enable / disable colors in the output (reporters and logs)
    colors: true,


    // level of logging
    // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
    logLevel: config.LOG_INFO,


    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,


    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: ['Chrome'],


    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: false
  })
}
