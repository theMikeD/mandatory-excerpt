<?php
/**
 * Provides the Mandatory_Excerpt class
 *
 * Causes the excerpt to be required before a post can be published. Filters are provided for customization.
 *
 * @package cnmd
 * @since 1.0.0
 */
namespace cnmd;

class Mandatory_Excerpt {

	/**
	 * If a post has any of these status' and the excerpt is not set, the status will be reset to 'draft'.
	 *
	 * @var array
	 */
	private $reset_these_status = array(
		'publish',
		'future',
		'pending',
	);

	/**
	 * If a post has any of these status' the check for the excerpt will be skipped.
	 *
	 * @var array
	 */
	private $skip_these_status = array(
		'trash',
		'draft',
		'auto-draft',
	);


	/**
	 * Mandatory_Excerpt constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			$this->init__admin();
		}
	}


	/**
	 * Does the setup for the admin side of things.
	 */
	private function init__admin() {
		$this->set_hooks__admin();
	}


	/**
	 * Sets hooks and filters for the admin.
	 *
	 * @since 1.0.0
	 */
	private function set_hooks__admin() {
		add_filter( 'plugin_action_links_' . CNMD_ME_BASENAME, array( $this, 'add_action_links' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'make_excerpt_mandatory' ) );
		add_action( 'admin_notices', array( $this, 'show_message' ) );
		add_action( 'init', array( $this, 'register_gutenberg_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_gutenberg_assets' ) );
	}


	/**
	 * Adds a Help link to the plugin listing.
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function add_action_links( $links ) {
		$links[] = '<a target="_blank" href="https://www.codenamemiked.com/plugins/mandatory-excerpt/">Help</a>';
		return $links;
	}


	/**
	 * Checks to see if the excerpt has been set and, if not, and if other conditions are met, sets the post status
	 * to draft and prevents the post from being published.
	 *
	 * @param array $data    Array of data about the post.
	 *
	 * @return array
	 */
	public function make_excerpt_mandatory( array $data ) {
		if ( ! array_key_exists( 'post_excerpt', $data ) || ! array_key_exists( 'post_status', $data ) ) {
			return $data;
		}

		if ( in_array( $data['post_status'], $this->get_status_to_skip(), true ) ) {
			return $data;
		}

		if ( ! $this->is_valid_post_type( $data['post_type'] ) ) {
			return $data;
		}

		if ( empty( $data['post_excerpt'] ) ) {
			add_filter( 'redirect_post_location', array( $this, 'do_redirect' ), '99' );
			if ( in_array( $data['post_status'], $this->get_status_to_reset(), true ) ) {
				$data['post_status'] = 'draft';
			}
		}
		return $data;
	}


	/**
	 * Determines if the post type is one for which the excerpt should be checked.
	 *
	 * @provides mandatory_excerpt_post_types
	 *
	 * @param string $post_type
	 *
	 * @return bool
	 */
	private function is_valid_post_type( string $post_type ) {

		/**
		 * Adjust the post types for which the excerpt would be required.
		 *
		 * @param array $this->reset_these_status {
		 *     If the post has any of these post types, the post will be checked for a valid excerpt.
		 *     Default is [ 'post', 'page' ]
		 *     @type string   A valid post type
		 * }
		 * @since 1.0.0
		 */
		$valid_post_types = apply_filters( 'mandatory_excerpt_post_types', array( 'post', 'page' ) );

		if ( ! is_array( $valid_post_types ) ) {
			$valid_post_types = array( 'post', 'page' );
		}

		if ( ! in_array( $post_type, $valid_post_types, true ) ) {
			return false;
		}

		if ( ! post_type_supports( $post_type, 'excerpt' ) ) {
			return false;
		}
		return true;
	}


