<?php
/**
 * Register all actions and filters for Combo Coupon
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/combo_coupon
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'Wt_Smart_Coupon_Combo_Coupon_Hooks' ) ) {
    class Wt_Smart_Coupon_Combo_Coupon_Hooks extends Wt_Smart_Coupon_Hooks {
        
        function load_hooks() {

            $combo_coupon = new Wt_Smart_Coupon_Combo_Coupon( );
            $this->add_action('woocommerce_coupon_options_usage_restriction', $combo_coupon, 'admin_coupon_usage_restrictions', 10, 1);
			$this->add_action('woocommerce_process_shop_coupon_meta', $combo_coupon, 'save_combo_coupon_meta', 10, 2);
			$this->add_filter( 'woocommerce_coupon_is_valid', $combo_coupon,  'validate_coupon_with_combo_coupon', 10, 2 );

            
        }

    }

    $combo_coupon = new  Wt_Smart_Coupon_Combo_Coupon_Hooks();
    $combo_coupon->run();

  
}