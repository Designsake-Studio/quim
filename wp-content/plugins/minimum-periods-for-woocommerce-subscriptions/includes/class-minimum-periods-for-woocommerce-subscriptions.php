<?php
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
 * @package    Minimum_Periods_For_Woocommerce_Subscriptions
 * @subpackage Minimum_Periods_For_Woocommerce_Subscriptions/includes
 */
class Minimum_Periods_For_Woocommerce_Subscriptions {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @var      Minimum_Periods_For_Woocommerce_Subscriptions_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
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
		if ( defined( 'MINIMUM_PERIODS_FOR_WOOCOMMERCE_SUBSCRIPTIONS_VERSION' ) ) {
			$this->version = MINIMUM_PERIODS_FOR_WOOCOMMERCE_SUBSCRIPTIONS_VERSION;
		} else {
			$this->version = '1.1.0';
		}
		$this->plugin_name = 'minimum-periods-for-woocommerce-subscriptions';

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
	 * - Minimum_Periods_For_Woocommerce_Subscriptions_Loader. Orchestrates the hooks of the plugin.
	 * - Minimum_Periods_For_Woocommerce_Subscriptions_I18n. Defines internationalization functionality.
	 * - Minimum_Periods_For_Woocommerce_Subscriptions_Admin. Defines all hooks for the admin area.
	 * - Minimum_Periods_For_Woocommerce_Subscriptions_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-minimum-periods-for-woocommerce-subscriptions-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-minimum-periods-for-woocommerce-subscriptions-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-minimum-periods-for-woocommerce-subscriptions-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-minimum-periods-for-woocommerce-subscriptions-public.php';

		$this->loader = new Minimum_Periods_For_Woocommerce_Subscriptions_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Minimum_Periods_For_Woocommerce_Subscriptions_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function set_locale() {

		$plugin_i18n = new Minimum_Periods_For_Woocommerce_Subscriptions_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_admin_hooks() {

		$plugin_admin    = new Minimum_Periods_For_Woocommerce_Subscriptions_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );

		// Check plugin dependencies.
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'mpws_plugins_loaded' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'mpws_plugin_dependencies_notice' );

		// Admin Scripts & Styles.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Minimum Period options on a store-wide level.
		$this->loader->add_action( 'woocommerce_subscription_settings', $plugin_admin, 'mpws_subscription_settings' );

		// Minimum Period options on a per-subscription basis.
		$this->loader->add_action( 'woocommerce_subscriptions_product_options_pricing', $plugin_admin, 'mpws_admin_edit_product_fields', 11 );

		// Minimum Periods options on a per-variation subscription basis.
		$this->loader->add_action( 'woocommerce_variable_subscription_pricing', $plugin_admin, 'mpws_variable_subscription_pricing_fields', 11, 3 );

		// Saving Minimum Period options.
		$this->loader->add_action( 'save_post', $plugin_admin, 'mpws_save_subscription_meta', 11 );

		// Saving Minimum Period options on a per-variation subscription basis.
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'mpws_save_variable_subscription_meta', 11, 2 );

		// Add plugin actions links.
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'mpws_add_action_links', 10, 1 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_public_hooks() {

		$plugin_public = new Minimum_Periods_For_Woocommerce_Subscriptions_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'wcs_view_subscription_actions', $plugin_public, 'mpws_remove_cancel_button', 11, 2 );

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
	 * @return    Minimum_Periods_For_Woocommerce_Subscriptions_Loader    Orchestrates the hooks of the plugin.
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
