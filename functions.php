<?php

// Include our color palette selection tool.
include_once( __DIR__ . '/includes/plugin-color-palette.php' );

// Allow for subtitles and calls to action.
include_once( __DIR__ . '/includes/plugin-headlines.php' );

/**
 * Provide a cache breaking script version for the theme.
 *
 * @return string Current script version
 */
function wsu_cob_script_version() {
	return spine_get_script_version() . '0.5.1';
}

add_action( 'after_setup_theme', 'cob_theme_setup' );
/**
 * Setup functionality used by the theme.
 */
function cob_theme_setup() {
	// Register the nav menu we're using in the header.
	register_nav_menu( 'cob-header', 'Header' );

	// Add support for the BU Navigation plugin.
	add_theme_support( 'bu-navigation-primary' );
	remove_theme_support( 'bu-navigation-widget' );
}

add_filter( 'bu_navigation_filter_item_attrs', 'cob_bu_navigation_filter_item_attrs', 10, 2 );
/**
 * Filter the list item classes to manually add current and dogeared when necessary.
 *
 * @param array   $item_classes List of classes assigned to the list item.
 * @param WP_Post $page         Post object for the current page.
 *
 * @return array
 */
function cob_bu_navigation_filter_item_attrs( $item_classes, $page ) {
	if ( in_array( 'current_page_item', $item_classes ) || in_array( 'current_page_parent', $item_classes ) ) {
		$item_classes[] = 'current';
	}

	if ( is_singular() && get_the_ID() == $page->ID ) {
		$item_classes[] = 'dogeared';
	}

	return $item_classes;
}

add_filter( 'bu_navigation_filter_anchor_attrs', 'cob_bu_navigation_filter_anchor_attrs', 10, 2 );
/**
 * Filter anchor attributes in generate page menu to remove the title so that our default Overview
 * behavior remains.
 *
 * If a label is assigned to the page, it will be displayed as the section label. The actual page
 * title will be used as the Overview replacement.
 *
 * @param array   $attrs List of attributes to output as part of the anchor.
 * @param WP_Post $page  Post object for the current page.
 *
 * @return mixed
 */
function cob_bu_navigation_filter_anchor_attrs( $attrs, $page ) {
	if ( trim( $page->post_title ) !== $attrs['title'] ) {
		$attrs['title'] = $page->post_title;
	} else {
		$attrs['title'] = '';
	}

	return $attrs;
}

add_filter( 'wsuwp_content_syndicate_json', 'cob_content_syndication_display', 10, 2 );
/**
 * Add a container to the output of the JSON data.
 *
 * @param $content
 * @param $atts
 *
 * @return string
 */
function cob_content_syndication_display( $content, $atts ) {
	wp_enqueue_script( 'cob-display-json-feeds', get_stylesheet_directory_uri() . '/js/display-json-feeds.js', array( 'jquery' ), wsu_cob_script_version(), true );

	return $content .= '<div class="wsuwp-json-content" data-source="' . esc_js( $atts['object'] ) . '"></div>';
}

add_filter( 'tribe_events_getLink', 'cob_modify_events_url', 10, 2 );
/**
 * Replace generated URLs from The Events Calendar to appear under a second level rather
 * than the single level forced by the plugin.
 *
 * @param string $event_url URL being output.
 * @param string $type      Type of URL being output - home, list, month, day, single, etc...
 *
 * @return string URL to output.
 */
function cob_modify_events_url( $event_url, $type ) {
	if ( 'single' === $type ) {
		$event_url = str_replace( home_url( '/event/' ), home_url( '/news-events/calendar/event/' ), $event_url );
	} else {
		$event_url = str_replace( home_url( '/events/' ), home_url( '/news-events/calendar/' ), $event_url );
	}

	return $event_url;
}

add_filter( 'generate_rewrite_rules', 'cob_filter_rewrite_rules', 11 );
/**
 * Replace rewrite rules provided by The Events Calendar with our own to support
 * a nested URL structure.
 *
 * @param WP_Rewrite $wp_rewrite
 *
 * @return WP_Rewrite
 */
function cob_filter_rewrite_rules( $wp_rewrite ) {
	foreach( $wp_rewrite->rules as $key => $rule ) {
		if ( 0 === strpos( $key, 'events/' ) ) {
			$new_key = str_replace( 'events/', 'news-events/calendar/', $key );
		} elseif( 0 === strpos( $key, '(.*)events/' ) ) {
			$new_key = str_replace( '(.*)events/', '(.*)news-events/calendar/', $key );
		} elseif( 0 === strpos( $key, 'event/' ) ) {
			$new_key = str_replace( 'event/', 'news-events/calendar/event/', $key );
		} else {
			$new_key = $key;
		}
		unset( $wp_rewrite->rules[ $key ] );
		$wp_rewrite->rules[ $new_key ] = $rule;
	}
	return $wp_rewrite;
}