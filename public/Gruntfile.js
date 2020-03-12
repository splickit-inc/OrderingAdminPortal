/*jslint node: true */
'use strict';

var pkg = require('./package.json');

//Using exclusion patterns slows down Grunt significantly
//instead of creating a set of patterns like '**/*.js' and '!**/node_modules/**'
//this method is used to create a set of inclusive patterns for all subdirectories
//skipping node_modules, bower_components, dist, and any .dirs
//This enables users to create any directory structure they desire.
var createFolderGlobs = function (fileTypePatterns) {
    fileTypePatterns = Array.isArray(fileTypePatterns) ? fileTypePatterns : [fileTypePatterns];
    var ignore = ['node_modules', 'bower_components', 'dist', 'temp', 'assets', 'app-dark.css', 'app-light.css', '*.min.css', '*.min.js', 'e2e_testing'];
    var fs = require('fs');
    return fs.readdirSync(process.cwd())
        .map(function (file) {
            if (ignore.indexOf(file) !== -1 ||
                file.indexOf('.') === 0 ||
                !fs.lstatSync(file).isDirectory()) {
                return null;
            } else {
                return fileTypePatterns.map(function (pattern) {
                    return file + '/**/' + pattern;
                });
            }
        })
        .filter(function (patterns) {
            return patterns;
        })
        .concat(fileTypePatterns);
};

module.exports = function (grunt) {

    // load all grunt tasks
    require('load-grunt-tasks')(grunt);

    // Project configuration.
    grunt.initConfig({
        connect: {
            main: {
                options: {
                    port: 9001
                }
            }
        },
        jshint: {
            main: {
                options: {
                    jshintrc: '.jshintrc',
                    reporterOutput: ""
                },
                src: createFolderGlobs('*.js')
            }
        },
        clean: {
            before: {
                src: ['app-dark.css', 'app-light.css', '*.min.css', '*.min.js', 'dist', 'temp']
            },
            after: {
                src: ['temp', 'app-dark.css', 'app-light.css']
            }
        },
        less: {
            production: {
                options: {
                    strictImports: true,
                    syncImport: true
                    //compress:true
                },
                files: {
                    'app-dark.css': 'app-dark.less', 'app-light.css': 'app-light.less'
                }
            }
        },
        copy: {
            main: {
                files: [
                    {src: ['img/**'], dest: 'dist/'},
                    {
                        src: ['bower_components/font-awesome/fonts/**'],
                        dest: 'dist/',
                        filter: 'isFile',
                        expand: true
                    },
                    {
                        src: ['bower_components/bootstrap/fonts/**'],
                        dest: 'dist/',
                        filter: 'isFile',
                        expand: true
                    }
                    //{src: ['bower_components/angular-ui-utils/ui-utils-ieshiv.min.js'], dest: 'dist/'},
                    //{src: ['bower_components/select2/*.png','bower_components/select2/*.gif'], dest:'dist/css/',flatten:true,expand:true},
                    //{src: ['bower_components/angular-mocks/angular-mocks.js'], dest: 'dist/'}
                ]
            }
        },
        dom_munger: {
            read: {
                options: {
                    read: [
                        {
                            selector: 'script[data-concat!="false"][src!="bower_components/less.js/dist/less.js"]',
                            attribute: 'src',
                            writeto: 'appjs'
                        },
                        {
                            selector: 'link[rel="stylesheet"][data-concat!="false"]',
                            attribute: 'href',
                            writeto: 'appcss'
                        }
                    ]
                },
                src: 'index.html'
            },
            update: {
                options: {
                    remove: ['script[data-remove!="false"]', 'link[data-remove!="false"]'],
                    append: [
                        {
                            selector: 'body',
                            html: '<script src="/app.full.<%= timestamp %>.min.js"></script>'
                        },
                        {
                            selector: 'head',
                            html: '<link rel="stylesheet"' + 'href="/app-light.full.<%= timestamp %>.min.css"' + '>\n'
                        },
                        {
                            selector: 'head',
                            html: '<link rel="stylesheet" ng-href="/app-{{theme}}.full.<%= timestamp %>.min.css"' + '>\n'
                        }
                    ]
                },
                src: 'index.html',
                dest: 'index_production.html'
            }
        },
        cssmin: {
            target: {
                files: {
                    'app-dark.full.<%= timestamp %>.min.css': ['<%= dom_munger.data.appcss %>', 'app-dark.css', 'css/style.css'],
                    'app-light.full.<%= timestamp %>.min.css': ['<%= dom_munger.data.appcss %>', 'app-light.css', 'css/style.css']
                }
            }
        },
        concat: {
            main: {
                src: ['<%= dom_munger.data.appjs %>', '<%= ngtemplates.main.dest %>'],
                dest: 'temp/app.full.js'
            }
        },
        ngAnnotate: {
            main: {
                src: 'temp/app.full.js',
                dest: 'temp/app.full.js'
            }
        },
        uglify: {
            main: {
                src: 'temp/app.full.js',
                dest: 'app.full.<%= timestamp %>.min.js'
            }
        },
        htmlmin: {
            main: {
                options: {
                    collapseBooleanAttributes: true,
                    collapseWhitespace: true,
                    keepClosingSlash:true,
                    removeAttributeQuotes: true,
                    removeComments: true,
                    removeEmptyAttributes: true,
                    removeScriptTypeAttributes: true,
                    removeStyleLinkTypeAttributes: true
                },
                files: {
                    'index_production.html': 'index_production.html'
                }
            }
        },
        watch: {
            scripts: {
                files: createFolderGlobs('*.less'),
                tasks: ['clean:before', 'less'],
                options: {
                    livereload: 8081,
                    spawn: false
                }
            }
        },
        karma: {
            options: {
                frameworks: ['jasmine'],
                files: [  //this files data is also updated in the watch handler, if updated change there too
                    '<%= dom_munger.data.appjs %>',
                    'bower_components/angular-mocks/angular-mocks.js',
                    createFolderGlobs('*-spec.js')
                ],
                logLevel: 'ERROR',
                reporters: ['mocha'],
                autoWatch: false, //watching is handled by grunt-contrib-watch
                singleRun: true
            },
            all_tests: {
                browsers: ['PhantomJS', 'Chrome', 'Firefox']
            },
            during_watch: {
                browsers: ['PhantomJS']
            }
        },
        wiredep: {
            task: {

                // Point to the files that should be updated when
                // you run `grunt wiredep`
                src: [
                    'index.html'
                ],
                options: {}
            }
        },
        ngtemplates: {
            main: {
                options: {
                    module: 'adminPortal',
                    standalone: false,
                    htmlmin: '<%= htmlmin.main.options %>'
                },
                src: [createFolderGlobs('*.html'), '!index.html', '!index_production.html', '!_SpecRunner.html'],
                dest: 'temp/templates.<%= timestamp %>.js'
            }
        },
        timestamp: Math.floor((1 + Math.random()) * 0x10000000).toString(16).substring(1)
    });

    grunt.loadNpmTasks('grunt-angular-templates');
    grunt.loadNpmTasks('grunt-wiredep');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.registerTask('build', ['clean:before', 'jshint', 'wiredep', 'less', 'dom_munger','cssmin', 'ngtemplates','concat', 'ngAnnotate', 'uglify', 'htmlmin']);
    grunt.registerTask('serve', ['clean:before', 'wiredep', 'less', 'watch']);
    grunt.registerTask('testcss', ['less', 'dom_munger', 'cssmin']);
    grunt.registerTask('test', ['dom_munger:read', 'karma:all_tests']);
};
