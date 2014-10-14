(function($){
	if ( $('.home' ).length > 0 ) {
		$('.wsuwp-json-content' ).each(function(){
			var container = $(this);
			var source_data_obj = $(this ).data('source');
			var source_data = window[source_data_obj];

			container.append( '<ul>' );
			for ( var item in source_data ) {
				container.append( '<li><a href="' + source_data[item ].link + '">' + source_data[item].title + '</a></li>' );
			}
			container.append( '</ul>' );
		});
	} else {
		$('.wsuwp-json-content' ).each(function(){
			var container = $(this);
			var source_data_obj = $(this ).data('source');
			var source_data = window[source_data_obj];

			for ( var item in source_data ) {
				var cob_excerpt = source_data[item ].content.split('<!--more-->');
				container.append( '<div><h2><a href="' + source_data[item ].link + '">' + source_data[item].title + '</a></h2>' + cob_excerpt[0] + '</div>');
			}
		});
	}

}(jQuery));