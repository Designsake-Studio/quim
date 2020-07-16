<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
class WT_Giveaway_Free_Product {


    public function __construct( ) {

    }
    /**
     * Display give away products
     */
    public function add_give_away_products_with_coupon($valid, $coupon) {

        if (!$valid) {
            return false;
        }

        $coupon_id  = $coupon->get_id();
        //  display Giveaway products
        $free_products = get_post_meta( $coupon_id, '_wt_free_product_ids', true );

        if( ''!= $free_products && ! is_array( $free_products ) ) {
            $free_products = explode(',',$free_products);
        } else {
            $free_products = array();
        }

        if( !empty( $free_products )) {
            
            $first_product = wc_get_product( $free_products[0] );

            if( sizeof($free_products ) == 1 && ( ! $first_product->has_child() ) ) {
                add_action('woocommerce_applied_coupon', array($this,'add_free_product_into_cart'),10,1  );
            } else {
                
                add_action('woocommerce_cart_contents',array($this,'display_give_away_products') );

            }
        }

        return $valid;
    }

    /**
     * Add Giveaway product into cart ( When product is single )
     * @since 1.0.0
     */
    public  function add_free_product_into_cart( $coupon_code  ) {
        $free_products = $this->get_free_product_for_a_coupon( $coupon_code );
        if( empty( $free_products ) )  return;
        $first_product = wc_get_product( $free_products[0] );
        
        if( $this->is_give_away_is_fully_free( $coupon_code,$first_product->get_price() ) ) {
            wc_add_notice( __('Congratulations! you got a free gift!','wt-smart-coupons-for-woocommerce-pro' ), 'success' );

        } else {
            $discount_text = $this->get_give_away_discount_text( $coupon_code );

            wc_add_notice( sprintf(__('Congratulations! <br/>
            A discounted product has been added to your cart at %s discount.','wt-smart-coupons-for-woocommerce-pro' ),$discount_text ), 'success' );

        }
        $free_product_id = $free_products[0];
        $found 		= false;

        /**
         * Managing Quantity for giveaway item
         *  @since 1.2.6
         */
        $quantity_of_give_away_item = $this->get_give_away_quantity( $coupon_code );

        /** End Managing Quantity */

        // if ( sizeof( WC()->cart->get_cart() ) > 0 ) { // no need to check they can purchase the give away only.
            if( !$found) {
                $quantity  = $quantity_of_give_away_item;
                $cart_item_data = array(
                    'free_product' => 'wt_give_away_product',
                    'free_gift_coupon' => $coupon_code
                );

                $variation_id = '';
                $variation = array();
                WC()->cart->add_to_cart( $free_product_id, $quantity, $variation_id, $variation,$cart_item_data  );

            }
        // }
    }


    /**
     * Remove Free Prodiuct from cart ( Hook to When Coupon removed)
     * @since 1.0.0
     */

    public function remove_free_product_from_cart( $coupon_code ) {

        global $woocommerce;
        $applied_coupons  = $woocommerce->cart->applied_coupons;
        if (isset($coupon_code) && !empty($coupon_code)) {
            if (!in_array($coupon_code,$applied_coupons)) {

                $free_products = $this->get_free_product_for_a_coupon( $coupon_code );
                if( empty( $free_products ) )  return;
                foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

                    $_product = $values['data'];
                    if ( in_array( $_product->get_id(),$free_products )  && isset( $values['free_product'] ) && $values['free_product'] == "wt_give_away_product" ){
                        WC()->cart->remove_cart_item( $cart_item_key );
                    }
                }
            }
        }
    }

    /**
     * get free product applicable for a coupon
     * @since 1.0.0
    */

    public function get_free_product_for_a_coupon( $coupon_code ) {
        $coupon = new WC_Coupon($coupon_code);
        $coupon_id      = $coupon->get_id();
        $free_products  = get_post_meta( $coupon_id, '_wt_free_product_ids', true );


        if( ''!= $free_products && ! is_array( $free_products ) ) {
            $free_products = explode(',',$free_products);
        } else {
            $free_products = array();
        }

        return $free_products;
    }


    /**
     * Action function folr displaying give-away products on cart.
     * @since 1.0.0
     */
    public function display_give_away_products(  ) {
        $discount_text = '';
        echo '<tr><td colspan=6>';
        global $woocommerce;
        $applied_coupons  = $woocommerce->cart->applied_coupons;
        
        $free_products =  array();
        $free_prodcut_coupon = array();

        foreach( $applied_coupons as $coupon ) {
            $coupon_id =  wc_get_coupon_id_by_code( $coupon) ;
            $products = get_post_meta( $coupon_id, '_wt_free_product_ids', true );
            
            if( ! is_array( $products ) && '' != $products ) {
                $products = explode(',',$products);
            }
            
            if( !empty ($products  )) {
                $free_products  = array_merge($free_products, $products );
                
                
                // $coupon_details = array(
                //     'coupon' => $coupon_id,
                //     'products' => $products
                // );
                $free_prodcut_coupon[$coupon_id] = $products;
                // array_push( $free_prodcut_coupon , $coupon_details ) ;

                
            }

        }
        if( empty($free_products) ) {
            return;
        }
        $first_product = wc_get_product( $free_products[0] );


        if( sizeof($free_products ) == 1 && $first_product->is_type( 'simple' ) ) {
            return;
            // Product already added to cart.

        } else {

            foreach( $free_prodcut_coupon as $coupon_id => $free_product_items ) {
                $contains_free_product = $this->is_cart_contains_free_products( get_the_title( $coupon_id) );
                if( sizeof($free_product_items ) == 1 || ( is_array( $contains_free_product ) && !empty($contains_free_product)  ) ) {
                    continue;
                }
                if( $this->is_give_away_is_hundred_percent(  $coupon_id ) ) {
                    $choose_gift = __('Congratulations! <br/> Choose from one of the free gifts.','wt-smart-coupons-for-woocommerce-pro');
    
                } else {
                    $discount_text = $this->get_give_away_discount_text( $coupon_id );

                    $choose_gift = sprintf(__('Congratulations! <br/> You can now choose from one of the products at %s discount.','wt-smart-coupons-for-woocommerce-pro'),$discount_text);
                    

                }
                $message = '<h4 class="giveaway-title">'.$choose_gift.'<span class="coupon-code">[ '.get_the_title( $coupon_id) .' ]</span></h4>';
                $message = apply_filters( 'wt_smartcoupon_give_away_message', $message, $discount_text, $coupon_id );
                echo $message;
            ?>
            <ul class="woocommcerce wt_give_away_products" coupon = <?php echo $coupon_id; ?> >

            <?php
            
            foreach( $free_product_items as $product_id ) {

                $_product = wc_get_product( $product_id );
                
               if( $_product->get_stock_quantity() &&   $_product->get_stock_quantity() < 1 ) {
                   continue;
               }

                $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );
                ?>
                <li class="wt_get_away_product">
                    <div class="wt_product_image">
                        <img src="<?php  echo $image[0]; ?>" data-id="<?php echo $product_id; ?>" />
                    <div class="wt_choose_button_outer">
                            <button class="wt_choose_free_product" prod-id="<?php echo $product_id; ?>" variation="0">Choose Product</button>
                        </div>

                    </div>
                    <a href="<?php echo get_post_permalink($product_id); ?>" >
                        <?php echo get_the_title( $product_id ); ?>
                    </a>
                    <p><?php echo $_product->get_price_html(); ?></p>
                    <?php
                    if ( $_product->is_type( 'variable' ) ) {
                        ?>
                    
                        <?php if ( empty( $_product->get_available_variations() ) && false !== $_product->get_available_variations() ) : ?>
                            <p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
                        <?php else : ?>
                            <table class="variations wt_variations" cellspacing="0">
                                <tbody>
                                    <?php 
                                    $selected_options = array();
                                    foreach ( $_product->get_variation_attributes() as $attribute_name => $options ) : ?>
                                        <tr>
                                            <td class="label"><label for="<?php echo sanitize_title( $attribute_name ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label></td>
                                            <td class="value">
                                                <?php
                                                    $selected = $options[0];
                                                    $selected_options['attribute_'."$attribute_name"] = $options[0];
                                                    wc_dropdown_variation_attribute_options( 
                                                        array( 
                                                            'options'           => $options,
                                                            'attribute'         => $attribute_name,
                                                            'product'           => $_product,
                                                            'selected'          => $selected,
                                                            'class'             => 'wt_give_away_product_attr',
                                                            'show_option_none'  => false
                                                        ) 
                                                    );
                                                    
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>

                            <?php endif; ?>
                            <?php 
                            $variation_id = $this->find_matching_product_variation_id($product_id,$selected_options);
                            ?>
                            <input type="hidden" name="variation_id" value="<?php echo $variation_id; ?>" />
                            <input type="hidden" name="wt_product_id" value="<?php echo $product_id; ?>" />
                            <input type="hidden" name="wt_variation_options" value='<?php echo json_encode($selected_options); ?>' />
                    
                        <?php
                        
                    }
                    

                    ?>
            </li>
            <?php
            }
            ?>
            </ul>

            <?php } ?>

        <?php
        }

        echo '</td></tr>';

    }

    /**
     * Check whether cart contains any Giveaway products from given coupon
     * @since 1.0.0
     */
    public function is_cart_contains_free_products( $coupon_code='' ) {

        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        
        $free_gift_items = array();
        $cart_items  = array();
        foreach( $items as $item ) {
            if( $this->is_a_free_gift_item( $item,$coupon_code ) ) {
                $cart_items[] =  $item;
            }
        }
        if( ! empty( $cart_items ) ) {
            return $cart_items;
        }
        return false;
    }

    /**
     * Function for getting Giveaway products based on coupons applied.
     * @since 1.0.0
     */
    public function get_free_products() {
        global $woocommerce;
        $applied_coupons  = $woocommerce->cart->applied_coupons;
        if( empty($applied_coupons)){
            return false;
        }
        
        $free_products =  array();

        foreach( $applied_coupons as $coupon ) {
            $coupon_id =  wc_get_coupon_id_by_code( $coupon) ;
            $products = get_post_meta( $coupon_id, '_wt_free_product_ids', true );
            if( ! is_array( $products ) ) {
                    $products = explode(',',$products);
                }
            
            $free_products  = array_merge($free_products, $products );

        }
        return $free_products ;
    }

    /**
     * function for checking a cart item is a giveaway.
     * @since 1.0.0
     */
    public function is_a_free_gift_item( $cart_item,$coupon_code='' ) {
        
        if( !empty( $this->get_free_products() ) && isset( $cart_item['free_product']) && $cart_item['free_product']=='wt_give_away_product'  ) {

            if( '' == $coupon_code || strtolower( $cart_item['free_gift_coupon'] ) == strtolower( $coupon_code ) ) {
                return true;
            } else {
                return false;
            }
            return true;

        }
        return false;
    }

    /**
     * filter function for updating cart item price ( Displaying cart item price in cart and checkout page )
     * @param $price Price html.
     * @param $cart_item Cart item object
     * @since 1.0.0
     */
    public function add_custom_cart_item_total( $price,$cart_item ) {

        global $woocommerce;
        $free_products = $this->get_free_products();

        $free_gift_cupon = ( isset( $cart_item['free_gift_coupon'] ) )?  $cart_item['free_gift_coupon'] : '';
        $free_gift_cupon_id =  wc_get_coupon_id_by_code( $free_gift_cupon) ;
        // Check whether discount is applied already.
        $is_discount_before_tax = get_post_meta( $free_gift_cupon_id,'wt_apply_discount_before_tax_calculation',true );
        
        $product_id = $cart_item['product_id'];
        
        if( isset( $cart_item['variation_id']) && $cart_item['variation_id'] > 0 ) {
            $product_id = $cart_item['variation_id'];

        }
        if ( empty($free_products) || ! in_array( $product_id,$free_products ) || ! ( $this->is_a_free_gift_item( $cart_item ) )   ) {
            return $price;
        }
        
        $_product = wc_get_product( $product_id );
        
        
        $product_price = $_product->get_price();
        $coupon_code                    = $cart_item['free_gift_coupon'];
        $discount = $this->get_available_discount_for_give_away_product( $coupon_code,$_product,$product_id );
        $sale_price_after_discount = ( $product_price - $discount ) * $cart_item['quantity'] ;

        if( $is_discount_before_tax ) {
            $return = '<del><span>'.Wt_Smart_Coupon_Admin::get_formatted_price( ( number_format((float) $product_price * $cart_item['quantity'],2,'.','' ) ) ) .'</span></del> <span>'. Wt_Smart_Coupon_Admin::get_formatted_price( ( number_format((float) $sale_price_after_discount,2,'.','' ) ) ).'</span>' ;

        } else {
            $return = '<del><span>'.$price.'</span></del> <span>'.Wt_Smart_Coupon_Admin::get_formatted_price(( number_format((float) $sale_price_after_discount,2,'.','' ) )).'</span>' ;
        }
        return $return;
    }

    /**
     * Function for updating cart item price display ( used when applly give away discount before tax calculation).
     * @since 1.2.4
     */
    function update_cart_item_price( $price, $cart_item ) {
        global $woocommerce;
        $free_products = $this->get_free_products();

        $free_gift_cupon = ( isset( $cart_item['free_gift_coupon'] ) )?  $cart_item['free_gift_coupon'] : '';
        $free_gift_cupon_id =  wc_get_coupon_id_by_code( $free_gift_cupon) ;
        // Check whether discount is applied already.
        $is_discount_before_tax = get_post_meta( $free_gift_cupon_id,'wt_apply_discount_before_tax_calculation',true );
        
        if( $is_discount_before_tax ) {
            
            $_product = wc_get_product( $cart_item['product_id'] );
            if( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] > 0 ) {
                $_product = wc_get_product( $cart_item['variation_id'] );

            }
            
            $product_price = $_product->get_price();
            $price = '<span>'.Wt_Smart_Coupon_Admin::get_formatted_price( ( number_format((float) $product_price,2,'.','' ) ) ).'</span>' ;
        }

        return $price;

    }

