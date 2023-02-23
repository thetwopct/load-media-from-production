<?php
/**
 * Load Media from Production
 *
 * @package     WordPress
 * @author      James Hunt
 * @copyright 	2023 James Hunt
 * @license     GPL-3.0+
 *
 * @wordpress-plugin
 * Plugin Name: Load Media from Production
 * Plugin URI: https://www.thetwopercent.co.uk
 * Description: Redirect all references in your WordPress database to the uploads directory to another URL with the same file path. This plugin is useful if you want to work locally on a website without the need to download all of the media library. Options to set via WP-CLI or constant.
 * Author: James Hunt
 * Version: 1.1.0
 * Author URI: https://www.thetwopercent.co.uk
 * License:                 GPLv3 or later
 * Requires PHP:            7.4
 * Requires at least:       5.0
 * Tested up to:            6.2
 */

// phpcs:ignore WordPress.Files.FileName.InvalidClassFileName.

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Load_Media_From_Production' ) ) :
	/**
	 * Load Media from Production
	 */
	class Load_Media_From_Production {

		/**
		 * Production URL array
		 *
		 * @since 1.0.0
		 * @var string
		 * @author James Hunt
		 */
		public $production_url = '';

		/**
		 * Construct
		 *
		 * @since  1.0.0
		 * @author James Hunt
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'lmfp_redirect_header' ) );
			add_filter( 'plugin_action_links', array( $this, 'lmfp_add_settings_link' ), 99, 2 );
			add_action( 'admin_menu', array( $this, 'lmfp_add_admin_menu' ), 99 );
			add_action( 'cli_init', array( $this, 'lmfp_cli_register_commands' ) );
		}

		/**
		 * Update header to redirect.
		 *
		 * @since  1.0.0
		 * @author James Hunt
		 */
		public function lmfp_redirect_header() {
			$production_url = $this->lmfp_get_production_url();

			// the request from browser.
			$request = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

			if ( ! $production_url || ! $request ) {
				return;
			}

			// the path of the request.
			$path = $this->lmfp_get_path( $request );
			// the uploads directory.
			$base_path = $this->lmfp_get_upload_path();

			// check that its something in the uploads folder.
			if ( ! stristr( $path, $base_path ) ) {
				return;
			}

			header( 'HTTP/1.0 301 Moved Permanently' );
			header( "Location: $production_url/$path" );
			exit;
		}

		/**
		 * Add settings link to Installed Plugins.
		 *
		 * @param array  $links Available links.
		 * @param string $file File name.
		 *
		 * @since  1.0.0
		 * @author James Hunt
		 */
		public function lmfp_add_settings_link( $links, $file ) {
			if ( plugin_basename( __FILE__ ) == $file ) {
				$settings_link = '<a href="' . admin_url( 'options-general.php?page=load-media-from-production' ) . '">Settings</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
		}

		/**
		 * Create page in Admin.
		 *
		 * @since  1.0.0
		 * @author James Hunt
		 */
		public function lmfp_add_admin_menu() {
			add_options_page( 'Load Media from Production', 'Load Media from Production', 'manage_options', 'load-media-from-production', array( $this, 'lmfp_settings_page' ) );
		}

		/**
		 * Settings page setup.
		 *
		 * @since  1.0.0
		 * @author James Hunt
		 */
		public function lmfp_settings_page() {
			// check for user level.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'Unauthorized user' );
			}

			include 'includes/settings.php';
		}

		/**
		 * Get path from URL
		 *
		 * @param string $url URL path.
		 *
		 * @since  1.0.0
		 * @author James Hunt
		 */
		public function lmfp_get_path( $url ) {
			$args = parse_url( $url );
			return $args['path'];
		}

		/**
		 * Get upload path.
		 *
		 * @since  1.0.0
		 * @author James Hunt
		 */
		public function lmfp_get_upload_path() {
			$args = wp_upload_dir();
			$base = $args['baseurl'];
			return $this->lmfp_get_path( $base );
		}

		/**
		 * Registers our command when cli get's initialized.
		 *
		 * @since  1.0.0
		 * @author James Hunt
		 */
		public function lmfp_cli_register_commands() {
			if ( ! defined( 'WP_CLI' ) && ! WP_CLI ) {
				return;
			}
			include 'includes/class-load-media-from-production-cli.php';
			WP_CLI::add_command( 'load-media-from-production', 'Load_Media_From_Production_CLI' );
		}

		/**
		 * Gets the production URL.
		 *
		 * @since  1.0.0
		 * @author James Hunt
		 */
		public function lmfp_get_production_url() {
			$production_url = $this->production_url;

			if ( defined( 'LOAD_MEDIA_FROM_PRODUCTION_URL' ) && LOAD_MEDIA_FROM_PRODUCTION_URL ) {
				   $production_url = LOAD_MEDIA_FROM_PRODUCTION_URL;
			} else {
				$production_url = get_option( 'lmfp_production_url', null );

			}
				return $production_url;
		}

		/**
		 * Checks presence of defined constant.
		 *
		 * @since  1.0.0
		 * @author James Hunt
		 */
		public function lmfp_check_defined_constant() {
			return ( defined( 'LOAD_MEDIA_FROM_PRODUCTION_URL' ) && LOAD_MEDIA_FROM_PRODUCTION_URL );
		}

		/**
		 * Check trim and sanitize the URL.
		 *
		 * @param string $host URL hostname.
		 */
		public function lmfp_sanitize_trim_check( $host ) {
			$host      = sanitize_text_field( $host );
			$base_path = trim( $this->lmfp_get_upload_path(), '/' );
			$host      = str_ireplace( $base_path, '', $host );
			$host      = trim( $host, '/' );

			// check this is a URL with very basic checks.
			if ( filter_var( $host, FILTER_VALIDATE_URL ) === false ) {
				return false;
			}
			return $host;
		}

	} // end of class.
	new Load_Media_From_Production();

endif;
