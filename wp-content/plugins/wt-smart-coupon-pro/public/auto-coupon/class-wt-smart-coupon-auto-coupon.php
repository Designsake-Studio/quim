<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'Wt_Smart_Coupon_Auto_Coupon' ) ) {
class Wt_Smart_Coupon_Auto_Coupon {

    protected $overwrite_coupon_message, $_user_emails,$_session_data;
    
    public function __construct() {

        $this->overwrite_coupon_message = array();
        $this->_user_emails = array();
        
    }


    /**
     * Add coupon meta field for setting AutoCoupon
     * @since 1.1.0
     */
    function add_auto_coupon_options( $coupon_id, $coupon ) {
        
        $_wt_make_auto_coupon = get_post_meta($coupon_id , '_wt_make_auto_coupon', true );

        woocommerce_wp_checkbox(
            array(
                'id' => '_wt_make_auto_coupon',
                'label' => __('Apply coupon automatically', 'wt-smart-coupons-for-woocommerce-pro'),
                'desc_tip' => true,
                'description' => __('This coupon will be applied automatically if the specifications are met and the corresponding coupon description will be shown onscreen.', 'wt-smart-coupons-for-woocommerce-pro'),
                'wrapper_class' => 'wt_auto_coupon',
                'value'       =>  wc_bool_to_string( $_wt_make_auto_coupon  ),
                )
            );
    
    }
    /**
     * Save AutoCoupon meta
     * @since 1.1.0
     */
    function process_shop_coupon_meta( $post_id, $post ) {
        if( isset( $_POST['_wt_make_auto_coupon'] ) && $_POST['_wt_make_auto_coupon']!='' ) {
            update_post_meta($post_id, '_wt_make_auto_coupon',  true );
        } else {
            update_post_meta($post_id, '_wt_make_auto_coupon', false );
        }
    }

    /**
     * Function to check specified coupon is autocoupon
     * @since 1.1.0
     */
    function is_auto_coupon ( $coupon ) {
        // need to update the funcion, make based on attribute.
        if( is_object( $coupon ) ) {
            $coupon = $coupon->get_id();
        }
        $auto_coupons = $this->get_all_auto_coupons();
        return ( in_array( $coupon,$auto_coupons ) )? true : false;
    }

    /**
     * Function to retreave all auto coupons
     * @since 1.1.0
     */
    function get_all_auto_coupons() {
        $auto_coupons = array();

        global $wpdb;
        
        $coupon_items = $wpdb->get_results( "SELECT meta.`post_id` FROM `" . $wpdb->postmeta . "` meta WHERE  ( meta.`meta_key` = '_wt_make_auto_coupon' AND meta.`meta_value` = 1 )");
        foreach( $coupon_items as $coupon_item ) {
            $auto_coupons[] = $coupon_item->post_id;
        }
        return $auto_coupons;
    }

    /**
     * Get all available auto coupons.
     * @since 1.1.0
     */
    function get_available_auto_coupons( $return ="OBJECT" ) {

        $available_coupons = array();
        $all_auto_coupons =  $this->get_all_auto_coupons();

        foreach( $all_auto_coupons as $auto_coupon ) {
            $post = get_post( $auto_coupon );
            $coupon_obj = new WC_Coupon( $post->post_title );
            if( $this->is_valid_coupon( $coupon_obj ) ) {
                if( $return == "OBJECT") {
                    $available_coupons[] = $coupon_obj;
                } else {
                    $available_coupons[] = $coupon_obj->get_code();
                }
                
            }
            
        }

        return $available_coupons;

    }
    
    /**
     * Check is coupon can apply. 
     * @since 1.1.0
     */
    private function is_valid_coupon( $coupon ) {

        // echo $coupon->get_code(); echo '<br/>';
        $can_be_applied = true;

        $cart = WC()->cart;

        //Test validity
        if ( ! $coupon->is_valid() ) {
            $can_be_applied = false;
        }

        if ( $can_be_applied && $coupon->get_usage_limit() > 0 && $coupon->get_usage_count() >= $coupon->get_usage_limit() ) {
            $can_be_applied = false;
        }

        $check_emails = $this->get_user_emails();
        if ( $can_be_applied ) {
            $restrictions = $coupon->get_email_restrictions();
            if ( is_array( $restrictions ) && 0 < count( $restrictions ) && ! $this->is_coupon_emails_allowed( $check_emails, $restrictions, $cart ) ) {
                $can_be_applied = false;
            }
        }
        

        if ( $can_be_applied ) {

            $limit_per_user = $coupon->get_usage_limit_per_user();
            if ( 0 < $limit_per_user ) {
                $used_by         = $coupon->get_used_by();
                $usage_count     = 0;
                $user_id_matches = array( get_current_user_id() );

                // Check usage Registered emails.
                foreach ( $check_emails as $check_email ) {
                    $usage_count      += count( array_keys( $used_by, $check_email, true ) );
                    $user              = get_user_by( 'email', $check_email );
                    $user_id_matches[] = $user ? $user->ID : 0;
                }
                // Check against billing Email.
                $users_query = new WP_User_Query(
                    array(
                        'fields'     => 'ID',
                        'meta_query' => array(
                            array(
                                'key'     => '_billing_email',
                                'value'   => $check_emails,
                                'compare' => 'IN',
                            ),
                        ),
                    )
                );

                $user_id_matches = array_unique( array_filter( array_merge( $user_id_matches, $users_query->get_results() ) ) );
                foreach ( $user_id_matches as $user_id ) {
                    $usage_count += count( array_keys( $used_by, (string) $user_id, true ) );
                }

                if ( $usage_count >= $coupon->get_usage_limit_per_user() ) {
                    $can_be_applied = false;
                }

                
            }
        }

        return apply_filters( 'wt_is_valid_coupon', $can_be_applied, $coupon );
    }

   
    /**
     * Store the userdata into session
     * @since 1.1.0
     */
    public function store_billing_email_into_session( $post_data ) {
		if ( ! is_array( $post_data ) ) {
			parse_str( $post_data, $posted );
		} else {
			$posted = $post_data;
		}

		if ( isset( $posted['billing_email'] ) ) {
			$this->set_session( 'billing_email', $posted['billing_email'] );
		}
    }
    
    /**
     * Set smartcoupon session.
     * @since 1.1.0
     */
    public function set_session( $key, $value ) {
		if ( ! isset( $this->_session_data ) ) {
			if ( ! isset( WC()->session ) ) {
				return null;
			}
			$this->_session_data = WC()->session->get( '_wt_smart_coupon_session_data', array() );
		}
		if ( is_null( $value ) ) {
			unset( $this->_session_data[ $key ] );
		} else {
			$this->_session_data[ $key ] = $value;
		}

		WC()->session->set( '_wt_smart_coupon_session_data', $this->_session_data );
    }
    

    /**
     * Cache the session data into private variable 
     * @since 1.1.0
     */
	public function get_session( $key = null, $default = false ) {
		if ( ! isset( $this->_session_data ) ) {
			if ( ! isset( WC()->session ) ) {
				return null;
			}
			$this->_session_data = WC()->session->get( '_wt_smart_coupon_session_data', array() );
		}

		if ( ! isset( $key ) ) {
			return $this->_session_data;
		}
		if ( ! isset( $this->_session_data[ $key ] ) ) {
			return $default;
		}
		return $this->_session_data[ $key ];
    }


     /**
     * Removed unmatched cart item ( Befor removing woocommmerce )
     * @since 1.1.0
     */
    function woocommerce_check_cart_items() {
        $this->remove_unmatched_autocoupons();
    }
    

    
    /**
     * Get user Account and billing email
     * @since 1.1.0
     */
    public function get_user_emails() {
		if ( ! is_array( $this->_user_emails ) ) {
			$this->_user_emails = array();
			if ( is_user_logged_in() ) {
				$current_user         = wp_get_current_user();
				$this->_user_emails[] = $current_user->user_email;
			}
		}
		$user_emails = $this->_user_emails;

		$billing_email = $this->get_session( 'billing_email', '' );
		if ( is_email( $billing_email ) ) {
			$user_emails[] = $billing_email;
		}

		$user_emails = array_map( 'strtolower', $user_emails );
		$user_emails = array_map( 'sanitize_email', $user_emails );
		$user_emails = array_filter( $user_emails, 'is_email' );
		return array_unique( $user_emails );
    }
    
    /**
     * Check whether the email allowed the restricted email list.
     * @since 1.1.0
     */
    private function is_coupon_emails_allowed( $check_emails, $restrictions, $cart ) {
        if ( is_callable( array( $cart, 'is_coupon_emails_allowed' ) ) ) {
            return $cart->is_coupon_emails_allowed( $check_emails, $restrictions );
        }

        return sizeof( array_intersect( $check_emails, $restrictions ) ) > 0;
    }

    /**
     * Remove unmatched coupons silentley
     * @since 1.1.0
     */
    private function remove_unmatched_autocoupons( $valid_coupon_codes = null ) {

        if ( is_null( $valid_coupon_codes ) ) {
            $valid_coupon_codes = $this->get_available_auto_coupons( "CODE" );
        }

        //Remove invalids
        $calc_needed = false;
        foreach ( WC()->cart->get_applied_coupons() as $coupon_code ) {
            if ( in_array( $coupon_code, $valid_coupon_codes ) ) {
                continue;
            }

            $coupon = new WC_Coupon( $coupon_code );

            if ( ! $this->is_auto_coupon( $coupon ) && $coupon->is_valid() ) {
                continue;
            }
            if ( ! apply_filters( 'wt_remove_invalid_coupon_automatically', $this->is_auto_coupon( $coupon ), $coupon ) ) {
                continue;
            }
           WC()->cart->remove_coupon( $coupon_code );
            $calc_needed = true;
        }

        $calc_needed = false;

        return $calc_needed;
    }

    /**
     *  Apply matched Coupons
     * @since 1.1.0
     */
    function update_matched_coupons( $cart ) {
        
        $available_coupons = $this->get_available_auto_coupons();
        if( is_checkout() ) {
            $need_calc = $this->remove_unmatched_autocoupons();  // used for removing invalid auto coupons from our end

        } else {
            $need_calc = false;
        }
        if( !empty( $available_coupons ) ) {
            foreach( $available_coupons as $available_coupon ) {

                $coupon_code = $available_coupon->get_code();
    
                if( ! $cart->has_discount(  $coupon_code ) ) {
                    $coupon_desc = $available_coupon->get_description();
                    if( $coupon_desc ) {
                        $coupon_desc = ': '.$coupon_desc;
                    }
                    $new_message = apply_filters( 'wt_applied_auto_coupon_message', __('Coupon code applied successfully '.$coupon_desc,'wt-smart-coupons-for-woocommerce-pro',$available_coupon ) ) ;
                    $this->start_overwrite_coupon_success_message( $coupon_code,$new_message );
           
                    WC()->cart->add_discount( $coupon_code );
                    $this->stop_overwrite_coupon_success_message();
                    $need_calc  = false;
                }
            }
        }

        if(  $need_calc ) {
            WC()->cart->calculate_totals();
        }

    }

    /**
     * Owerwrite Coupon default success message with specified message.
     * @since 1.1.0
     */
    function start_overwrite_coupon_success_message( $coupon,$new_message = "" ) {
        $this->overwrite_coupon_message[$coupon] =  $new_message;
        add_filter( 'woocommerce_coupon_message', array( $this, 'owerwrite_coupon_code_message' ), 10, 3 );
    }
    /**
     * Unset owewriting coupon success message.
     * @since 1.1.0
     */
    function stop_overwrite_coupon_success_message() {
        remove_filter( 'woocommerce_coupon_message', array( $this, 'owerwrite_coupon_code_message' ), 10 );
        $this->overwrite_coupon_message = array();
    }
    /**
     * Filter function for owerwriting message.
     * @since 1.1.0
     */
    function owerwrite_coupon_code_message( $msg, $msg_code, $coupon ) {
        if ( isset( $this->overwrite_coupon_message[ $coupon->get_code() ] ) ) {
			$msg = $this->overwrite_coupon_message[ $coupon->get_code() ];
        }
        return $msg;
    }


    /**
     * Update coupon HTML on cart total
     * @since 1.1.0
     */
    function coupon_html( $originaltext, $coupon ) {
        if ( $this->is_auto_coupon( $coupon )  ) {
            $value = array();

            $amount = WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );
            if ( $amount ) {
                $discount_html = '-' . wc_price( $amount );
            } else {
                $discount_html = '';
            }

            $value[] = apply_filters( 'woocommerce_coupon_discount_amount_html', $discount_html, $coupon );

            if ( $coupon->get_free_shipping() ) {
                $value[] = __( 'Free shipping coupon', 'woocommerce' );
            }

            return implode( ', ', array_filter( $value ) );
        } else {
            return $originaltext;
        }
    }
}

}
// $Wt_Smart_Coupon_Auto_Coupon = new Wt_Smart_Coupon_Auto_Coupon();