	/**
	 * Modify the redirect so that the post is not published.
	 *
	 * @since 1.0.0
	 *
	 * @param string $location   Where the page will be redirected to.
	 *
	 * @return string
	 */
	public function do_redirect( string $location ) {
		remove_filter( 'redirect_post_location', '__FILTER__', '99' );
		$location = str_replace( '&message=6', '', $location );
		return add_query_arg( 'excerpt_required', 1, $location );
	}


	/**
	 * Show the message about the missing excerpt.
	 *
	 * @since 1.0.0
	 */
	public function show_message() {
		if ( ! isset( $_GET['excerpt_required'] ) ) {
			return;
		}
		switch ( absint( $_GET['excerpt_required'] ) ) {
			case 1:
				$message = __( 'Your request to publish has been cancelled because an excerpt is required. Please add an excerpt and try again.', 'mandatory-excerpt' );
				break;
			default:
				$message = __( 'Unexpected error', 'mandatory-excerpt' );
		}
		echo '<div id="notice" class="notice error"><p>' . esc_html( $message ) . '</p></div>';
	}


	/**
	 * Enables the i18n functions for the gutenberg script(s).
	 *
	 * @see https://developer.wordpress.org/block-editor/developers/internationalization/
	 *
	 * @since 1.1.0
	 */
	public function register_gutenberg_assets() {
		wp_register_script(
				'mandatory-excerpt',
				CNMD_ME_URL . '/js/mandatory-excerpt.min.js',
				array( 'wp-data', 'wp-editor', 'wp-i18n', 'wp-edit-post', 'wp-element', 'word-count' )
		);
		$v = wp_set_script_translations( 'mandatory-excerpt', 'mandatory-excerpt', CNMD_ME_DIR . 'languages' );
	}


	/**
	 * Enqueues the scripts required for gutenberg support.
	 */
	public function enqueue_gutenberg_assets() {
		if ( $this->is_valid_post_type( get_post_type() ) ) {
			wp_localize_script(
				'mandatory-excerpt',
				'mandatory_excerpt_opts',
				array(
					'is_required' => 1,
					//'skip_these_status'  => $this->get_status_to_skip(),
					//'reset_these_status' => $this->get_status_to_reset(),
				)
			);
		}
		wp_enqueue_script( 'mandatory-excerpt' );
	}


	/**
	 * If a post has any of these status' and the excerpt is not set, the status will be reset to 'draft'.
	 *
	 * @since 1.0.0
	 *
	 * @provides mandatory_excerpt_reset_these_status
	 *
	 * @return array
	 */
	private function get_status_to_reset() {
		/**
		 * After it has been determined that the excerpt is required but missing, if the post's status is one of these
		 * then it will be reset to 'draft'.
		 *
		 * @param array $this->reset_these_status {
		 *     If the post has any of these status' the post status will be reset to 'draft'.
		 *     Default is [ 'publish', 'future', 'pending' ]
		 *     @type string   A valid post status
		 * }
		 * @since 1.0.0
		 */
		$reset_these_status = apply_filters( 'mandatory_excerpt_reset_these_status', $this->reset_these_status );
		if ( ! is_array( $reset_these_status ) ) {
			$reset_these_status = $this->reset_these_status;
		}
		return $reset_these_status;
	}


	/**
	 * Get the list of status' that will cause the check for the excerpt to be skipped.
	 *
	 * @provides mandatory_excerpt_skip_these_status
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_status_to_skip() {

		/**
		 * Adjust the post status types that will will cause the check for the excerpt to be skipped.
		 *
		 * @param array $this->skip_these_status {
		 *     If the post has any of these status' the check for the excerpt will be skipped.
		 *     Default is [ 'trash', 'draft', 'auto-draft' ]
		 *     @type string   A valid post status
		 * }
		 * @since 1.0.0
		 *
		 */
		$skip_these_status = apply_filters( 'mandatory_excerpt_skip_these_status', $this->skip_these_status );
		if ( ! is_array( $skip_these_status ) ) {
			$skip_these_status = $this->skip_these_status;
		}
		return $skip_these_status;
	}

}
