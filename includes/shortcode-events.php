<?php

class WSU_COB_Events {
	public function __construct() {
		add_shortcode( 'cob_events', array( $this, 'display_events' ) );
	}

	public function display_events( $atts ) {
		$default_atts = array(
			'display' => 'full',
			'count' => 10,
		);
		$atts = shortcode_atts( $default_atts, $atts );

		$events = tribe_get_events( array( 'posts_per_page' => absint( $atts['count'] ) ) );

		if ( 'headlines' === $atts['display'] ) {
			ob_start();
			echo '<ul class="events-headlines">';
			foreach ( $events as $event ) {
				setup_postdata( $event );
				echo '<li><a href="' . get_the_permalink( $event->post_id ) . '">' . $event->post_title . '</a></li>';
				wp_reset_postdata();
			}
			echo '</ul>';
			$content = ob_get_contents();
			ob_end_clean();

		} else {
			$content = '';
		}

		return $content;
	}
}
new WSU_COB_Events();