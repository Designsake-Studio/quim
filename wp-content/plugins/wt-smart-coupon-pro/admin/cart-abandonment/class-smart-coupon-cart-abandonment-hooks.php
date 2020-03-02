<?php
/**
 * Register all actions and filters for Combo Coupon
 *
 * @link       http://www.webtoffee.com
 * @since      1.2.8
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/combo_coupon
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'Wt_Smart_Coupon_Cart_Abandonment_Hooks' ) ) {
    class Wt_Smart_Coupon_Cart_Abandonment_Hooks extends Wt_Smart_Coupon_Hooks {
       

        function load_hooks() {

            $cart_abandonment = new Wt_Smart_Coupon_Cart_Checkout_Abandonment();
            
            $this->add_filter('wt_smart_coupon_action_coupon_items',$cart_abandonment,'abandonment_coupon_tab',10,1);
            $this->add_action('wt_action_coupon_page_content', $cart_abandonment,'abandonment_coupon_tab_content');
            $this->add_filter('wt_smart_coupon_default_options',$cart_abandonment,'abandonment_coupon_coupon_options',11,1);
            $this->add_action( 'wt_before_action_coupon_tabs',$cart_abandonment, 'save_abandonment__coupon_settings', 10,0 );
            // Create tables required for 
            $this->add_action( 'after_wt_smart_coupon_for_wocommerce_is_activated', $cart_abandonment,'add_abandonment_coupon_tables' );
            $this->add_action( 'plugins_loaded',$cart_abandonment,'check_for_db_upgrade' );

            $this->add_action( 'woocommerce_add_to_cart',                     $cart_abandonment, 'wt_insert_abandoment_data' , 100 );
            $this->add_action( 'woocommerce_cart_item_removed',               $cart_abandonment, 'wt_insert_abandoment_data' , 100 );
            $this->add_action( 'woocommerce_cart_item_restored',             $cart_abandonment, 'wt_insert_abandoment_data' , 100 );
            $this->add_action( 'woocommerce_after_cart_item_quantity_update', $cart_abandonment, 'wt_insert_abandoment_data' , 100 );
            // $this->add_action( 'woocommerce_calculate_totals',                 $cart_abandonment, 'wt_insert_abandoment_data' , 100 );

            $this->add_action('wt_check_and_create_abandonment_item',          $cart_abandonment, 'create_abadoment_coupon' , 10,3 );
            $this->add_action('woocommerce_email_classes',$cart_abandonment,'add_abandonment_coupon_email', 10, 1);

            $this->add_action('woocommerce_order_status_changed',$cart_abandonment,'check_successfull_order_is_recovered',10,4 );
			
        }

    }

    $cart_abandonment = new  Wt_Smart_Coupon_Cart_Abandonment_Hooks();
    $cart_abandonment->run();

  
}