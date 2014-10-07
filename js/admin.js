(function($){

	$(document ).ready(function() {
		$('.cob-palettes' ).on('click', '.admin-palette-option', function() {
			var selected_palette = $(this);
			$('#cob-wsu-palette' ).val( selected_palette.data('palette') );
			$('.admin-palette-current' ).removeClass('admin-palette-current');
			selected_palette.addClass( 'admin-palette-current');
		})
	} );
}(jQuery));