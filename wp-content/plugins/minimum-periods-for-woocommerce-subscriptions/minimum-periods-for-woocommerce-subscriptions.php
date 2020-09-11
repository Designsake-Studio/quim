<?php
/**
 * Minimum Periods for WooCommerce Subscriptions
 *
 * @link              https://wpextend.io
 * @since             1.0.2
 * @package           Minimum_Periods_For_Woocommerce_Subscriptions
 *
 * @wordpress-plugin
 *
 * Plugin Name:       Minimum Periods for WooCommerce Subscriptions
 * Plugin URI:        https://wpextend.io
 * Description:       Configure minimum periods for WooCommerce Subscriptions before allowing customers to cancel their subscription.
 * Version:           1.1.0
 * Author:            WP Extend
 * Author URI:        https://wpextend.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       minimum-periods-for-woocommerce-subscriptions
 * Domain Path:       /languages
 *
 * Requires at least: 5.2
 * Tested up to: 5.5
 * Requires PHP: 7.0
 *
 * WC requires at least: 3.7
 * WC tested up to: 4.5
 *
 * Woo: 6267526:a7ae526990ef2608c2947f28a787fb69
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'MINIMUM_PERIODS_FOR_WOOCOMMERCE_SUBSCRIPTIONS_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-minimum-periods-for-woocommerce-subscriptions-activator.php
 */
function activate_minimum_periods_for_woocommerce_subscriptions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-minimum-periods-for-woocommerce-subscriptions-activator.php';
	Minimum_Periods_For_Woocommerce_Subscriptions_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-minimum-periods-for-woocommerce-subscriptions-deactivator.php
 */
function deactivate_minimum_periods_for_woocommerce_subscriptions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-minimum-periods-for-woocommerce-subscriptions-deactivator.php';
	Minimum_Periods_For_Woocommerce_Subscriptions_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_minimum_periods_for_woocommerce_subscriptions' );
register_deactivation_hook( __FILE__, 'deactivate_minimum_periods_for_woocommerce_subscriptions' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-minimum-periods-for-woocommerce-subscriptions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_minimum_periods_for_woocommerce_subscriptions() {

	$plugin = new Minimum_Periods_For_Woocommerce_Subscriptions();
	$plugin->run();

}
run_minimum_periods_for_woocommerce_subscriptions();
