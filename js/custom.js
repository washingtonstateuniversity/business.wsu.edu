(function($, window){
	if ( 0 === $('.home' ).length ) {
		// Thanks to a good write-up on approach from https://medium.com/@TonyJing/medium-style-header-aa738696c6ac
		var scroll_top = $(window).scrollTop();
		var cob_headline_container = $('.cob-headline-container');

		// Background images are 792px high, calculate our headroom.
		var head_room = 792 - $('.main-header' ).height();

		// The headline container starts with no margin property.
		var margin_top = 0;

		$(window).on( 'scroll', function() {
			var scroll_amount = $( this ).scrollTop();
			var delta = scroll_amount - scroll_top;

			margin_top += ( delta / 4.5 );

			cob_headline_container.css( { 'margin-top' : margin_top, 'opacity' : "-=" + delta / 700 } );
			scroll_top = scroll_amount;

			// If we manage to scroll with momentum, make sure opacity stays sane.
			if ( 0 > scroll_amount ) {
				cob_headline_container.css( 'opacity', '1' );
			}

			// If the background image, at 792px hits the bottom of the main header container, stop margining.
			if ( head_room < margin_top ) {
				cob_headline_container.css( 'margin-top', head_room );
			}
		});
	}
}(jQuery, window));