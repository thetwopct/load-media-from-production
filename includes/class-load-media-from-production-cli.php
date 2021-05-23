<?php
/**
 * Load Media from Production CLI
 *
 * @package     WordPress
 * @author      James Hunt
 * @license     GPL-3.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Load Media from Production CLI
 */
class Load_Media_From_Production_CLI {
	/**
	 * Displays the current Production URL that is set.
	 *
	 * @since  1.0.0
	 * @author James Hunt
	 */
	public function get() {
		$url = ( new Load_Media_From_Production() )->lmfp_get_production_url();
		if ( $url ) {
			return WP_CLI::line( 'Load Media From Production URL is: ' . $url );
		}
		return WP_CLI::warning( 'No production URL is set' );
	}

	/**
	 * Set a Production URL to load media from.
	 *
	 * @since  1.0.0
	 * @author James Hunt
	 * @param array $args Passed arguments.
	 * @param array $assoc_args Associated arguments.
	 */
	public function set( $args, $assoc_args ) {
		$url = '';
		if ( isset( $args[0] ) ) {
			$url = $args[0];
		}

		if ( ! $url ) {
			return WP_CLI::error( WP_CLI::colorize( 'No URL value was given. To set a production URL enter: %Bwp load-media-from-production set https://production-url.com%n or to reset enter: %Bwp load-media-from-production reset%n' ) );
		}

		if ( ( new Load_Media_From_Production() )->lmfp_check_defined_constant() ) {
			return WP_CLI::error( 'A defined constant is set for LOAD_MEDIA_FROM_PRODUCTION_URL, so a URL can not be set here' );
		}

		$url = ( new Load_Media_From_Production() )->lmfp_sanitize_trim_check( $url );

		if ( ! $url ) {
			return WP_CLI::error( 'That URL caused an error, please check it and try again' );
		}

		if ( $url ) {
			update_option( 'lmfp_production_url', $url );
		}

		WP_CLI::success( 'Your WordPress is set to load media from ' . $url );
	}

	/**
	 * Resets Production URL.
	 *
	 * @since  1.0.0
	 * @author James Hunt
	 */
	public function reset() {

		if ( ( new Load_Media_From_Production() )->lmfp_check_defined_constant() ) {
			return WP_CLI::error( 'A defined constant is set, so a URL can not be reset here' );
		}

		$url = get_option( 'lmfp_production_url', false );

		if ( ! $url ) {
			return WP_CLI::error( 'There is no Production URL set for Load Media From Production plugin. Aborting.' );
		}

		delete_option( 'lmfp_production_url' );

		return WP_CLI::success( 'The Production URL for Load Media From Production plugin has been removed' );
	}
}
