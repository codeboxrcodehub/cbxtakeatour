<?php
/**
 * Fired during plugin activation
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/includes
 * @author     Codeboxr Team <sabuj@codeboxr.com>
 */
class CBXTakeaTourActivator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		set_transient( 'cbxtakeatour_activated_notice', 1 );

		// Update the saved version
		update_option('cbxtakeatour_version', CBXTAKEATOUR_PLUGIN_VERSION);
	}//end activate
}//end class CBXTakeaTourActivator.php
