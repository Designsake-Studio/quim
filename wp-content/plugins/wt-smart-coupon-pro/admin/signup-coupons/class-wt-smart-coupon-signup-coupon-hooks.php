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

if( ! class_exists ( 'Wt_Smart_Coupon_Signup_Coupon_Hooks' ) ) {
    class Wt_Smart_Coupon_Signup_Coupon_Hooks extends Wt_Smart_Coupon_Hooks {


        function load_hooks() {
            $signup_coupon_admin = new Wt_Smart_Coupon_Signup_Coupon();
            $this->add_filter('wt_smart_coupon_action_coupon_items',$signup_coupon_admin,'singup_coupon_tab',10,1);
            $this->add_action('wt_action_coupon_page_content',$signup_coupon_admin,'signup_page_content');
            $this->add_filter('wt_smart_coupon_default_options',$signup_coupon_admin,'singup_coupon_options',11,1);
            $this->add_action( 'user_register', $signup_coupon_admin,'add_coupon_for_new_user', 10, 1 );

            $this->add_action( 'wt_before_action_coupon_tabs',$signup_coupon_admin, 'save_signup_coupon_settings', 10,0 );
            
            $this->add_action('woocommerce_email_classes', $signup_coupon_admin,'add_signup_coupon_email', 10, 1);
            
        }

    }

    $signup_coupon = new  Wt_Smart_Coupon_Signup_Coupon_Hooks();
    $signup_coupon->run();

  
}