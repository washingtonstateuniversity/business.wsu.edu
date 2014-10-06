<?php

add_action( 'after_setup_theme', 'cob_theme_setup' );
/**
 * Setup functionality used by the theme.
 */
function cob_theme_setup() {
	register_nav_menu( 'cob-header', 'Header' );
}