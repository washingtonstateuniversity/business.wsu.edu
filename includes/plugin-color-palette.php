<?php

class WSU_COB_Color_Palette {

	/**
	 * @var array List of color palettes available for pages.
	 */
	public $color_palettes = array(
		'default' => array( 'name' => 'Default', 'hex' => '#f6861f' ),
		'yellow' => array( 'name' => 'Yellow', 'hex' => '#ffb81c' ),
		'blue' => array( 'name' => 'Blue', 'hex' => '#00a5bd' ),
		'green' => array( 'name' => 'Green', 'hex' => '#ada400' ),
		'grey' => array( 'name' => 'Grey', 'hex' => '#5e6a71' ),
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
	 *
	 * @param string $hook Hook indicating the current admin page.
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( 'post.php' !== $hook ) {
			return;
		}

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
			$classes[] = 'cob-palette-' . $palette;
		}

		return $classes;
	}
}
new WSU_COB_Color_Palette();