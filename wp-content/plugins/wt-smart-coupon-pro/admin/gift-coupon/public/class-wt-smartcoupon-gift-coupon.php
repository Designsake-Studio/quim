<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if( ! class_exists ( 'Wt_Smart_Coupon_Gift_Coupon' ) ) {


    class Wt_Smart_Coupon_Gift_Coupon {
        public function __construct( ) {
            add_action( 'wt_smart_coupon_gift_coupon_details', array($this,'display_coupon_details_for_variation'),10,2 );
            add_action('wp_ajax_wt_get_coupon_formatted_price',array($this,'get_coupons_formatted_price'));
        }

         /**
         * Helper function for getting Cupons attached for product
         * @param $product_ids - Product ids as array.
         * @since 1.1.0
         */
        function get_coupons_attached_for_products( $product_ids,$variation_ids ) {
            $coupons = array();            
            if( is_array( $product_ids ) && !empty( $product_ids ) ) {
                foreach( $product_ids as $product_id ) {
                    $coupons_attached = get_post_meta( $product_id,'_wt_product_coupon' , true );
                    $coupon_ides =  explode( ',', $coupons_attached );
                    $coupons = array_merge( $coupons,$coupon_ides );
                }
            } elseif( intval( $product_ids ) ) {
                $coupons[] =  get_post_meta( $product_id,'_wt_product_coupon' , true );   
            }
            
            if( is_array( $variation_ids ) && !empty( $variation_ids ) ) {
                foreach( $variation_ids as $variation_id ) {
                    $coupons_from_variation =   get_post_meta( $variation_id  , '_wt_product_coupon_for_variation', true );          
                    $coupons_from_variation = explode(',',$coupons_from_variation);
                    $coupons = array_merge( $coupons,$coupons_from_variation );
                }
            } elseif( ''  != $variation_ids  ) {
                $coupons[] =   get_post_meta( $variation_id  , '_wt_product_coupon_for_variation', true );
            }
            if( !empty( $coupons )) {
                
                return array_filter( $coupons );
            }
            return false;
        }


        /***
         * get valid coupons ( remove trashed and deleted ) from list of coupon ids
         * @since 1.2.1
         */
        function get_valid_coupons( $coupon_ids ) {
            if( empty( $coupon_ids ) ) {
                return false;
            }
            $return = array();
            if( is_array($coupon_ids ) ) {
                foreach( $coupon_ids as $coupon_id ) {
                    if( get_post_status( $coupon_id ) == 'publish'  ) {
                        $return[] = $coupon_id;
                    }
                }
            } else {
                if( get_post_status( $coupon_ids ) ) {
                    $return[] = $coupon_ids;
                }
            }
            if( !empty ($return ) ) {
                return array_unique( array_filter( $return ) );

            }
            return false;
           
        } 


        /**
         * Form in checkout for specifying gift coupon receiver.
         * @since 1.1.0
         */
        function coupon_receiver_detail_form() {
            
            $free_coupons = array();

            $cart_contains_credit_purchase = false;
            
            foreach ( WC()->cart->cart_contents as $product ) {
                $coupons_from_variation = array();
                if ( empty( $product['product_id'] ) ) {
					$product['product_id'] = ( ! empty( $product['variation_id'] ) ) ? wp_get_post_parent_id( $product['variation_id'] ) : 0;
                }
                if( isset( $product['variation_id'] ) && $product['variation_id']  > 0  ) {
                    $coupons_from_variation =   get_post_meta( $product['variation_id']  , '_wt_product_coupon_for_variation', true );          
                    $coupons_from_variation = explode(',',$coupons_from_variation);
                }

				if ( empty( $product['product_id'] ) ) {
					continue;
				}
                
                $coupon_ids = get_post_meta( $product['product_id'] , '_wt_product_coupon', true );
                $coupon_ids = explode(',',$coupon_ids);
                $coupon_ids = array_merge( $coupons_from_variation,$coupon_ids );
                
                $free_coupons = $coupon_ids = $this->get_valid_coupons( $coupon_ids );
                if( !empty($free_coupons)) {
                    break;
                }
                

            }
            if( !empty( $free_coupons )  ) {
                ?>

                    <div class="wt_smart_coupon_send_coupon_wrap">
                        <h4><?php _e('Congrats, Your order contains Coupons ','wt-smart-coupons-for-woocommerce-pro'); ?></h4>
                        <p> <?php _e('What would you like to do?','wt-smart-coupons-for-woocommerce-pro') ?></p>
                        <ul>


                            <li class="wt_send_to_me"> 
                                <label> <input type="radio" value="wt_send_to_me" name="wt_coupon_to_do" id="wt_send_to_me" checked/>
                                 <?php _e('Send to me','wt-smart-coupons-for-woocommerce-pro' ) ?> </label>
                            </li>
                            <li class="gift_to_a_friend"> 

                                <label> <input type="radio" value="gift_to_a_friend"  name="wt_coupon_to_do" id="wt_send_to_me" />
                               <?php _e('Gift to a friend','wt-smart-coupons-for-woocommerce-pro' ) ?> </label>
                            </li>

                            
                            <div class="gift_to_friend_form" style="display:none">
                                <div  class="wt-form-item">
                                    <input type="email" name="wt_coupon_send_to" id="wt_coupon_send_to" placeholder="<?php _e('Email to send coupon','wt-smart-coupons-for-woocommerce-pro'); ?>" />
                                </div>
                                <div  class="wt-form-item">
                                    <textarea  name="wt_coupon_send_to_message" id="wt_coupon_send_to_message" placeholder="<?php _e('Message','wt-smart-coupons-for-woocommerce-pro'); ?>"></textarea>
                                </div>
                            </div>
                        
                        </ul>
                    </div>


                <?php
            }
           
        }




        /**
         * Display Product Coupons after Product Summery
         * @since 1.1.0
         */
        function display_coupon_details(  ) {
            global $post;
            $valid_coupons = 0;            
            $coupons = get_post_meta($post->ID,'_wt_product_coupon',true);
            echo '<ul class="available_coupons_with_product">';                

            if( '' != $coupons ) {
                $coupon_items = explode(',',$coupons);
                $coupon_items = $this->get_valid_coupons( $coupon_items );
                
                if( !empty($coupon_items)) {

                    foreach( $coupon_items as $coupon_id ) {

                        $coupon_title = get_the_title( $coupon_id );
                        $coupon = new WC_Coupon( $coupon_title );

                        $expired_date = $coupon->get_date_expires();
                        $expire_text = '';
                        if( $expired_date ) {
                            $expired_date = $expired_date->getTimestamp();
                            $expire_text = $this->get_expiration_date_text( $expired_date );
                        }
                        if( $expire_text == 'expired') {
                            continue;
                        }
                        $valid_coupons ++ ;

                        if(  $valid_coupons ==1 ) {
                            _e('You will get following coupon(s) when you buy this item:','wt-smart-coupons-for-woocommerce-pro');

                        }

                        $formatted_amount = $this->get_formatted_coupon_amount(  $coupon );
                        if( $formatted_amount ) {
                            echo '<li>'; echo $formatted_amount;  echo '</li>';
                        }


                    }
                }
                
            }
            do_action('wt_smart_coupon_gift_coupon_details',$post,$valid_coupons);
            echo '</ul>';
        }

        /**
         * Update order meta if any coupon attached with order item
         * @since 1.0.0
         */
        function wt_smart_coupon_update_order_meta( $order_id ) {
            
            if( isset( $_POST['wt_coupon_to_do'] ) ) {
                $product_ids = $variation_id = array();
                $order = wc_get_order( $order_id );
                $items = $order->get_items();
                foreach ( $items as $item ) {
                    $variation_id[] = $item->get_variation_id();

                    $product_ids[] = $item->get_product_id();
                }
                $wt_coupons = $this->get_coupons_attached_for_products( $product_ids,$variation_id );

                if( ! $wt_coupons ) {
                    return;
                }
                 /**
                 * Auto genrate coupons based on the gift items
                 * @since 1.2.6
                */
                if( $_POST['wt_coupon_to_do'] == 'gift_to_a_friend' ) {
                    $coupon_email = isset( $_POST['wt_coupon_send_to'] )? $_POST['wt_coupon_send_to'] : '';
                } else {
                    $coupon_email = sanitize_email( $order->get_billing_email() );
                }
                $generated_coupons = $this->generate_coupons( $wt_coupons, sanitize_email( $coupon_email ) );
                
                

                if( $_POST['wt_coupon_to_do'] == 'gift_to_a_friend' ) {
                        
                    $coupon_message = isset( $_POST['wt_coupon_send_to_message'] )? $_POST['wt_coupon_send_to_message'] : '';
                    update_post_meta($order_id, 'wt_coupon_send_to', sanitize_email( $coupon_email ));
                    update_post_meta($order_id, 'wt_coupon_send_to_message', sanitize_text_field( $coupon_message ));
                    update_post_meta( $order_id, 'wt_coupon_send_from', sanitize_email( $order->get_billing_email() ) );
                    update_post_meta($order_id, 'wt_coupons',maybe_serialize( $generated_coupons ) );
                    
                } else {
                    update_post_meta($order_id, 'wt_coupons',maybe_serialize($generated_coupons) );
                    update_post_meta( $order_id, 'wt_coupon_send_to', sanitize_email( $order->get_billing_email() ) );
                    update_post_meta( $order_id, 'wt_coupon_send_from', sanitize_email( $order->get_billing_email() ) );

                }
            }
        }

       

        /**
         *  Validate Coupon receiver field
         * @since 1.1.0
         */
        function validate_coupon_fields() {
            if( isset( $_POST['wt_coupon_to_do'] ) && $_POST['wt_coupon_to_do'] == 'gift_to_a_friend' ) {
                if ( !isset( $_POST['wt_coupon_send_to'] ) ||  ! $_POST['wt_coupon_send_to'] || !is_email( $_POST['wt_coupon_send_to'] ) ) {
                    wc_add_notice(__('Please enter Email address to send Coupon.') , 'error');
                }

            }
        }

         /**
         * Function for getting formatted coupon amount
         * @since 1.1.0
         */
        function get_formatted_coupon_amount( $coupon ) {
            if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
               return false;
            }
            $coupon_id = $coupon->get_id();
            if ( empty( $coupon_id ) ) {
                return false;
            }
            $discount_type               = $coupon->get_discount_type();
            $coupon_amount               = $coupon->get_amount();
            $is_free_shipping            = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
            $product_ids                 = $coupon->get_product_ids();
            $excluded_product_ids        = $coupon->get_excluded_product_ids();
            $product_categories          = $coupon->get_product_categories();
            $excluded_product_categories = $coupon->get_excluded_product_categories();

            $attached_give_away_product  =  get_post_meta( $coupon_id, '_wt_free_product_ids', true );


            switch ( $discount_type ) {
                case 'fixed_cart':
                $amount = wc_price( $coupon_amount ) . esc_html__( ' discount on your entire purchase', 'wt-smart-coupons-for-woocommerce-pro' );
                break;

            case 'fixed_product':
                if ( ! empty( $product_ids ) || ! empty( $excluded_product_ids ) || ! empty( $product_categories ) || ! empty( $excluded_product_categories ) ) {
                    $discount_on_text = esc_html__( 'some products', 'wt-smart-coupons-for-woocommerce-pro' );
                } else {
                    $discount_on_text = esc_html__( 'all products', 'wt-smart-coupons-for-woocommerce-pro' );
                }
                $amount = wc_price( $coupon_amount ) . esc_html__( ' discount on ', 'wt-smart-coupons-for-woocommerce-pro') . $discount_on_text;
                break;

            case 'percent_product':
                if ( ! empty( $product_ids ) || ! empty( $excluded_product_ids ) || ! empty( $product_categories ) || ! empty( $excluded_product_categories ) ) {
                    $discount_on_text = esc_html__( 'some products', 'wt-smart-coupons-for-woocommerce-pro' );
                } else {
                    $discount_on_text = esc_html__( 'all products','wt-smart-coupons-for-woocommerce-pro' );
                }
                $amount = $coupon_amount . '%' . esc_html__( ' discount on ', 'wt-smart-coupons-for-woocommerce-pro' ) . $discount_on_text;
                break;

            case 'percent':
                if ( ! empty( $product_ids ) || ! empty( $excluded_product_ids ) || ! empty( $product_categories ) || ! empty( $excluded_product_categories ) ) {
                    $discount_on_text = esc_html__( 'some products', 'wt-smart-coupons-for-woocommerce-pro' );
                } else {
                    $discount_on_text = esc_html__( 'your entire purchase', 'wt-smart-coupons-for-woocommerce-pro' );
                }
                $amount = $coupon_amount . '%' . esc_html__( ' discount on ', 'wt-smart-coupons-for-woocommerce-pro' ) . $discount_on_text;
                break;

            default:
                $default_coupon_type = ( ! empty( $all_discount_types[ $discount_type ] ) ) ? $all_discount_types[ $discount_type ] : ucwords( str_replace( array( '_', '-' ), ' ', $discount_type ) );
                $coupon_amount       = apply_filters( 'wc_sc_coupon_amount', $coupon_amount, $coupon );
                $amount = sprintf( esc_html__( '%1$s coupon of %2$s', 'wt-smart-coupons-for-woocommerce-pro' ), $default_coupon_type, wc_price( $coupon_amount ) );
                $amount = apply_filters( 'wt_smart_coupon_description', $amount, $coupon );
                break;
            }

            


            if ( 'yes' === $is_free_shipping && in_array( $discount_type, array( 'fixed_cart', 'fixed_product', 'percent_product', 'percent' ), true ) ) {
                $amount = sprintf( esc_html__( '%s Free Shipping', 'wt-smart-coupons-for-woocommerce-pro' ), ( ( ! empty( $coupon_amount ) ) ? $amount . esc_html__( ' &', 'wt-smart-coupons-for-woocommerce-pro' ) : '' ) );
            }

            if( '' != $attached_give_away_product ) {
                $attached_give_away_products  = explode( ',', $attached_give_away_product );
                if( !empty( $attached_give_away_products ) ) {
                    $product_title = '';
                    foreach( $attached_give_away_products as $product_id ) {
                        $product = wc_get_product( $product_id );
                        $product_link = get_permalink( $product_id );
                        if( '' == $product_title ) {
                            $product_title = '<a href="'.$product_link.'">'.$product->get_title().'</a>';
                        } else {
                            $product_title =  $product_title .','. '<a href="'.$product_link.'">'.$product->get_title().'</a>';

                        }
                    }
                    

                    $amount = sprintf( esc_html__( '%s %s as a giveaway free product', 'wt-smart-coupons-for-woocommerce-pro' ), ( ( ! empty( $coupon_amount ) ) ? $amount . esc_html__( ' &', 'wt-smart-coupons-for-woocommerce-pro' ) : '' ),$product_title );

                }
            }

            return $amount;
        }


         /**
         * Send Coupon email on change order status into  complete/process 
         * @since 1.1.0
         */
        function send_gift_coupon_email( $order_id, $old_status,$new_status,$order ) {
            $email_coupon_for_order_status = Wt_Smart_Coupon_Admin::get_option('email_coupon_for_order_status');
            if( '' == $email_coupon_for_order_status ) {
                $email_coupon_for_order_status = 'completed';
            }

            if( $new_status  == $email_coupon_for_order_status ) {
                $coupons = get_post_meta( $order_id, 'wt_coupons', true );
                $coupons = maybe_unserialize( $coupons  );
                if( !empty($coupons )) {
                    WC()->mailer();
                    do_action( 'wt_send_gift_coupon_to_customer',$order,$coupons);
                    
                }
            }
        }


        /**
         * Display gift coupon details for variable product
         * @since 1.2.4
         */
        function display_coupon_details_for_variation( $post,$valid_coupons ) {
            $product = wc_get_product( $post->ID );

            if ( $product->is_type('variable') ) {
                echo '<div class="wt_coupon_from_variation"></div>';
                
                ?>

                <script>
                jQuery(document).ready(function($) {
                    
                   $('input.variation_id').change( function(){
                        if( '' != $('input.variation_id').val() ) {
                            
                            var var_id = $('input.variation_id').val();
                            var data = {
                                'action'        			: 'wt_get_coupon_formatted_price',
                                'variation_id'		        : var_id,
                                'valid_coupons'             : <?php echo $valid_coupons; ?>,
                            };

                            
                            jQuery.ajax({
                                type: "POST",
                                url: WTSmartCouponOBJ.ajaxurl,
                                data: data,
                                success: function (response) {
                                    $('.wt_coupon_from_variation').html( response );

                                }
                            
                            });
                        }
                    });
                });
                </script>
                <?php
              
             }
        }

        /**
         * Formatted coupon price for variable product 
         * @since 1.2.4
         */
        function get_coupons_formatted_price( ) {
            $variation_id  = ( isset( $_POST['variation_id'] ) )? $_POST['variation_id'] : '';
            $valid_coupons  = ( isset( $_POST['valid_coupons'] ) )? $_POST['valid_coupons'] : '';
            $output = '';
            if( $variation_id ) {
                $coupons = get_post_meta($variation_id , '_wt_product_coupon_for_variation', true );
                $coupons = explode(',',$coupons);
                $coupons = array_unique( array_filter($coupons) );
                if( is_array($coupons) && !empty($coupons) ) {
                    foreach( $coupons as $coupon_id ) {
                        $coupon_title = get_the_title( $coupon_id );
                        $coupon = new WC_Coupon( $coupon_title );

                        $expired_date = $coupon->get_date_expires();
                        $expire_text = '';
                        if( $expired_date ) {
                            $expired_date = $expired_date->getTimestamp();
                            $expire_text = $this->get_expiration_date_text( $expired_date );
                        }
                        if( $expire_text == 'expired') {
                            continue;
                        }
                        $valid_coupons ++ ;

                        if(  $valid_coupons ==1 ) {
                            $output .= __('You will get following coupon(s) when you buy this item:','wt-smart-coupons-for-woocommerce-pro');

                        }

                        $formatted_amount = $this->get_formatted_coupon_amount(  $coupon );
                        if( $formatted_amount ) {
                            $output .='<li>'.$formatted_amount.'</li>';
                        }

                    }
                }

                echo $output;
                die();
            }
        }

        /**
         * AutoGenrate clone coupons 
         * @since 1.2.6
         */

        function generate_coupons( $coupons_to_clone,$email ) {
            $random_coupons = array();
            foreach( $coupons_to_clone as $coupon ) {
                $randon_coupon = Wt_Smart_Coupon_Admin::clone_coupon( $coupon );
                if( $randon_coupon ) {
                    $coupon_obj = new WC_Coupon( $randon_coupon );
                    $coupon_obj->set_email_restrictions(  $email );
                    $coupon_obj->save();
                    $random_coupons[] = $randon_coupon;
                }
            }

            return $random_coupons;
        }

    }
}