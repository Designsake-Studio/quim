<?php
/**
 * Register all actions and filters for Gift Coupon
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

if( ! class_exists ( 'Wt_Smart_Coupon_Gift_Coupon_Hooks' ) ) {
    class Wt_Smart_Coupon_Gift_Coupon_Hooks extends Wt_Smart_Coupon_Hooks {
        

        function load_hooks() {

            $gift_coupon_admin = new Wt_Smart_Coupon_Gift_Coupon_Admin();
            $this->add_action('woocommerce_product_options_general_product_data',$gift_coupon_admin,'add_coupon_field_forproduct');
            $this->add_action('save_post_product', $gift_coupon_admin, 'save_product_coupon_meta_data', 11,1);
            $this->add_action( 'add_meta_boxes', $gift_coupon_admin, 'add_coupon_details_into_order' );
            $this->add_action('woocommerce_email_classes', $gift_coupon_admin,'add_wt_smart_gift_coupon_emails', 10, 1);
            $this->add_action('wt_smart_coupon_general_settings', $gift_coupon_admin,'add_gift_cuopon_settings', 10);
            $this->add_action('wt_smart_coupon_general_settings_updated', $gift_coupon_admin,'update_gift_coupon_settings', 10);
            
            $this->add_action( 'woocommerce_product_after_variable_attributes', $gift_coupon_admin,'add_coupon_field_forproduct_variations', 10, 3 );
            $this->add_action( 'woocommerce_save_product_variation', $gift_coupon_admin,'save_coupon_field_forproduct_variations', 9, 2 );
            $this->add_action( 'wp_ajax_wt_send_coupon', $gift_coupon_admin,'send_coupons' );
            
            
            $gift_coupon = new Wt_Smart_Coupon_Gift_Coupon();
            $this->add_action( 'woocommerce_checkout_after_customer_details', $gift_coupon, 'coupon_receiver_detail_form'  );
            $this->add_action( 'woocommerce_single_product_summary', $gift_coupon,'display_coupon_details', 60,1 );
            $this->add_action('woocommerce_checkout_update_order_meta', $gift_coupon,'wt_smart_coupon_update_order_meta' );
            $this->add_action('woocommerce_checkout_process',$gift_coupon, 'validate_coupon_fields');
            $this->add_action('woocommerce_order_status_changed',$gift_coupon,'send_gift_coupon_email', 10, 4);

        }

    }

    $gift_coupon = new  Wt_Smart_Coupon_Gift_Coupon_Hooks();
    $gift_coupon->run();

  
}