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
	 * Setup the hooks.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
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

		if ( isset( $_POST['cob_subtitle'] ) ) {
			update_post_meta( $post_id, $this->subtitle_meta_key, wp_kses_post( $_POST['cob_subtitle'] ) );
		}

		if ( isset( $_POST['cob_page_headline'] ) ) {
			update_post_meta( $post_id, $this->headline_meta_key, sanitize_text_field( $_POST['cob_page_headline'] ) );
		}
	}

	public function get_headline( $post_id ) {
		return get_post_meta( $post_id, $this->headline_meta_key, true );
	}

	public function get_subtitle( $post_id ) {
		return get_post_meta( $post_id, $this->subtitle_meta_key, true );
	}

	public function get_call_to_action( $post_id ) {
		return get_post_meta( $post_id, $this->call_to_action_meta_key, true );
	}
}
$wsu_cob_headlines = new WSU_COB_Headlines();

function cob_get_page_headline( $post_id = 0 ) {
	global $wsu_cob_headlines;

	$post_id = absint( $post_id );

	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	return $wsu_cob_headlines->get_headline( $post_id );
}

function cob_get_page_subtitle( $post_id = 0 ) {
	global $wsu_cob_headlines;

	$post_id = absint( $post_id );

	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	return $wsu_cob_headlines->get_subtitle( $post_id );
}

function cob_get_page_call_to_action( $post_id = 0 ) {
	global $wsu_cob_headlines;

	$post_id = absint( $post_id );

	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	return $wsu_cob_headlines->get_call_to_action( $post_id );
}