<?php

class WSU_COB_Headlines {

	/**
	 * @var string Meta key for storing headline.
	 */
	public $headline_meta_key = '_wsu_cob_headline';

	/**
	 * @var string Meta key for storing subtitle.
	 */
	public $subtitle_meta_key = '_wsu_cob_subtitle';

	/**
	 * @var string Meta key for storing the call to action.
	 */
	public $call_to_action_meta_key = '_wsu_cob_call_to_action';

	/**
	 * @var string Meta key for storing the call to action's URL.
	 */
	public $call_to_action_url_meta_key = '_wsu_cob_call_to_action_url';

	/**
	 * Setup the hooks.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_shortcode( 'cob_home_headline', array( $this, 'display_home_headline' ) );
	}

	/**
	 * Add metaboxes for subtitle and call to action to page and post edit screens.
	 *
	 * @param string $post_type Current post type screen being displayed.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( ! in_array( $post_type, array( 'page', 'post' ) ) ) {
			return;
		}

		add_meta_box( 'wsu_cob_headlines', 'Page Headlines', array( $this, 'display_headlines_metabox' ), null, 'normal', 'high' );
	}

	/**
	 * Display the metabox used to capture additional headlines for a post or page.
	 *
	 * @param WP_Post $post
	 */
	public function display_headlines_metabox( $post ) {
		$headline = get_post_meta( $post->ID, $this->headline_meta_key, true );
		$subtitle = get_post_meta( $post->ID, $this->subtitle_meta_key, true );
		$call_to_action = get_post_meta( $post->ID, $this->call_to_action_meta_key, true );
		$call_to_action_url = get_post_meta( $post->ID, $this->call_to_action_url_meta_key, true );

		wp_nonce_field( 'cob-headlines-nonce', '_cob_headlines_nonce' );
		?>
		<label for="cob-page-headline">Headline:</label>
		<input type="text" class="widefat" id="cob-page-headline" name="cob_page_headline" value="<?php echo esc_attr( $headline ); ?>" />
		<p class="description">Primary headline to be used for the page.</p>

		<label for="cob-subtitle">Subtitle:</label>
		<input type="text" class="widefat" id="cob-subtitle" name="cob_subtitle" value="<?php echo esc_attr( $subtitle ); ?>" />
		<p class="description">Subtitle to be used on various views throughout the theme.</p>

		<label for="cob-cta">Call to Action:</label>
		<input type="text" class="widefat" id="cob-cta" name="cob_call_to_action" value="<?php echo esc_attr( $call_to_action ); ?>" />
		<p class="description">Call to action text for use as a guide to this page.</p>

		<label for="cob-cta-url">Call to Action URL:</label>
		<input type="text" class="widefat" id="cob-cta-url" name="cob_call_to_action_url" value="<?php echo esc_attr( $call_to_action_url ); ?>" />
		<?php
	}

