<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Minimum_Periods_For_Woocommerce_Subscriptions
 * @subpackage Minimum_Periods_For_Woocommerce_Subscriptions/public
 */
class Minimum_Periods_For_Woocommerce_Subscriptions_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Remove the "Cancel" button if minimum period(s) haven't been paid.
	 *
	 * @since    1.0.0
	 * @param      array    $actions       The cancellation state of the subscription.
	 * @param      interger $subscription       The id of the active subscription.
	 */
	public function mpws_remove_cancel_button( $actions, $subscription ) {

		// Get global settings for Minimum Periods.
		$mpws_get_global_cancel      = get_option( 'mpws_allow_cancelling', 'yes' );
		$mpws_get_global_min_periods = get_option( 'mpws_allow_cancelling_periods', '0' );

		// Get number of payment periods that have already been made on a per-subscription basis.
		$mpws_get_payment_count = $subscription->get_payment_count();

		// Set empty container in case there are multiple subscription products with different periods.
		$line_item_required_payments = array();

		if ( 'yes' === $mpws_get_global_cancel ) {

			foreach ( $subscription->get_items() as $item ) {

				// Get local settings for Minimum Periods on a per-subscription basis.
				$mpws_get_local_cancel      = $item->get_product()->get_meta( 'mpws_allow_cancelling' );
				$mpws_get_local_min_periods = $item->get_product()->get_meta( 'mpws_allow_cancelling_periods' );

				if ( 'override-storewide' === $mpws_get_local_cancel ) {

					if ( $mpws_get_local_min_periods >= $mpws_get_payment_count ) {

						// For multiple subscriptions in the same order, check their local min periods
						// and to the array if there are any unfulfilled periods.
						$line_item_required_payments[] = intval( $mpws_get_local_min_periods );
					}
				} else {

					if ( $mpws_get_global_min_periods >= $mpws_get_payment_count ) {

						// For multiple subscriptions in the same order, check their storewide min periods
						// and to the array if there are any unfulfilled periods.
						$line_item_required_payments[] = intval( $mpws_get_global_min_periods );
					}
				}
			}

			if ( ! empty( $line_item_required_payments ) ) {
				// If the array is not empty, at least one product hasn't met the minimum period
				// requirement and cancelling needs to be disabled.

				$required_payments = max( $line_item_required_payments );
			} else {
				// If array is empty, it means the subscription period is set to 0 and
				// customer can cancel immediately.

				$required_payments = 0;
			}

			if ( $required_payments > $mpws_get_payment_count ) {
				unset( $actions['cancel'] );
				return $actions;

			} else {

				return $actions;

			}
		} else {

			unset( $actions['cancel'] );
			return $actions;

		}
	}
}
