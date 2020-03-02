<?php
if (!defined('WPINC')) {
    die;
}
if( ! class_exists ( 'Wt_Smart_Coupon_Customisable_Gift_Card' ) ) {
    class Wt_Smart_Coupon_Customisable_Gift_Card extends Wt_Smart_Coupon_Store_Credit {
        public function __construct( ) {
            

        }
        /**
         * Define the templates
         * @since 1.2.8
         */
        public static function get_tempate_images() {
            $desgn_images = array(
                'general' => array ( 
                    'image_url'         => esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ))).'/assets/images/general-gift.jpg',
                    'top_bg_color'      => '#f5a640',
                    'bottom_bg_color'   => '#f5a640',
                ),                 
                'happy_birtday' => array(
                    'image_url'         => esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ))).'/assets/images/happy-bdy.jpg',
                    'top_bg_color'      => '#373a9e',
                    'bottom_bg_color'   => '#373a9e',
                ),
                'new_year' => array( 
                    'image_url'         => esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ))).'/assets/images/new-year.jpg',
                    'top_bg_color'      => '#e84353',
                    'bottom_bg_color'   => '#e84353',
                ),
                'anniversary' => array(
                    'image_url'         => esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ))).'/assets/images/anniversary.jpg',
                    'top_bg_color'      => '#5e2b92',
                    'bottom_bg_color'   => '#5e2b92',
                ),
                'christmas' => array( 
                    'image_url'         => esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ))).'/assets/images/christmas.jpg',
                    'top_bg_color'      => '#357933',
                    'bottom_bg_color'   => '#357933',
                )
            );

            return apply_filters( 'wt_smart_coupon_store_credit_designs', $desgn_images );
        }
        /**
         * Get the tmeplate Image
         * @since 1.2.8
         */
        public static function get_template_image( $template ) {
            $templates = self::get_tempate_images();

            if( array_key_exists( $template , $templates  ) ) {
                return $templates[ $template ];
            }

            return $templates['general'];
        }
        
        /**
         * Remove unwanted hooks for product page
         * @since 1.2.8
         */
        function remove_unwanted_product_page_hooks() {
            remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
            remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
            
            remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
            
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
            
            // function woocommerce_template_single_add_to_cart() generates the following 4 actions
            // remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
            remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
            remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
            remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
            
            remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
            remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
            
            remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
            remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
            remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );



            add_action( 'wt_gift_coupon_setup_form', 'woocommerce_template_single_add_to_cart',9999 );
        }

        /**
         * Credit coupon single page contemt and design
         * @since 1.2.8
         */
        function shop_single_page_design() {
            global $product;
            $currency_symbol = get_woocommerce_currency_symbol();
			if( ! $this->is_product_is_store_credit_purchase( $product->get_id() ) ) {
				return;
            }
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
            wp_enqueue_style( 'jquery-ui' );
            $this->remove_unwanted_product_page_hooks();
            $desgn_images =  self::get_tempate_images();
               
            ?>
            <div class="wt_customise_gift_coupon_wrapper">
                <div class="wt_gift_coupon_title"> <h1><?php _e('Customise your Store Credit coupon','wt-smart-coupons-for-woocommerce-pro'); ?></h1></div>
                <div class="wt_gift_coupn_designs">
                    <h2><?php _e('Store credit design','wt-smart-coupons-for-woocommerce-pro'); ?> </h2>
                    <ul>
                        <?php 
                        $i = 0;
                        foreach( $desgn_images as $designname => $image ) {
                            $class = '';
                            if( $i++ == 0 ) {
                                $class = 'active';
                            }
                            echo '<li class='.$class.'>';
                                    echo '<img design="'.$designname.'" src="'.$image['image_url'].'" alt="'.$designname.'" top_bg="'.$image['top_bg_color'].'" bottom_bg="'.$image['bottom_bg_color'].'" />';
                            echo '</li>';
                        }
                        
                        ?>

                    </ul>
                </div>

                <div class="wt_gift_coupon_preview">
                   <div class="wt_gift_coupon_preview_wrapper">
                        <h2><?php _e('Preview','wt-smart-coupons-for-woocommerce-pro'); ?></h2>

                        <div class="store_credit_preview">
                            <div class="store_credit_preview_wrapper">
                                <div class="wt_gift_coupon_preview_caption" style="background-color:<?php echo $desgn_images['general']['top_bg_color'];  ?>">
                                    <?php _e('Have a nice day','wt-smart-coupons-for-woocommerce-pro' ); ?>
                                </div>
                                <div class="wt_gift_coupon_preview_image">
                                    <?php echo '<img src="'.$desgn_images['general']['image_url'].'" alt="general" />'; ?>
                                </div>
                                <div class="wt_coupon-code-block">
                                    <div class="coupon-code">
                                        XXXX-XXXX-XXXX
                                    </div>
                                    <div class="coupon_price"> 
                                        <?php 
                                        $currentcy_positon = get_option('woocommerce_currency_pos');
                                        if( $currentcy_positon  == 'left' ) {
                                            $amount  = $currency_symbol.'<span>200</span>';    
                                        } else {
                                            $amount  = '<span>200</span>'.$currency_symbol;  
                                        }
                                        echo $amount; 
                                        ?>
                                    </div>
                                </div>

                                <div class="coupon-message-block"  style="background-color:<?php echo $desgn_images['general']['bottom_bg_color'];  ?>">
                                    <div class="coupon-message"><?php _e('A gift awaiting you','wt-smart-coupons-for-woocommerce-pro'); ?></div>
                                    <div class="coupon-from"><?php _e('FROM:','wt-smart-coupons-for-woocommerce-pro'); ?> <span><?php _e('Your Name','wt-smart-coupons-for-woocommerce-pro') ?></span> </div>
                                </div>
                            </div>
                        </div>

                   </div>
                </div>

                <div class="wt_gift_coupon_setup">
                    <h2><?php _e('Store Credit Details','wt-smart-coupons-for-woocommerce-pro'); ?></h2>
                    <?php do_action('wt_gift_coupon_setup_form'); ?>
                </div>
            </div>
            <?php
        }
        /**
         * Remove Quantity option for cart page
         * @since 1.2.8
         */
        function remove_quantity_selction_for_gift_card( $solid_individually,$product ) {

			if( !$product  || ! $this->is_product_is_store_credit_purchase( $product->get_id() ) ) {
				return $solid_individually;
            }

            return apply_filters('wt_store_credit_product_is_sold_individually',true);

        }
        /**
         * Make the store credit product virtual by default
         */
        function make_the_sore_credit_product_virtual ( $is_virtual, $product ) {

			if( !$product  || ! $this->is_product_is_store_credit_purchase( $product->get_id() ) ) {
                
                return $is_virtual;
            }

            return apply_filters('wt_store_credit_product_is_virtual',true);
        }


        /**
         * Add Store credit fields into product single page.
         * @snce 1.2.8
         */
        function add_store_credit_fields_on_product_page() {
            global $product;
			if( ! $this->is_product_is_store_credit_purchase( $product->get_id() ) ) {
				return;
            }
            ?>

                <div class="wt_gift_coupon_setup_wrapper">
                    
                    <div  class="wt-form-item">
                        <input type="email" name="wt_credit_coupon_send_to" id="wt_credit_coupon_send_to" placeholder="<?php _e('Recipient email','wt-smart-coupons-for-woocommerce-pro'); ?>" />
                    </div>
                    <div  class="wt-form-item">
                        <textarea  name="wt_credit_coupon_send_to_message" id="wt_credit_coupon_send_to_message" placeholder="<?php _e('Message','wt-smart-coupons-for-woocommerce-pro'); ?>"></textarea>
                    </div>
                    <div  class="wt-form-item">
                        <input type="text" name="wt_credit_coupon_from" id="wt_credit_coupon_from" placeholder="<?php _e('Sender name:','wt-smart-coupons-for-woocommerce-pro'); ?>" />
                    </div>
                    <input type="hidden" name="wt_credit_coupon_image"  id="wt_credit_coupon_image"  value="general" />


                </div>

            <?php

        }
        /**
         * Validate Recipient Email
         * @since 2.1.4
         */
        function validate_store_credit_for_email_address( $passed, $product_id  ) {
            if ( $this->is_product_is_store_credit_purchase( $product_id ) ){
               
                if ( ! isset( $_REQUEST['wt_credit_coupon_send_to'] ) || '' === $_REQUEST['wt_credit_coupon_send_to'] ) {
					wc_add_notice( 
						__( 'Recipient email is required ', 'wt-smart-coupons-for-woocommerce-pro' ) , 'error' );
					return false;
                } elseif( !is_email($_REQUEST['wt_credit_coupon_send_to']) ) {
                    wc_add_notice( 
						__( 'Please enter valid email address ', 'wt-smart-coupons-for-woocommerce-pro' ) , 'error' );
					return false;
                }
            } 
            return $passed;
        }

        /**
         * Save store credit Details into cart item session
         * @since 1.2.8
         */
        function add_store_credit_template_details_to_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
			if ( ! $this->is_product_is_store_credit_purchase( $product_id ) ){
                return $cart_item_data;
            } 
            $template_items = array();
            
            if( isset( $_REQUEST['wt_credit_coupon_send_to'] ) ) {
                $template_items['wt_credit_coupon_send_to'] = sanitize_email( $_REQUEST['wt_credit_coupon_send_to'] );
            }
            if( isset( $_REQUEST['wt_credit_coupon_send_to_message'] ) ) {
                $template_items['wt_credit_coupon_send_to_message'] = sanitize_textarea_field( $_REQUEST['wt_credit_coupon_send_to_message'] );
            }
            if( isset( $_REQUEST['wt_credit_coupon_from'] ) ) {
                $template_items['wt_credit_coupon_from'] = sanitize_text_field( $_REQUEST['wt_credit_coupon_from'] );
            }
            $template_items['wt_smart_coupon_schedule'] = '';
            if( isset( $_REQUEST['wt_smart_coupon_schedule'] ) ) {
                $template_items['wt_smart_coupon_schedule'] = sanitize_text_field( $_REQUEST['wt_smart_coupon_schedule'] );
            }
            $template_items['wt_smart_coupon_template_image'] = 'general';
            if( isset( $_REQUEST['wt_credit_coupon_image'] ) ) {
                $template_items['wt_smart_coupon_template_image'] = sanitize_text_field( $_REQUEST['wt_credit_coupon_image'] );
            }
            
            $cart_item_data['wt_store_crdit_template'] = $template_items;

			return $cart_item_data;
        }

        /**
         * Display Store credit cart items into cart and checkout page (cart item description )
         * @since 1.2.8
         */
        function display_credit_details_into_cart_item( $item_data, $cart_item_data ) {
            

            if( isset( $cart_item_data['wt_store_crdit_template'] ) ) {
                $template_details = $cart_item_data['wt_store_crdit_template'];
                $item_data[] = array(
                    'key' =>  __('Recipient email'),
                    'value' => $template_details['wt_credit_coupon_send_to']
                    );
                $item_data[] = array(
                        'key' =>  __('Sender Name'),
                        'value' => $template_details['wt_credit_coupon_from']
                    );
                if( isset( $template_details['wt_smart_coupon_schedule'])) {
                    $item_data[] = array(
                        'key' =>  __('Send Date'),
                        'value' => $template_details['wt_smart_coupon_schedule']
                    );
                }
               
            }
            return $item_data;
        }



        function add_customisable_gift_coupon_option( $default_option ) {
            if(isset( $default_option['enabled_extended_store_credit'] )) {
                return $default_option;
            }

            $default_option['enabled_extended_store_credit'] = true;
            
            return $default_option;
        } 
        
        /**
         * Is customisable coupon is enabled.
         * @since 1.2.8
         * 
         */
        public static function is_extended_store_credit_enebaled( ) {
           return Wt_Smart_Coupon_Admin::get_option('enabled_extended_store_credit');
        }
        /**
         * Add Customizable store coredit settings
         * @since 1.2.8
         */
        function add_customizable_store_credit_option() {

           

            $is_enabled = Wt_Smart_Coupon_Admin::get_option('enabled_extended_store_credit');
            $checked = '';
            if( $is_enabled ) {
                $checked = 'checked';
            }
            ?>

            <div class="customization-option-instruction">
            <div class="wt_section_title">
                <h2><?php _e('Extended Store credit','wt-smart-coupons-for-woocommerce-pro'); ?>
                <a class="thickbox" href="<?php echo esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ).'/assets/images/extended-store-credit.png' )  ); ?>?TB_iframe=true&width=824&height=867"><small>[<?php _e('Product preview','wt-smart-coupons-for-woocommerce-pro'); ?>]</small></a>
                <a class="thickbox" href="<?php echo esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ).'/assets/images/store-credit-email.png' )  ); ?>?TB_iframe=true&width=621&height=620"><small>[<?php _e('Email preview','wt-smart-coupons-for-woocommerce-pro'); ?>]</small></a>

                </h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr  valign="top">
                        <td scope="row" class="titledesc"> 
                            <?php _e('Enable extended store credit','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                            <?php echo wc_help_tip( __('The extended model allows your customers to choose a template from the available options.','wt-smart-coupons-for-woocommerce-pro') ); ?>
                        </td>
                        <td class="forminp forminp-checkbox">
                            <label><input type="checkbox" id="_wt_enable_customizable_store_credit" name="_wt_enable_customizable_store_credit"  <?php echo $checked; ?>/>
                            <?php echo wc_help_tip( __('If enabled the customers will get to choose a gifting template from the available options in the product page. This template will also be used for email.','wt-smart-coupons-for-woocommerce-pro') ); ?>

                        </td>   
                    </tr>
                </tbody>
            </table>
                
            </div>
            <?php
        }

        /**
         * save the extended coupon option
         * @since 1.2.8
         */
        function save_extended_coupon_option( ) {
            if( isset( $_POST['update_wt_smart_coupon_store_credit_settings'] ) ) {

                check_admin_referer('wt_smart_coupons_store_credit_settings');
                $smart_coupon_option = get_option( 'wt_smart_coupon_options' );

                if( isset( $_POST['_wt_enable_customizable_store_credit'] ) && $_POST['_wt_enable_customizable_store_credit'] =='on' ) {
                    $smart_coupon_option['enabled_extended_store_credit'] = true;
                } else {
                    $smart_coupon_option['enabled_extended_store_credit'] = false;
                }

                update_option( 'wt_smart_coupon_options', $smart_coupon_option );
            }
        }
        
        /**
         * Add New feature warning 
         * @since 1.2.8
         */
        function customizable_store_credit_warning() {
            $updated = $this->save_extended_coupon_option();            
            if( self::is_extended_store_credit_enebaled()  ) {
                return;
            }
            ?>
            <div class="wt_warning wt_notice">
                <?php  _e('We have introduced an extended version of store credit. ','wt-smart-coupons-for-woocommerce-pro'); ?>
                <span id="wt_try_store_credit_now"><?php _e('Try now','wt-smart-coupons-for-woocommerce-pro'); ?></span>
            </div>
            <?php
        }

        /**
         * Ajax action on for enaling extended store credit
         * @since 1.2.8
         */
        function try_store_credit_now( ) {
            $this->update_customisable_store_crdit_option( true );

            _e('Activated extended store credit feature','wt-smart-coupons-for-woocommerce-pro');
            die();
        }

        /**
         * Helper function for updating extended store credit settings
         * @since 1.2.8
         */
        function update_customisable_store_crdit_option( $option_value ) {

            $smart_coupon_option = get_option( 'wt_smart_coupon_options' );
            $smart_coupon_option['enabled_extended_store_credit'] = $option_value;
            update_option('wt_smart_coupon_options',$smart_coupon_option);
        }
        
        
    }
    
}