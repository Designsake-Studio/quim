<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/public
 * @author     markhf <info@webtoffee.com>
 */

if( ! class_exists ( 'Wt_Smart_Coupon_Public' ) ) {
    class Wt_Smart_Coupon_Public {

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        private $plugin_name;

        /**
         * The version of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $version    The current version of this plugin.
         */
        private $version;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         * @param      string    $plugin_name       The name of the plugin.
         * @param      string    $version    The version of this plugin.
         */
        public function __construct($plugin_name, $version) {

            $this->plugin_name = $plugin_name;
            $this->version = $version;
   
        }

        

        /**
         * Register the stylesheets for the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function enqueue_styles() {

            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wt-smart-coupon-public.css', array(), $this->version, 'all');
            wp_enqueue_style( 'dashicons' );
        }

        /**
         * Register the JavaScript for the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts() {

            $_nonces = array(
                'public' => wp_create_nonce( 'wt_smart_coupons_public' ),
                'apply_coupon' => wp_create_nonce( 'wt_smart_coupons_apply_coupon' ),
            );
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wt-smart-coupon-public.js', array('jquery'), $this->version, false);
            wp_localize_script($this->plugin_name,'WTSmartCouponOBJ',array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ,'nonces' => $_nonces ) );
        }

        /**
         * Filter Function updating woocommcerce coupon validation.
         * @param $valid
         * @param $coupon - Coupon code
         * @since 1.0.0
         */
        public function wt_woocommerce_coupon_is_valid($valid, $coupon) {
            global $woocommerce;
            
            if (!$valid) {
                return false;
            }

            $coupon_id                   = $coupon->get_id();
            $coupon_shipping_method_ids = get_post_meta($coupon_id, '_wt_sc_shipping_methods',true);

            if( ''!=$coupon_shipping_method_ids && ! is_array( $coupon_shipping_method_ids ) ) {
                $coupon_shipping_method_ids = explode(',',$coupon_shipping_method_ids);
            } else {
                $coupon_shipping_method_ids = array();
            }
            
            $coupon_payment_method_ids  = get_post_meta($coupon_id, '_wt_sc_payment_methods',true);
            if( ''!= $coupon_payment_method_ids && ! is_array( $coupon_payment_method_ids ) ) {
                $coupon_payment_method_ids = explode(',',$coupon_payment_method_ids);
            } else {
                $coupon_payment_method_ids = array();
            }
        
            $_wt_sc_user_roles         = get_post_meta($coupon_id, '_wt_sc_user_roles',true);
            if( ''!= $_wt_sc_user_roles && ! is_array( $_wt_sc_user_roles ) ) {
                $_wt_sc_user_roles = explode(',',$_wt_sc_user_roles);
            } else {
                $_wt_sc_user_roles = array();
            }
            
            // shipping method check
            if ( sizeof($coupon_shipping_method_ids ) > 0) {

                $chosen_shipping_methods = WC()->session->get('chosen_shipping_methods');
                $chosen_shipping = $chosen_shipping_methods[0];
                $chosen_shipping = substr($chosen_shipping, 0, strpos($chosen_shipping, ":"));
                if (!in_array($chosen_shipping, $coupon_shipping_method_ids)) {
                    $valid = false;
                }
    
                if ( ! $valid ) {
                    throw new Exception( __( 'Sorry, this coupon is not applicable to selected shipping method', 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
                }
            }

            // payment method check
            if (sizeof($coupon_payment_method_ids) > 0) {

                $chosen_payment_method = isset(WC()->session->chosen_payment_method) ? WC()->session->chosen_payment_method : array();
                
                if (!in_array($chosen_payment_method, $coupon_payment_method_ids)) {
                    $valid = false;
                }
    
                if ( ! $valid ) {
                    throw new Exception( __( 'Sorry, this coupon is not applicable to selected Payment method', 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
                }
            }

            // user role check
            if (sizeof($_wt_sc_user_roles) > 0) {

                $user = wp_get_current_user();
                $user_roles = (array) $user->roles;

                if (!array_intersect($_wt_sc_user_roles, $user_roles)) {
                    $valid = false;
                }
    
                if ( ! $valid ) {
                    throw new Exception( __( 'Sorry, this coupon is not applicable for your Role', 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
                }
            }


            // Available location check

            $_wt_coupon_available_location  = get_post_meta($coupon_id, '_wt_coupon_available_location',true);
            if( ''!= $_wt_coupon_available_location && ! is_array( $_wt_coupon_available_location ) ) {
                $_wt_coupon_available_location = explode(',',$_wt_coupon_available_location);
            } else {
                $_wt_coupon_available_location = array();
            }
             if (sizeof($_wt_coupon_available_location) > 0) {
                $_wt_need_check_location_in  = get_post_meta($coupon_id, '_wt_need_check_location_in',true);
                if( $_wt_need_check_location_in == 'billing' ) {
                    $choosed_location = WC()->session->customer['country'];
                } else {
                    $choosed_location = WC()->session->customer['shipping_country'];
                }

                if (!in_array($choosed_location, $_wt_coupon_available_location)) {
                    $valid = false;
                }
    
                if ( ! $valid ) {
                    throw new Exception( __( 'Sorry, this coupon is not applicable to selected Location', 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
                }
            }



            // Usage restriction "AND" for ptoducts
            $wt_product_condition = get_post_meta($coupon_id,'_wt_product_condition',true);
            if( $wt_product_condition == 'and') {
                $valid = true;
                $coupon_products = $coupon->get_product_ids() ;
                if ( count( $coupon_products ) > 0 ) {
                    global $woocommerce;
                    $items = $woocommerce->cart->get_cart();
                    $items_to_check = array();
                    foreach( $items as $item ) {
                        array_push($items_to_check,$item['product_id']);
                    }
                    foreach( $coupon_products as $coupon_product  ) {
                        if ( !in_array( $coupon_product, $items_to_check ) ) {
                            $valid = false;
                            break;
                        }
                    }

                    if ( ! $valid ) {
                        throw new Exception( __( 'Sorry, this coupon is not applicable for selected products.', 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
                    }
                }
            }

            // Usage restriction "AND" for Categories
            $wt_category_condition = get_post_meta($coupon_id,'_wt_category_condition',true);
            if( $wt_category_condition == 'and') {
                $valid = true;
                global $woocommerce;
                $coupon_categores = $coupon->get_product_categories() ;
                $items = $woocommerce->cart->get_cart();
                $items_to_check = array();
                foreach( $items as $item ) {
                    $product_cats = wc_get_product_cat_ids( $item['product_id'] );
                    $items_to_check = array_merge( $items_to_check,$product_cats );
                }

                foreach( $coupon_categores as $coupon_categry ) {
                    if ( !in_array( $coupon_categry, $items_to_check ) ) {
                        $valid = false;
                        break;
                    }

                }

                if ( ! $valid ) {
                    throw new Exception( __( 'Sorry, this coupon is not applicable for selected products.', 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
                }


            }

            // Quantity of matching Products
            $wt_min_matching_product_qty = get_post_meta($coupon_id,'_wt_min_matching_product_qty',true);
            $wt_max_matching_product_qty = get_post_meta($coupon_id,'_wt_max_matching_product_qty',true);

            if( $wt_min_matching_product_qty > 0 ||  $wt_max_matching_product_qty >0 ) {
                $quantity_of_matching_product = $this->get_quantity_of_matching_product( $coupon );
                if( $wt_min_matching_product_qty > 0 && $quantity_of_matching_product < $wt_min_matching_product_qty ) {
                    $valid = false;
                    throw new Exception(
                        sprintf( __( 'The minimum quantity of matching products for this coupon is %s.', 'wt-smart-coupons-for-woocommerce-pro' ), $wt_min_matching_product_qty ),110

                    );
                }
                if( $wt_max_matching_product_qty >0 && $quantity_of_matching_product > $wt_max_matching_product_qty ) {            
                    $valid = false;                
                    throw new Exception(
                        sprintf( __( 'The maximum quantity of matching products for this coupon is %s.', 'wt-smart-coupons-for-woocommerce-pro' ), $wt_max_matching_product_qty ),111
                    );
                }
            }

            // Subtotal of matching products
            $wt_min_matching_product_subtotal = get_post_meta($coupon_id,'_wt_min_matching_product_subtotal',true);
            $wt_max_matching_product_subtotal = get_post_meta($coupon_id,'_wt_max_matching_product_subtotal',true);
            $subtotal_of_matching_product = $this->get_sub_total_of_matching_products($coupon);
            $discount_amount =  WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );
            if( $wt_min_matching_product_subtotal > 0 ||  $wt_max_matching_product_subtotal >0 ) {
                if( $wt_min_matching_product_subtotal > 0 && $subtotal_of_matching_product    < $wt_min_matching_product_subtotal  ) {
                    if(in_array($coupon->get_code(), $woocommerce->cart->get_applied_coupons()) ) {
                        if( $subtotal_of_matching_product + $discount_amount < $wt_min_matching_product_subtotal ) {
                            $valid = false;
                            throw new Exception(
                                sprintf( __( 'The minimum subtotal of matching products for this coupon is %s.', 'wt-smart-coupons-for-woocommerce-pro' ), Wt_Smart_Coupon_Admin::get_formatted_price( $wt_min_matching_product_subtotal ) ),112
        
                            );
                        }
                    } else {
                        $valid = false;
                        throw new Exception(
                            sprintf( __( 'The minimum subtotal of matching products for this coupon is %s.', 'wt-smart-coupons-for-woocommerce-pro' ), Wt_Smart_Coupon_Admin::get_formatted_price( $wt_min_matching_product_subtotal ) ),112
    
                        );
                    }
                    
                   
                }
                if( $wt_max_matching_product_subtotal > 0 && $subtotal_of_matching_product > $wt_max_matching_product_subtotal ) {            
                    $valid = false;                
                    throw new Exception(
                        sprintf( __( 'The maximum subtotal of matching products for this coupon is %s.', 'wt-smart-coupons-for-woocommerce-pro' ), Wt_Smart_Coupon_Admin::get_formatted_price(  $wt_max_matching_product_subtotal ) ),113
                    );
                }
            }

            

            return $valid;
        }


      
        /**
         * Get sub total for mtching product - used for coupon validation
         * @since 1.0.0
         */
        public function get_sub_total_of_matching_products( $coupon ) {
            global $woocommerce;        
            $coupon_products =  $coupon->get_product_ids();

            $coupon_categores = $coupon->get_product_categories() ;
            $items = $woocommerce->cart->get_cart();
            $total = 0;
            if( count( $coupon_products ) > 0 || count($coupon_categores) > 0  ) { // check with matching products by include condition.
                foreach( $items as $item ) {
                    
                    $product_cats = wc_get_product_cat_ids( $item['product_id'] );
                    if( ( count( $coupon_products ) && in_array( $item['product_id'],$coupon_products ) ) ||  ( count($coupon_categores) && count( array_intersect($coupon_categores,$product_cats) ) > 0 ) ){
                        if( isset( $item['line_subtotal'] ) ) {
                            $total += $item['line_subtotal'];
                        }

                    }
                
                }
            } else {
                foreach( $items as $item ) {
                    if( isset( $item['line_subtotal'] ) ) {
                        $total += $item['line_subtotal'];
                    }
                }
            }

            return $total;
        }
        /**
         * Get Quantity of matching products - Used for Coupon validation.
         * @since 1.0.0
         */
        public function get_quantity_of_matching_product( $coupon ) {
            global $woocommerce;        
            $coupon_products =  $coupon->get_product_ids();

            $coupon_categores = $coupon->get_product_categories() ;
            $items = $woocommerce->cart->get_cart();
            $qty = 0;
            if( count( $coupon_products ) > 0 || count($coupon_categores) > 0  ) { // check with matching products by include condition.
                foreach( $items as $item ) {
                    $product_cats = wc_get_product_cat_ids( $item['product_id'] );
                    if( ( count( $coupon_products ) && in_array( $item['product_id'],$coupon_products ) ) || ( count($coupon_categores) && count( array_intersect($coupon_categores,$product_cats) ) > 0 ) ){
                        $qty += $item['quantity'];

                    }
                
                }
            } else {
                foreach( $items as $item ) {
                    $qty += $item['quantity'];
                }
            }
            return $qty;
        }



        

        /**
         * Get formatted Meta values of a coupon.
         * @since 1.0.0
         */
        public static function get_coupon_meta_data( $coupon ) {

            if( !$coupon || !is_a ( $coupon,'WC_Coupon') ) {
                return;
            }

            $discount_types = wc_get_coupon_types();
            $coupon_data = array();
            $coupon_amount = $coupon->get_amount();
            switch( $coupon->get_discount_type() ) {
                case 'fixed_cart':
                    $coupon_data['coupon_type']     = __( 'Cart Discount', 'wt-smart-coupons-for-woocommerce-pro' );
                    $coupon_data['coupon_amount']   = Wt_Smart_Coupon_Admin::get_formatted_price( $coupon_amount ) ;
                    break;

                case 'fixed_product':
                    $coupon_data['coupon_type']     = __( 'Product Discount', 'wt-smart-coupons-for-woocommerce-pro' );
                    $coupon_data['coupon_amount']   = Wt_Smart_Coupon_Admin::get_formatted_price( $coupon_amount );
                    break;

                case 'percent_product':
                    $coupon_data['coupon_type']     = __( 'Product Discount', 'wt-smart-coupons-for-woocommerce-pro' );
                    $coupon_data['coupon_amount']   = $coupon_amount . '%';
                    break;

                case 'percent':
                    $coupon_data['coupon_type'] = __( 'Cart Discount', 'wt-smart-coupons-for-woocommerce-pro' );
                    $coupon_data['coupon_amount'] = $coupon_amount . '%';
                    break;
                case 'store_credit':
                    $coupon_data['coupon_type'] = $discount_types[ $coupon->get_discount_type() ];
                    $coupon_data['coupon_amount'] = Wt_Smart_Coupon_Admin::get_formatted_price( $coupon_amount );
                    break;

                default:

                    $coupon_data['coupon_type'] = $discount_types[ $coupon->get_discount_type() ];
                    $coupon_data['coupon_amount'] = $coupon_amount;
                    break;

            }

            if( $coupon_amount == 0 && $coupon->get_free_shipping() ) {
                $coupon_data['coupon_type'] = __('Free shipping','wt-smart-coupons-for-woocommerce-pro');
		        $coupon_data['coupon_amount'] = '';
            }

            $free_products  = get_post_meta( $coupon->get_id(), '_wt_free_product_ids', true );
            
            $free_products = array_filter( explode(',',$free_products) );
            if( $coupon_amount == 0 && '' != $free_products && ! empty( $free_products ) ) {
                
                $coupon_data['coupon_type'] =  __('Free products','wt-smart-coupons-for-woocommerce-pro');
                $coupon_data['coupon_amount'] = '';
            }


            $coupon_data['coupon_expires']      = $coupon->get_date_expires();
            $coupon_data['email_restriction']   = $coupon->get_email_restrictions();
            $coupon_data['coupon_id']           = $coupon->get_id();
            return apply_filters( 'wt_smart_coupon_meta_data', $coupon_data );
        }
        
        /**
         * Get formattd Expiration date of a coupon.
         * @since 1.0.0
         */
        public static function get_expiration_date_text( $expiry_date ) {
            
            $expiry_days = ( ( $expiry_date - time() )/( 24*60*60 ) );
            if( $expiry_days  > 0 ) {
                $expiry_days = (int) $expiry_days ;
            }

            if( $expiry_days < 0   ) {
                $expires_string = 'expired';
            } elseif( $expiry_days < 1   ) {
                $expires_string = __( 'Expires Today ', 'wt-smart-coupons-for-woocommerce-pro' );
            } elseif ( $expiry_days < 31 ) {
                $expires_string = __( 'Expires in ', 'wt-smart-coupons-for-woocommerce-pro' ) . $expiry_days . __( ' days', 'wt-smart-coupons-for-woocommerce-pro' );
            } else {
                $expires_string = __( 'Expires on ', 'wt-smart-coupons-for-woocommerce-pro' ) . esc_html( date_i18n( get_option( 'date_format', 'F j, Y' ), $expiry_date ) );
            }
            return $expires_string;
        }

        /**
         * Get all coupons used by a customer in previous orders.
         * @since 1.0.0
         */
        public static function get_coupon_used_by_a_customer( $user,$coupon_code = '', $return = 'COUPONS' ) {
            global $current_user,$woocommerce,$wpdb;

            if( !$user ) {
                $user = wp_get_current_user();
            }
            $coupon_used = array();
            $customer_id = $user->ID;
            $args = array(
                'numberposts' => -1,
                'meta_key' => '_customer_user',
                'meta_value'	=> $customer_id,
                'post_type' => 'shop_order',
                'post_status' => 'any'
            );
            $customer_orders = get_posts($args);
            if ($customer_orders) :
                foreach ($customer_orders as $customer_order) :
                    $order = wc_get_order( $customer_order->ID );
                    if( Wt_Smart_Coupon::wt_cli_is_woocommerce_prior_to( '3.7' ) ) {
                        $coupons  = $order->get_used_coupons();
                    } else {
                        $coupons  = $order->get_coupon_codes();
                    }
                    if( $coupons ) {
                        $coupon_used = array_merge( $coupon_used, $coupons );
                    }
                endforeach;

                if( $return =='NO_OF_TIMES' && $coupon_code != '' ) {
                    $count_of_used = array_count_values($coupon_used);
                    
                    return isset( $count_of_used[ $coupon_code ] )? $count_of_used[ $coupon_code ] : 0 ;

                }
                return apply_filters('wt_smart_coupon_used_coupons',array_unique( $coupon_used ),$user );

            else :
                return false;
            endif;
        }


        
        /**
         * Get available coupons
         * @since 1.1.0
         */

         public static function get_available_coupons_for_user( $user = '', $section = 'my_account' ) {
            global $wpdb;
            if( !$user ) {
                $user= wp_get_current_user();
            }
            if( $user ) {
                $user_id = $user->ID; 
                $email = $user->user_email;
            }


            $coupons_available_in_any_page  = $wpdb->get_results("SELECT meta.`post_id`,meta.`meta_value` FROM `" . $wpdb->postmeta . "` meta WHERE  ( meta.`meta_key` =  '_wc_make_coupon_available' AND meta.`meta_value` != '' ) ");

            $available_in_my_account    = array();
            $available_in_cart          = array();
            $available_in_checkout      = array();
            if( !empty($coupons_available_in_any_page) ) {
                foreach( $coupons_available_in_any_page as $coupons ) {

                    if( in_array( 'my_account',explode( ',',$coupons->meta_value) ) ) {
                        $available_in_my_account[] = $coupons->post_id;
                    }

                    if( in_array( 'cart',explode( ',',$coupons->meta_value) ) ) {
                        $available_in_cart[] = $coupons->post_id;
                    }

                    if( in_array( 'checkout',explode( ',',$coupons->meta_value) ) ) {
                        $available_in_checkout[] = $coupons->post_id;
                    }
                }
            }
           

            switch( $section  ) {
                case 'checkout' :
                    $couponarrayfinal = array_unique( $available_in_checkout );
                    break;
                case 'cart' :
                    $couponarrayfinal = array_unique( $available_in_cart );
                    break;
                default : // My Account
                    // check coupons with old meta
                    $available_in_my_account_old_meta = $wpdb->get_results("SELECT meta.`post_id` FROM `" . $wpdb->postmeta . "` meta WHERE  ( meta.`meta_key`  =  '_wt_make_coupon_available_in_myaccount' AND meta.`meta_value` = 1) ");
                    foreach( $available_in_my_account_old_meta as $coupon ) {
                        $available_in_my_account[] = $coupon->post_id;
                    }

                    $couponarrayfinal = array_unique( $available_in_my_account );
                    break;
                


            }
            if( $user && $email ) {
                $coupon_specific_user = array();
                $coupon_specific_for_user = $wpdb->get_results("SELECT meta.`post_id` FROM `" . $wpdb->postmeta . "` meta WHERE  ( meta.`meta_key` =  'customer_email' AND meta.`meta_value` LIKE '%" . $email . "%' ) ");
                foreach( $coupon_specific_for_user as $coupon ) {
                    $coupon_specific_user[] = $coupon->post_id;
                }
                $coupon_specific_user = apply_filters( 'wt_coupon_added_email_restriction_to_the_user', $coupon_specific_user,$email );
                $couponarrayfinal = array_unique( array_merge( $coupon_specific_user,$couponarrayfinal ) );
            }
            
           
           
            if( empty ( $couponarrayfinal ) ) {
                $coupons = array();
            } else {
                $couponargs = array(
                    'post_type' => 'shop_coupon',
                    'post__in' => $couponarrayfinal,
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'posts_per_page' => '-1'
                );

                $coupons = get_posts($couponargs);
            }
            return apply_filters('wt_available_coupons_for_user',$coupons,$user,$section);
        }


        /**
         * Action for displaying avalable coupon in cart page.
         * @since 1.1.0
         */
        public function display_available_coupon_in_cart() {
            // $is_enabled = Wt_Smart_Coupon_Admin::get_option('wt_display_coupons_in_cart_checkout');
            // if( !$is_enabled ) {
            //     return false;
            // }
            $current_user = wp_get_current_user();
            $coupons = self::get_available_coupons_for_user(  $current_user , 'cart' );
            $i = 0;
            foreach( $coupons as $coupon ) {
                $coupon_obj = new WC_Coupon( $coupon->post_title );

                if( $coupon_obj->get_amount() <= 0 ) {
                    $give_away = new WT_Giveaway_Free_Product();
                    if( empty( $give_away->get_free_product_for_a_coupon( $coupon_obj->get_code() ) ) ) {
                        continue;
                    }
                }

                $coupon_data  = self::get_coupon_meta_data( $coupon_obj );

                if( $coupon_data['coupon_expires'] ) {
                    $exp_date =  $coupon_data['coupon_expires']->getTimestamp();
                    $expire_text = self::get_expiration_date_text( $exp_date );
                    if( $expire_text == 'expired') {
                        continue;
                    }
                }
                if( !empty( $coupon_data['email_restriction'] ) && ( ! is_user_logged_in() || ! in_array( $current_user->user_email,$coupon_data['email_restriction'] ) )  ) {
                    continue;
                }
                if( $i++ == 0 ) {
                    echo '<div class="wt_coupon_wrapper">';
                }
                $coupon_data['display_on_page'] = 'cart_page';
                echo self::get_coupon_html( $coupon,$coupon_data );
            }
            if( $i > 0 ){
                echo '</div>';
            }
        }

         /**
         * Action for displaying avalable coupon in checkout page.
         * @since 1.1.0
         */
        public function display_available_coupon_in_checkout() {

            // $is_enabled = Wt_Smart_Coupon_Admin::get_option('wt_display_coupons_in_cart_checkout');
            // if( !$is_enabled ) {
            //     return false;
            // }
            $current_user = wp_get_current_user();
            $coupons = self::get_available_coupons_for_user( $current_user, 'checkout' );
            $i = 0;
            foreach( $coupons as $coupon ) {
                $coupon_obj = new WC_Coupon( $coupon->post_title );
                $coupon_data  = self::get_coupon_meta_data( $coupon_obj );

                if( $coupon_data['coupon_expires'] ) {
                    $exp_date =  $coupon_data['coupon_expires']->getTimestamp();
                    $expire_text = self::get_expiration_date_text( $exp_date );
                    if( $expire_text == 'expired') {
                        continue;
                    }
                }
                if( !empty( $coupon_data['email_restriction'] ) && ( ! is_user_logged_in() || ! in_array( $current_user->user_email,$coupon_data['email_restriction'] ) )  ) {
                  continue;
                }
                if( $i++ == 0 ) {
                    echo '<div class="wt_coupon_wrapper">';
                }
                $coupon_data['display_on_page'] = 'checkout_page';
                echo self::get_coupon_html( $coupon,$coupon_data );
            }
            if( $i > 0 ){
                echo '</div>';
            }
        }
        

        /**
         * get coupon html based on current style
         * @since 1.1.0
         */

        public static function get_coupon_html( $coupon,$coupon_data,$coupon_type = "available_coupon" ) {
            $admin_options = Wt_Smart_Coupon_Admin::get_option('wt_coupon_styles');
            $display_on_class = (isset($coupon_data['display_on_page']))? $coupon_data['display_on_page'] : ''; // for apply on click.
            $coupon_style   = isset( $admin_options[$coupon_type] )? $admin_options[$coupon_type] : $admin_options['available_coupon'];
            $style_name     = $coupon_style['style'];
            $colors         = $coupon_style['color'];
            $block_title    = '';
            switch( $coupon_type ) {
                case 'expired_coupon' : 
                    $coupon_class   = ' used-coupon expired';
                    break;
                case 'used_coupon' :
                    $coupon_class   = ' used-coupon';
                    
                    break;
                default :
                    $coupon_class = 'active-coupon';
                    $block_title = 'title = "'.__('Click to apply coupon','wt-smart-coupons-for-woocommerce-pro').'"';
                    
            }

            $coupon_class .= ' '.$display_on_class;
            $is_store_credit = false;
            if( $coupon !== -1 ) { // skip the preview mode
                if( $coupon instanceof WC_Coupon ) {
                    $coupon =  get_post( $coupon->get_id() );
                }
                
                $coupon_obj = new WC_Coupon( $coupon->post_title );
                if( $coupon_data['coupon_expires'] ) {
    
                    $exp_date =  $coupon_data['coupon_expires']->getTimestamp();
                    $expire_text = self::get_expiration_date_text( $exp_date );
                    
                } else{
                    $expire_text = '';
                }
                $coupon_desc        = $coupon_obj->get_description();
                $coupon_code        = $coupon_obj->get_code();
                $is_store_credit    = ( $coupon_obj->is_type('store_credit') )? true: false;
                $coupon_id          =  $coupon_data['coupon_id'];
                $store_credit_title = __('Credit history','wt-smart-coupons-for-woocommerce-pro');

            } else {
                $coupon_code = $expire_text = '';
                $block_title = '';

            }

            // remove click to apply title for store credit

            // if( $is_store_credit ) {
            //     $block_title = '';
            // }

            if( '' == $display_on_class || 'by_shortcode' == $display_on_class ) {
                $block_title = '';
            }
            
            
            switch( $style_name ) {
    
                case 'stitched_padding' :
                        $style= 'style = "background:'.$colors[0].';color:'.$colors[2].';border:border:2px dashed '.$colors[1].';box-shadow: 0 0 0 4px '.$colors[0].',2px 1px 6px 4px rgba(10, 10, 0, 0)"';
                        $coupon_html =  '
                        <div class="wt-single-coupon  '.$coupon_class.' '. $style_name .'"  '.$style.' '.$block_title.'  >
                            <div class="wt-coupon-content">
                                <div class="wt-coupon-amount">
                                    <span class="amount">'.$coupon_data['coupon_amount'].'</span>
                                    <span>'.$coupon_data['coupon_type'].'</span>
                                </div>
                                <div class="wt-coupon-code"> 
                                    <code>' .$coupon_code.'</code>
                                </div>';
                                if(  'used_coupon' != $coupon_type  && '' != $expire_text ) { 
                                    $coupon_html .= '<div class="wt-coupon-expiry">'.$expire_text.'</div>';
                                }
                                if( 'available_coupon' == $coupon_type &&'' != $coupon_desc ) {
                                    $coupon_html .= '<div class="coupon-desc-wrapper">
                                        <i class="info"> i </i>
                                        <div class="coupon-desc">'.$coupon_desc.'</div>
                                    </div>';
                                }

                                if( isset( $coupon_data['display_on_page'] ) && $coupon_data['display_on_page'] !='credit_meta' && $is_store_credit ) {
                                    $coupon_html .= '<div class="coupon-history">
                                            <a title="'.$store_credit_title.'" class="credit_history" href="'.self::get_store_credit_url($coupon_id).'"><span class="dashicons dashicons-backup"></span> </a>
                                        </div>';
                                }
                                $coupon_html .= '
                            </div>
                        </div>';
                        break;
                case 'stitched_edge' :
                    $style = 'style="background: '.$colors[0].';color: '.$colors[2].'; border:2px dashed  '.$colors[1].';box-shadow:none"';
                    $coupon_html = '<div class="wt-single-coupon  '.$coupon_class.' '.$style_name.'"  '.$style.' '.$block_title.' >
                        <div class="wt-coupon-content">
                            <div class="wt-coupon-amount">
                                <span class="amount">'.$coupon_data['coupon_amount'].'</span>
                                <span> '.$coupon_data['coupon_type'].'</span>
                            </div>
                            <div class="wt-coupon-code"> 
                                <code>'.$coupon_code.'</code>
                            </div>';
                            if(  'used_coupon' != $coupon_type  && '' != $expire_text ) { 
                                $coupon_html .= '<div class="wt-coupon-expiry">'.$expire_text.'</div>';
                            }
                            if( 'available_coupon' == $coupon_type &&'' != $coupon_desc ) {
                                $coupon_html .= '<div class="coupon-desc-wrapper">
                                    <i class="info"> i </i>
                                    <div class="coupon-desc">'.$coupon_desc.'</div>
                                </div>';
                            }
                            if( isset( $coupon_data['display_on_page'] ) && $coupon_data['display_on_page'] !='credit_meta' && $is_store_credit ) {
                                $coupon_html .= '<div class="coupon-history">
                                        <a title="'.$store_credit_title.'"  class="credit_history" href="'.self::get_store_credit_url($coupon_id) .'"> <span class="dashicons dashicons-backup"></span> </a>
                                    </div>';
                            }
                            $coupon_html .= '
                        </div>
                    </div>';
                    break;
                case 'ticket_style' : 
                    $style1 = 'style="background:'.$colors[0] .'; border:1px dotted '.$colors[1].'; color: '.$colors[2].'"';
                    $style2 = 'style="color: '.$colors[1] .'"';
                    $coupon_html ='<div class="wt-single-coupon  '.$coupon_class.' '.$style_name.'" '.$style1.' '.$block_title.' >
                                <div class="wt-coupon-content">
                                    <div class="wt-coupon-amount"  '.$style2.' >
                                        <span class="amount">'.$coupon_data['coupon_amount'].'</span>
                                    </div>
                                    <div class="wt-coupon-code"> 
                                        <span class="discount_type"> '.$coupon_data['coupon_type'].' </span>
                                        <code>' .$coupon_code. '</code>
                                    </div>';
                                    
                                    if( 'available_coupon' == $coupon_type &&'' != $coupon_desc ) {
                                        $coupon_html .= '<div class="coupon-desc-wrapper">
                                            <i class="info"> i </i>
                                            <div class="coupon-desc">'.$coupon_desc.'</div>
                                        </div>';
                                    }
                                    if( isset( $coupon_data['display_on_page'] ) && $coupon_data['display_on_page'] !='credit_meta' && $is_store_credit ) {
                                        $coupon_html .= '<div class="coupon-history">
                                                <a title="'.$store_credit_title.'"  class="credit_history" href="'.self::get_store_credit_url($coupon_id).'"> <span class="dashicons dashicons-backup"></span> </a>
                                            </div>';
                                    }
                                    $coupon_html .= '
                                </div>
                            </div>';
                        break;
    
                case 'plane_coupon' :
					$style  = 'style="background:'.$colors[0].'; color:'.$colors[1].';"';
                   
                    $coupon_html = '<div class="wt-single-coupon  '.$coupon_class.' '.$style_name.'"  '.$style.' '.$block_title.' >
                                    <div class="wt-coupon-content">
                                        <div class="wt-coupon-amount">
                                            <span class="amount">'.$coupon_data['coupon_amount'].'</span>
                                            <span> '.$coupon_data['coupon_type'].' </span>
                                        </div>
                                        <div class="wt-coupon-code"> 
                                            <code>' .$coupon_code. '</code>
                                        </div>';
                                        if(  'used_coupon' != $coupon_type  && '' != $expire_text ) { 
                                            $coupon_html .= '<div class="wt-coupon-expiry">'.$expire_text.'</div>';
                                        }
                                        if( 'available_coupon' == $coupon_type &&'' != $coupon_desc ) {
                                            $coupon_html .= '<div class="coupon-desc-wrapper">
                                                <i class="info"> i </i>
                                                <div class="coupon-desc">'.$coupon_desc.'</div>
                                            </div>';
                                        }
                                        if( isset( $coupon_data['display_on_page'] ) && $coupon_data['display_on_page'] !='credit_meta' &&  $is_store_credit ) {
                                            $coupon_html .= '<div class="coupon-history">
                                                    <a title="'.$store_credit_title.'"  class="credit_history" href="'.self::get_store_credit_url($coupon_id).'"><span class="dashicons dashicons-backup"></span></a>
                                                </div>';
                                        }
                                        $coupon_html .= '
                                    </div>
                                </div>';
                    break;
                default : 
                $style= 'style = "background:'.$colors[0].';color:'.$colors[2].';border:border:2px dashed '.$colors[1].';box-shadow: 0 0 0 4px '.$colors[0].',2px 1px 6px 4px rgba(10, 10, 0, 0)"';

                    $coupon_html = '
                    <div class="wt-single-coupon  '.$coupon_class.' '. $style_name .'"  '.$style.' '.$block_title.'  >
                            <div class="wt-coupon-content">
                                <div class="wt-coupon-amount">
                                    <span class="amount">'.$coupon_data['coupon_amount'].'</span>
                                    <span> '.$coupon_data['coupon_type'].'</span>
                                </div>
                                <div class="wt-coupon-code"> 
                                    <code>'.$coupon_code.'</code>
                                </div>';
                                if(  'used_coupon' != $coupon_type  && '' != $expire_text ) { 
                                    $coupon_html .= '<div class="wt-coupon-expiry">'.$expire_text.'</div>';
                                }
                                if( 'available_coupon' == $coupon_type &&'' != $coupon_desc ) {
                                    $coupon_html .= '<div class="coupon-desc-wrapper">
                                        <i class="info"> i </i>
                                        <div class="coupon-desc">'.$coupon_desc.'</div>
                                    </div>';
                                }
                                if( isset( $coupon_data['display_on_page'] ) && $coupon_data['display_on_page'] !='credit_meta' && $is_store_credit ) {
                                    $coupon_html .= '<div class="coupon-history">
                                            <a title="'.$store_credit_title.'"  class="credit_history" href="'.self::get_store_credit_url($coupon_id).'"><span class="dashicons dashicons-backup"></span> </a>
                                        </div>';
                                }
                                $coupon_html .= '
                            </div>
                        </div>';
                        break;
                
    
            }

            return $coupon_html;
        }


       


        /**
         * Ajax action function for applying coupon on button click
         */
        function apply_coupon() {
            check_ajax_referer( 'wt_smart_coupons_apply_coupon', '_wpnonce' );
            $coupon_code = ( isset( $_POST['coupon_code']) ) ?  Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['coupon_code'] ) : false;
            if( !$coupon_code ) {
                return;
            }
            if( WC()->cart->get_cart_contents_count() != 0 ) {
      
                WC()->cart->add_discount( $coupon_code );
            } else {
                $new_message = apply_filters( 'wt_smart_coupon_click_to_apply_coupon_message_cart_empty', __('Coupon code applied successfully, Please add products into cart','wt-smart-coupons-for-woocommerce-pro') );
                $this->start_overwrite_coupon_success_message($coupon_code,$new_message);
           
                WC()->cart->add_discount( $coupon_code );
                $this->stop_overwrite_coupon_success_message();
            }
            wc_print_notices();

            die();
        }


         /**
         * Owerwrite Coupon default success message with specified message.
         * @since 1.2.2
         */
        function start_overwrite_coupon_success_message( $coupon,$new_message = "" ) {
            $this->overwrite_coupon_message[$coupon] =  $new_message;
            add_filter( 'woocommerce_coupon_message', array( $this, 'owerwrite_coupon_code_message' ), 10, 3 );
        }
        /**
         * Unset owewriting coupon success message.
         * @since 1.2.2
         */
        function stop_overwrite_coupon_success_message() {
            remove_filter( 'woocommerce_coupon_message', array( $this, 'owerwrite_coupon_code_message' ), 10 );
            $this->overwrite_coupon_message = array();
        }
        /**
         * Filter function for owerwriting message.
         * @since 1.1.2
         */
        function owerwrite_coupon_code_message( $msg, $msg_code, $coupon ) {
            if ( isset( $this->overwrite_coupon_message[ $coupon->get_code() ] ) ) {
                $msg = $this->overwrite_coupon_message[ $coupon->get_code() ];
            }
            return $msg;
        }

        /**
         * Add Gift coupon details with order table.
         * @since 1.1.0
         */
        function add_coupon_details_with_order( $order ) {
            $order_id = $order->get_id();
            $coupon_attached = get_post_meta( $order_id , 'wt_coupons', true );
            if( $coupon_attached ) {
                $coupons = maybe_unserialize( $coupon_attached );
                if( empty($coupons )) {
                    return;
                }
                ?>
                <h4><?php _e('Gift coupons issued','wt-smart-coupons-for-woocommerce-pro'); ?></h4>
                <table>
                    <tr>
            
                        <td><?php _e('No of coupons gifted','wt-smart-coupons-for-woocommerce-pro'); ?></td>
                        <td><?php echo sizeof( $coupons ); ?></td>
                    </tr>
                </table>

                <?php
                
            }
        }

        /**
         * Add Gift coupon details with Email order.
         *  @since 1.1.0</table>
         */
        function add_coupon_details_with_order_email( $order, $sent_to_admin, $plain_text, $email ) {
            if( $sent_to_admin ) {
                return;
            }

            $order_id = $order->get_id();
            $coupon_attached = get_post_meta( $order_id , 'wt_coupons', true );
            if( $coupon_attached ) {
                $coupons = maybe_unserialize( $coupon_attached );
                ?>
                <h2><?php _e('Gift coupons issued','wt-smart-coupons-for-woocommerce-pro'); ?></h2>
                <?php
                if( $plain_text ) {
                    _e('No of coupons gifted: ','wt-smart-coupons-for-woocommerce-pro');
                    echo sizeof( $coupons );
                } else {
                    ?>
                    <div style="margin-bottom:20px">
                        <table cellspacing="0" cellpadding="6"  style="color:#636363;border:none;vertical-align:middle;width:100%;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif" >
                            
                            <tr>
                                <th colspan="2" style="border:1px solid #e5e5e5"><?php _e('No of coupons gifted','wt-smart-coupons-for-woocommerce-pro'); ?></th>
                                <td style="border:1px solid #e5e5e5" ><?php echo sizeof( $coupons ); ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                    </div>

                <?php

                }
            }
        }



        public static function get_store_credit_url( $coupon_id ) {
			$view_store_credit_url = wc_get_endpoint_url('wt-view-store-credit', $coupon_id, wc_get_page_permalink('myaccount'));
			return apply_filters('wt_smart_coupon_view_credit_history_url', $view_store_credit_url, $coupon_id );
		}

        
    }
}