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
	return spine_get_script_version() . '1.0.18';
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
 * Filter the list item classes to manually add active when necessary.
 *
 * @param array   $item_classes List of classes assigned to the list item.
 * @param WP_Post $page         Post object for the current page.
 *
 * @return array
 */
function cob_bu_navigation_filter_item_attrs( $item_classes, $page ) {
	global $wp_query;

	if ( isset( $wp_query->tribe_is_event ) && true === $wp_query->tribe_is_event ) {
		if ( isset( $page->url ) && home_url( '/news-events/calendar/' ) === $page->url ) {
			$item_classes[] = 'active';
		}

		if ( isset( $page->url ) && home_url( '/news-events/' ) === $page->url ) {
			$item_classes[] = 'active';
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

	wp_enqueue_script( 'cob-remarketing', 'https://www.googleadservices.com/pagead/conversion.js', array(), false, true );
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
	} elseif ( 'tribe_venue' === $post->post_type ) {
		$post_link = str_replace( home_url( '/venue/' ), home_url( '/news-events/calendar/venue/' ), $post_link );
	} elseif ( 'tribe_organizer' === $post->post_type ) {
		$post_link = str_replace( home_url( '/organizer/' ), home_url( '/news-events/calendar/organizer/' ), $post_link );
	}

	return $post_link;
}

add_filter( 'term_link', 'cob_filter_term_link', 10 );
/**
 * Filter category links attached to events to match our slug.
 *
 * @param $term_link
 *
 * @return mixed
 */
function cob_filter_term_link( $term_link ) {
	$term_link = str_replace( home_url( '/events/category/' ), home_url( '/news-events/calendar/category/' ), $term_link );

	return $term_link;
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
		} elseif ( 0 === strpos( $key, '(?:events)' ) ) {
			$new_key = str_replace( '(?:events)', '(?:news-events/calendar)', $key );
		} else {
			$new_key = $key;
		}
		unset( $wp_rewrite->rules[ $key ] );
		$wp_rewrite->rules[ $new_key ] = $rule;
	}
	return $wp_rewrite;
}

add_filter( 'tribe_events_register_event_type_args', 'cob_filter_event_content_type' );
/**
 * Alter the default arguments for the event content type to support our URL structure.
 *
 * @param $args
 *
 * @return mixed
 */
function cob_filter_event_content_type( $args ) {
	$args['rewrite']['slug'] = '/news-events/calendar/event';
	$args['rewrite']['with_front'] = true;
	return $args;
}

add_filter( 'tribe_events_register_venue_type_args', 'cob_filter_venue_content_type' );
/**
 * Alter the default arguments for the venue content type to support our URL structure.
 *
 * @param $args
 *
 * @return mixed
 */
function cob_filter_venue_content_type( $args ) {
	$args['rewrite']['slug'] = '/news-events/calendar/venue';
	$args['rewrite']['with_front'] = true;
	return $args;
}

add_filter( 'tribe_events_register_organizer_type_args', 'cob_filter_organizer_content_type' );
/**
 * Alter the default arguments for the organizer content type to support our URL structure.
 *
 * @param $args
 *
 * @return mixed
 */
function cob_filter_organizer_content_type( $args ) {
	$args['rewrite']['slug'] = '/news-events/calendar/organizer';
	$args['rewrite']['with_front'] = true;
	return $args;
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
	if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
		return;
	}

	if ( 'edit' === get_current_screen()->base && 'tribe_events' !== get_current_screen()->post_type && $query->is_main_query() ) {
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

add_action( 'after_setup_theme', 'cob_remove_feed_links' );
/**
 * Remove extra feeds to comments and other areas that we aren't necessarily prepared for.
 */
function cob_remove_feed_links() {
	remove_action( 'wp_head', 'feed_links_extra', 3 );
}

add_filter( 'embed_oembed_html', 'embed_html', 99, 4 );
/**
 * Add responsive container to embeds.
 *
 * @param $html
 *
 * @return string
 */
function embed_html( $html ) {
    return '<div class="fluid-container">' . $html . '</div>';
}

add_filter( 'gform_field_validation', 'cob_validate_confirm_fields', 10, 4 );
/**
 * Provides a hacky method for adding a "confirmation" field immediately after a field with
 * a css class of "confirm-next". Very rudimentary.
 *
 * @param $result
 * @param $value
 * @param $form
 * @param $field
 *
 * @return mixed
 */
function cob_validate_confirm_fields( $result, $value, $form, $field ) {
	if ( isset( $field['cssClass'] ) && 'confirm-next' === $field['cssClass'] ) {
		$id = $field['id'] + 1;
		$next_value = $_POST['input_' . $id ];

		if ( $value !== $next_value ) {
			$result['is_valid'] = false;
			$result['message'] = 'This field should match the value in the confirmation field below.';
		}
	}
	return $result;
}

add_action( 'wp_footer', 'cob_remarketing_tag' );
/**
 * Setup variables for the enqueued remarketing script.
 */
function cob_remarketing_tag() {
	?>
	<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 1019899730;
		var google_custom_params = window.google_tag_params;
		var google_remarketing_only = true;
		/* ]]> */
	</script>
	<noscript><div style="display:inline;"><img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1019899730/?value=0&amp;guid=ON&amp;script=0"/></div></noscript>
	<?php
}

add_action( 'wp_head', 'cob_facebook_pixel', 99 );
/**
 * Add a Facebook tracking pixel.
 */
function cob_facebook_pixel() {
	?>
	<!-- Facebook Pixel Code -->
	<script>
	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
	n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
	document,'script','//connect.facebook.net/en_US/fbevents.js');

	fbq('init', '851335368294885');
	fbq('track', "PageView");

	</script>
	<noscript><img height="1" width="1" style="display:none"
	src="https://www.facebook.com/tr?id=851335368294885&ev=PageView&noscript=1"
	/></noscript>
	<!-- End Facebook Pixel Code -->
	<?php
}

add_filter( 'register_taxonomy_args', 'disable_university_taxonomy_archives', 12, 2 );
/**
 * Sets the `public` argument to `false` for University Taxonomies.
 *
 * @since 1.0.18
 *
 * @param array  $args     Arguments for registering a taxonomy.
 * @param string $taxonomy Taxonomy key.
 *
 * @return array
 */
function disable_university_taxonomy_archives( $args, $taxonomy ) {
	$university_taxonomies = array(
		'wsuwp_university_category',
		'wsuwp_university_location',
		'wsuwp_university_org',
	);

	if ( in_array( $taxonomy, $university_taxonomies, true ) ) {
		$args['public'] = false;
		$args['query_var'] = false;
	}

	return $args;
}


function ccb_add_local_menu_setting() {

	// register our setting
    $args = array(
        'type' => 'boolean', 
        'default' => false,
    );
    register_setting( 
        'reading', // option group "reading", default WP group
        'ccb_use_local_menu', // option name
        $args 
    );

	// add our new setting
    add_settings_field(
        'ccb_use_local_menu', // ID
        'Use Local Menu', // Title
        'ccb_add_local_menu_render', // Callback
        'reading', // page
        'default', // section
    );

}

function ccb_add_local_menu_render( $args ) {

	$use_local = get_option('ccb_use_local_menu', false );

    echo '<input type="checkbox" id="ccb_use_local_menu" name="ccb_use_local_menu"';

	if ( $use_local ) {

		echo ' checked="checked"';
	}
	
	echo ' />';
   
}


/**
 * Register and define the local menu settings
 */
add_action('admin_init', 'ccb_add_local_menu_setting');
