<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if( ! class_exists('WT_Giveaway_Free_Product_Admin') ) {
    class WT_Giveaway_Free_Product_Admin {


        public function __construct( ) {
    
        }

        public function add_give_way_coupon_data_tab( $tabs ) {


            $tabs['wt_give_away_free_product'] = array(
                'label'  => __( 'Giveaway Products', 'wt-smart-coupons-for-woocommerce-pro' ),
                'target' => 'wt_give_away_free_products',
                'class'  => '',
            );

            return $tabs;
        }

        /**
         * Giveaway Product tab content
         * @since 1.0.0
         * @moved into here on 1.2.1
         */
        public function give_away_free_product_tab_content( $post ) {
    
            ?>
            <div id="wt_give_away_free_products" class="panel woocommerce_options_panel">
                <?php
                    $free_products = get_post_meta( $post, '_wt_free_product_ids', true );
                    if( '' !=  $free_products &&  !is_array( $free_products ) ) {
                        $free_products = explode(',',$free_products);
                    }
                ?>
                <div class="options_group">
                    <p class="form-field"><label><?php _e( 'Free Products', 'wt-smart-coupons-for-woocommerce-pro' ); ?></label>
                        <select class="wc-product-search" multiple="multiple" style="width: 50%;" name="_wt_free_product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" data-action="woocommerce_json_search_products_and_variations_without_parent">
                            <?php
                            if(  $free_products ) {
                                foreach ( $free_products as $product_id ) {
                                    $product = wc_get_product( $product_id );
                                    if ( is_object( $product ) ) {
                                        echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                                    }
                                }
                            }
                                
                            ?>
                        </select> <?php echo wc_help_tip( __( 'Specified quantity of the selected free product/s is added to the customer cart when the coupon is applied successfully. In case of multiple products the customer will have to choose one from among the list.', 'wt-smart-coupons-for-woocommerce-pro' ) ); ?>
                    </p>

                    <?php
                    /**
                     * Add Quantity control for giveaway
                     * @since 1.2.6
                     */
                        $discount_quantity   = get_post_meta( $post, '_wt_product_discount_quantity', true );

                    ?>
                    <p class="form-field"><label><?php _e( 'Quantity', 'wt-smart-coupons-for-woocommerce-pro' ); ?></label>
                        <input type="number" step="1" min="1" name="_wt_product_discount_quantity" value="<?php echo ( '' !== $discount_quantity ) ? esc_attr( $discount_quantity ) : 1; ?>" placeholder="<?php echo esc_attr__( '1', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" style="width: 5em;"> 
                        <?php echo wc_help_tip( __( 'Specified quantity of the product will be added to the cart.', 'wt-smart-coupons-for-woocommerce-pro' ) ); ?>
                    </p>
                    <?php // End managing Quantity. ?>


                    <?php
                        $discount_amount    = get_post_meta( $post, '_wt_product_discount_amount', true );
                        $discount_type      = get_post_meta( $post, '_wt_product_discount_type', true );

                    ?>
                    
                    <div class = "give_away_product_discount">
                        <p class="form-field">
                            <label><?php echo esc_html__( 'Giveaway discount ', 'wt-smart-coupons-for-woocommerce-pro' ); ?></label>
                            <input type="number" step="0.01" name="_wt_product_discount_amount" value="<?php echo ( '' !== $discount_amount ) ? esc_attr( $discount_amount ) : ''; ?>" placeholder="<?php echo esc_attr__( '00.00', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" style="width: 5em;">
                            
                            <select name="_wt_product_discount_type">
                                <option value="percent" <?php selected( $discount_type, 'percent' ); ?>><?php echo esc_html__( '%', 'wt-smart-coupons-for-woocommerce-pro' ); ?></option>
                                <option value="flat" <?php selected( $discount_type, 'flat' ); ?>><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></option>
                            </select>
                            <?php echo wc_help_tip( esc_html__( 'Indicates the discount percentage/value of the giveaway product.e.g, you can giveaway a cap at 10% discount upon purchase of a T-shirt', 'wt-smart-coupons-for-woocommerce-pro' ) ); ?>
                        </p>
                    </div>

                    <?php
                        $wt_apply_discount_before_tax_calculation = get_post_meta( $post, 'wt_apply_discount_before_tax_calculation', true );
                        if( $wt_apply_discount_before_tax_calculation == '' ) {
                            $wt_apply_discount_before_tax_calculation = true;
                        }
                        woocommerce_wp_checkbox(
                            array(
                                'id'          => 'wt_apply_discount_before_tax_calculation',
                                'label'       => __( 'Apply tax only on discounted value', 'wt-smart-coupons-for-woocommerce-pro' ),
                                'description' =>  __( 'Enable this option to caculate the tax only on the discounted value. e.g if you are providing a discount of $10 on a $100 product, enabling this option will caluclate tax only on $90, which is the product giveaway price(sale price).', 'wt-smart-coupons-for-woocommerce-pro' ),
                                'value'       => wc_bool_to_string( $wt_apply_discount_before_tax_calculation  ),
                                'desc_tip'    => true,
                            )
                        );
                    ?>

                </div>
            </div>
                    


            <?php
        }

        public function process_shop_coupon_meta_give_away($post_id, $post) {
            // Giveaway free Products.

            if( isset($_POST['_wt_free_product_ids']) && $_POST['_wt_free_product_ids']!='' ) {

                update_post_meta($post_id, '_wt_free_product_ids', implode(',', Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_free_product_ids'], 'int_arr' ) ) );
            } else {
                update_post_meta($post_id, '_wt_free_product_ids', '');
            }
            if( isset( $_POST['wt_apply_discount_before_tax_calculation'] ) && $_POST['wt_apply_discount_before_tax_calculation'] =='yes' ) {

                update_post_meta($post_id, 'wt_apply_discount_before_tax_calculation',  1 );
            } else {
                update_post_meta($post_id, 'wt_apply_discount_before_tax_calculation', 0 );
            }

            if( isset( $_POST['_wt_product_discount_amount']) && '' != $_POST['_wt_product_discount_amount'] ) {
                update_post_meta($post_id, '_wt_product_discount_amount',Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_product_discount_amount'], 'float' ) );
            } else {
                update_post_meta($post_id, '_wt_product_discount_amount', '' );
            }
            if( isset( $_POST['_wt_product_discount_type']) && '' != $_POST['_wt_product_discount_type'] ) {
                update_post_meta($post_id, '_wt_product_discount_type',Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_product_discount_type'] ) );
            } else {
                update_post_meta($post_id, '_wt_product_discount_type', 'percent' );
            }

            if( isset( $_POST['_wt_product_discount_quantity']) && '' != $_POST['_wt_product_discount_quantity'] ) {
                update_post_meta($post_id, '_wt_product_discount_quantity',Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_product_discount_quantity'], 'int' ) );
            } else {

                update_post_meta($post_id, '_wt_product_discount_quantity', 1);
            }
        }

        /**
         * Alter product search - disable parent to lsit
         * @since 1.2.4
         */
        function wt_products_and_variations_no_parent() {

            check_ajax_referer( 'search-products', 'security' );
            if (!current_user_can('manage_woocommerce')) 
            {
                wp_die(__('You do not have sufficient permission to perform this operation', 'wt-smart-coupons-for-woocommerce-pro'));
            }
            add_filter('woocommerce_json_search_found_products',array($this,'exclude_parent_product_from_search'),10,1);
            
            $products = WC_AJAX::json_search_products('',true);
        }

        /**
	 * Exclude Parent Product from product search
	 * @since 1.2.4
	 */
	function exclude_parent_product_from_search( $products ) {
		foreach( $products as $product_id =>$product ) {
			$product_obj = wc_get_product( $product_id );
			if( $product_obj->has_child() ) {
				unset( $products[$product_id] );
			}
		}
		wp_send_json( $products );
	}
    }

}
