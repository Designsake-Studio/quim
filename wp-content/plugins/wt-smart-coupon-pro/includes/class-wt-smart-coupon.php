<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/includes
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
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/includes
 * @author     markhf <info@webtoffee.com>
 */
if( ! class_exists ( 'Wt_Smart_Coupon' ) ) {
	class Wt_Smart_Coupon {

		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      Wt_Smart_Coupon_Loader    $loader    Maintains and registers all hooks for the plugin.
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

			protected $plugin_base_name = WT_SMARTCOUPON_BASE_NAME;
				
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
				
			if ( defined( 'WEBTOFFEE_SMARTCOUPON_VERSION' ) ) {
				$this->version = WEBTOFFEE_SMARTCOUPON_VERSION;
			} else {
				$this->version = '1.0.0';
			}
			$this->plugin_name = 'wt-smart-coupon';

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
		 * - Wt_Smart_Coupon_Loader. Orchestrates the hooks of the plugin.
		 * - Wt_Smart_Coupon_i18n. Defines internationalization functionality.
		 * - Wt_Smart_Coupon_Admin. Defines all hooks for the admin area.
		 * - Wt_Smart_Coupon_Public. Defines all hooks for the public side of the site.
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
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wt-smart-coupon-loader.php';

			/**
			 * Webtoffee Security Library
			 * Includes Data sanitization, Access checking
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wt-security-helper.php';

			/**
			 * The class responsible for defining internationalization functionality
			 * of the plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wt-smart-coupon-i18n.php';

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wt-smart-coupon-admin.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/licence/class-wt-smart-coupon-licence.php';
			
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wt-smart-coupon-hooks-abstract.php';

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wt-smart-coupon-admin-display.php';


			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wt-smart-coupon-public.php';
			


			require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/settings/class-smart-coupon-settings.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/class-wt-duplicate-coupon.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) .'public/class-myaccount-smart-coupon.php';

			/**
			 * This Class will describe the modules we have used and enabled
			 * 
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/enable-modules/class-wt-smart-coupon-enable-disable-feature.php';
			$wt_smart_coupon_features = WT_smart_Coupon_Enable_Module::get_features_option();
			/**
			 * Inlcude required files for enabled features.
			 */
			foreach( $wt_smart_coupon_features as $feature_name => $properties ) {
				if( $properties['status'] ) {
					if( isset( $properties['file_to_include'] ) && is_array( $properties['file_to_include'] ) ) {
						foreach( $properties['file_to_include'] as $file ) {
							require_once WT_SMARTCOUPON_MAIN_PATH.$file;
						}
					}
				}
			}
			
			$this->loader = new Wt_Smart_Coupon_Loader();

		}

		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * Uses the Wt_Smart_Coupon_i18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function set_locale() {

			$plugin_i18n = new Wt_Smart_Coupon_i18n();

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

			$plugin_admin = new Wt_Smart_Coupon_Admin( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_filter( 'wt_smart_coupons_alter_role_access', $plugin_admin,'wt_sc_alter_user_roles',10,1  );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles',10,0 );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts',10,0 );
			$this->loader->add_filter('plugin_action_links_' . $this->get_plugin_base_name(), $plugin_admin, 'add_plugin_links_wt_smartcoupon');
			$this->loader->add_filter('woocommerce_coupon_data_tabs', $plugin_admin, 'admin_coupon_options_tabs', 20, 1);
			$this->loader->add_action('woocommerce_coupon_data_panels', $plugin_admin, 'admin_coupon_options_panels', 10, 0);
			$this->loader->add_action('woocommerce_coupon_options_usage_restriction', $plugin_admin, 'admin_coupon_usage_restrictions', 10, 1);
			$this->loader->add_action('webtoffee_coupon_metabox_checkout',$plugin_admin, 'admin_coupon_metabox_checkout2', 10, 2);
			$this->loader->add_action('webtoffee_coupon_metabox_customer',$plugin_admin, 'admin_coupon_metabox_customer', 10, 2);
			$this->loader->add_action('woocommerce_process_shop_coupon_meta', $plugin_admin, 'process_shop_coupon_meta', 10, 2);
			$this->loader->add_filter( 'views_edit-shop_coupon', $plugin_admin, 'smart_coupons_views_row'  );
			$this->loader->add_action('admin_menu', $plugin_admin,'wt_smart_coupon_admin_page',10,0);
			$this->loader->add_action('woocommerce_coupon_options', $plugin_admin,'add_new_coupon_options',10,2);
			$this->loader->add_filter( 'woocommerce_screen_ids', $plugin_admin,'add_wc_screen_id',10,1  );

			// Coupon with product @since 1.1.0
            $this->loader->add_action( 'wp_ajax_wt_json_search_coupons', $plugin_admin, 'wt_json_search_coupons'  );

            $this->loader->add_filter('woocommerce_email_styles',$plugin_admin, 'coupon_inline_style' ,1,1);
			$this->loader->add_action( 'smart_coupons_display_views',$plugin_admin, 'smart_coupon_admin_tabs' );
			
		}

		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_public_hooks() {

			$plugin_public 		= new Wt_Smart_Coupon_Public( $this->get_plugin_name(), $this->get_version() );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
			$this->loader->add_filter( 'woocommerce_coupon_is_valid', $plugin_public,  'wt_woocommerce_coupon_is_valid', 10, 2 );
			// $this->loader->add_action( 'woocommerce_calculate_totals',$plugin_public,'woocommerce_calculate_totals', 30 );
			$this->loader->add_action('woocommerce_after_cart_table',$plugin_public,'display_available_coupon_in_cart');
			$this->loader->add_action('woocommerce_before_checkout_form',$plugin_public,'display_available_coupon_in_checkout');
            
            $this->loader->add_action('wp_ajax_apply_coupon_on_click',$plugin_public,'apply_coupon');
			$this->loader->add_action('wp_ajax_nopriv_apply_coupon_on_click',$plugin_public,'apply_coupon');
			$this->loader->add_action('woocommerce_order_details_after_order_table',$plugin_public,'add_coupon_details_with_order',10,1 );
			$this->loader->add_action('woocommerce_email_after_order_table',$plugin_public,'add_coupon_details_with_order_email',10,4 );

			
		}

		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since    1.0.0
		 */
		public function run() {
			$this->loader->run();
		}

		public function get_plugin_name() {
			return $this->plugin_name;
		}

		/**
		 * The reference to the class that orchestrates the hooks with the plugin.
		 *
		 * @since     1.0.0
		 * @return    Wt_Smart_Coupon_Loader    Orchestrates the hooks of the plugin.
		 */
		public function get_loader() {
			return $this->loader;
		}


		public function get_version() {
			return $this->version;
		}
			
		public function get_plugin_base_name() {
			return $this->plugin_base_name;
		}
		public static function wt_cli_is_woocommerce_prior_to($version) {
			$woocommerce_is_pre_version = (!defined('WC_VERSION') || version_compare(WC_VERSION, $version, '<')) ? true : false;
			return $woocommerce_is_pre_version;
		}

	}
}