<?php

class WSU_COB_Color_Palette {

	/**
	 * @var array List of color palettes available for pages.
	 */
	public $color_palettes = array(
		'default' => array( 'name' => 'Default (Orange)', 'hex' => '#f6861f' ),
		'green' => array( 'name' => 'Green', 'hex' => '#c6d02e' ),
		'blue-1' => array( 'name' => 'Blue (One)', 'hex' => '#00a5bd' ),
		'blue-2' => array( 'name' => 'Blue (Two)', 'hex' => '#82a9af' ),
		'blue-3' => array( 'name' => 'Blue (Three)', 'hex' => '#aec7cd' ),
		'yellow' => array( 'name' => 'Yellow', 'hex' => '#ffb81c' ),
		'beige-1' => array( 'name' => 'Beige (One)', 'hex' => '#ccc4a2' ),
		'beige-2' => array( 'name' => 'Beige (Two)', 'hex' => '#a9a387' ),
		'wazzu-1' => array( 'name' => 'Wazzu (One)', 'hex' => '#8d959a' ),
		'wazzu-2' => array( 'name' => 'Wazzu (Two)', 'hex' => '#5b6770' ),
	);

	/**
	 * @var string The meta key used to track a page's color palette.
	 */
	public $color_palette_meta_key = '_wsu_cob_color_palette';

	/**
	 * Setup hooks.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_filter( 'body_class', array( $this, 'add_body_class' ), 11 );
	}

	/**
	 * Configure the meta boxes to display for capturing palette.
	 *
	 * @param string $post_type The current post's post type.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( 'page' === $post_type ) {
			add_meta_box( 'wsu_cob_color_palette', 'Select Color Palette', array( $this, 'display_color_palette_meta_box' ), null, 'normal', 'default' );
		}
	}

	/**
	 * Display the meta box to capture color palette information.
	 *
	 * @param WP_Post $post
	 */
	public function display_color_palette_meta_box( $post ) {
		$current_palette = get_post_meta( $post->ID, $this->color_palette_meta_key, true );

		if ( ! array_key_exists( $current_palette, $this->color_palettes ) ) {
			$current_palette = 'default';
		}

		?>
		<ul class="cob-palettes">
		<?php

		foreach( $this->color_palettes as $key => $palette ) {
			if ( $current_palette === $key ) {
				$class = ' admin-palette-current';
			} else {
				$class = '';
			}
			echo '<li data-palette="' . esc_attr( $key ) . '" class="admin-palette-option' . $class . '" style="background-color: ' . esc_attr( $palette['hex'] ) . ';"></li>';
		}

		?>
		</ul>
		<input type="hidden" name="cob_wsu_palette" id="cob-wsu-palette" value="<?php echo esc_attr( $current_palette ); ?>" />
		<?php
	}

	/**
	 * Enqueue styles to be used for the display of taxonomy terms.
	 */
	public function admin_enqueue_scripts() {
		if ( 'page' === get_current_screen()->id ) {
			wp_enqueue_style( 'wsu-cob-palette-admin', get_stylesheet_directory_uri() . '/css/admin.css', array(), wsu_cob_script_version() );
			wp_enqueue_script( 'wsu-cob-palette-admin-js', get_stylesheet_directory_uri() . '/js/admin.js', array( 'jquery' ), wsu_cob_script_version() );
		}

	}

	/**
	 * Assign the selected color palette to the page.
	 *
	 * @param int     $post_id The ID of the post being saved.
	 * @param WP_Post $post    The full post object being saved.
	 */
	public function save_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'page' !== $post->post_type ) {
			return;
		}

		if ( 'auto-draft' === $post->post_status ) {
			return;
		}

		if ( ! isset( $_POST['cob_wsu_palette'] ) || ! array_key_exists( $_POST['cob_wsu_palette'], $this->color_palettes ) ) {
			return;
		}

		$new_palette = sanitize_key( $_POST['cob_wsu_palette'] );
		update_post_meta( $post_id, $this->color_palette_meta_key, $new_palette );
	}

	/**
	 * Assign a color palette to a page's view.
	 *
	 * @param array $classes List of classes to assign to this view's body element.
	 *
	 * @return array Modified list of classes.
	 */
	public function add_body_class( $classes ) {
		if ( is_singular( 'page' ) ) {
			$palette = get_post_meta( get_the_ID(), $this->color_palette_meta_key, true );
			if ( ! array_key_exists( $palette, $this->color_palettes ) ) {
				$palette = 'default';
			}

			// Only apply a master palette class to the body if no background image is present.
			if ( '' === spine_has_background_image() ) {
				$classes[] = 'cob-palette-' . $palette;
			}

			// Always apply a palette class to the body for text.
			$classes[] = 'cob-palette-text-' . $palette;
		} else {
			$classes[] = 'cob-palette-default';
			$classes[] = 'cob-palette-text-default';
		}

		return $classes;
	}

	public function get_color_palette( $post_id ) {
		return get_post_meta( $post_id, $this->color_palette_meta_key, true );
	}
}
$wsu_cob_color_palette = new WSU_COB_Color_Palette();

function cob_get_page_color_palette( $post_id = 0 ) {
	global $wsu_cob_color_palette;

	$post_id = absint( $post_id );

	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	return $wsu_cob_color_palette->get_color_palette( $post_id );
}