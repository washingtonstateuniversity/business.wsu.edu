/** MIT Licensed FontDetect - https://github.com/JenniferSimonds/FontDetect */
FontDetect=function(){function e(){if(!n){n=!0;var e=document.body,t=document.body.firstChild,i=document.createElement("div");i.id="fontdetectHelper",r=document.createElement("span"),r.innerText="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",i.appendChild(r),e.insertBefore(i,t),i.style.position="absolute",i.style.visibility="hidden",i.style.top="-200px",i.style.left="-100000px",i.style.width="100000px",i.style.height="200px",i.style.fontSize="100px"}}function t(e,t){return e instanceof Element?window.getComputedStyle(e).getPropertyValue(t):window.jQuery?$(e).css(t):""}var n=!1,i=["serif","sans-serif","monospace","cursive","fantasy"],r=null;return{onFontLoaded:function(t,i,r,o){if(t){var s=o&&o.msInterval?o.msInterval:100,a=o&&o.msTimeout?o.msTimeout:2e3;if(i||r){if(n||e(),this.isFontLoaded(t))return void(i&&i(t));var l=this,f=(new Date).getTime(),d=setInterval(function(){if(l.isFontLoaded(t))return clearInterval(d),void i(t);var e=(new Date).getTime();e-f>a&&(clearInterval(d),r&&r(t))},s)}}},isFontLoaded:function(t){var o=0,s=0;n||e();for(var a=0;a<i.length;++a){if(r.style.fontFamily='"'+t+'",'+i[a],o=r.offsetWidth,a>0&&o!=s)return!1;s=o}return!0},whichFont:function(e){for(var n=t(e,"font-family"),r=n.split(","),o=r.shift();o;){o=o.replace(/^\s*['"]?\s*([^'"]*)\s*['"]?\s*$/,"$1");for(var s=0;s<i.length;s++)if(o==i[s])return o;if(this.isFontLoaded(o))return o;o=r.shift()}return null}}}();

/** MIT Licensed Fluidbox - https://github.com/terrymun/Fluidbox/ */
!function(a,b){var c=function(a,b,c){var d;return function(){function g(){c||a.apply(e,f),d=null}var e=this,f=arguments;d?clearTimeout(d):c&&a.apply(e,f),d=setTimeout(g,b||100)}};jQuery.fn[b]=function(a){return a?this.bind("resize",c(a)):this.trigger(b)}}(jQuery,"smartresize"),function(a){a.fn.fluidbox=function(b){var c=a.extend(!0,{viewportFill:.95,debounceResize:!0,stackIndex:1e3,stackIndexDelta:10,closeTrigger:[{selector:".fluidbox-overlay",event:"click"},{selector:"document",event:"keyup",keyCode:27}]},b);c.stackIndex<c.stackIndexDelta&&(c.stackIndexDelta=c.stackIndex),$fbOverlay=a("<div />",{"class":"fluidbox-overlay",css:{"z-index":c.stackIndex}});var f,d=this,e=a(window),g=function(){a(".fluidbox-opened").trigger("click")},h=function(a){var b=a.find("img"),d=a.find(".fluidbox-ghost"),g=a.find(".fluidbox-wrap"),h=a.data(),i=0,j=0;b.data().imgRatio=h.natWidth/h.natHeight;var k,l,m;if(f>b.data().imgRatio)i=h.natHeight<e.height()*c.viewportFill?h.natHeight:e.height()*c.viewportFill,h.imgScale=i/b.height(),h.imgScaleY=h.imgScale,k=b.height()*h.imgScaleY,l=k/h.natHeight,m=h.natWidth*l/b.width(),h.imgScaleX=m;else{j=h.natWidth<e.width()*c.viewportFill?h.natWidth:e.width()*c.viewportFill,h.imgScale=j/b.width(),h.imgScaleX=h.imgScale;var n=b.width()*h.imgScaleX,l=n/h.natWidth,m=h.natHeight*l/b.height();h.imgScaleY=m}var o=e.scrollTop()-b.offset().top+.5*b.data("imgHeight")*(b.data("imgScale")-1)+.5*(e.height()-b.data("imgHeight")*b.data("imgScale")),p=.5*b.data("imgWidth")*(b.data("imgScale")-1)+.5*(e.width()-b.data("imgWidth")*b.data("imgScale"))-b.offset().left,q=parseInt(1e3*h.imgScaleX)/1e3+","+parseInt(1e3*h.imgScaleY)/1e3;d.css({transform:"translate("+parseInt(10*p)/10+"px,"+parseInt(10*o)/10+"px) scale("+q+")",top:b.offset().top-g.offset().top,left:b.offset().left-g.offset().left})},i=function(){d.each(function(){j(a(this))})},j=function(a){function i(){h.imgWidth=b.width(),h.imgHeight=b.height(),h.imgRatio=b.width()/b.height(),d.css({width:b.width(),height:b.height(),top:b.offset().top-g.offset().top+parseInt(b.css("borderTopWidth"))+parseInt(b.css("paddingTop")),left:b.offset().left-g.offset().left+parseInt(b.css("borderLeftWidth"))+parseInt(b.css("paddingLeft"))}),h.imgScale=f>h.imgRatio?e.height()*c.viewportFill/b.height():e.width()*c.viewportFill/b.width()}if(f=e.width()/e.height(),a.hasClass("fluidbox")){var b=a.find("img"),d=a.find(".fluidbox-ghost"),g=a.find(".fluidbox-wrap"),h=b.data();i(),b.load(i)}},k=function(b){if(a(this).hasClass("fluidbox")){var d=a(this),e=a(this).find("img"),f=a(this).find(".fluidbox-ghost"),g=a(this).find(".fluidbox-wrap"),i={};0!==a(this).data("fluidbox-state")&&a(this).data("fluidbox-state")?(d.data("fluidbox-state",0).removeClass("fluidbox-opened").addClass("fluidbox-closed"),i.open&&window.clearTimeout(i.open),i.close=window.setTimeout(function(){a(".fluidbox-overlay").remove(),g.css({"z-index":c.stackIndex-c.stackIndexDelta})},10),a(".fluidbox-overlay").css({opacity:0}),f.css({transform:"translate(0,0) scale(1)",opacity:0,top:e.offset().top-g.offset().top+parseInt(e.css("borderTopWidth"))+parseInt(e.css("paddingTop")),left:e.offset().left-g.offset().left+parseInt(e.css("borderLeftWidth"))+parseInt(e.css("paddingLeft"))}),e.css({opacity:1})):a("<img />",{src:e.attr("src")}).load(function(){a("<img />",{src:d.attr("href")}).load(function(){d.data("natWidth",a(this)[0].naturalWidth).data("natHeight",a(this)[0].naturalHeight),d.append($fbOverlay).data("fluidbox-state",1).removeClass("fluidbox-closed").addClass("fluidbox-opened"),i.close&&window.clearTimeout(i.close),i.open=window.setTimeout(function(){a(".fluidbox-overlay").css({opacity:1})},10),a(".fluidbox-wrap").css({zIndex:c.stackIndex-c.stackIndexDelta-1}),g.css({"z-index":c.stackIndex+c.stackIndexDelta}),f.css({"background-image":"url("+e.attr("src")+")",opacity:1}),e.css({opacity:0}),f.css({"background-image":"url("+d.attr("href")+")"}),h(d)})}),b.preventDefault()}};c.closeTrigger&&a.each(c.closeTrigger,function(b){var d=c.closeTrigger[b];"window"!=d.selector?"document"==d.selector?d.keyCode?a(document).on(d.event,function(a){a.keyCode==d.keyCode&&g()}):a(document).on(d.event,g):a(document).on(d.event,c.closeTrigger[b].selector,g):e.on(d.event,g)}),d.each(function(){if(a(this).is("a")&&1===a(this).children().length&&a(this).children().is("img")&&"none"!==a(this).css("display")&&"none"!==a(this).parents().css("display")){var d=a("<div />",{"class":"fluidbox-wrap",css:{"z-index":c.stackIndex-c.stackIndexDelta}}),e=a(this);e.addClass("fluidbox").wrapInner(d).find("img").css({opacity:1}).after('<div class="fluidbox-ghost" />').each(function(){var b=a(this);b.width()>0&&b.height()>0?(j(e),e.click(k)):b.load(function(){j(e),e.click(k)})})}});var l=function(){i();var b=a("a.fluidbox.fluidbox-opened");b.length>0&&h(b)};return c.debounceResize?a(window).smartresize(l):a(window).resize(l),d}}(jQuery);

