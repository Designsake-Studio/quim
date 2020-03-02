<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * Implement Basic URL coupon
 * @since 1.2.3
 */
if( ! class_exists ( 'Wt_Smart_Coupon_URL_Coupon' ) ) {
    class Wt_Smart_Coupon_URL_Coupon {

        protected $overwrite_coupon_message;

        public function __construct() {
            // add_action('wp_loaded',array($this,'wt_apply_smart_coupon'));
            // add_action('wt_smart_coupon_general_settings',array($this,'add_coupon_instruction'),10);
            $overwrite_coupon_message  = array();
        }
        /**
         * Apply coupon by URL
         * @since 1.2.3
         */
        function wt_apply_smart_coupon(  ) {

            if( isset( $_GET['wt_coupon'] ) && '' != $_GET['wt_coupon'] &&  post_exists( $_GET['wt_coupon'] ) ) {
                $coupon_code = $_GET['wt_coupon'];
               
                if( WC()->cart->get_cart_contents_count() != 0 ) {
                    $new_message = apply_filters( 'wt_smart_coupon_url_coupon_message', __('Coupon code applied successfully','wt-smart-coupons-for-woocommerce-pro') );
                }  else {
                    $woo_shop_page = get_option( 'woocommerce_shop_page_id' );
                    $shop_page_url = get_page_link( $woo_shop_page );
                    
                    $new_message = apply_filters( 'wt_smart_coupon_url_coupon_message', sprintf( __( 'Oops your cart is empty! Add %1$s to your cart to avail the offer.','wt-smart-coupons-for-woocommerce-pro'),'<a href="'.$shop_page_url.'">'.esc_html__('products','wt-smart-coupons-for-woocommerce-pro').'</a>' ) );
                }

                if ( WC()->cart->get_cart_contents_count() == 0 ) { 
                    set_transient('wt_smart_url_coupon_pending_coupon',$coupon_code,1800);
                    // store for further apply coupon
                    return;
                }


                if( is_array( WC()->cart->get_applied_coupons( ) ) && ! in_array( $coupon_code, WC()->cart->get_applied_coupons() ) ) {
                    $this->start_overwrite_coupon_success_message( $coupon_code,$new_message );
                    WC()->cart->add_discount( sanitize_text_field( $coupon_code ));
                    
                    $this->stop_overwrite_coupon_success_message();
                } else {
                    delete_transient('wt_smart_url_coupon_pending_coupon');
                }
            }
        }
        
        /**
         * overwrite the coupon added message
         * @since 1.2.3
         */
        function start_overwrite_coupon_success_message( $coupon,$new_message = "" ) {
            $this->overwrite_coupon_message[$coupon] =  $new_message;
            add_filter( 'woocommerce_coupon_message', array( $this, 'owerwrite_coupon_code_message' ), 10, 3 );
        }

        /**
         * stop owerwriting coupon
         * @since 1.2.3
         */
        function stop_overwrite_coupon_success_message() {
            remove_filter( 'woocommerce_coupon_message', array( $this, 'owerwrite_coupon_code_message' ), 10 );
            $this->overwrite_coupon_message = array();
        }
        /**
         * Display the coupon message
         * @since 1.2.3
         */
        function owerwrite_coupon_code_message( $msg, $msg_code, $coupon ) {
            if ( isset( $this->overwrite_coupon_message[ $coupon->get_code() ] ) ) {
                $msg = $this->overwrite_coupon_message[ $coupon->get_code() ];
            }
            return $msg;
        }

        /**
         * Add URL coupon instruction on general settings.
         * @since 1.2.3
         */
        function add_coupon_instruction() {
            ?>
             <div class="wt_section_title wt_url_option_instruction">
                <h2>
                    <?php _e('URL coupon','wt-smart-coupons-for-woocommerce-pro') ?>
                    
                </h2>

                <p><?php _e('URL Coupons help you add a unique URL to any coupon in your e-commerce store. Clicking this URL ensures that the underlying coupon is applied as per its respective configuration e.g allow discount, giveaway free product whatever the case maybe.','wt-smart-coupons-for-woocommerce-pro'); ?></p>
                <p><b><?php _e('Usage:','wt-smart-coupons-for-woocommerce-pro') ?>{site_url}/?wt_coupon={coupon_code}</b> </p>
                <p style="color:#b3b2b2">e.g, https://www.webtoffee.com/cart/?wt_coupon=flat30</p>
                <p style="color:#b3b2b2"><?php _e('A URL coupon that offers a flat 30% discount at www.webtoffee.com, coupon_code = FLAT30, site_url(preferably cart page) = https://www.webtoffee.com/cart','wt-smart-coupons-for-woocommerce-pro'); ?> </p>
                <p style="background-color:#d9edf7;border-color:#bce8f1;color:#31708f;padding:15px;border-radius:4px"> <?php echo sprintf( __('Alternatively, buy our URL Coupon for WooCommerce add-on to share your coupons via a customised URL or a QR Code. %s','wt-smart-coupons-for-woocommerce-pro'), '<a href="https://www.webtoffee.com/product/url-coupons-for-woocommerce/" target="_blank"><b>'.esc_html__( 'Know more.', 'xa-woocommerce-subscription' ).'</b></a>'  ); ?></p>
            </div>


            <?php
        }



        /**
	 * Apply coupon from cookie if coupon is not valid when visit URL
	 * @since 1.0.2
	 */
	function may_be_re_apply_coupon_from_cookie() {
        $coupon_need_to_apply = get_transient('wt_smart_url_coupon_pending_coupon');
        
		if( '' == $coupon_need_to_apply ) {
			return;
		}
        $coupon_obj = new WC_Coupon( $coupon_need_to_apply );

		if( !(in_array($coupon_obj->get_code(),WC()->cart->get_applied_coupons())) ) {
       
            $applied = WC()->cart->add_discount( $coupon_need_to_apply );
			if( $applied ) {
				delete_transient('wt_smart_url_coupon_pending_coupon');
			}
		} else {
			delete_transient('wt_smart_url_coupon_pending_coupon');
        }
        
        if ( WC()->cart->get_cart_contents_count() > 0 ) { 
			delete_transient('wt_smart_url_coupon_pending_coupon');
        }

	}
    

    }

   $url_coupon =  new Wt_Smart_Coupon_URL_Coupon();
}