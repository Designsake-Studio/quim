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

if( ! class_exists ( 'Wt_Smart_Coupon_Auto_Coupon_Hooks' ) ) {
    class Wt_Smart_Coupon_Auto_Coupon_Hooks extends Wt_Smart_Coupon_Hooks {
        
        function load_hooks() {

            $auto_coupon = new Wt_Smart_Coupon_Auto_Coupon();
            $this->add_action('woocommerce_coupon_options',$auto_coupon,'add_auto_coupon_options' ,10,2);
            $this->add_action('woocommerce_process_shop_coupon_meta', $auto_coupon, 'process_shop_coupon_meta', 11, 2);
            $this->add_action( 'woocommerce_checkout_update_order_review', $auto_coupon, 'store_billing_email_into_session' , 10 ); 
            $this->add_action( 'woocommerce_after_checkout_validation', $auto_coupon, 'store_billing_email_into_session' , 10 ); 
            $this->add_action( 'woocommerce_check_cart_items', $auto_coupon, 'woocommerce_check_cart_items' , 0, 0 );
            $this->add_action( 'woocommerce_after_calculate_totals', $auto_coupon, 'update_matched_coupons' , 5,1 );
            $this->add_filter( 'woocommerce_cart_totals_coupon_html', $auto_coupon, 'coupon_html', 10, 2 );
            
        }

    }

    $auto_coupon = new  Wt_Smart_Coupon_Auto_Coupon_Hooks();
    $auto_coupon->run();

  
}