(function($, FontDetect, window){

	var view_data = {};

	var incrementor = {
		background: 4.5,
		margin: 6.25,
		opacity: 500,
		cta_opacity: 200
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

		var background_position = 0 + ( page_position / incrementor.background );
		var margin_top = 100 + ( page_position / incrementor.margin );
		var header_opacity = 1 - ( page_position / incrementor.opacity );
		var cta_opacity = 1 - ( page_position / incrementor.cta_opacity );

		view_data.main_header.css( { 'background-position-y' : background_position } );
		view_data.headline_container.css( { 'margin-top' : margin_top, 'opacity' : header_opacity } );
		view_data.call_to_action.css( { 'opacity' : cta_opacity } );

		$(window).on( 'scroll', function() {
			var page_position = $(this).scrollTop();

			if ( page_position > view_data.main_header_height ) {
				return;
			}

			if ( page_position > view_data.last_position ) {
				// scrolling down
				background_position = background_position + ( ( page_position - view_data.last_position ) / incrementor.background );
				margin_top = margin_top + ( ( page_position - view_data.last_position ) / incrementor.margin );
				header_opacity = header_opacity - ( ( page_position - view_data.last_position ) / incrementor.opacity );
				cta_opacity = cta_opacity - ( ( page_position - view_data.last_position ) / incrementor.cta_opacity );
			} else {
				// scrolling up
				background_position = background_position - ( ( view_data.last_position - page_position ) / incrementor.background );
				margin_top = margin_top - ( ( view_data.last_position - page_position ) / incrementor.margin );
				header_opacity = header_opacity + ( ( view_data.last_position - page_position ) / incrementor.opacity );
				cta_opacity = cta_opacity + ( ( view_data.last_position - page_position ) / incrementor.cta_opacity );
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
		if ( ! is_ios() && 0 === $('.home' ).length && 0 !== $('.has-background-image' ).length ) {
			FontDetect.onFontLoaded( 'Open Sans Condensed', on_font_loaded, function() { console.log('did not load' ) }, {msTimeout: 3000});
		}

		// Add Fluidbox to any gallery images
		$(".gallery-item a").fluidbox({ stackIndex: 99999 });
	});
}(jQuery, FontDetect, window));