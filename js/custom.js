/** MIT Licensed FontDetect - https://github.com/JenniferSimonds/FontDetect */
FontDetect=function(){function e(){if(!n){n=!0;var e=document.body,t=document.body.firstChild,i=document.createElement("div");i.id="fontdetectHelper",r=document.createElement("span"),r.innerText="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",i.appendChild(r),e.insertBefore(i,t),i.style.position="absolute",i.style.visibility="hidden",i.style.top="-200px",i.style.left="-100000px",i.style.width="100000px",i.style.height="200px",i.style.fontSize="100px"}}function t(e,t){return e instanceof Element?window.getComputedStyle(e).getPropertyValue(t):window.jQuery?$(e).css(t):""}var n=!1,i=["serif","sans-serif","monospace","cursive","fantasy"],r=null;return{onFontLoaded:function(t,i,r,o){if(t){var s=o&&o.msInterval?o.msInterval:100,a=o&&o.msTimeout?o.msTimeout:2e3;if(i||r){if(n||e(),this.isFontLoaded(t))return void(i&&i(t));var l=this,f=(new Date).getTime(),d=setInterval(function(){if(l.isFontLoaded(t))return clearInterval(d),void i(t);var e=(new Date).getTime();e-f>a&&(clearInterval(d),r&&r(t))},s)}}},isFontLoaded:function(t){var o=0,s=0;n||e();for(var a=0;a<i.length;++a){if(r.style.fontFamily='"'+t+'",'+i[a],o=r.offsetWidth,a>0&&o!=s)return!1;s=o}return!0},whichFont:function(e){for(var n=t(e,"font-family"),r=n.split(","),o=r.shift();o;){o=o.replace(/^\s*['"]?\s*([^'"]*)\s*['"]?\s*$/,"$1");for(var s=0;s<i.length;s++)if(o==i[s])return o;if(this.isFontLoaded(o))return o;o=r.shift()}return null}}}();

(function($, FontDetect, window){
	var view_data = {
		is_home : 0
	};

	/**
	 * Determine if this page view has the `home` class assigned to body.
	 *
	 * @returns bool True if home page, false if not.
	 */
	var is_home = function() {
		if ( 0 === view_data.is_home ) {
			view_data.is_home = ( $('.home' ).length > 0 );
		}

		return view_data.is_home;
	};

	var is_ios = function() {
		return ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
	};

	var setup_header = function() {
		view_data.last_position = $(window ).scrollTop();
		view_data.headline_container = $('.cob-headline-container');
		view_data.headline_container_height = view_data.headline_container.height();
		view_data.main_header = $('.main-header');
		view_data.main_header_height = view_data.main_header.height();
		view_data.call_to_action = $('.page-call-to-action');
	};

	var setup_scroll = function() {
		view_data.main_header.css( { 'height' : view_data.main_header_height } );

		// Main headline
		var page_position = $(this ).scrollTop();

		var background_position = 0 + ( page_position / 4.5 );
		var margin_top = 100 + ( page_position / 6.25 );
		var header_opacity = 1 - ( page_position / 700 );
		var cta_opacity = 1 - ( page_position / 300 );

		view_data.main_header.css( { 'background-position-y' : background_position } );
		view_data.headline_container.css( { 'margin-top' : margin_top, 'opacity' : header_opacity } );
		view_data.call_to_action.css( { 'opacity' : cta_opacity } );

		$(window).on( 'scroll', function() {
			var page_position = $(this).scrollTop();

			if ( page_position > view_data.last_position ) {
				// scrolling down
				background_position = background_position + ( ( page_position - view_data.last_position ) / 4.5 );
				margin_top = margin_top + ( ( page_position - view_data.last_position ) / 6.25 );
				header_opacity = header_opacity - ( ( page_position - view_data.last_position ) / 700 );
				cta_opacity = cta_opacity - ( ( page_position - view_data.last_position ) / 300 );
			} else {
				// scrolling up
				background_position = background_position - ( ( view_data.last_position - page_position ) / 4.5 );
				margin_top = margin_top - ( ( view_data.last_position - page_position ) / 6.25 );
				header_opacity = header_opacity + ( ( view_data.last_position - page_position ) / 700 );
				cta_opacity = cta_opacity + ( ( view_data.last_position - page_position ) / 300 );
			}
			view_data.last_position = page_position;

			view_data.main_header.css( { 'background-position-y' : background_position } );
			view_data.headline_container.css( { 'margin-top' : margin_top, 'opacity' : header_opacity } );
			view_data.call_to_action.css( { 'opacity' : cta_opacity } );
		});
	};

	var on_font_loaded = function() {
		setup_header();
		setup_scroll();
	};

	$(document).ready(function(){
		if ( ! is_home() && ! is_ios() && 0 !== $('.has-background-image' ).length ) {
			FontDetect.onFontLoaded( 'Open Sans Condensed', on_font_loaded, function() { console.log('did not load' ) }, {msTimeout: 3000});
		}
	});
}(jQuery, FontDetect, window));