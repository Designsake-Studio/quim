<?php
/**
 * Register all actions and filters for Bulk genrate Option
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/bulk-generate
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'Wt_Smart_Coupon_Bulk_Generate_Hooks' ) ) {
    class Wt_Smart_Coupon_Bulk_Generate_Hooks extends Wt_Smart_Coupon_Hooks {
       

        function load_hooks() {

            $bulk_generate = new Wt_Smart_Coupon_Bulk_Generate( );

            $this->add_filter( 'wt_smart_coupon_admin_tab_items', $bulk_generate,'add_admin_tab', 9, 1 );
            $this->add_action('wt_smart_coupon_tab_content_bulk-generate', $bulk_generate,'bulk_generate_tab_content',10);
            $this->add_action('wt_smart_coupon_tab_content_create-coupon', $bulk_generate,'generate_bulk_coupon_action',10);


			$this->add_action( 'admin_enqueue_scripts', $bulk_generate,'generate_coupon_styles_and_scripts',11,0);
            $this->add_action('woocommerce_email_classes', $bulk_generate,'add_wt_smart_coupon_emails', 11, 1);
            $this->add_action('wt_smart_coupon_general_settings',$bulk_generate,'bulk_generate_settings');
            $this->add_action('wt_smart_coupon_general_settings_updated', $bulk_generate,'update_bulk_generate_settings', 10);
        }

    }

   $bulk_genrate = new  Wt_Smart_Coupon_Bulk_Generate_Hooks();
   $bulk_genrate->run();
}