    /**
     * Function for getting variation id from product and selected attributes
     * @param $prodcut_id Given Product Id.
     * @param $attributes Attribute values ad key value pair.
     * @since 1.0.0
     */

    public  function  find_matching_product_variation_id($product_id, $attributes) {
        return (new \WC_Product_Data_Store_CPT())->find_matching_product_variation(
            new \WC_Product($product_id),
            $attributes
        );
    }

    /**
     * Ajax action function for getting variation id
     * @since 1.0.0
     */

    public function ajax_find_matching_product_variation_id() {

        check_ajax_referer( 'wt_smart_coupons_public', '_wpnonce' );
        if( isset( $_POST['attributes'] ) && isset( $_POST['product'] )  ) {
            $product_id = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['product'], 'int' );
            $attributes = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['attributes'], 'text_arr' );
            if( $product_id == '' || empty( $attributes )) {
                echo 'invalid request!';
                return false;
                wp_die();
            }
            echo $this->find_matching_product_variation_id( $product_id, $attributes );
            wp_die();
        }

        echo 'invalid request!';
        return false;
        wp_die();
    }

    /**
     * Ajax action function for adding Giveaway products into cart.
     * @since 1.0.0
     */
    public function add_to_cart( ) {
       
        check_ajax_referer( 'wt_smart_coupons_public', '_wpnonce' );
        if( isset( $_POST['product_id'] ) && isset( $_POST['variation_id'] ) ) {
            $product_id     = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['product_id'], 'int' );
            $variation_id   = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['variation_id'], 'int' );
        } else {
            echo 'invalid request!';
            wp_die();
        }

        $coupon_code = isset( $_POST['applied_coupon'] )?  Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['applied_coupon'] ) : '';

        if( ! $product_id || '' == $coupon_code ) {
            return false;
        }
        $coupon_code = get_the_title( $coupon_code );
        $_product =  wc_get_product( $product_id );

        if( $_product->is_type('variable') ) {

            $attributes  = isset( $_POST['attributes'] ) ? Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['attributes'], 'text_arr' ) : '';

            $attributes = json_decode( stripslashes($attributes ));

            if( !empty($attributes) ) {
                foreach( $attributes as $key => $value ) {
                    $variation_key = str_replace('attribute_','',$key);
                    $variation[$variation_key] = $value;
                }
            }
            $quantity =   1;

            if(! $variation_id) {
                echo 'no variation fount! ';
                wp_die();
            }
            

            $cart_item_data = array();
            if( ! $this->is_cart_contains_free_products( $coupon_code ) ) {
                $cart_item_data = array(
                    'free_product'      => 'wt_give_away_product',
                    'free_gift_coupon'  => $coupon_code
                );
                $quantity = $this->get_give_away_quantity( $coupon_code );
            }

            WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation,$cart_item_data );
            echo 'success';
            wp_die();

        }

        $quantity = 1;
        $variation_id = '';
        $variation  = array();


        $cart_item_data = array();
        if( ! $free_items  = $this->is_cart_contains_free_products( $coupon_code ) ) {
            $cart_item_data = array(
                'free_product' => 'wt_give_away_product',
                'free_gift_coupon' => $coupon_code
            );
            $quantity = $this->get_give_away_quantity( $coupon_code );
        }

        
        if( WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation,$cart_item_data  ) ) {
            echo 'success';
            wp_die();

        }
        wp_die();

    }

    /**
     * Action function for displaying description for Givesaway product on cart page
     * @Since 1.0.0
     */
    public function display_give_away_product_description( $cart_item ) {
        $product_id     = $cart_item['product_id'];
        $variation_id   = $cart_item['variation_id'];

        if( $this->is_a_free_gift_item( $cart_item ) ) {
            $coupon_code = $cart_item['free_gift_coupon'];

            $product_id = $cart_item['product_id'];
            $variation_id = $cart_item['variation_id'];
            if( $variation_id > 0 ) {
                $product = wc_get_product( $variation_id  );
            } else {
                $product = wc_get_product( $product_id  );
            }

            if( $this->is_give_away_is_fully_free( $coupon_code,$product->get_price() ) ) {
                $free_gift_text = __('It\'s a free gift for you','wt-smart-coupons-for-woocommerce-pro');
            }else {
                $discount_text = $this->get_give_away_discount_text( $coupon_code );
                $free_gift_text = sprintf( __('Product added with %s discount ','wt-smart-coupons-for-woocommerce-pro'),$discount_text );
            }
            echo '<p style="color:green;clear:both">'.$free_gift_text.'</p>';
        }
    }

    /**
     *  Calculate the Cart Total after reducing the free product price.
     * @since 1.0.0.
    */

    public function discounted_calculated_total( $total, $cart_object ){
        $gift_items = $this->is_cart_contains_free_products( );

        if( !empty( $gift_items ) ) {
            $new_total = $total;
            foreach( $gift_items as $gift_item ) {
                $free_gift_cupon = $gift_item['free_gift_coupon'];
                $free_gift_cupon_id =  wc_get_coupon_id_by_code( $free_gift_cupon) ;

                $is_discount_before_tax = get_post_meta( $free_gift_cupon_id,'wt_apply_discount_before_tax_calculation',true );
                if( $is_discount_before_tax ) {
                   // return $total;
                   continue;
                }
                $product_id = $gift_item['product_id'];
                if( isset( $gift_item['variation_id'] ) && $gift_item['variation_id']  > 0 ) {
                    $product_id = $gift_item['variation_id'];
                }
                $_product = wc_get_product( $product_id );
    
                $discount = $this->get_available_discount_for_give_away_product( $free_gift_cupon,$_product,$product_id );
                $new_total = $new_total - $discount;
            }
            
            return round( $new_total, $cart_object->dp );
        }
        return $total;
        
    
    }

    /**
     * Add Free gift item price details into cart and checkout.
     * @since 1.0.0
    */
    public function add_give_away_product_discount() {
        $gift_items = $this->is_cart_contains_free_products();
        if( !empty( $gift_items ) ) {
            foreach( $gift_items as $gift_item ) {
                $free_gift_cupon = $gift_item['free_gift_coupon'];
                $free_gift_cupon_id =  wc_get_coupon_id_by_code( $free_gift_cupon) ;
                $is_discount_before_tax = get_post_meta( $free_gift_cupon_id,'wt_apply_discount_before_tax_calculation',true );
                if( $is_discount_before_tax ) {
                    continue;
                }
                if( $gift_item) {
                    $_product_id =  $gift_item['product_id'];

                    if( isset( $gift_item['variation_id'] ) && $gift_item['variation_id']  > 0 ) {
                        $_product_id =  $gift_item['variation_id'];
                    }
                    $_product = wc_get_product( $_product_id );

                    $discount = $this->get_available_discount_for_give_away_product($free_gift_cupon,$_product,$_product_id );
                    
                    ?>
                    <tr class="woocommerce-give_away_product wt_give_away_product">
                        <th><?php _e( 'Free Gift Item', 'wt-smart-coupons-for-woocommerce-pro' ); ?></th>
                        <td>-<?php echo  Wt_Smart_Coupon_Admin::get_formatted_price( ( number_format((float) $discount,2,'.','' ) ) ) ?></td>
                
                    </tr>
                
                <?php
                }
            }
        }

    
    }

    
    /**
     * Add Free Prodcut details on cart item list.
     * @since 1.0.0
    */
    function add_free_product_details_into_order( $item, $cart_item_key, $values, $order ) {
        if ( empty( $values['free_product'] ) ) {
            return;
        }
        
        $item->add_meta_data( 'free_product' , '<p style="color:green">Its a free Product</p> ' );
        $item->add_meta_data( 'free_gift_coupon' , $values['free_gift_coupon'] );
    }

    /**
     * Display free product Discount Detail on order details.
     * @since 1.0.0
     */
    function woocommerce_get_order_item_totals( $total_rows, $tax_display  ) {
        $order = $tax_display;
        $order_items = $order->get_items();

        foreach( $order_items as $id => $order_item ) {
            $free_item = wc_get_order_item_meta($id,'free_product',true);

            $free_gift_cupon = wc_get_order_item_meta($id,'free_gift_coupon',true);
            $free_gift_cupon_id =  wc_get_coupon_id_by_code( $free_gift_cupon) ;
            $is_discount_before_tax = get_post_meta( $free_gift_cupon_id,'wt_apply_discount_before_tax_calculation',true );
            

            if( !empty($free_item) && ! $is_discount_before_tax ) {
                $product_id = $order_item['product_id'];
                if(  $order_item['variation_id']) {
                    $product_id  = $order_item['variation_id'];
                }
                $_product  = wc_get_product( $product_id ) ;
                if( ! $_product instanceof WC_Product ) {
                    continue;
                }
                $product_price = $_product->get_price();
                $quantity = $order_item['quantity'];
                $coupon_code = $order_item['free_gift_coupon'];
                $discount = $this->get_available_discount_for_give_away_product( $coupon_code,$_product,$product_id );
                $sale_price_after_discount = ( $product_price - $discount ) * $cart_item['quantity'];

                $value = '<del><span>'.Wt_Smart_Coupon_Admin::get_formatted_price( ( number_format((float) $product_price,2,'.','' ) ) ).'</span></del> <span>'.Wt_Smart_Coupon_Admin::get_formatted_price( ( number_format((float) $sale_price_after_discount,2,'.','' ) ) ).'</span>' ;

                $key = 'shipping';
                $offset = array_search($key, array_keys($total_rows));

                $total_rows = array_merge
                        (
                            array_slice($total_rows, 0, $offset),
                            array(
                                'free_product' => array(
                                    'label' => __( 'Free Product:', 'wt-smart-coupons-for-woocommerce-pro' ),
                                    'value' => $value
                                )
                            ),
                            array_slice($total_rows, $offset, null)
                        );
            }

        }
        return $total_rows;
    }

    /**
     * Manage Item Meta on order page
     * @since 1.0.0
     */
    
    function  unset_free_product_order_item_meta_data( $formatted_meta, $item ) {

        foreach( $formatted_meta as $key => $meta ) {
            if( in_array( $meta->key, array('free_product','free_gift_coupon') ) ) {
                unset($formatted_meta[$key]);
            }
                
        }
        return $formatted_meta;
    }

    /**
     *  Update cart item value for applaying price before tax calculation.
     * @since 1.0.0
     */
    function update_cart_item_in_session( $session_data = array(), $values = array(), $key = '' ) {
        if( isset( $session_data['free_product'] ) &&  $session_data['free_product'] == 'wt_give_away_product' && isset( $session_data['free_gift_coupon'] ) ) {
            
            $free_gift_cupon = $session_data['free_gift_coupon'];

            $free_gift_cupon_id =  wc_get_coupon_id_by_code( $free_gift_cupon) ;
            $is_discount_before_tax = get_post_meta( $free_gift_cupon_id,'wt_apply_discount_before_tax_calculation',true );
            if( ! $is_discount_before_tax ) {
                return $session_data;
            }
            $qty            = ( isset( $session_data['quantity'] ))?  $session_data['quantity'] :  1 ;
            
            $session_data    = $this->update_cart_item_values( $session_data, $session_data['product_id'], $session_data['variation_id'], $qty );
        }
        

        return $session_data;
    }

    /**
     * Update Cart item values
     * @since 1.0.0
     */
    function update_cart_item_values( $cart_item_data, $product_id = 0, $variation_id = 0, $qty = 1 ) {
        $product_id     = $cart_item_data['product_id'];
        $variation_id   = $cart_item_data['variation_id'];
        
        if( isset( $cart_item_data['free_product'] ) &&  $cart_item_data['free_product'] == 'wt_give_away_product' ) {
            
            $free_gift_cupon = $cart_item_data['free_gift_coupon'];
            $free_gift_cupon_id =  wc_get_coupon_id_by_code( $free_gift_cupon) ;
            $is_discount_before_tax = get_post_meta( $free_gift_cupon_id,'wt_apply_discount_before_tax_calculation',true );
            if( ! $is_discount_before_tax ) {
                return $cart_item_data;
            }
            
            $_product_id    =    $product_id;
            if ( ! empty( $variation_id ) ) {
                $product = wc_get_product( $variation_id );
                $_product_id    =    $variation_id;

            } else {
                $product = wc_get_product( $product_id );
            }
            $product_price  = $product->get_price();
            $coupon_code    = $cart_item_data['free_gift_coupon'];
            
            $discount = $this->get_available_discount_for_give_away_product( $coupon_code,$product,$_product_id );
            $discounted_price = ( $product_price - $discount )   ;
            $cart_item_data['data']->set_price( $discounted_price );
            $cart_item_data['data']->set_regular_price( $product_price );
            $cart_item_data['data']->set_sale_price( $discounted_price );

        }

        return $cart_item_data;
    }

    /**
     * Update Cart item Quantity field non editable
     * @since 1.0.0
     */
    public function update_cart_item_quantity_field( $product_quantity = '', $cart_item_key = '', $cart_item = array() ) {

        if( isset( $cart_item['free_product'] ) &&  $cart_item['free_product'] == 'wt_give_away_product' ) {
        
            $product_quantity = sprintf( '%s <input type="hidden" name="cart[%s][qty]" value="%s" />', $cart_item['quantity'], $cart_item_key, $cart_item['quantity'] );
        }

        return $product_quantity;
    }

    /**
     * function to get actual discount available for giveaway item.
     * @since 1.2.4
     */
    function get_available_discount_for_give_away_product( $coupon_code,$product,$_product_id ) {
        if( $product->is_on_sale() ) {
            $product_price =  $product->get_sale_price();
        } else {
            $product_price = $product->get_regular_price();
        }
        $coupon_id                      = wc_get_coupon_id_by_code( $coupon_code );
        $wt_product_discount_amount     = get_post_meta( $coupon_id, '_wt_product_discount_amount',true );
        $wt_product_discount_type       = get_post_meta( $coupon_id, '_wt_product_discount_type',true );
        if( isset( $wt_product_discount_amount ) && ''!= $wt_product_discount_amount && 
            isset( $wt_product_discount_type ) && ''!= $wt_product_discount_type  ) {
                if( $wt_product_discount_type == 'percent' ) {
                    $discount =  ( $product_price * $wt_product_discount_amount/100 ) ;
                } else {
                    $discount =  min( $wt_product_discount_amount,$product_price );
                }
                
                return $discount;
            }

            return $product_price;
    }

    /**
     * function to check is 100% free giveaway item
     * @since 1.2.4
     */
    function is_give_away_is_fully_free( $coupon_code,$product_price ) {
        $coupon_id                      = wc_get_coupon_id_by_code( $coupon_code );
        $wt_product_discount_amount     = get_post_meta( $coupon_id, '_wt_product_discount_amount',true );
        $wt_product_discount_type       = get_post_meta( $coupon_id, '_wt_product_discount_type',true );
        $discount = $product_price;
        if( isset( $wt_product_discount_amount ) && ''!= $wt_product_discount_amount && 
            isset( $wt_product_discount_type ) && ''!= $wt_product_discount_type  ) {
                if( $wt_product_discount_type == 'percent' ) {
                    $discount =  ( $product_price * $wt_product_discount_amount/100 ) ;
                } else {
                    $discount =  min( $wt_product_discount_amount,$product_price );
                }
            }
            return ( $discount == $product_price )? true : false;
    }

    /**
     * helper function to check the product 100%
     * @since 1.2.4
     */
    function is_give_away_is_hundred_percent( $coupon_code ) {
        if( is_int($coupon_code)) {
            $coupon_id = $coupon_code;
        } else {
            $coupon_id  = wc_get_coupon_id_by_code( $coupon_code );
        }
        $wt_product_discount_amount     = get_post_meta( $coupon_id, '_wt_product_discount_amount',true );
        $wt_product_discount_type       = get_post_meta( $coupon_id, '_wt_product_discount_type',true );
        if( ( ! isset( $wt_product_discount_amount ) ) || ( !isset( $wt_product_discount_type ) ) || ( $wt_product_discount_type == 'percent' && $wt_product_discount_amount == 100) ) {
            return true;
        }
        return false;
    }


    /**
     * make the giveaway product already dsicounted ( used in limit discount option).
     * @since 1.2.4
     */
    function make_give_away_product_price_is_already_discounted( $discount_amount ) {
        if( $gift_item = $this->is_cart_contains_free_products() ) {
            $product_id =$_product_id = $gift_item['product_id'];
            $variation_id = isset( $gift_item['variation_id'] )? $gift_item['variation_id'] : 0;
            if( $variation_id ) {
                $_product_id = $variation_id;
            }
            $product = wc_get_product( $_product_id );

            if( $product->is_on_sale() ) {
                $product_price =  $product->get_sale_price();
            } else {
                $product_price = $product->get_regular_price();
            }

            $discount_amount += $product_price;
        }

        return $discount_amount;
    }

    /**
     * helper function to get giveaway product discount text
     * @since 1.2.4
     */
    function get_give_away_discount_text( $coupon_code ) {
        if( is_int($coupon_code)) {
            $coupon_id = $coupon_code;
        } else {
            $coupon_id  = wc_get_coupon_id_by_code( $coupon_code );
        }
        $wt_product_discount_amount     = get_post_meta( $coupon_id, '_wt_product_discount_amount',true );
        $wt_product_discount_type       = get_post_meta( $coupon_id, '_wt_product_discount_type',true );
        if( '' == $wt_product_discount_amount  || '' == $wt_product_discount_type  ) {
            return '100%';
        }
        switch( $wt_product_discount_type ) {
            case 'percent': 
                $discount_text = $wt_product_discount_amount.'%';
                break;
            default:
                $discount_text = Wt_Smart_Coupon_Admin::get_formatted_price( $wt_product_discount_amount );

        }
        return $discount_text;
    }


    /**
     * Get Quantity of giveaway item
     * @since 1.2.6
     */
    function get_give_away_quantity( $coupon_code ) {
        if( is_int($coupon_code)) {
            $coupon_id = $coupon_code;
        } else {
            $coupon_id  = wc_get_coupon_id_by_code( $coupon_code );
        }
        $quantity =  get_post_meta( $coupon_id, '_wt_product_discount_quantity', true );

        return ( $quantity > 0 )? $quantity : 1;
    }

    /**
	 * Remove giveaway products from the cart if no other products are present
	 *
	 * @since  1.3.0
	 * @param  string $cart_item_key Cart item key to remove from the cart.
     * @param WC_Cart $instance Cart object.
	 */
    public function remove_free_products_from_cart( $cart_item_key, $instance ) {

        $cart_items = WC()->cart->get_cart_contents();
        $cart_items_count = WC()->cart->get_cart_contents_count();
        $free_products = array();

        if( !empty( $cart_items ) && is_array( $cart_items ) ) {
        
            foreach ( $cart_items as $cart_item_key => $cart_item ) {
                
                if( $this->is_a_free_gift_item( $cart_item ) ) {
                    $free_products[] = $cart_item_key;
                }
                
            }
            if( count( $free_products ) === $cart_items_count ) {
                
                if( !empty( $free_products ) && is_array( $free_products )) {
                    foreach ($free_products as $product ) {
                        echo "product".$product;
                        WC()->cart->remove_cart_item( $product );
                    }
                }
                wc_add_notice( __('Giveaway products have removed from the cart.','wt-smart-coupons-for-woocommerce'), 'success' );
            }
        }
        
    }
}