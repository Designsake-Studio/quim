<?php
/**
 * Fired during plugin activation
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/includes
 * @author     markhf <info@webtoffee.com>
 */

if( ! class_exists ( 'Wt_Smart_Coupon_Activator' ) ) {
	class Wt_Smart_Coupon_Activator {

		/**
		 * Run immediately on plugin actuvation
		 *
		 * Check Woocommerce is activated, check is basic version is activated,
		 * enable woocommerce coupon settings if it disabled
		 *
		 * @since    1.0.0
		 */
		public static function activate() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				deactivate_plugins( WT_SMARTCOUPON_BASE_NAME );
				wp_die(__("Oops! Woocommerce not activated..", 'wt-smart-coupons-for-woocommerce-pro'), "", array('back_link' => 1));
				
			}
			
			if( defined('WT_SMARTCOUPON_INSTALLED_VERSION') && WT_SMARTCOUPON_INSTALLED_VERSION == 'BASIC' ) {
				
				deactivate_plugins( WT_SMARTCOUPON_BASE_NAME );
				wp_die(__("Oops! BASIC Version of this Plugin Installed. Please uninstall the BASIC Version before activating PREMIUM.", 'wt-smart-coupons-for-woocommerce-pro'), "", array('back_link' => 1));
				
			}
			/**
			 *  Enable woocommmerce coupon settings
			 * @since 1.2.9
			 */
			update_option( 'woocommerce_enable_coupons', 'yes' );
			do_action('after_wt_smart_coupon_for_wocommerce_is_activated');
			
			
		}

		
	}
}