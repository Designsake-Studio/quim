<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wpextend.io
 * @since      1.0.0
 *
 * @package    Minimum_Periods_For_Woocommerce_Subscriptions
 * @subpackage Minimum_Periods_For_Woocommerce_Subscriptions/includes
 */
class Minimum_Periods_For_Woocommerce_Subscriptions_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'minimum-periods-for-woocommerce-subscriptions',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
