<?php

class WSU_COB_Events {
	public function __construct() {
		add_shortcode( 'cob_events', array( $this, 'display_events' ) );
	}

	public function display_events( $atts ) {
		$default_atts = array(
			'display' => 'full',
			'headline_wrap' => 'h3',
			'category' => '',
			'count' => 10,
		);
		$atts = shortcode_atts( $default_atts, $atts );

		if ( ! in_array( $atts['headline_wrap'], array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) ) ) {
			$atts['headline_wrap'] = 'h3';
		}

		$args = array( 'post_type' => 'tribe_events', 'posts_per_page' => absint( $atts['count'] ) );

		if ( '' !== $atts['category'] ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'tribe_events_cat',
					'field' => 'slug',
					'terms' => $atts['category'],
				),
			);
		}

		$events = new WP_Query( $args );

		if ( 'headlines' === $atts['display'] ) {
			ob_start();

			echo '<ul class="events-headlines">';
			while ( $events->have_posts() ) {
				$events->the_post();
				$date = tribe_get_start_date( get_the_ID(), false, 'U' );
				echo '<li><time datetime="' . date( 'Y-m-d', $date ) .'">' . date( 'M&\\nb\\sp;j', $date ) . '</time> <a href="' . get_the_permalink( get_the_ID() ) . '">' . get_the_title() . '</a></li>';
			}
			echo '</ul>';

			$content = ob_get_contents();
			ob_end_clean();

		} elseif ( 'sidebar' === $atts['display'] ) {
			ob_start();

			echo '<div class="cob-events-sidebar">';
			while ( $events->have_posts() ) {
				$events->the_post();
				echo '<div class="cob-sidebar-event">';
				echo '<' . $atts['headline_wrap'] . '><a href="' . get_the_permalink( get_the_ID() ) . '">' . get_the_title() . '</a></' . $atts['headline_wrap'] . '>';
				echo tribe_events_event_schedule_details();
				echo '<p>' . get_the_excerpt() . '</p>';
				echo '</div>';
			}
			echo '</div>';

			$content = ob_get_contents();
			ob_end_clean();
		} else {
			ob_start();
			?>
			<div class="tribe-events-loop vcalendar">

				<?php while ( $events->have_posts() ) : $events->the_post(); ?>

					<!-- Month / Year Headers -->
					<?php tribe_events_list_the_date_headers(); ?>

					<!-- Event  -->
					<div id="post-<?php get_the_ID() ?>" class="<?php tribe_events_event_classes() ?>">
						<?php tribe_get_template_part( 'list/single', 'event' ) ?>
					</div><!-- .hentry .vevent -->

				<?php endwhile; ?>

			</div><!-- .tribe-events-loop -->
			<?php
			$content = ob_get_contents();
			ob_end_clean();
		}

		wp_reset_query();

		return $content;
	}
}
new WSU_COB_Events();