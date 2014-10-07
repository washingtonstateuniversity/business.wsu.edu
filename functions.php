<?php

// Include our color palette selection tool.
include_once( __DIR__ . '/includes/plugin-color-palette.php' );

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