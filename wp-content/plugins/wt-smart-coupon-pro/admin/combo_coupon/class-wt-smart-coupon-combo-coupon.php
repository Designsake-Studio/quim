<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if( ! class_exists ( 'Wt_Smart_Coupon_Combo_Coupon' ) ) {


    class Wt_Smart_Coupon_Combo_Coupon {
        public function __construct( ) {
            
        }
        /**
         * Create Combo coupon fields.
         * @since 1.2.0
         */
        function admin_coupon_usage_restrictions($post ) {
            $coupon    = new WC_Coupon( $post );

            $all_discount_types = wc_get_coupon_types();
            $coupon_ids = get_post_meta( $post, '_wt_combo_coupon_can_use_with', true );
            $coupon_ides = array_filter( explode( ',', $coupon_ids ));

            $individual_use = $coupon->get_individual_use();
            
            if( $individual_use  ) {
                $style = 'display:none';
            } else {
                $style = '';
            }

            ?>
                <div class="wt_combo_coupon_fields" style="<?php echo $style; ?>">
                
                 <p class="form-field _wt_combo_coupon_field" id="sc-field">
				<label for="_wt_combo_coupon_can_use_with"><?php echo esc_html__( 'Coupon can be used with', 'wt-smart-coupons-for-woocommerce-pro'); ?></label>

					<select class="wt-coupon-search" style="width: 50%;" multiple="multiple" id="_wt_combo_coupon_can_use_with" name="_wt_combo_coupon_can_use_with[]" data-placeholder="<?php echo esc_attr__( 'Search for a coupon&hellip;', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" data-action="wt_json_search_coupons" data-security="<?php echo esc_attr( wp_create_nonce( 'search-coupons' ) ); ?>" data-postid= <?php echo $post; ?> >
						<?php

						if ( ! empty( $coupon_ides ) ) {
							foreach ( $coupon_ides as $coupon_id ) {
                                $coupon_title = get_the_title( $coupon_id );

								$coupon = new WC_Coupon( $coupon_title );

								$discount_type = $coupon->get_discount_type();

								if ( ! empty( $discount_type ) ) {
									$discount_type = sprintf( __( ' ( %1$s: %2$s )', 'wt-smart-coupons-for-woocommerce-pro' ), __( 'Type', 'wt-smart-coupons-for-woocommerce-pro' ), $all_discount_types[ $discount_type ] );
								}

								echo '<option value="' . esc_attr( $coupon_id ) . '"' . selected( true, true, false ) . '>' . esc_html( $coupon_title . $discount_type ) . '</option>';
							}
						}
						?>

					</select>
                    <?php echo wc_help_tip( __('Configure the list of coupons that can be redeemed together with the specified.','wt-smart-coupons-for-woocommerce-pro') ); ?>

                </p>

                <?php

                    $coupon_ids = get_post_meta( $post, '_wt_combo_coupon_cannot_use_with', true );
                    $coupon_ides = array_filter( explode( ',', $coupon_ids ) );

                ?>

                <p class="form-field _wt_combo_coupon_field" id="sc-field">
				<label for="_wt_combo_coupon_cannot_use_with"><?php echo esc_html__( 'Coupon can\'t be used with', 'wt-smart-coupons-for-woocommerce-pro'); ?></label>

					<select class="wt-coupon-search" style="width: 50%;" multiple="multiple" id="_wt_combo_coupon_cannot_use_with" name="_wt_combo_coupon_cannot_use_with[]" data-placeholder="<?php echo esc_attr__( 'Search for a coupon&hellip;', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" data-action="wt_json_search_coupons" data-security="<?php echo esc_attr( wp_create_nonce( 'search-coupons' ) ); ?>" data-postid= <?php echo $post; ?> >
						<?php

						if ( ! empty( $coupon_ides ) ) {
							foreach ( $coupon_ides as $coupon_id ) {
                                $coupon_title = get_the_title( $coupon_id );

								$coupon = new WC_Coupon( $coupon_title );

								$discount_type = $coupon->get_discount_type();

								if ( ! empty( $discount_type ) ) {
									$discount_type = sprintf( __( ' ( %1$s: %2$s )', 'wt-smart-coupons-for-woocommerce-pro' ), __( 'Type', 'wt-smart-coupons-for-woocommerce-pro' ), $all_discount_types[ $discount_type ] );
								}

								echo '<option value="' . esc_attr( $coupon_id ) . '"' . selected( true, true, false ) . '>' . esc_html( $coupon_title . $discount_type ) . '</option>';
							}
						}
						?>

					</select>
                    <?php echo wc_help_tip( __('Configure the list of coupons that cannot be redeemed together with the specified.','wt-smart-coupons-for-woocommerce-pro') ); ?>

                </p>
                </div>

            <?php
        }

        /**
         * Save combo coupon meta values
         * @since 1.2.0
         */
        function save_combo_coupon_meta( $post_id, $post  ) {
            
            if( ! isset( $_POST['individual_use'] ) ||  $_POST['individual_use'] == '' ) {
                if( isset( $_POST['_wt_combo_coupon_can_use_with'] ) && !empty(  $_POST['_wt_combo_coupon_can_use_with'] )  ) {
                    update_post_meta($post_id, '_wt_combo_coupon_can_use_with',  implode(',',$_POST['_wt_combo_coupon_can_use_with']) );
                } else {
                    update_post_meta($post_id, '_wt_combo_coupon_can_use_with', '' );

                }

                if( isset( $_POST['_wt_combo_coupon_cannot_use_with'] ) && !empty(  $_POST['_wt_combo_coupon_cannot_use_with'] )  ) {
                    update_post_meta($post_id, '_wt_combo_coupon_cannot_use_with',  implode(',',$_POST['_wt_combo_coupon_cannot_use_with']) );
                } else {
                    update_post_meta($post_id, '_wt_combo_coupon_cannot_use_with', '' );
                }
            }
        }

        /**
         * Validate combo coupon option.
         * @since 1.2.0
         */
        function validate_coupon_with_combo_coupon( $valid, $coupon ) {
            // return false;
            if (! $valid) {
                return false;
            }

            
            global $woocommerce;
            $coupon_id  = $coupon->get_id();
            $applied_coupons = $woocommerce->cart->applied_coupons;

            if( empty( $applied_coupons )) {
                return $valid;
            }
            $applied_coupon_ids = array();
            foreach( $applied_coupons as $applied_coupon ) {
                $ap_coupon_id = wc_get_coupon_id_by_code( $applied_coupon );
                if($ap_coupon_id == $coupon_id ) {
                    continue;
                }
                $applied_coupon_ids[] = $ap_coupon_id;
            }
            
            $coupon_cannot_use_with = get_post_meta( $coupon_id, '_wt_combo_coupon_cannot_use_with', true );
            $cannot_ids = explode( ',',$coupon_cannot_use_with );

            if( ! empty( $cannot_ids ) && ! empty(array_intersect($cannot_ids, $applied_coupon_ids) ) ) {
                $valid = false;
                throw new Exception( __( 'Sorry, this coupon cannot be used in conjunction with the applied coupon', 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
            }


            $coupon_can_use_with = get_post_meta( $coupon_id, '_wt_combo_coupon_can_use_with', true );
            $can_ids = array_filter( explode( ',',$coupon_can_use_with ) );
            $array_diff = array_diff( $applied_coupon_ids,$can_ids );
            if( ! empty( $can_ids ) &&  ! empty(  $array_diff ) ) {
                $valid = false;
                throw new Exception( __( 'Sorry, this coupon cannot be used in conjunction with the applied coupon', 'wt-smart-coupons-for-woocommerce-pro' ), 110 );
            }
           

            return $valid;
            
        }


    }
}