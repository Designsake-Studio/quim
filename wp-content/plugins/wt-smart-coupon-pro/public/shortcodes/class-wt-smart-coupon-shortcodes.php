<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if( ! class_exists ( 'WT_Smart_Coupon_Shortcodes' ) ) {
    class WT_Smart_Coupon_Shortcodes {

        public function __construct() {
            add_shortcode( 'wt-smart-coupon', array($this,'display_smart_coupon') );
            add_filter('manage_edit-shop_coupon_columns', array($this,'add_short_code_column' ),10,1 );
            add_action('manage_shop_coupon_posts_custom_column',  array($this,'add_shortcode_column_content'), 10, 2);
        }

         /**
         * Display shotcode for each Coupons on Coupo admin table
         * @since 1.2.1
         */
        function add_short_code_column($defaults) {
            $defaults['wt_short_code'] = __( 'Shortcode', 'wt-smart-coupons-for-woocommerce-pro' );

            return $defaults;
        }
        /**
         * Coupon Admin page content
         * @since 1.2.1
         */
        function add_shortcode_column_content($column_name, $post_ID) {
            if ($column_name == 'wt_short_code') {
                echo '[wt-smart-coupon id='.$post_ID.']';
            }
        }
        
        /**
         * Shortcode for displaying coupon by id
         * @since 1.2.1
         */
        function display_smart_coupon( $atts ) {
            if( ! $atts['id'] ) {
                return __('invalid Coupon','wt-smart-coupons-for-woocommerce-pro');
            }
            $post_type = get_post_type( $atts['id']  );
            if( 'shop_coupon' != $post_type ) {
                return __('invalid Coupon','wt-smart-coupons-for-woocommerce-pro');
            }
            $coupon_title = get_the_title( $atts['id'] );
            $coupon = new WC_Coupon( $atts['id'] );
            $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon );
            $coupon_data['display_on_page'] = 'by_shortcode';
            $coupon_html =  Wt_Smart_Coupon_Public::get_coupon_html( $coupon,$coupon_data );
            return $coupon_html;
        }
    }
}

$short_code = new WT_Smart_Coupon_Shortcodes();
