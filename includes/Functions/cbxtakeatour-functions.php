<?php
// If this file is called directly, abort.
use cbxtakeatour\includes\Helpers\CBXTakeaTourHelper;

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'cbxtakeatour_display' ) ) {
	/**
	 * Tour quick display
	 *
	 * @param  array  $tour_data
	 *
	 * @return string
	 */
	function cbxtakeatour_display( $tour_data = [] ) {
		return CBXTakeaTourHelper::display_tour( $tour_data );
	}//end cbxtakeatour_display
}

if ( ! function_exists( 'cbxtakeatour_allow_create_tour' ) ) {
	/**
	 * Allow to create new tour
	 *
	 * @return mixed|void
	 */
	function cbxtakeatour_allow_create_tour() {
		return CBXTakeaTourHelper::allow_create_tour();
	}
}

if ( ! function_exists( 'cbxtakeatour_load_svg' ) ) {
	/**
	 * Load an SVG file from a directory.
	 *
	 * @param  string  $svg_name  The name of the SVG file (without the .svg extension).
	 * @param  string  $directory  The directory where the SVG files are stored.
	 *
	 * @return string|false The SVG content if found, or false on failure.
	 * @since 1.0.0
	 */
	function cbxtakeatour_load_svg( $svg_name = '', $folder = '' ) {
		if ( $svg_name == '' ) {
			return '';
		}


		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$credentials = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, null );
		if ( ! WP_Filesystem( $credentials ) ) {
			return ''; // Error handling here
		}

		global $wp_filesystem;


		$directory = cbxtakeatour_icon_path();

		// Sanitize the file name to prevent directory traversal attacks.
		$svg_name = sanitize_file_name( $svg_name );
		if ( $folder != '' ) {
			$folder = trailingslashit( $folder );
		}

		// Construct the full file path.
		$file_path = $directory . $folder . $svg_name . '.svg';
		$file_path = apply_filters( 'cbxtakeatour_svg_file_path', $file_path, $svg_name );

		// Check if the file exists.
		//if ( file_exists( $file_path ) && is_readable( $file_path ) ) {
		if ( $wp_filesystem->exists( $file_path ) && is_readable( $file_path ) ) {
			// Get the SVG file content.
			return $wp_filesystem->get_contents( $file_path );
		} else {
			// Return false if the file does not exist or is not readable.
			return '';
		}
	}//end method cbxtakeatour_load_svg
}

if ( ! function_exists( 'cbxtakeatour_icon_path' ) ) {
	/**
	 * Resume icon path
	 *
	 * @return mixed|null
	 * @since 1.0.0
	 */
	function cbxtakeatour_icon_path() {
		$directory = trailingslashit( CBXTAKEATOUR_ROOT_PATH ) . 'assets/icons/';

		return apply_filters( 'cbxtakeatour_icon_path', $directory );
	}//end method cbxtakeatour_icon_path
}

if(!function_exists('cbxtakeatour_is_rest_api_request')){
	/**
	 * Check if doing rest request
	 *
	 * @return bool
	 */
	function cbxtakeatour_is_rest_api_request() {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );
		return ( false !== strpos( sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), $rest_prefix ) );
	}//end function cbxtakeatour_is_rest_api_request
}

if(!function_exists('cbxtakeatour_doing_it_wrong')){
	/**
	 * Wrapper for _doing_it_wrong().
	 *
	 * @since  1.0.0
	 * @param string $function Function used.
	 * @param string $message Message to log.
	 * @param string $version Version the message was added in.
	 */
	function cbxtakeatour_doing_it_wrong( $function, $message, $version ) {
		// @codingStandardsIgnoreStart
		$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

		if ( wp_doing_ajax() || cbxtakeatour_is_rest_api_request() ) {
			do_action( 'doing_it_wrong_run', $function, $message, $version );
			error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
		} else {
			_doing_it_wrong( $function, $message, $version );
		}
		// @codingStandardsIgnoreEnd
	}//end function cbxtakeatour_doing_it_wrong
}