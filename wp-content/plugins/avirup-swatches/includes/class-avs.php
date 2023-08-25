<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Avs
 * @subpackage Avs/includes
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
 * @package    Avs
 * @subpackage Avs/includes
 * @author     avirup <avirup@gmail.com>
 */
require_once(plugin_dir_path(__DIR__) .'includes/class-avs-helper.php');

class Avs {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Avs_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
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
		if ( defined( 'Avs_VERSION' ) ) {
			$this->version = Avs_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'avs';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Avs_Loader. Orchestrates the hooks of the plugin.
	 * - Avs_i18n. Defines internationalization functionality.
	 * - Avs_Admin. Defines all hooks for the admin area.
	 * - Avs_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-avs-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-avs-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-avs-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-avs-public.php';

		$this->loader = new Avs_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Avs_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Avs_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Avs_Admin( $this->get_plugin_name(), $this->get_version() );
		$taxonomy = isset( $_REQUEST['taxonomy'] ) ? sanitize_title( $_REQUEST['taxonomy'] ) : '';

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_settings_menu' );
		$this->loader->add_action( $taxonomy . '_add_form_fields', $plugin_admin, 'add_form_fields' );
		$this->loader->add_action( $taxonomy . '_edit_form_fields', $plugin_admin, 'edit_form_fields', 10 );
		// Adding swatch types.
		$this->loader->add_filter( 'product_attributes_type_selector', $plugin_admin, 'add_swatch_types', 10, 1 );
		$this->loader->add_action( 'created_' .$taxonomy, $plugin_admin, 'save_term_fields');
		$this->loader->add_action( 'edited_' .$taxonomy, $plugin_admin, 'save_term_fields');
		$this->loader->add_filter( 'manage_edit-' . $taxonomy . '_columns', $plugin_admin, 'add_attribute_column');
		$this->loader->add_filter( 'manage_' . $taxonomy . '_custom_column', $plugin_admin, 'add_preview_markup', 10, 3 );
		$this->loader->add_action( 'wp_ajax_avs_update_settings', $plugin_admin, 'avs_update_settings' );
		$this->loader->add_action( 'wp_ajax_nopriv_avs_update_settings', $plugin_admin, 'avs_update_settings' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Avs_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'woocommerce_dropdown_variation_attribute_options_html', $plugin_public, 'variation_html',20, 2 );
		$this->loader->add_filter( 'woocommerce_variation_is_active', $plugin_public, 'variation_check', 10, 2 );

		$get_options = Avs_Helper::get_option('avs_shop');
		if($get_options['enable_swatches'] == true){
			$this->loader->add_action( $get_options['swatch_position'], $plugin_public, 'variation_attribute_html_shop_page',  10 );
		}

		$this->loader->add_filter( 'woocommerce_loop_add_to_cart_args', $plugin_public, 'shop_page_add_to_cart_args', 10, 2 );
		$this->loader->add_action( 'wp_ajax_avs_ajax_add_to_cart', $plugin_public, 'avs_ajax_add_to_cart' );
		$this->loader->add_action( 'wp_ajax_nopriv_avs_ajax_add_to_cart', $plugin_public, 'avs_ajax_add_to_cart' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Avs_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
