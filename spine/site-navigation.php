<nav id="spine-sitenav" class="spine-sitenav">
	<?php
	$output_menu_script = false;

	$nav_site = get_site();
	if ( '/' !== $nav_site->path && '/dividend/' !== $nav_site->path ) {
		$switch_site = get_blog_details( array( 'domain' => $nav_site->domain, 'path' => '/' ) );
		switch_to_blog( $switch_site->blog_id );
		$output_menu_script = true;
	}

	if ( function_exists( 'bu_navigation_display_primary' ) ) {
		$bu_nav_args = array(
			'post_types'      => array( 'page' ), // post types to display
			'include_links'   => true, // whether or not to include BU Navigation links with pages
			'dive'            => true, // whether or not to display descendants
			'depth'           => 4, // how many levels of descendants to display
			'max_items'       => 99, // how many items to display per list
			'container_tag'   => 'ul', // HTML tag to use for menu container
			'container_id'    => '', // HTML ID attribute of menu container
			'container_class' => '', // HTML class attributes for menu container
			'item_tag'        => 'li', // HTML tag to use for individual menu items
			'identify_top'    => false, // If set to true, uses post name as HTML ID attribute for top level posts
			'whitelist_top'   => null, // optional string or array of post names to whitelist for top level
			'echo'            => 1 // whether to display immediately or return
		);
		remove_filter( 'bu_navigation_filter_pages', 'bu_navigation_filter_pages_ancestors' );
		bu_navigation_display_primary( $bu_nav_args );
		add_filter( 'bu_navigation_filter_pages', 'bu_navigation_filter_pages_ancestors' );
	} else {
		$spine_site_args = array(
			'theme_location'  => 'site',
			'menu'            => 'site',
			'container'       => false,
			'container_class' => false,
			'container_id'    => false,
			'menu_class'      => null,
			'menu_id'         => null,
			'items_wrap'      => '<ul>%3$s</ul>',
			'depth'           => 5,
		);
		wp_nav_menu( $spine_site_args );
	}

	if ( ms_is_switched() ) {
		restore_current_blog();
	}
	?>
</nav>

<?php if ( $output_menu_script ) : ?>
<script>
	(function($){
		$('document').ready( function() {
			var $spine_sitenav = $('#spine-sitenav'),
				$spine_current = $spine_sitenav.find('a[href="<?php echo esc_js( get_the_permalink() ); ?>"]');

			$spine_sitenav.find('.active').each(function(){ jQuery(this).removeClass('dogeared active current'); });
			$spine_current.addClass('active dogeared').parent().addClass('active current dogeared');
		});
	}(jQuery));
</script>
<?php endif;
