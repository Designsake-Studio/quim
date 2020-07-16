<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if( ! class_exists ( 'Wt_Smart_Coupon_Gift_Coupon_Admin' ) ) {


    class Wt_Smart_Coupon_Gift_Coupon_Admin {
        public function __construct( ) {
            
        }

        
        /**
         * Add new field into Product meta
         * @since 1.1.0
         */
        function add_coupon_field_forproduct() {
            global $post;

            echo '<div class="options_group">';
            $all_discount_types = wc_get_coupon_types();
			$coupon_ids = get_post_meta( $post->ID, '_wt_product_coupon', true );
            // $coupon_ides = array_filter( array_map('trim', explode( ',', $coupon_ids ) ) );
            $coupon_ides = array_filter( explode( ',', $coupon_ids ) ) ;
            ?>
            <p class="form-field" id="sc-field">
				<label for="_wt_product_coupon"><?php echo esc_html__( 'Gift a coupon(s)', 'wt-smart-coupons-for-woocommerce-pro'); ?></label>

					<select class="wt-coupon-search" style="width: 50%;" multiple="multiple" id="_wt_product_coupon" name="_wt_product_coupon[]" data-placeholder="<?php echo esc_attr__( 'Search for a coupon&hellip;', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" data-action="wt_json_search_coupons" data-security="<?php echo esc_attr( wp_create_nonce( 'search-coupons' ) ); ?>" >
						<?php

						if ( ! empty( $coupon_ides ) ) {
							foreach ( $coupon_ides as $coupon_id ) {
                                $coupon_title = get_the_title( $coupon_id );

								$coupon = new WC_Coupon( $coupon_title );

								$discount_type = $coupon->get_discount_type();

								if ( ! empty( $discount_type ) ) {
									$discount_type = sprintf( __( ' ( %1$s: %2$s )', 'wt-smart-coupons-for-woocommerce-pro' ), __( 'Type', 'wt-smart-coupons-for-woocommerce-pro' ), $all_discount_types[ $discount_type ] );
								}
                                if( $coupon_title && $discount_type ) {
								    echo '<option value="' . esc_attr( $coupon_id ) . '"' . selected( true, true, false ) . '>' . esc_html( $coupon_title . $discount_type ) . '</option>';
                                }
							}
						}
						?>

					</select>
                    <?php echo wc_help_tip( __('To gift a coupon upon product purchase associate here','wt-smart-coupons-for-woocommerce-pro') ); ?>

                </p>
            </div>
					<?php
        }


         /**
         * Save product meta data
         * @since 1.1.0
         */
        function save_product_coupon_meta_data( $post_id ) {
            $coupon_attached = ( isset( $_REQUEST['_wt_product_coupon'] ) )? $_REQUEST['_wt_product_coupon'] : ''; 
            if( ! $post_id  ) {
                return;
            }

            if( is_array( $coupon_attached ) ) {
                $coupon_attached = implode( ',', $coupon_attached);
            }
            update_post_meta($post_id, '_wt_product_coupon', $coupon_attached );

        }


         /**
         * Display coupen sent on order in order item meta
         * @since 1.1.0
         */
        public function add_coupon_details_into_order(  ) {
            global $post;
			if ( 'shop_order' !== $post->post_type ||  !get_post_meta( $post->ID, 'wt_coupons', true )  ) {
				return;
			}

			add_meta_box( 'wt-coupons-in-order', __( 'Coupon Sent', 'wt-smart-coupons-for-woocommerce-pro' ), array( $this, 'coupon_meta_box' ), 'shop_order', 'normal' );
        }

        /**
         * Coupon metabox content
         * @since 1.1.0
         */
        function coupon_meta_box() {
            global $post;
            $coupon_attached = get_post_meta( $post->ID, 'wt_coupons', true );
            if( $coupon_attached )  {
               $coupons = maybe_unserialize( $coupon_attached );
               echo '<div class="wt_order_coupons">';

               foreach( $coupons as $coupon_id ) {
                    $coupon = get_post( $coupon_id  );
                    $coupon_obj = new WC_Coupon( $coupon->post_title );
                    $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon_obj );
                    echo Wt_Smart_Coupon_Public::get_coupon_html( $coupon,$coupon_data );
                   
               }

                echo '<div class="coupon_meta">';
                    echo '<span><b>'.__('From: ','wt-smart-coupons-for-woocommerce-pro').'</b>'.get_post_meta($post->ID,'wt_coupon_send_from',true).'</span>';
                    echo '<span><b>'.__('To: ','wt-smart-coupons-for-woocommerce-pro').'</b>'.get_post_meta($post->ID,'wt_coupon_send_to',true).'</span>';
                echo '</div>';

               echo '<div class="wt-send-coupon">';
                    echo '<button order-id='.$post->ID.' class="btn wt-btn-resend-coupon button-primary button-large" >'.__(' Resend coupon','wt-smart-coupons-for-woocommerce-pro').' </button>';
                    echo '<div class="wt-send-status"> </div>';
               echo '</div>';

               echo '</div>';
            }
        }

        /**
         * Include Gift coupon email class
         */
        
        public  function add_wt_smart_gift_coupon_emails( $email_classes ) {
            require_once( WT_SMARTCOUPON_MAIN_PATH.'admin/gift-coupon/class-wt-smart-coupon-gift-email.php');
            $email_classes['WT_smart_Coupon_Gift'] = new WT_smart_Coupon_Gift();
            return $email_classes;


        }
        /**
         * Add gift coupon settings
         */
        function add_gift_cuopon_settings() {
            $general_settings_options = Wt_Smart_Coupon_Admin::get_option('wt_copon_general_settings');
            ?>
            <div class="wt_section_title">
                    <h2>
                        <?php _e('Gift coupon on product purchase','wt-smart-coupons-for-woocommerce-pro') ?>

                        
                    </h2>
                </div>


                <table class="form-table">
                    <tbody>
                        
                        <tr  valign="top">
                            <?php
                                $coupon_statuses = Wt_Smart_Coupon_Admin::success_order_statuses();
                                $status_selected = (isset( $general_settings_options['email_coupon_for_order_status'] ) )? $general_settings_options['email_coupon_for_order_status'] : 'completed';

                            ?>
                            <td scope="row" class="titledesc"> 
                                <label> <?php _e("Email coupon for order status",'wt-smart-coupons-for-woocommerce-pro');  ?></label>
                                
                            </td>
                            <td>
                                <select id="_email_coupon_for_order_status" name="_email_coupon_for_order_status[]" style="width: 300px"  class="wc-enhanced-select" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                                    <?php 
                                        foreach( $coupon_statuses  as $coupon_status => $status_text ) {
                                            $selected = '  ';
                                            if( $status_selected == $coupon_status  ) {
                                                $selected = ' selected';
                                            }
                                            echo '<option value="'.$coupon_status.'" '.$selected.'> '.$status_text.'</option>';
                                        }
                                    ?>
                                </select>
                                <?php echo wc_help_tip( __('Coupons will be mailed only for the chosen order statuses','wt-smart-coupons-for-woocommerce-pro') ); ?>

                            </td>
                        </tr>

                    </tbody>
                </table>

            <?php
        }

        /**
         * update gift coupon settings
         * @since 1.2.1
         */

        function update_gift_coupon_settings( ) {
            if( isset( $_POST['update_wt_smart_coupon_general_settings'] ) ) {
                if ( !Wt_Smart_Coupon_Security_Helper::check_write_access( 'smart_coupons', 'wt_smart_coupons_general_settings' ) ) {
                    wp_die(__('You do not have sufficient permission to perform this operation', 'wt-smart-coupons-for-woocommerce-pro'));
                }
                $smart_coupon_options = Wt_Smart_Coupon_Admin::get_options();
                if( isset( $_POST['_email_coupon_for_order_status'] ) && '' != $_POST['_email_coupon_for_order_status']  ) {
                    $wt_copon_general_settings = sanitize_text_field(  implode(',',$_POST['_email_coupon_for_order_status']) );                    
                } else {
                    $wt_copon_general_settings = 'completed';                    
                }
                $general_settings = $smart_coupon_options['wt_copon_general_settings'];
                $general_settings['email_coupon_for_order_status'] = $wt_copon_general_settings;
                $smart_coupon_options[ 'wt_copon_general_settings'] = $general_settings;
                update_option("wt_smart_coupon_options", $smart_coupon_options);

            }
        }

        /**
         * Add coupon fields for variations
         * @since 1.2.4
         */
        function add_coupon_field_forproduct_variations( $loop, $variation_data, $variation ) {
            $all_discount_types = wc_get_coupon_types();
            $coupon_ids = get_post_meta( $variation->ID, '_wt_product_coupon_for_variation', true );
            $coupon_ides = array_filter( explode( ',', $coupon_ids ) ) ;
            ?>
            <p class="form-field" id="sc-field">
				<label for="_wt_product_coupon_variation"><?php echo esc_html__( 'Gift a coupon(s)', 'wt-smart-coupons-for-woocommerce-pro'); ?></label>

					<select class="wt-coupon-search" style="width: 50%;" multiple="multiple" id="_wt_product_coupon_variation<?php echo '[' . $loop . ']' ?>" name="_wt_product_coupon_variation<?php echo '[' . $loop . ']' ?>[]" data-placeholder="<?php echo esc_attr__( 'Search for a coupon&hellip;', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" data-action="wt_json_search_coupons" data-security="<?php echo esc_attr( wp_create_nonce( 'search-coupons' ) ); ?>" >
						<?php

						if ( ! empty( $coupon_ides ) ) {
							foreach ( $coupon_ides as $coupon_id ) {
                                $coupon_title = get_the_title( $coupon_id );

								$coupon = new WC_Coupon( $coupon_title );

								$discount_type = $coupon->get_discount_type();

								if ( ! empty( $discount_type ) ) {
									$discount_type = sprintf( __( ' ( %1$s: %2$s )', 'wt-smart-coupons-for-woocommerce-pro' ), __( 'Type', 'wt-smart-coupons-for-woocommerce-pro' ), $all_discount_types[ $discount_type ] );
								}
                                if( $coupon_title && $discount_type ) {
								    echo '<option value="' . esc_attr( $coupon_id ) . '"' . selected( true, true, false ) . '>' . esc_html( $coupon_title . $discount_type ) . '</option>';
                                }
							}
						}
						?>

					</select>
                    <?php echo wc_help_tip( __('To gift a coupon upon product purchase associate here','wt-smart-coupons-for-woocommerce-pro') ); ?>
                </p>
                <?php
        }


        /**
         * Save variation coupon details
         * @since 1.2.4
         */

        function save_coupon_field_forproduct_variations( $variation_id, $i ) {
            $coupon_attached = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_product_coupon_variation'][$i], 'int_arr' );
            if( is_array( $coupon_attached ) ) {
                $coupon_attached = implode( ',', $coupon_attached);
            }
            update_post_meta($variation_id, '_wt_product_coupon_for_variation', $coupon_attached );

        }


        /**
         * Ajax action for send coupons from admin
         * @since 1.2.6
         */
        function send_coupons() {
            
            $return = array(
                'error' =>  false,
                'message' => __('Something went wrong','wt-smart-coupons-for-woocommerce-pro')
            );
            if ( Wt_Smart_Coupon_Security_Helper::check_write_access( 'smart_coupons', 'wt_smart_coupons_admin_nonce' ) ) {

                $order_id = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_order_id'], 'int' );
                $order = wc_get_order( $order_id );
                $coupons = get_post_meta( $order_id, 'wt_coupons', true );
                $coupons = maybe_unserialize( $coupons  );
                if( !empty($coupons )) {
                    WC()->mailer();
                    do_action( 'wt_send_gift_coupon_to_customer',$order,$coupons);
                    $return = array(
                        'error' =>  false,
                        'message' => __('Coupons send successfully','wt-smart-coupons-for-woocommerce-pro')
                    );
                    echo json_encode($return);
                    die();
                    
                } 
            }
            echo json_encode($return);
            die();

        }
    }   

}