(function($, d, w, undefined) { // eslint-disable-line no-unused-vars, no-shadow-restricted-names

	if ( ! w.console || ! w.console.log ) {
		// Polyfill for console.
		(function() {
			var noop    = function() {},
				methods = [ "assert", "clear", "count", "debug", "dir", "dirxml", "error", "exception", "group", "groupCollapsed", "groupEnd", "info", "log", "markTimeline", "profile", "profileEnd", "markTimeline", "table", "time", "timeEnd", "timeStamp", "trace", "warn" ],
				length  = methods.length,
				console = w.console = {};
			while ( length-- ) {
				console[ methods[ length ] ] = noop;
			}
		}());
	}

	w.sfabt = {
		// Tools ===================================================================================
		/*
		 * Init.
		 */
		init: function () {
			w.sfabtContext = w.sfabtContext || {};

			// Get `$wp_query`'s value with ajax.
			if ( w.sfabtContext.queryNonce ) {
				$( "body" )
					// Open the $wp_query lightbox
					.on( "click keydown", ".sfabt-get-var-button", w.sfabt.openLightboxCallback )
					// Close the $wp_query lightbox.
					.on( "click keydown", "#sfabt-pre-wrap", w.sfabt.closeLightboxCallback );
			}

			// Action input fields (admin).
			$( "#wp-admin-bar-sfabt-tools" )
				.on( "click focus", "input.no-adminbar-style", w.sfabt.enlargeYourHookCallback )
				.on( "blur", "input.no-adminbar-style", w.sfabt.reduceYourHookCallback );
		},
		/*
		 * Console.log only if debug is active.
		 *
		 * @param {mixed} Code The thing to log.
		 */
		log: function ( code ) {
			if ( w.sfabtContext.debug ) {
				w.console.log( code );
			}
		},
		/*
		 * Tells if a modifier key is pressed.
		 *
		 * @param  {object} jQuery's Event object.
		 * @return {bool}
		 */
		hasModifierKey: function ( e ) {
			return e.altKey || e.ctrlKey || e.metaKey || e.shiftKey;
		},
		/*
		 * Tells if the pressed key is Space or Enter (without modifier key).
		 *
		 * @param  {object} jQuery's Event object.
		 * @return {bool}
		 */
		isSpaceOrEnterKey: function ( e ) {
			return ( 13 === e.which || 32 === e.which ) && ! w.sfabt.hasModifierKey( e );
		},
		/*
		 * Tells if the pressed key is Escape (without modifier key).
		 *
		 * @param  {object} jQuery's Event object.
		 * @return {bool}
		 */
		isEscapeKey: function ( e ) {
			return 27 === e.which && ! w.sfabt.hasModifierKey( e );
		},
		// Event callbacks =========================================================================
		/*
		 * Opens a lightbox that displays the value of a var (or something else).
		 *
		 * @param {object} jQuery's Event object.
		 */
		openLightboxCallback: function ( e ) {
			var $button, $buttons, $preCode, globalVar, pageUrl, sep;

			if ( "keydown" === e.type && ! w.sfabt.isSpaceOrEnterKey( e ) ) {
				return;
			}

			e.preventDefault();

			$button = $( e.currentTarget );

			if ( $button.prop( "disabled" ) ) {
				return;
			}

			$buttons  = $( ".sfabt-get-var-button" ).prop( "disabled", true );
			$preCode  = $( "#sfabt-code" );
			globalVar = $button.data( "var" );
			pageUrl   = window.location.href;
			sep       = ( pageUrl.indexOf( "?" ) !== -1 ) ? "&" : "?";

			if ( "sfabt-pre" === globalVar ) {
				globalVar = $button.text().replace( "$", "" );
			}

			if ( pageUrl.indexOf( "#" ) !== -1 ) {
				pageUrl = pageUrl.split( "#" )[0];
			}

			$.get( pageUrl + sep + "_wpnonce=" + w.sfabtContext.queryNonce + "&sfabt-var=" + globalVar )
				.always( function ( data, status, jqXHR ) {
					var html     = "",
						dataType = typeof data;

					if ( "object" === dataType && 4 === data.readyState ) {
						html = data.responseText;
					}
					else if ( "object" !== dataType && 4 === jqXHR.readyState ) {
						html = data;
					}

					if ( ! $preCode.length ) {
						$preCode = $( $( '#tmpl-sfabt-adminbar-lightbox' ).html() );
						$preCode.find( '#sfabt-title button' ).data( 'var', globalVar );
						$preCode.find( '#sfabt-var-name' ).text( globalVar );
						$preCode = $preCode.appendTo( $( "body" ) ).find( "#sfabt-code" );
					}

					$buttons.prop( "disabled", false );
					$preCode.html( html ).prev( "#sfabt-title" ).children( ".sfabt-get-var-button" ).focus();
				} );
		},
		/*
		 * Closes the lightbox that displays the value of a var (or something else).
		 *
		 * @param {object} jQuery's Event object.
		 */
		closeLightboxCallback: function ( e ) {
			if ( "keydown" === e.type && w.sfabt.isEscapeKey( e ) ) {
				$( e.currentTarget ).remove();
				e.preventDefault();
			}
			else if ( e.currentTarget === e.target ) {
				if ( "keydown" === e.type && ! w.sfabt.isSpaceOrEnterKey( e ) ) {
					return;
				}
				$( e.currentTarget ).remove();
				e.preventDefault();
			}
		},
		/*
		 * Displays a `add_action( ... )` when focussing a "admin hook" in the adminbar.
		 *
		 * @param {object} jQuery's Event object.
		 */
		enlargeYourHookCallback: function ( e ) {
			var $input     = $( e.currentTarget ),
				nbr_params = $input.data( "nbrparams" ),
				newValue   = "add_action( '" + e.currentTarget.defaultValue + "', ''" + ( "undefined" !== nbr_params && Number( nbr_params ) > 1 ? ", 10, " + Number( nbr_params ) : "" ) + " );",
				newWidth   = newValue.length;

			$input.val( newValue ).css( "width", newWidth + "ch" );
			e.currentTarget.select();
		},
		/*
		 * Ramoves the `add_action( ... )` when bluring a "admin hook" in the adminbar.
		 *
		 * @param {object} jQuery's Event object.
		 */
		reduceYourHookCallback: function ( e ) {
			$( e.currentTarget ).val( e.currentTarget.defaultValue ).css( "width", "" );
		}
	};

	w.sfabt.init();

} )( jQuery, document, window );
