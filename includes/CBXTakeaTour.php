<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/includes
 * @author     Codeboxr Team <sabuj@codeboxr.com>
 */
class CBXTakeaTour {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.1.1
	 */
	private static $instance = null;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->plugin_name = CBXTAKEATOUR_PLUGIN_NAME;
		$this->version     = CBXTAKEATOUR_PLUGIN_VERSION;

		if ( cbxtakeatour_compatible_php_version() ) {
			$GLOBALS['cbxtakeatour_loaded'] = true;
			$this->load_dependencies();

			$this->define_common_hooks();
			$this->define_admin_hooks();
			$this->define_public_hooks();
		}
		else{
			add_action( 'admin_notices', [ $this, 'php_version_notice' ] );
		}
	}//end of constructor

	/**
	 * Singleton Instance.
	 *
	 * Ensures only one instance of cbxtakeatour is loaded or can be loaded.
	 *
	 * @return self Main instance.
	 * @see run_cbxtakeatour()
	 * @since  1.1.1
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}//end method instance


	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cbxtakeatour-tpl-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/CBXTakeaTour_Settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Helpers/CBXTakeaTourHelper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Functions/cbxtakeatour-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/CBXTakeaTourAdmin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/CBXTakeaTourPublic.php';
	}//end method load_dependencies


	/**
	 * All the common hooks
	 *
	 * @since    1.1.1
	 * @access   private
	 */
	private function define_common_hooks() {

	}//end method define_common_hooks

	/**
	 * All the hooks related with admin interface
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		global $wp_version;

		$plugin_admin = new CBXTakeaTourAdmin( $this->get_plugin_name(), $this->get_version() );

		add_action( 'admin_enqueue_scripts', [ $plugin_admin, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $plugin_admin, 'enqueue_scripts' ] );


		add_action( 'init', [ $plugin_admin, 'create_tour' ], 0 );
		add_action( 'admin_init', [ $plugin_admin, 'setting_init' ] );
		add_action( 'admin_menu', [ $plugin_admin, 'admin_pages' ], 11 );
		add_action( 'set-screen-option', [ $plugin_admin, 'cbxtakeatour_listing_per_page' ], 10, 3 );
		add_action( 'manage_toplevel_page_cbxtakeatour-listing_columns', [ $plugin_admin, 'tour_listing_screen_cols' ] );

		add_action( 'wp_ajax_cbxtakeatour_delete_auto_drafts', [ $plugin_admin, 'delete_auto_drafts' ] );
		add_action( 'wp_ajax_cbxtakeatour_create_auto_drafts', [ $plugin_admin, 'create_auto_drafts' ] );
		add_action( 'wp_ajax_cbxtakeatour_save_tour_post', [ $plugin_admin, 'save_tour_post' ] );
		add_action( 'wp_ajax_cbxtakeatour_move_to_trash', [ $plugin_admin, 'move_to_trash' ] );


		//plugin setting link, upgrade process, admin notices
		add_filter( 'plugin_action_links_' . CBXTAKEATOUR_BASE_NAME, [ $plugin_admin, 'plugin_action_links' ] );
		add_filter( 'plugin_row_meta', [ $plugin_admin, 'plugin_row_meta' ], 10, 4 );



		add_filter( 'cbxtour_allow_create_tour', [ $plugin_admin, 'disallow_create_tour' ], 10, 2 );

		//gutenberg


		//gutenberg blocks
		if ( version_compare( $wp_version, '5.8' ) >= 0 ) {
			add_filter( 'block_categories_all', [ $plugin_admin, 'gutenberg_block_categories' ], 10, 2 );
		} else {
			add_filter( 'block_categories', [ $plugin_admin, 'gutenberg_block_categories' ], 10, 2 );
		}

		add_action( 'init', [ $plugin_admin, 'gutenberg_blocks' ] );
		//add_action('enqueue_block_editor_assets', [$plugin_admin, 'enqueue_block_editor_assets']);

		//upgrade process
		add_action( 'plugins_loaded', [ $plugin_admin, 'plugin_upgrader_process_complete' ] );
		add_action( 'admin_notices', [ $plugin_admin, 'plugin_activate_upgrade_notices' ] );
		add_action( 'after_plugin_row_cbxtakeatourpro/cbxtakeatourpro.php', [ $plugin_admin, 'custom_message_after_plugin_row_proaddon' ], 10, 2 );

		//ajax plugin reset
		add_action( 'wp_ajax_cbxtakeatour_settings_reset_load', [ $plugin_admin, 'settings_reset_load' ] );
		add_action( 'wp_ajax_cbxtakeatour_settings_reset', [ $plugin_admin, 'plugin_reset' ] );
	}//end define_admin_hooks

	/**
	 * All the hooks related with public interface
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		global $wp_version;

		$plugin_public = new CBXTakeaTourPublic( $this->get_plugin_name(), $this->get_version() );

		add_action( 'wp_enqueue_scripts', [ $plugin_public, 'public_styles_scripts' ] );

		add_action( 'init', [ $plugin_public, 'init_shortcode' ] );

		//Classic Widget
		add_action( 'widgets_init', [ $plugin_public, 'register_widget' ] );

		//Elementor Widget
		add_action( 'elementor/widgets/widgets_registered', [ $plugin_public, 'init_elementor_widgets' ] );
		add_action( 'elementor/elements/categories_registered', [ $plugin_public, 'add_elementor_widget_categories' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $plugin_public, 'elementor_icon_loader' ], 99999 );

		//visual composer widget
		add_action( 'vc_before_init', [ $plugin_public, 'vc_before_init_actions' ] );
	}//end define_public_hooks

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}//end method get_plugin_name

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}//end method get_version

	/**
	 * Show php version notice in dashboard
	 *
	 * @return void
	 */
	public function php_version_notice() {
		echo '<div class="error"><p>';
		/* Translators:  PHP Version */
		echo sprintf(esc_html__( 'CBX Tour - User Walkthroughs & Guided Tours requires at least PHP %s. Please upgrade PHP to run CBX Tour - User Walkthroughs & Guided Tours.', 'cbxtakeatour' ), esc_attr(CBXTAKEATOUR_PHP_MIN_VERSION));
		echo '</p></div>';
	}//end method php_version_notice
}//end class CBXTakeaTour