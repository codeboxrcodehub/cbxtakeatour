<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://codeboxr.com
 * @since             1.0.0
 * @package           CBXTakeaTour
 *
 * @wordpress-plugin
 * Plugin Name:       CBX Tour - User Walkthroughs & Guided Tours
 * Plugin URI:        https://codeboxr.com/product/cbx-tour-user-walkthroughs-guided-tours-for-wordpress//
 * Description:       Interactive tour creator for product/service feature for WordPress
 * Version:           1.1.6
 * Author:            Codeboxr Team
 * Author URI:        https://codeboxr.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cbxtakeatour
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'CBXTAKEATOUR_PLUGIN_NAME' ) or define( 'CBXTAKEATOUR_PLUGIN_NAME', 'cbxtakeatour' );
defined( 'CBXTAKEATOUR_PLUGIN_VERSION' ) or define( 'CBXTAKEATOUR_PLUGIN_VERSION', '1.1.6' );
defined( 'CBXTAKEATOUR_BASE_NAME' ) or define( 'CBXTAKEATOUR_BASE_NAME', plugin_basename( __FILE__ ) );
defined( 'CBXTAKEATOUR_ROOT_PATH' ) or define( 'CBXTAKEATOUR_ROOT_PATH', plugin_dir_path( __FILE__ ) );
defined( 'CBXTAKEATOUR_ROOT_URL' ) or define( 'CBXTAKEATOUR_ROOT_URL', plugin_dir_url( __FILE__ ) );

defined( 'CBXTAKEATOUR_WP_MIN_VERSION' ) or define( 'CBXTAKEATOUR_WP_MIN_VERSION', '5.3' );
defined( 'CBXTAKEATOUR_PHP_MIN_VERSION' ) or define( 'CBXTAKEATOUR_PHP_MIN_VERSION', '7.4' );


/**
 * Checking wp version
 *
 * @param $version
 *
 * @return bool
 */
function cbxtakeatour_compatible_wp_version( $version = '' ) {
	if($version == '') $version = CBXTAKEATOUR_WP_MIN_VERSION;

	if ( version_compare( $GLOBALS['wp_version'], $version, '<' ) ) {
		return false;
	}

	// Add sanity checks for other version requirements here

	return true;
}//end method cbxtakeatour_compatible_wp_version

/**
 * Checking php version
 *
 * @param $version
 *
 * @return bool
 */
function cbxtakeatour_compatible_php_version( $version = '' ) {
	if($version == '') $version = CBXTAKEATOUR_PHP_MIN_VERSION;

	if ( version_compare( PHP_VERSION, $version, '<' ) ) {
		return false;
	}

	return true;
}//end method cbxtakeatour_compatible_php_version

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/CBXTakeaTourActivator.php
 */
function activate_cbxtakeatour() {
	$wp_version  = CBXTAKEATOUR_WP_MIN_VERSION;
	$php_version = CBXTAKEATOUR_PHP_MIN_VERSION;

	$activate_ok = true;

	if ( ! cbxtakeatour_compatible_wp_version() ) {
		$activate_ok = false;

		deactivate_plugins( plugin_basename( __FILE__ ) );

		/* translators: WordPress version */
		wp_die( sprintf( esc_html__( 'CBX Tour plugin requires WordPress %s or higher!', 'cbxtakeatour' ), esc_attr($wp_version) ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	if ( ! cbxtakeatour_compatible_php_version() ) {
		$activate_ok = false;

		deactivate_plugins( plugin_basename( __FILE__ ) );

		/* translators: PHP version */
		wp_die( sprintf( esc_html__( 'CBX Tour plugin requires PHP %s or higher!', 'cbxtakeatour' ), esc_attr($php_version) ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	if($activate_ok){
		require_once plugin_dir_path( __FILE__ ) . 'includes/CBXTakeaTourActivator.php';
		CBXTakeaTourActivator::activate();
	}
}//end function activate_cbxtakeatour

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/CBXTakeaTourDeactivator.php
 */
function deactivate_cbxtakeatour() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/CBXTakeaTourDeactivator.php';
	CBXTakeaTourDeactivator::deactivate();
}//end function deactivate_cbxtakeatour

register_activation_hook( __FILE__, 'activate_cbxtakeatour' );
register_deactivation_hook( __FILE__, 'deactivate_cbxtakeatour' );

if ( ! class_exists( 'CBXTakeaTour', false ) ) {
	require plugin_dir_path( __FILE__ ) . 'includes/CBXTakeaTour.php';
}

/**
 * Returns the main instance of CBXTakeaTour.
 *
 * @since  1.0
 */
function cbxtakeatour_core() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	global $cbxtakeatour_core;
	if ( ! isset( $cbxtakeatour_core ) ) {
		$cbxtakeatour_core = run_cbxtakeatour_core();
	}

	return $cbxtakeatour_core;
}//end method comfortsmtp_core

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.1.1
 */
function run_cbxtakeatour_core() {
	return CBXTakeaTour::instance();
}

$GLOBALS['cbxtakeatour_core'] = run_cbxtakeatour_core();