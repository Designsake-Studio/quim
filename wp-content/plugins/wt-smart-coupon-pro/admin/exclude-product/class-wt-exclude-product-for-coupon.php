<?php
if (!defined('WPINC')) {
    die;
}
if( ! class_exists ( 'Wt_smart_coupon_exclude_product_from_coupon' ) ) {

    
    class Wt_smart_coupon_exclude_product_from_coupon {
        protected $option;

        public function __construct() {

            add_filter('wt_smart_coupon_default_options',array($this,'add_disabled_product_option'),10,1);
            add_action( 'woocommerce_product_options_general_product_data', array($this,'add_exclude_product_check_box' ) );
        
            add_action( 'woocommerce_process_product_meta', array($this,'save_exclude_product_data'), 10, 1 );
            add_filter( 'woocommerce_coupon_is_valid', array($this,'set_coupon_validity_for_excluded_products'), 10, 2);

            // add_action('wt_smart_coupon_general_settings',array($this,'add_exclude_coupon_settings'));

            // add_action('wt_smart_coupon_general_settings_updated', array($this,'update_exclude_coupon_settings' ), 11 );
           
        }

        /**
         * Add Disabled Product into Smart Coupon options.
         * @since 1.2.1
         */
        function add_disabled_product_option( $default_optons ) {
            $default_optons['exclude_from_coupons'] = array(
                'disabled_products' =>array(),
                'disabled_store_credits' => array()
            );
        }

        /**
         * Get all disabled Products
         * @since 1.2.1
         */
        function get_disabled_product( ) {
            if( empty( $this->option )) {
                $this->option = Wt_Smart_Coupon_Admin::get_option('exclude_from_coupons');
            }
            return $this->option;
        }

        /**
         * Update Disable Product Options
         * @since 1.2.1
         */
        function set_disabled_products( $products = array() ) {
            // if( empty($products) ) {
            //     return;
            // }
            $smart_coupon_option    =   get_option( 'wt_smart_coupon_options' );
            $exclude_from_coupons   =   $smart_coupon_option['exclude_from_coupons'];
            $exclude_from_coupons['disabled_products'] = $products;
            $smart_coupon_option['exclude_from_coupons'] = $exclude_from_coupons;
            update_option('wt_smart_coupon_options',$smart_coupon_option);
            $this->option = $exclude_from_coupons;
         
        }

        /**
         * Update Disable Product Options
         * @since 1.2.1
         */
        function set_disabled_store_credit( $products = array() ) {
            // if( empty($products) ) {
            //     return;
            // }
            $smart_coupon_option    =   get_option( 'wt_smart_coupon_options' );
            $exclude_from_coupons   =   $smart_coupon_option['exclude_from_coupons'];
            $exclude_from_coupons['disabled_store_credits'] = $products;
            $smart_coupon_option['exclude_from_coupons'] = $exclude_from_coupons;
            update_option('wt_smart_coupon_options',$smart_coupon_option);
            $this->option = $exclude_from_coupons;
         
        }


        /**
         * Add Exclude for coupon settings under Product general settings.
         * @since 1.2.1
         */
        function add_exclude_product_check_box(){
            global $post;

            echo '<div class="wt-exclude-product-from-coupon">';
            woocommerce_wp_checkbox( array(
                'id'        => '_wt_disabled_for_coupons',
                'label'     => __('Exclude  from coupons', 'wt-smart-coupons-for-woocommerce-pro'),
                'description' => __('Exclude this product from coupon discounts', 'wt-smart-coupons-for-woocommerce-pro'),
                'desc_tip'  => 'true',
            ) );

            woocommerce_wp_checkbox( array(
                'id'        => '_wt_disabled_for_store_credit',
                'label'     => __('Exclude from store credit', 'wt-smart-coupons-for-woocommerce-pro'),
                'description' => __('Exclude this product from store credit purchases', 'wt-smart-coupons-for-woocommerce-pro'),
                'desc_tip'  => 'true',
            ) );
        
            echo '</div>';;
        }
        

        /**
         * Save exclude Product meta. 
         * @since 1.2.1
         */
        function save_exclude_product_data( $post_id ){
        
            $current_disabled       = isset( $_POST['_wt_disabled_for_coupons'] ) ? 'yes' : 'no';
            $exclude_store_credit   = isset( $_POST['_wt_disabled_for_store_credit'] ) ? 'yes' : 'no';
        
            $excluded_product = $this->get_disabled_product();
            $disabled_products = $excluded_product['disabled_products'];
            $disabled_store_credit = $excluded_product['disabled_store_credits'];

            // Save Disabled coupons
            if( empty($disabled_products) ) {
                if( $current_disabled == 'yes' )
                    $disabled_products = array( $post_id );
            } else {
                if( $current_disabled == 'yes' ) {
                    $disabled_products[] = $post_id;
                    $disabled_products = array_unique( $disabled_products );
                } else {
                    if ( ( $key = array_search( $post_id, $disabled_products ) ) !== false )
                        unset( $disabled_products[$key] );
                }
            }
            update_post_meta( $post_id, '_wt_disabled_for_coupons', $current_disabled );
            $this->set_disabled_products( $disabled_products );

            // Save Disabled StoreCredits
            if( empty($disabled_store_credit) ) {
                if( $exclude_store_credit == 'yes' )
                    $disabled_store_credit = array( $post_id );
            } else {
                if( $exclude_store_credit == 'yes' ) {
                    $disabled_store_credit[] = $post_id;
                    $disabled_store_credit = array_unique( $disabled_store_credit );
                } else {
                    if ( ( $key = array_search( $post_id, $disabled_store_credit ) ) !== false )
                        unset( $disabled_store_credit[$key] );
                }
            }
        
            update_post_meta( $post_id, '_wt_disabled_for_store_credit', $exclude_store_credit );
            $this->set_disabled_store_credit( $disabled_store_credit );
        }
        
        /**
         * Check validity of coupon with exclude product.
         * @since 1.2.1
         */
        function set_coupon_validity_for_excluded_products($valid, $coupon ){

            if( $valid == false ) {
                return $valid;
            }
            $excluded_products = $this->get_disabled_product();
            if( !is_array( $excluded_products['disabled_products'] ) || empty( $excluded_products['disabled_products'])) {
                return $valid;
            }
            
            if( ! count(  $excluded_products['disabled_products'] ) > 0 && ( ! $coupon->is_type('store_credit') ||  ! count(  $excluded_products['disabled_store_credits'] ) > 0 ) ) return $valid;

            global $woocommerce;
            $items = $woocommerce->cart->get_cart();
            if( empty($items) ) {
                return $valid;
            }
            $items_to_check = array();
            foreach( $items as $item ) {
                array_push($items_to_check,$item['product_id']);
            }
        

            if( $coupon->is_type('store_credit') ) {
                $disabled_products = $excluded_products['disabled_store_credits'];
            } else {
                $disabled_products = $excluded_products['disabled_products'];
            }
            
            
            if( !empty( $disabled_products ) ) {
                foreach( $disabled_products as $disabled_product  ) {
                    if ( in_array( $disabled_product, $items_to_check ) ) {
                        $valid = false;
                        break;
                    }
                }
            }
            if ( ! $valid ) {
                throw new Exception( __( 'Sorry, this coupon is not applicable for selected products.', 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
            }
        
            return $valid;
        }

       
       
    }
}

$exclude_prod = new Wt_smart_coupon_exclude_product_from_coupon();