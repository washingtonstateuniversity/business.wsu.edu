<?php

// Include our color palette selection tool.
include_once( __DIR__ . '/includes/plugin-color-palette.php' );

// Allow for subtitles and calls to action.
include_once( __DIR__ . '/includes/plugin-headlines.php' );

include_once( __DIR__ . '/includes/shortcode-events.php' );

/**
 * Provide a cache breaking script version for the theme.
 *
 * @return string Current script version
 */
function wsu_cob_script_version() {
	return spine_get_script_version() . '0.8.0';
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
	global $wp_query;

	if ( in_array( 'current_page_item', $item_classes ) || in_array( 'current_page_parent', $item_classes ) ) {
		$item_classes[] = 'current';
	}

	if ( is_singular() && get_the_ID() == $page->ID ) {
		$item_classes[] = 'dogeared';
	}

	if ( isset( $wp_query->tribe_is_event ) && true === $wp_query->tribe_is_event ) {
		if ( isset( $page->url ) && home_url( '/news-events/calendar/' ) === $page->url ) {
			$item_classes[] = 'current dogeared';
		}
		if ( isset( $page->url ) && home_url( '/news-events/' ) === $page->url ) {
			$item_classes[] = 'current';
		}
	}

	return $item_classes;
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

add_action( 'wp_enqueue_scripts', 'cob_enqueue_scripts' );
/**
 * Enqueue any custom scripting provided by the child theme.
 */
function cob_enqueue_scripts() {
	if ( is_singular() ) {
		wp_enqueue_script( 'cob-custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery' ), wsu_cob_script_version(), true );
	}
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

add_filter( 'post_type_link', 'cob_filter_post_type_link', 10, 2 );
/**
 * Filter the post type link to show a single event under news-events/calendar/event/
 *
 * @param string  $post_link
 * @param WP_Post $post
 *
 * @return string
 */
function cob_filter_post_type_link( $post_link, $post ) {
	if ( 'tribe_events' === $post->post_type ) {
		$post_link = str_replace( home_url( '/event/' ), home_url( '/news-events/calendar/event/' ), $post_link );
	}

	return $post_link;
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
		} elseif ( 0 === strpos( $key, '(.*)events/' ) ) {
			$new_key = str_replace( '(.*)events/', '(.*)news-events/calendar/', $key );
		} elseif ( 0 === strpos( $key, 'event/' ) ) {
			$new_key = str_replace( 'event/', 'news-events/calendar/event/', $key );
		} elseif ( 0 === strpos( $key, 'organizer/' ) ) {
			$new_key = str_replace( 'organizer/', 'news-events/calendar/organizer/', $key );
		} elseif ( 0 === strpos( $key, 'venue/' ) ) {
			$new_key = str_replace( $key, 'news-events/calendar/venue/', $key );
		} else {
			$new_key = $key;
		}
		unset( $wp_rewrite->rules[ $key ] );
		$wp_rewrite->rules[ $new_key ] = $rule;
	}
	return $wp_rewrite;
}

add_action( 'parse_query', 'cob_remove_events_from_edit', 51 );
/**
 * The Events Calendar makes the decision that tribe_events items should appear in every
 * taxonomy query via a `parse_query` action. This causes some issues on the edit screen
 * in WordPress, as a string is expected for the post_type data. This resets the query back
 * to the originally requested post type.
 *
 * @param $query
 */
function cob_remove_events_from_edit( $query ) {
	if ( ! is_admin() || ! get_current_screen() ) {
		return;
	}

	if ( 'edit' === get_current_screen()->base && 'tribe_events' !== get_current_screen()->post_type ) {
		$query->set( 'post_type', get_current_screen()->post_type );
	}

	return;
}

add_filter( 'bu_navigation_filter_pages', 'cob_filter_page_urls', 11 );
/**
 * Look for pages that are intended to be section labels rather than
 * places where content exists.
 *
 * @param $pages
 *
 * @return array
 */
function cob_filter_page_urls( $pages ) {
	global $wpdb;

	$filtered = array();

	if ( is_array( $pages ) && count( $pages ) > 0 ) {

		$ids = array_map( 'absint', array_keys( $pages ) );
		$query = $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '%s' AND post_id IN (" .  implode( ',', $ids ) . ") and meta_value = '%s'", '_wp_page_template', 'template-section-label.php' );
		$labels = $wpdb->get_results( $query, OBJECT_K );

		if ( is_array( $labels ) && count( $labels ) > 0 ) {
			foreach ( $pages as $page ) {
				if ( array_key_exists( $page->ID, $labels ) ) {
					$page->url = '#';
				}
				$filtered[ $page->ID ] = $page;
			}
		} else {
			$filtered = $pages;
		}
	}

	return $filtered;
}

add_filter( 'bu_navigation_filter_anchor_attrs', 'cob_bu_navigation_filter_anchor_attrs', 10, 1 );
/**
 * Filter anchor attributes in generate page menu to remove the title so that our default Overview
 * behavior remains.
 *
 * @param array $attrs List of attributes to output as part of the anchor.
 *
 * @return array
 */
function cob_bu_navigation_filter_anchor_attrs( $attrs ) {
	$attrs['title'] = '';

	return $attrs;
}