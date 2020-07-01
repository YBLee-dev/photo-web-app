module.exports = function(config) {
  config.set({
 
    basePath: '',
 
    frameworks: [ 'jasmine', 'jquery-1.8.3'],
      
    files: [
      '../public/js/script.js',
      '../resources/assets/test/*.js' 
    ],

    exclude: [
         
    ],
 
    plugins: [
        require('karma-jasmine'),        
        require('karma-chrome-launcher'),
        require('karma-jquery') 
    ],
 
    reporters: ['progress'],
 
 
    colors: true,
 
 
    autoWatch: false,
    
    // Simulating and opening all browsers
    // browsers: ['PhantomJS', 'Chrome', 'Firefox', 'IE', 'IE9'],
    browsers: ['Chrome'],    
 
    singleRun: true,

    browserNoActivityTimeout: 100000
 
  });
};