	/**
	 * Save the subtitle and call to action assigned to the post.
	 *
	 * @param int     $post_id ID of the post being saved.
	 * @param WP_Post $post    Post object of the post being saved.
	 */
	public function save_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! in_array( $post->post_type, array( 'page', 'post' ) ) ) {
			return;
		}

		if ( 'auto-draft' === $post->post_status ) {
			return;
		}

		if ( ! isset( $_POST['_cob_headlines_nonce'] ) || false === wp_verify_nonce( $_POST['_cob_headlines_nonce'], 'cob-headlines-nonce' ) ) {
			return;
		}

		if ( isset( $_POST['cob_call_to_action'] ) ) {
			update_post_meta( $post_id, $this->call_to_action_meta_key, wp_kses_post( $_POST['cob_call_to_action'] ) );
		}

		if ( isset( $_POST['cob_call_to_action_url'] ) && ! empty( trim( $_POST['cob_call_to_action_url'] ) ) ) {
			update_post_meta( $post_id, $this->call_to_action_url_meta_key, esc_url_raw( $_POST['cob_call_to_action_url'] ) );
		} elseif ( ! isset( $_POST['cob_call_to_action_url'] ) || empty( trim( $_POST['cob_call_to_action_url'] ) ) ) {
			delete_post_meta( $post_id, $this->call_to_action_url_meta_key );
		}

		if ( isset( $_POST['cob_subtitle'] ) ) {
			update_post_meta( $post_id, $this->subtitle_meta_key, wp_kses_post( $_POST['cob_subtitle'] ) );
		}

		if ( isset( $_POST['cob_page_headline'] ) ) {
			update_post_meta( $post_id, $this->headline_meta_key, strip_tags( $_POST['cob_page_headline'], '<br><span><em><strong>' ) );
		}
	}

	/**
	 * Display a content block, intended for the home page, that links through to the
	 * page it represents.
	 *
	 * @param array $atts List of attributes to apply to the shortcode.
	 *
	 * @return string HTML content to display.
	 */
	public function display_home_headline( $atts ) {
		$atts = shortcode_atts( array( 'id' => 0, 'headline' => '', 'subtitle' => '', 'palette' => '', 'link' => 'page', 'cta' => '' ), $atts );

		if ( ! isset( $atts['id'] ) || empty( absint( $atts['id'] ) ) ) {
			$post = false;
		} else {
			$post = get_post( absint( $atts['id'] ) );
		}

		if ( ! $post && empty( $atts['headline'] ) ) {
			$headline = '';
		} elseif ( '' !== $atts['headline'] ) {
			$headline = $atts['headline'];
		} else {
			$headline = $this->get_headline( $post->ID );
		}

		if ( ! $post && empty( $atts['subtitle'] ) ) {
			$subtitle = '';
		} elseif ( '' !== $atts['subtitle'] ) {
			$subtitle = $atts['subtitle'];
		} else {
			$subtitle = $this->get_subtitle( $post->ID );
		}

		if ( $post && class_exists( 'MultiPostThumbnails' ) ) {
			$background_image = MultiPostThumbnails::get_post_thumbnail_url( $post->post_type, 'background-image', $post->ID, 'spine-xlarge_size' );
		} else {
			$background_image = false;
		}

		if ( ! $post && empty( $atts['palette'] ) ) {
			$atts['palette'] = 'default';
		} elseif ( '' !== $atts['palette'] ) {
			$palette = $atts['palette'];
		} else {
			$palette = cob_get_page_color_palette( $post->ID );
		}

		if ( $background_image ) {
			$class = 'headline-has-background';
			$style = 'style="background-image: url(' . esc_url( $background_image ) .');"';
		} else {
			$class = 'cob-palette-block-' . $palette;
			$style = '';
		}

		if ( $post && 'page' === $atts['link'] ) {
			$page_url = get_the_permalink( $post->ID );
		} elseif ( 'none' === $atts['link'] || ( ! $post && 'page' === $atts['link'] ) ) {
			$page_url = false;
		} else {
			$page_url = $atts['link'];
		}

		if ( ! empty( $atts['cta'] ) ) {
			$call_to_action = '<div class="home-cta">' . sanitize_text_field( $atts['cta'] ) . '</div>';
		} else {
			$call_to_action = '';
		}

		$content = '';

		if ( $page_url ) {
			$content = '<a class="home-link-wrap cob-palette-text-' . $palette . '" href="' . esc_url( $page_url ) . '">';
		}

		$content .= '<div ' . $style . ' class="home-headline ' . $class . '"><div><h2>' . strip_tags( $headline, '<br><span><em><strong>' ) . '</h2><div class="home-subtitle">' . strip_tags( $subtitle, '<br><span><em><strong>' ) .  '</div>' . $call_to_action . '</div></div>';

		if ( $page_url ) {
			$content .= '</a>';
		}

		return $content;
	}

	/**
	 * Retrieve the assigned headline of a page.
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function get_headline( $post_id ) {
		return get_post_meta( $post_id, $this->headline_meta_key, true );
	}

	/**
	 * Retrieve the assigned subtitle of a page.
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function get_subtitle( $post_id ) {
		return get_post_meta( $post_id, $this->subtitle_meta_key, true );
	}

	/**
	 * Retrieve the assigned call to action of a page.
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function get_call_to_action( $post_id ) {
		return get_post_meta( $post_id, $this->call_to_action_meta_key, true );
	}

	/**
	 * Retrieve the assigned URL for the call to action of a page.
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function get_call_to_action_url( $post_id ) {
		return get_post_meta( $post_id, $this->call_to_action_url_meta_key, true );
	}
}
$wsu_cob_headlines = new WSU_COB_Headlines();

/**
 * Wrapper to retrieve an assigned page headline. Will fallback to the current page if
 * a post ID is not specified.
 *
 * @param int $post_id
 *
 * @return mixed
 */
function cob_get_page_headline( $post_id = 0 ) {
	global $wsu_cob_headlines;

	$post_id = absint( $post_id );

	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	return $wsu_cob_headlines->get_headline( $post_id );
}

/**
 * Wrapper to retrieve an assigned page subtitle. Will fallback to the current page if
 * a post ID is not specified.
 *
 * @param int $post_id
 *
 * @return mixed
 */
function cob_get_page_subtitle( $post_id = 0 ) {
	global $wsu_cob_headlines;

	$post_id = absint( $post_id );

	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	return $wsu_cob_headlines->get_subtitle( $post_id );
}

/**
 * Wrapper to retrieve an assigned page call to action. Will fallback to the current page
 * if a post ID is not specified.
 *
 * @param int $post_id
 *
 * @return mixed
 */
function cob_get_page_call_to_action( $post_id = 0 ) {
	global $wsu_cob_headlines;

	$post_id = absint( $post_id );

	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	return $wsu_cob_headlines->get_call_to_action( $post_id );
}

/**
 * Wrapper to retrieve an assigned URL for a page's call to action. Will fallback to the
 * current page if a post ID is not specified.
 *
 * @param int $post_id
 *
 * @return mixed
 */
function cob_get_page_call_to_action_url( $post_id = 0 ) {
	global $wsu_cob_headlines;

	$post_id = absint( $post_id );

	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	return $wsu_cob_headlines->get_call_to_action_url( $post_id );
}