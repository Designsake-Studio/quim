<?php

if (!defined('WPINC')) {
    die;
}
/**
 * Limit maximum discount for persentage coupon.
 *
 * @link       http://www.webtoffee.com
 * @since      1.2.4
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/exclude-product
 */

if( ! class_exists ( 'Wt_smart_Coupon_limit_maximum_discount' ) ) {

    
    class Wt_smart_Coupon_limit_maximum_discount {

        protected $sub_total_for_discounted_item;

        public function __construct() {
            $this->sub_total_for_discounted_item = 0;
            add_action( 'woocommerce_coupon_options_usage_limit',array($this, 'maximum_discount_field' ), 10, 2 );
            add_action( 'woocommerce_coupon_options_save', array($this, 'save_maximum_discount_data' ), 10, 2 );
            //add_action( 'woocommerce_applied_coupon', array($this,'add_discount_details_into_cache'), 5, 1 );
            add_filter( 'woocommerce_coupon_get_discount_amount', array($this,'calculate_maximum_discount'), 20, 5 );
        }

        /**
         * create maximum discount field
         * @since 1.2.4
         */
        function maximum_discount_field( $coupon_id, $coupon ){
            if( $coupon->is_type('percent') || $coupon->is_type('fixed_product') ) {
                $style = '';
            } else {
                $style = 'style="display:none"';
            }
        
            echo '<div id="wt_max_discount"  '.$style.'>';
            $max_discount =  get_post_meta( $coupon_id, '_wt_max_discount', true );
            woocommerce_wp_text_input( array(
                'id'                => '_wt_max_discount',
                'label'             => __( 'Maximum discount value', 'wt-smart-coupons-for-woocommerce-pro' ),
                'placeholder'       => esc_attr__( 'Unlimited discount', 'wt-smart-coupons-for-woocommerce-pro' ),
                'description'       => __( 'Use this option to set a cap on the discount value especially for percentage discounts. e.g, you may provide a 5% discount coupon for a product but with a maximum discount upto $10.', 'wt-smart-coupons-for-woocommerce-pro' ),
                'type'              => 'number',
                'desc_tip'          => true,
                'class'             => 'short',
                'custom_attributes' => array(
                    'step' 	=> 1,
                    'min'	=> 0,
                ),
                'value' => $max_discount ? $max_discount : '',
            ) );
            echo '</div>';
        
        }
        /**
         * Save maximum discount meta.
         * @since 1.2.4
         */
        function save_maximum_discount_data( $coupon_id, $coupon ) {
        
            update_post_meta( $coupon_id, '_wt_max_discount', wc_format_decimal( $_POST['_wt_max_discount'] ) );
        
        }
        /**
         * Calculate discounting amount
         * @since 1.2.4
         */
        function calculate_maximum_discount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {

            if( ! $coupon->is_type('percent') && ! $coupon->is_type('fixed_product') ) {
                return ($discount);
            }
            $cart_discount_details = isset($cart_item['wt_discount_details'])? $cart_item['wt_discount_details'] : array();
            $max_discount = get_post_meta( $coupon->get_id(), '_wt_max_discount', true );


            if ( is_numeric( $max_discount ) && $max_discount > 0 && ! is_null( $cart_item ) && WC()->cart->subtotal_ex_tax ) {
                $cart_item_qty = is_null( $cart_item ) ? 1 : $cart_item['quantity'];
                
                $sub_total_for_available_product = $this->get_allowed_prodcuts_from_cart( $coupon ) ;
                if ( wc_prices_include_tax() ) {
                    $product_price = wc_get_price_including_tax( $cart_item['data'] );
                } else {
                    $product_price = wc_get_price_excluding_tax( $cart_item['data'] );
                }
                $discount_percent = ( $product_price * $cart_item_qty ) / ( $sub_total_for_available_product  );


                $_discount = ( $max_discount * $discount_percent );
                $discount = min( $_discount, $discount );

            }
        
            return ($discount);
        }

        /**
         * Get total price of allowed products for a coupon
         * @since 1.2.4
         */
        function get_allowed_prodcuts_from_cart( $coupon ) {
            $cart = WC()->cart->get_cart() ;
            //$coupon = new WC_Coupon($coupon);
            $coupon_id      = $coupon->get_id();

            $sum_allowed_product = 0;
            // used to apply any reduction before calculating discount (will implement later ).
            $pre_discount_to_sub_total = apply_filters( 'wt_pre_applied_discount_into_sub_total', 0 );

            foreach(  $cart as $cart_item_key => $cart_item  ) {
                $cart_item_qty = is_null( $cart_item ) ? 1 : $cart_item['quantity'];
                $_product = $cart_item['data'];                
                if( $coupon->is_valid_for_product( $_product )) {
                    $allowed_products[] = $_product->get_id();
                    if ( wc_prices_include_tax() ) {
                        $sum_allowed_product += wc_get_price_including_tax( $cart_item['data'] ) * $cart_item_qty;
                    } else {
                        $sum_allowed_product += wc_get_price_excluding_tax( $cart_item['data'] ) * $cart_item_qty;
                    }
                }
            }

            return $sum_allowed_product;
        }
    }

    $limit  = new Wt_smart_Coupon_limit_maximum_discount();
}