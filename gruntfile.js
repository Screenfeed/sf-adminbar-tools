/* globals module: true, require: false */

module.exports = function( grunt ) {
	grunt.initConfig( {
		browserslist: [
			'last 3 versions'
		],
		// JS linter.
		eslint: {
			all: [ 'gruntfile.js', 'assets/js/*.js', '!**/*.min.js' ]
		},
		// JS minify.
		uglify: {
			all: {
				files: [ {
					expand: true,
					cwd:    'assets/js',
					src:    [ '*.js', '!*.min.js' ],
					dest:   'assets/js',
					ext:    '.min.js'
				} ]
			}
		},
		// PostCSS: Autoprefixer.
		postcss: {
			options: {
				processors: [
					require( 'autoprefixer' )()
				]
			},
			target: {
				files: [ {
					expand: true,
					cwd:    'assets/css',
					src:    [ '*.css', '!*.min.css' ],
					dest:   'assets/css',
					ext:    '.min.css'
				} ]
			}
		},
		// CSS minify.
		cssmin: {
			options: {
				mergeIntoShorthands: false,
				roundingPrecision:   -1
			},
			target: {
				files: [ {
					expand: true,
					cwd:    'assets/css',
					src:    [ '*.css', '!*.min.css' ],
					dest:   'assets/css',
					ext:    '.min.css'
				} ]
			}
		}
	} );

	/**
	 * Allow local configuration. For example:
	 * {
	 *   "copy": {
	 *     "whatever": {
	 *       "files": [ { "cwd": "/absolute/path/to/a/local/directory" } ]
	 *     }
	 *   }
	 * }
	 */
	if ( grunt.file.exists( 'gruntlocalconf.json' ) ) {
		grunt.config.merge( grunt.file.readJSON( 'gruntlocalconf.json' ) );
	}

	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( 'grunt-postcss' );

	// Our custom tasks.
	grunt.registerTask( 'css',    [ 'postcss', 'cssmin' ] );
	grunt.registerTask( 'js',     [ 'eslint', 'uglify' ] );
	grunt.registerTask( 'jsh',    [ 'eslint' ] );
	grunt.registerTask( 'esl',    [ 'eslint' ] );
	grunt.registerTask( 'minify', [ 'eslint', 'uglify', 'postcss', 'cssmin' ] );
};
