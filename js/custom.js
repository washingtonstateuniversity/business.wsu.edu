(function($, window){
	var view_is_home = 0;

	/**
	 * Determine if this page view has the `home` class assigned to body.
	 *
	 * @returns bool True if home page, false if not.
	 */
	function is_home() {
		if ( 0 === view_is_home ) {
			view_is_home = ( $('.home' ).length > 0 );
		}

		return view_is_home;
	}

	function is_ios() {
		return ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
	}

	if ( ! is_home() && ! is_ios() ) {
		// Thanks to a good write-up on approach from https://medium.com/@TonyJing/medium-style-header-aa738696c6ac
		var last_position = $(window).scrollTop();

		var cob_headline_container = $('.cob-headline-container');
		var cob_headline_container_height = cob_headline_container.height();
		var main_header = $('.main-header');
		var main_header_height = main_header.height();

		var page_cta = $('.page-call-to-action');

		//rmain_header.css( { 'height' : main_header_height } );

		// The headline container starts with no margin property.
		var margin_top = 0;
		var header_opacity = 1;
		var cta_opacity = 1;

		$(window).on( 'scroll', function() {
			var page_position = $( this ).scrollTop();

			if ( page_position > last_position ) {
				// scrolling down
				main_header_height = main_header_height - ( page_position - last_position );
				margin_top = margin_top - ( ( page_position - last_position ) / 1.85 );
				header_opacity = header_opacity - ( ( page_position - last_position ) / 300 );
				cta_opacity = cta_opacity - ( ( page_position - last_position ) / 200 );
			} else {
				// scrolling up
				main_header_height = main_header_height + ( last_position - page_position );
				margin_top = margin_top + ( ( last_position - page_position ) / 1.85 );
				header_opacity = header_opacity + ( ( last_position - page_position ) / 300 );
				cta_opacity = cta_opacity + ( ( last_position - page_position ) / 200 );
			}
			last_position = page_position;

			if ( main_header_height < ( cob_headline_container_height - 200 ) ) {
				// Avoid overflow.
			} else {
				main_header.css( { 'height' : main_header_height } );
				cob_headline_container.css( { 'margin-top' : margin_top, 'opacity' : header_opacity } );
				page_cta.css( { 'opacity' : cta_opacity } );
			}
		});
	}
}(jQuery, window));