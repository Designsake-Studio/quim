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

if( ! class_exists ( 'Wt_Smart_Coupon_URL_Coupon_Hooks' ) ) {
    class Wt_Smart_Coupon_URL_Coupon_Hooks extends Wt_Smart_Coupon_Hooks {
        
        function load_hooks() {

            $url_coupon = new Wt_Smart_Coupon_URL_Coupon();
            $this->add_action('wp_loaded',$url_coupon,'wt_apply_smart_coupon');
            if (  apply_filters( 'wt_need_to_url_coupon_instructions', true ) ) {
                $this->add_action('wt_smart_coupon_general_settings',$url_coupon,'add_coupon_instruction',11);
            }
            $this->add_action( 'woocommerce_after_calculate_totals', $url_coupon, 'may_be_re_apply_coupon_from_cookie',11 );
        }

    }

    $store_credit = new  Wt_Smart_Coupon_URL_Coupon_Hooks();
    $store_credit->run();

  
}