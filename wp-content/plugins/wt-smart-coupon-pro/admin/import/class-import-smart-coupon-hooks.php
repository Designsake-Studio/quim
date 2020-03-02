<?php
/**
 * Register all actions and filters for import Coupon
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

if( ! class_exists ( 'Wt_Smart_Coupon_import_Hooks' ) ) {
    class Wt_Smart_Coupon_import_Hooks extends Wt_Smart_Coupon_Hooks {

        /**
         * Load Required Hooks.
         * @since 1.2.1
         */

        function load_hooks() {
            $import_coupon  = new Wt_Smart_Coupon_import();
            $this->add_filter( 'wt_smart_coupon_admin_tab_items', $import_coupon,'add_admin_tab', 10, 1 );
            $this->add_action('wt_smart_coupon_tab_content_import_coupon',$import_coupon,'import_coupon_content',10);
            $this->add_action('wp_ajax_wt_import_csv_coupon_rows',$import_coupon,'import_start',10);

        }

    }

   $import_coupon = new  Wt_Smart_Coupon_import_Hooks();
   $import_coupon->run();
}