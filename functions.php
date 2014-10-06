<?php

add_action( 'after_setup_theme', 'cob_theme_setup' );
/**
 * Setup functionality used by the theme.
 */
function cob_theme_setup() {
	register_nav_menu( 'cob-header', 'Header' );

	register_nav_menu( 'cob-section-1', 'Site - Section 1' );
	register_nav_menu( 'cob-section-2', 'Site - Section 2' );
	register_nav_menu( 'cob-section-3', 'Site - Section 3' );
	register_nav_menu( 'cob-section-4', 'Site - Section 4' );
	register_nav_menu( 'cob-section-5', 'Site - Section 5' );
	register_nav_menu( 'cob-section-6', 'Site - Section 6' );
	register_nav_menu( 'cob-section-7', 'Site - Section 7' );
	register_nav_menu( 'cob-section-8', 'Site - Section 8' );
	register_nav_menu( 'cob-section-9', 'Site - Section 9' );
	register_nav_menu( 'cob-section-10', 'Site - Section 10' );
}