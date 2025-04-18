<?php
// If this file is called directly, abort.
use cbxtakeatour\includes\Helpers\CBXTakeaTourHelper;

if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Fired during plugin uninstallation
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/includes
 */

/**
 * Fired during plugin uninstallation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/includes
 * @author     codeboxr <info@codeboxr.com>
 */
class CBXTakeaTourUninstall {
	/**
	 * Uninstall plugin functionality
	 *
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
		// For the regular site.
		if ( ! is_multisite() ) {
			self::uninstall_tasks();
		} else {
			//for multi site
			global $wpdb;

			//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$blog_ids         = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			$original_blog_id = get_current_blog_id();

			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );

				self::uninstall_tasks();
			}

			switch_to_blog( $original_blog_id );
		}
	}//end method uninstall

	/**
	 * Do the necessary uninstall tasks
	 *
	 * @return void
	 * @since 3.1.1
	 */
	public static function uninstall_tasks() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		do_action( 'cbxtakeatour_plugin_uninstall_before' );

		global $wpdb;
		$settings             = new CBXTakeaTour_Settings();
		$delete_global_config = $settings->get_field( 'delete_global_config', 'cbxtakeatour_tools', '' );

		if ( $delete_global_config == 'on' ) {

			//delete plugin options

			do_action( 'cbxtakeatour_plugin_options_deleted_before' );

			$option_values = CBXTakeaTourHelper::getAllOptionNames();
			foreach ( $option_values as $option_value ) {
				//delete_option( $option_value['option_name'] );
				$option = $option_value['option_name'];

				do_action( 'cbxtakeatour_plugin_option_delete_before', $option );
				delete_option( $option );
				do_action( 'cbxtakeatour_plugin_option_delete_after', $option );
			}

			do_action( 'cbxtakeatour_plugin_options_deleted_after' );
			do_action( 'cbxtakeatour_plugin_options_deleted' );
			//end delete options


			//delete tables
			$table_names = CBXTakeaTourHelper::getAllDBTablesList();
			do_action( 'cbxtakeatour_plugin_tables_deleted_before', $table_names );

			if ( is_array( $table_names ) && sizeof( $table_names ) > 0 ) {
				do_action( 'cbxtakeatour_plugin_tables_delete_before', $table_names );

				foreach ( $table_names as $table_name ) {
					$sanitized_table_name = esc_sql( $table_name );
					//phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
					$wpdb->query( "DROP TABLE IF EXISTS $sanitized_table_name" );
				}

				do_action( 'cbxtakeatour_plugin_tables_delete_after', $table_names );
			}

			do_action( 'cbxtakeatour_plugin_tables_deleted_after', $table_names );
			do_action( 'cbxtakeatour_plugin_tables_deleted' );
			//end delete tables

			do_action( 'cbxtakeatour_plugin_uninstall' );
		}
	}//end uninstall
}//end class CBCurrencyConverter_Uninstall