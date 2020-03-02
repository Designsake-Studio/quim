<?php
if (!defined('WPINC')) {
    die;
}
if( ! class_exists ( 'Wt_Smart_Coupon_Store_Credit_Admin' ) ) {
    class Wt_Smart_Coupon_Store_Credit_Admin {
        protected $option;
        

        public function __construct( ) {
            add_filter( 'wt_smart_coupon_admin_tab_items', array($this,'add_admin_tab'), 11, 1 );
            add_action('wt_smart_coupon_tab_content_store_credit',array($this,'store_credit_tab_content'),10);
            add_action('init', array($this,'init_theme_method') );


        }

        /**
         * Enqueue Admin Scripts.
         * @since 1.2.9
         * Added seperate file store credit scripts
         */
        public function enqueue_scripts() {

            $screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';

            $script_parameters['ajaxurl'] = admin_url( 'admin-ajax.php' ) ;
            if ( function_exists('wc_get_screen_ids') && in_array( $screen_id, wc_get_screen_ids() ) ) {
                wp_enqueue_script('wt-smart-coupon-store-credit', plugin_dir_url(__FILE__) . 'js/wt-store-credit-admin.js', array('jquery'),WEBTOFFEE_SMARTCOUPON_VERSION , false);
                wp_localize_script('wt-smart-coupon-store-credit','WTSmartCouponOBJ',$script_parameters );
            }
        }
 
        function init_theme_method() {
            add_thickbox();
        }

        /**
         * Add Store Credit tabs into Smart Coupon settings.
         * @since 1.2.1
         */
        function add_admin_tab( $admin_tabs ) {
            $admin_tabs['store_credit'] =__('Store Credit','wt-smart-coupons-for-woocommerce-pro');
            return $admin_tabs;
        }
        /**
         * StoreCredit tab content
         * @since 1.2.1
         */
        function store_credit_tab_content() {
            $this->display_store_credit_form();
        }

        /**
         * Store credit tab section
         * @since 1.2.0
         */
        function get_sections() {

            $sections = array(
                'setup_store_credit' => __('Setup', 'wt-smart-coupons-for-woocommerce-pro'),
                'send_store_credit' => __('Email Credit', 'wt-smart-coupons-for-woocommerce-pro')
            );

            return apply_filters( 'wt_smart_coupon_store_credit_tabs', $sections );
        }
        

        /**
		 *  Admin store Credit tab content
		 * @since 1.2.0
		 */
        public function display_store_credit_form() {
			?>
	

            <div id="normal-sortables-2" class="meta-box-sortables ui-sortable">
                <div style="display:none" class="wt_smart_coupon_credit_message notice is-dismissible"></div>
                <ul class="wt_sub_tab">
                            
                <?php
                    $sections = $this->get_sections();
                    $i = 0;
                    foreach( $sections as $id => $section ) {
                        $class = '';
                        if( $i++ == 0 ) {
                            $class="active";
                        }
                        ?>
                        <li class="<?php echo $class; ?>">
                            <a href = "#<?php echo $id; ?>" >
                            <?php  echo $section ; ?>
                            
                            </a>
                        </li>

                        <?php
                    }
                
                ?>
                
                </ul>

                <div id="wt_store_credit_coupon_top" class="postbox ">
                    <div class="wt_store_credit_content">
                        <div class="wt_sub_tab_container">
                           
                            
                            <div  class="wt_sub_tab_content active" id="setup_store_credit" >
                                <?php echo $this->store_credit_settings_form(); ?>
                            </div>

                            <div class="wt_sub_tab_content" id="send_store_credit" >
                               <?php $this->send_credit_form(); ?>
                            </div>

                            <?php do_action('wt_smart_coupon_after_store_credit_tab_content'); ?>
                            
                        </div>

                    </div>
                    

                </div>

            </div>

        <?php
        }

        /**
         * StoreCredit Settings options
         * @since 1.2.0
         */
        function store_credit_options( $default_options ) {
            if(isset( $default_options['wt_store_credit_settings'] )) {
                return $default_options;
            }
            $store_credit_settings = array(
                'store_credit_purchase_product'         =>  '',
                'minimum_store_credit_purchase'         =>  '',
                'maximum_store_credit_purchase'         =>  '',
                'apply_store_credit_before_tax'         =>  false,
                'store_credit_coupon_prefix'            => '',
                'store_credit_coupon_suffix'            => '',
                'store_credit_coupon_length'            => 12,
                'send_purchased_credit_on_order_status' => 'processing',
                'make_coupon_individual_use_only'       => true

            );
            $default_options['wt_store_credit_settings'] = $store_credit_settings;
            return $default_options;
        }
        /**
         * Get StoreCredit Options
         * @since 1.2.0
         */
        function get_options() {
            
            if( !empty( $this->option )) {
                return $this->option;
            } else {
                $this->option = Wt_Smart_Coupon_Admin::get_option('wt_store_credit_settings');

                return $this->option;
            }
        }

        /**
         * Update StoreCredit Options
         * @since 1.2.0
         */

         function update_option( $store_credit_option ) {
             if( empty($store_credit_option) ) {
                 return;
             }
            $smart_coupon_option = get_option( 'wt_smart_coupon_options' );
            $smart_coupon_option['wt_store_credit_settings'] = $store_credit_option;
            update_option('wt_smart_coupon_options',$smart_coupon_option);
            $this->option = $store_credit_option;
         }

       
        /**
         * Display StoreCredit Settings Form
         * @since 1.2.0
         */
        function store_credit_settings_form() {

            $updated = $this->save_store_credit_settings();
            $options  = $this->get_options();


            if( $updated ) {
                ?>
                    <div class="wt_smart_coupon_credit_settings_message notice-success notice is-dismissible"> <p><?php _e("Updated Store credit settings",'wt-smart-coupons-for-woocommerce-pro'); ?></p></div>
                <?php 
            }
            do_action( 'wt_smart_coupon_before_store_credit_settings_from' );
            ?>  
                 <form name="wt_smart_store_credit_Settings" method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>" >
                    <?php wp_nonce_field('wt_smart_coupons_store_credit_settings'); ?>
                    
                    <div class="wt_settings_section wt_section_title">
                        <h2><?php _e('Setup Store Credit','wt-smart-coupons-for-woocommerce-pro'); ?></h2>
                        <p><?php _e('Setup store credits with customers choice of denomination using the below configuration. These credits can be purchased by the customers for themselves or emailed as gifts to others in order to make multiple purchases.','wt-smart-coupons-for-woocommerce-pro'); ?></p>
                    </div>

                    <?php do_action('wt_store_credit_before_settigns_form_items'); ?>
                    <div class="wt_section_title">
                        <h2>
                            <?php _e('Manage tax','wt-smart-coupons-for-woocommerce-pro') ?>
                        </h2>
                    </div>
                    <table class="form-table">
                        <tbody>
                            <tr  valign="top">
                                <?php 
                                    $apply_store_credit_before_tax =  ( isset( $options['apply_store_credit_before_tax'] ) )? $options['apply_store_credit_before_tax'] : false; 
                                    $checked = '';
                                    if( $apply_store_credit_before_tax ) {
                                        $checked = 'checked = checked';
                                    }
                                ?>
                                <td scope="row" class="titledesc"> 
                                    <?php _e('Apply store credit before calculating tax','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                                    <?php echo wc_help_tip( __('Check this to calculate tax on the cart total after store credit has been applied.','wt-smart-coupons-for-woocommerce-pro') ); ?>
                                </td>
                                <td class="forminp forminp-checkbox">
                                    <label><input type="checkbox" id="_wt_apply_store_credit_before_tax" name="_wt_apply_store_credit_before_tax" <?php echo $checked; ?>>
                                    <small><?php _e('If enabled, tax will be calculated only on discounted value instead of actual value. For eg, if order value is $110 and you apply a store credit of value $100, tax will be applicable only on $10(order total after discount).','wt-smart-coupons-for-woocommerce-pro'); ?></small>                               
                                </td>   
                            </tr>
                        </tbody>
                    </table>

                    <?php do_action( 'wt_smart_coupon_after_store_credit_manage_tax_settings' ) ?>

                    
                    <div class="wt_section_title">
                        <h2>
                            <?php _e('Purchase store credit','wt-smart-coupons-for-woocommerce-pro') ?>
                            <a class="thickbox" href="<?php echo esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ).'/assets/images/purchase-credit.png' )  ); ?>?TB_iframe=true&width=100&height=100"><small>[<?php _e('Preview','wt-smart-coupons-for-woocommerce-pro'); ?>]</small></a>
                        </h2>
                    </div>
                    
                    <table class="form-table">
                        <tbody>
                            <tr valign="top">
                                <td scope="row" class="titledesc"> 
                                   <?php _e("Associate a product",'wt-smart-coupons-for-woocommerce-pro');  ?>
                                
                                </td>
                                <td class="forminp forminp-select">
                                        <?php
                                        $store_credit_product = ( isset( $options['store_credit_purchase_product'] ) )? $options['store_credit_purchase_product']  : '';
                                        ?>
                                        <select id="_store_credit_product" class="wc-product-search" style="width: 300px;" name="_store_credit_product" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'wt-smart-coupons-for-woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations"  data-allow_clear="true">
                                        <?php

                                        if(isset( $store_credit_product ) && '' != $store_credit_product ) {
                                            $product = wc_get_product( $store_credit_product );
                                            if ( is_object( $product ) ) {
                                                echo '<option value="' . esc_attr( $store_credit_product ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                                            }
                                        }
                                        ?>
                                        </select>
                                        <small><?php   _e('Create a zero price product and select the product from the list.','wt-smart-coupons-for-woocommerce-pro'); ?></small>
                                        <?php echo wc_help_tip( __('Create a woocommerce product with 0 sale price and associate the product here to configure it as a store credit purhcase.','wt-smart-coupons-for-woocommerce-pro') ); ?>

                                </td>
                            </tr>

                            <?php do_action('wt_after_associate_product_field_store_credit'); ?>

                            <tr valign="top">
                                <?php  $minimum_store_credit = $options['minimum_store_credit_purchase']; ?>
                                <td scope="row" class="titledesc"> 
                                     <?php _e('Minumum credit','wt-smart-coupons-for-woocommerce-pro'); ?>
                                </td>
                                <td class="forminp forminp-text">
                                    <input type="number" name="_wt_minimum_store_credit_purchase" min="1" value="<?php echo $minimum_store_credit ; ?>" />
                                        
                                </td>

                            </tr>

                            <tr  valign="top">
                                <?php  $maximum_store_credit = $options['maximum_store_credit_purchase']; ?>
                                <td scope="row" class="titledesc"> 
                                    <?php _e('Maximum credit','wt-smart-coupons-for-woocommerce-pro'); ?>
                                </td>
                                <td class="forminp forminp-text">
                                    <input type="number" name="_wt_maximum_store_credit_purchase" min="1" value="<?php echo $maximum_store_credit ; ?>" />
                                </td>
                            </tr>
                            
                            <tr  valign="top">
                                <td scope="row" class="titledesc"> 
                                    <?php _e("Email store credit for order status",'wt-smart-coupons-for-woocommerce-pro');  ?>
                                </td>    
                                    <?php
                                        $coupon_statuses = Wt_Smart_Coupon_Admin::success_order_statuses();
                                        $status_selected = (isset( $options['send_purchased_credit_on_order_status'] ) )? $options['send_purchased_credit_on_order_status'] : 'processing';

                                    ?>
                                <td class="forminp forminp-select">
                                        <select id="_email_store_credit_for_order_status" name="_email_store_credit_for_order_status" style="width: 300px"  class="wc-enhanced-select" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
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
                                        <?php echo wc_help_tip( __('Purchased store credit will be mailed only for the chosen order status.','wt-smart-coupons-for-woocommerce-pro') ); ?>
                                </td>
                            </tr>

                            

                        </tbody>
                    </table>
                    
                    <?php do_action( 'wt_smart_coupon_after_purchase_store_credit_settings' ) ?>

                    <div class="wt_section_title">
                        <h2><?php _e('Store credit coupon format','wt-smart-coupons-for-woocommerce-pro') ?>
                            <a class="thickbox" href="<?php echo esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ).'/assets/images/coupon-format-small.jpg' )  ); ?>?TB_iframe=true&width=100&height=100"><small>[<?php _e('Preview','wt-smart-coupons-for-woocommerce-pro'); ?>]</small></a>
                        </h2>
                    </div>

                    <table class="form-table">
                        <tbody>
                            <tr  valign="top">
                                <?php  $coupon_prefix = $options['store_credit_coupon_prefix']; ?>
                                <td scope="row" class="titledesc">
                                    <?php _e('Prefix','wt-smart-coupons-for-woocommerce-pro'); ?>
                                            
                                </td>
                                <td class="forminp forminp-text">
                                    <input type="text" name="_wt_store_credit_coupon_prefix"  value="<?php echo $coupon_prefix ; ?>" />

                                </td>
                            </tr>

                            <tr  valign="top">
                                <?php  $coupon_suffix = $options['store_credit_coupon_suffix']; ?>
                                <td scope="row" class="titledesc">
                                    <?php _e('Suffix','wt-smart-coupons-for-woocommerce-pro'); ?> 
                                </td>
                                <td class="forminp forminp-text">
                                    <input type="text" name="_wt_store_credit_coupon_suffix"  value="<?php echo $coupon_suffix ; ?>" />
                                </td>

                            </tr>

                            <tr  valign="top">
                                <?php  $coupon_length = $options['store_credit_coupon_length']; ?>
                                            
                                <td scope="row" class="titledesc">
                                    <?php _e('Length of the coupon code','wt-smart-coupons-for-woocommerce-pro'); ?> 
                                </td>
                                <td  class="forminp forminp-text">   
                                    <input type="number" name="_wt_store_credit_coupon_length" min="7"   value="<?php echo $coupon_length ; ?>" />
                                </td>
                            </tr>

                        </tbody>
                    </table>




                    <?php do_action('wt_smart_coupon_after_store_credit_settings_from'); ?>

                    <div class="wt_form_submit">
                        <div class="form-submit">
                            <button id="update_wt_smart_coupon_store_credit_settings" name="update_wt_smart_coupon_store_credit_settings" type="submit" class="button button-primary button-large"><?php _e( 'Save','wt-smart-coupons-for-woocommerce-pro'); ?></button>
                        </div>
                    </div>
                </form>
                            


            <?php 

        }
        /**
         * Save StoreCredit Settings
         * @since 1.2.0
         */
        function save_store_credit_settings() {
            if(isset( $_POST['update_wt_smart_coupon_store_credit_settings']) ) {
                check_admin_referer('wt_smart_coupons_store_credit_settings');
                $store_credit_options = $this->get_options();


                if( isset( $_POST['_store_credit_product'] ) ) {
                    $store_credit_options['store_credit_purchase_product'] = $_POST['_store_credit_product'];
                } else {
                    $store_credit_options['store_credit_purchase_product'] = '';
                }
                if( isset( $_POST['_wt_minimum_store_credit_purchase'] ) ) {
                    $store_credit_options['minimum_store_credit_purchase'] = $_POST['_wt_minimum_store_credit_purchase'];
                }
                if( isset( $_POST['_wt_maximum_store_credit_purchase'] ) ) {
                    $store_credit_options['maximum_store_credit_purchase'] = $_POST['_wt_maximum_store_credit_purchase'];
                }
                if( isset( $_POST['_wt_apply_store_credit_before_tax'] ) && $_POST['_wt_apply_store_credit_before_tax'] =='on' ) {
                    $store_credit_options['apply_store_credit_before_tax'] = true;
                } else {
                    $store_credit_options['apply_store_credit_before_tax'] = false;
                }

                // if( isset( $_POST['_wt_make_coupon_individual_use_only'] ) && $_POST['_wt_make_coupon_individual_use_only'] =='on' ) {
                //     $store_credit_options['make_coupon_individual_use_only'] = true;
                // } else {
                //     $store_credit_options['make_coupon_individual_use_only'] = false;
                // }

                
                if( isset( $_POST['_wt_store_credit_coupon_prefix'] ) ) {
                    $store_credit_options['store_credit_coupon_prefix'] = $_POST['_wt_store_credit_coupon_prefix'];
                }
                if( isset( $_POST['_wt_store_credit_coupon_suffix'] ) ) {
                    $store_credit_options['store_credit_coupon_suffix'] = $_POST['_wt_store_credit_coupon_suffix'];
                }
                if( isset( $_POST['_wt_store_credit_coupon_length'] ) ) {
                    $store_credit_options['store_credit_coupon_length'] = $_POST['_wt_store_credit_coupon_length'];
                }

                if( isset( $_POST['_email_store_credit_for_order_status'] ) ) {
                    $store_credit_options['send_purchased_credit_on_order_status'] = $_POST['_email_store_credit_for_order_status'];
                }

                $this->update_option( $store_credit_options );
                
                do_action( 'wt_store_credit_settings_updated', $store_credit_options );

                return true;
            }

            return false;

        }

        /**
         * Send credit for user form
         * @since 1.2.0
         */

         function send_credit_form() {
             ?>
             <div id="wt-credit-coupon-message"></div>
             <div class="wt_settings_section wt_section_title">
                <h2><?php _e('Email Store credit','wt-smart-coupons-for-woocommerce-pro') ?></h2>
                <p><?php _e('Use this option to manually email store credits of preferred value to customers of your choice. These credits can used by the customers to make multiple purchases from your store. ','wt-smart-coupons-for-woocommerce-pro'); ?></p>
            </div>
            <form name="wt_send_credit" id="wt_send_credit"  method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>" >
                <?php wp_nonce_field('wt_smart_coupons_send_store_credit'); ?>

                <table class="form-table">
                        <tbody>
                            <tr  valign="top">
                                <td scope="row" class="titledesc"> 
                                    <?php _e('Email Address ','wt-smart-coupons-for-woocommerce-pro'); ?><span class="required">*</span>
                                </td>
                                <td class="forminp forminp-checkbox">
                                    <label>
                                        <input type="text" name="_wt_send_credit_email" id="_wt_send_credit_email" />
                                        
                                    </label>
                                </td>   
                            </tr>

                            <tr  valign="top">
                                <td scope="row" class="titledesc"> 
                                    <?php _e('Credit Amount ','wt-smart-coupons-for-woocommerce-pro'); ?><span class="required">*</span> 
                                </td>
                                <td class="forminp forminp-checkbox">
                                    <label> <input type="number" id="_wt_send_credit_amount" name="_wt_send_credit_amount" min=1 /></label>
                                </td>   
                            </tr>

                            <tr  valign="top">
                                <td scope="row" class="titledesc"> 
                                    <label> <?php _e('Description ','wt-smart-coupons-for-woocommerce-pro'); ?> </label>

                                </td>
                                <td class="forminp forminp-checkbox">
                                    <label> <textarea id="_wt_send_credit_message" name="_wt_send_credit_message"></textarea></label>
                                </td>   
                            </tr>


                            <tr  valign="top">

                                <td scope="row" class="titledesc"> 
                                    <?php _e('Individual use only','wt-smart-coupons-for-woocommerce-pro'); ?>
                                </td>
                                <td class="forminp forminp-checkbox">
                                <label><input type="checkbox" id="_wt_make_coupon_individual_use_only" name="_wt_make_coupon_individual_use_only" >
                                    <?php _e('Check this box if the store credit voucher cannot be used in conjunction with other coupons.','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                                </td>   
                            </tr>

                            
                    </tbody>

               </table>
               

                <div class="wt-form-item ">
                    <?php
                        $email_settings_link = get_admin_url().'admin.php?page=wc-settings&tab=email&section=wt_smart_coupon_store_credit_email' ;
                    ?>
                    <p> <?php _e('To manage your store credit email settings use the ','wt-smart-coupons-for-woocommerce-pro' ); ?>
                    <a class="manage-store-credit" target="_blank" href=" <?php echo esc_url( $email_settings_link  ); ?> ">
                        <?php _e(  'Manage store credit email settings', 'wt-smart-coupons-for-woocommerce-pro' );?>
                    </a>
                    </p>
                </div>
                <input type="hidden" id="_wt_woo_currency" name="_wt_woo_currency" value="<?php echo get_woocommerce_currency_symbol(); ?>" />

                <div class="wt_form_submit">
                    <div class="form-submit">
                        <button id="wt_send_credit_submit" name="wt_send_credit_submit" type="submit" class="button button-primary button-large"><?php _e( 'Send email','wt-smart-coupons-for-woocommerce-pro'); ?></button>
                        <button id="wt_send_credit_preview" name="wt_send_credit_preview"  class="button button-primary button-large"><?php _e( 'Preview Email','wt-smart-coupons-for-woocommerce-pro'); ?></button>

                    </div>
                </div>

                


            </form> 
            <?php

                $wc_emails = WC_Emails::instance();
                $emails = $wc_emails->get_emails();

                $current_email =  $emails['WT_smart_Coupon_Store_Credit_Email'];

                /*The Woo Way to Do Things Need Exception Handling Edge Cases*/
                add_filter( 'woocommerce_email_recipient_' . $current_email->id, array($this,'no_recipient') );

                $credit_email_args = array(
                    'send_to'   => '',
                    'coupon_id' => 0,
                    'message'   => ''
                );
                $current_email->trigger( $credit_email_args );

                $content = $current_email->get_content_html();
                $content = apply_filters( 'woocommerce_mail_content', $current_email->style_inline( $content ) );
                echo '<div class="wt_coupon_peview" style="display:none">'.$content.'</div>';
            

            
           
         }

         public function no_recipient($recipient){

           
            $recipient = '';
    
            return $recipient;
        }



        /**
         * Send store credit action  [send from admin section ]
        * @since 1.2.0
        */

        function send_store_credit() {
            if(isset( $_POST['_wt_send_credit_email'] ) ) {
                
                if( isset( $_POST['_wt_send_credit_email'] ) && '' != $_POST['_wt_send_credit_email'] ) {
                    $email = $_POST['_wt_send_credit_email'];
                    $email_ids = explode(',',$email );
                }

                if( isset( $_POST['_wt_send_credit_amount'] ) && '' != $_POST['_wt_send_credit_amount'] ) {
                    $credit_amount = $_POST['_wt_send_credit_amount'];
                }

                if( empty( $email_ids ) ||  $credit_amount <= 0 ) {
                    if( empty( $email_ids ) ) {
                        $error = __("Please enter valid email address",'wt-smart-coupons-for-woocommerce-pro');
                    } else {
                        $error = __("Please enter valid credit amount",'wt-smart-coupons-for-woocommerce-pro');
                    }
                    $return = array(
                        'error'     =>  true,
                        'message'   =>  $error
                    );
                    echo json_encode( $return );
                    die();
                }

                $message = '';
                if( isset( $_POST['_wt_send_credit_message'] ) && '' != $_POST['_wt_send_credit_message'] ) {
                    $message = $_POST['_wt_send_credit_message'];
                }
                

				if( !empty( $this->option ) ) {
					$store_credit_options =  $this->option;
				} else {
					$store_credit_options = $this->option = Wt_Smart_Coupon_Admin::get_option('wt_store_credit_settings');
                }
                
                $prefix	= $store_credit_options['store_credit_coupon_prefix'];
                $suffix	= $store_credit_options['store_credit_coupon_suffix'];
                $coupn_length = $store_credit_options['store_credit_coupon_length'];
                if( !$coupn_length  ) {
                    $coupn_length  = 12;
                }
                $coupon_created = 0;
                foreach( $email_ids as $email_id ) {
                    if( ! is_email( $email_id  ) ) {
                        continue;
                    }

                    $coupon_code =  Wt_Smart_Coupon_Admin::generate_random_coupon( $prefix,$suffix,$coupn_length );
					
					$coupon_args = array(
						'post_title'    => strtolower( $coupon_code ),
						'post_content'  => '',
						'post_status'   => 'publish',
						'post_author'   => 1,
						'post_type'     => 'shop_coupon'
					);

					$coupon_id  = wp_insert_post( $coupon_args );
					$coupons_genrated[] = $coupon_id ;
					update_post_meta( $coupon_id, 'wt_auto_genrated_store_credit_coupon', true );

					$coupon_obj = new WC_Coupon( $coupon_id );
					$coupon_obj->set_email_restrictions(  $email_id );
					$coupon_obj->set_amount(  $credit_amount );
                    $coupon_obj->set_discount_type('store_credit');
                    $coupon_obj->set_description( $message  );

                     if( isset( $_POST['_wt_make_coupon_individual_use_only'] ) ) {
                        $make_coupon_individual_use_only = $_POST['_wt_make_coupon_individual_use_only'];
                    }
                    

                    if( $make_coupon_individual_use_only == true ) {
                        $coupon_obj->set_individual_use(true );
                    }

                    $coupon_obj->save();
                    do_action('wt_smart_coupon_send_store_credit_coupon_added',$coupon_obj);
                    $coupon_created ++;

                    // $coupon = new WC_coupon( $coupon_id );

                    $credit_email_args = array(
                        'send_to'   => $email_id,
                        'coupon_id' => $coupon_id,
                        'message'   => $message,
                        'from_name'      => '',
                        'template'  => 'general'
                    );

                    $enabled_customizing_store_Credit = Wt_Smart_Coupon_Customisable_Gift_Card::is_extended_store_credit_enebaled( );
                    if( ! $enabled_customizing_store_Credit ) { 
                        $credit_email_args = array(
                            'send_to'   => $email_id,
                            'coupon_id' => $coupon_id,
                            'message'   => $message
                        );
                    }

                    WC()->mailer();
                    do_action( 'wt_send_store_credit_coupon_to_customer',$credit_email_args );
                    update_post_meta( $coupon_id, '_wt_smart_coupon_credit_activated', true );
                    update_post_meta( $coupon_id, '_wt_smart_coupon_initial_credit', $coupon_obj->get_amount() );
                }

            }
            if( $coupon_created ) {
                $success_message = ($coupon_created == 1 )? 'Store credit Coupon created - 1. Coupon mailed - 1. ' :'Store credit Coupons created - '.$coupon_created.' . Coupon mailed - '.$coupon_created.'. ';
                $return = array(
                    'error' => false,
                    'message' =>  $success_message
                );
            }
            
            echo json_encode($return);

            die();
        }


        /**
         * Send credit coupon for the customer  [ on updating order into success status]
         * @since 1.2.0
         */
        function send_credit_coupon_email( $order_id, $old_status,$new_status,$order ) {

            $options  = $this->get_options();
            
            $email_coupon_for_order_status =  $options['send_purchased_credit_on_order_status'];
            if( '' == $email_coupon_for_order_status ) {
                $email_coupon_for_order_status = 'completed';
            }

            if( $new_status  == $email_coupon_for_order_status ) {
                $enabled_customizing_store_Credit = Wt_Smart_Coupon_Customisable_Gift_Card::is_extended_store_credit_enebaled( );
                if(  ! $enabled_customizing_store_Credit ) { 
                    $this->send_store_credit_email_for_order_old( $order_id );

                } else {
                    $this->send_store_credit_email_for_order( $order_id );

                }
            }
        }

        /**
         * helper function for sending store credit older verison < 1.2.8
         */

         function send_store_credit_email_for_order_old( $order_id ) {
            $coupons = get_post_meta( $order_id, 'wt_credit_coupons', true );
            $send_to = get_post_meta( $order_id, 'wt_credit_coupon_send_to', true );
            $message = get_post_meta( $order_id, 'wt_credit_coupon_send_to_message', true );
            $coupons = maybe_unserialize( $coupons  );
            $coupon_ids = array();
            if( !empty( $coupons ) ) {
                foreach( $coupons as $coupon_item ) {
                    $coupon_ids[] = $coupon_item['coupon_id'];
                    update_post_meta( $coupon_item['coupon_id'], '_wt_smart_coupon_credit_activated', true );
                }
            }
            

            // check is scheduled into any other time

            $send_now = apply_filters( 'wt_send_credit_coupon_on_order_success_status', true,$order_id );

            if(  $send_now && !empty($coupon_ids )) {

                $credit_email_args = array(
                    'send_to'   => $send_to,
                    'coupon_id' => $coupon_ids,
                    'message'   => $message
                );
                WC()->mailer();
                do_action( 'wt_send_store_credit_coupon_to_customer',$credit_email_args );
            }
         } 


        /**
         * Helper function for sending store credit email for specified coupon ID
         * @since 1.2.8
         */

        function send_store_credit_email_for_order( $order_id ) {
            $email_send = false;
            $coupon_templates = get_post_meta( $order_id, 'wt_credit_coupon_template_details', true );
            $coupon_template_items = maybe_unserialize( $coupon_templates );
            $order_obj = wc_get_order( $order_id );
            if( is_array( $coupon_template_items ) ) {
                foreach( $coupon_template_items as $coupon_items ) {
                    $coupon_ids    =   isset( $coupon_items['coupon_id'] ) ? $coupon_items['coupon_id']  : '';
                    $send_to    =   isset( $coupon_items['wt_credit_coupon_send_to'] ) ?  $coupon_items['wt_credit_coupon_send_to']:  '' ;
                    $message    =   isset( $coupon_items['wt_credit_coupon_send_to_message'] )? $coupon_items['wt_credit_coupon_send_to_message'] : '' ;
                    $from       =   isset( $coupon_items['wt_credit_coupon_from'] )? $coupon_items['wt_credit_coupon_from'] : $order_obj->get_billing_email() ;
                    $template   =   isset( $coupon_items['wt_smart_coupon_template_image'] )? $coupon_items['wt_smart_coupon_template_image'] : '' ;
                    $send_now = apply_filters( 'wt_send_credit_coupon_on_order_success_status', true,$order_id,$coupon_items );

                    // check is scheduled into any other time
                    if(  $send_now && $coupon_ids  ) {

                        $credit_email_args = array(
                            'send_to'   => $send_to,
                            'coupon_id' => $coupon_ids,
                            'message'   => $message,
                            'from_name' => $from,
                            'template'  => $template,
                        );
                        WC()->mailer();
                        do_action( 'wt_send_store_credit_coupon_to_customer',$credit_email_args );
                        $email_send = true;
                        if( is_array( $coupon_ids )) {
                            $coupon_id = $coupon_ids[0];
                        } else {
                            $coupon_id = $coupon_ids;
                        }
                        update_post_meta( $coupon_id, '_wt_smart_coupon_credit_activated', true );
                    }

                }
            }
            return $email_send;

        }

        /**
         * Display coupen sent on order in order item meta
         * @since 1.1.0
         */
        public function add_store_credit_details_into_order(  ) {
            global $post;

			if ( 'shop_order' !== $post->post_type || ! get_post_meta( $post->ID, 'wt_credit_coupons', true )  ) {
				return;
			}

			add_meta_box( 'wt-coupons-in-order', __( 'Store Credit Purchased', 'wt-smart-coupons-for-woocommerce-pro' ), array( $this, 'store_credit_meta_box' ), 'shop_order', 'normal' );
        }

        /**
         * Coupon metabox content
         * @since 1.1.0
         */
        function store_credit_meta_box() {
            global $post;
            $coupon_attached = get_post_meta( $post->ID, 'wt_credit_coupons', true );
            if( $coupon_attached )  {
               $coupons = maybe_unserialize( $coupon_attached );
               echo '<div class="wt_order_credit_coupons">';

               foreach( $coupons as $coupon_item ) {
                    $coupon_id = $coupon_item['coupon_id']; 
                    $coupon = get_post( $coupon_id );

                    if( ! $coupon ) {
                        continue;
                    }

                    $coupon_obj = new WC_Coupon( $coupon->post_title );
                    $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon_obj );
                    
                    $coupon_data['coupon_amount'] = Wt_Smart_Coupon_Admin::get_formatted_price( $coupon_item['credited_amount'] );
                    $coupon_data['display_on_page'] = 'credit_meta';
                    echo Wt_Smart_Coupon_Public::get_coupon_html( $coupon,$coupon_data );
                   
               }
               $enabled_customizing_store_Credit = Wt_Smart_Coupon_Customisable_Gift_Card::is_extended_store_credit_enebaled( );
               if( ! $enabled_customizing_store_Credit ) {
                    echo '<div class="coupon_meta">';
                        echo '<span><b>'.__('From: ','wt-smart-coupons-for-woocommerce-pro').'</b>'.get_post_meta($post->ID,'wt_credit_coupon_send_from',true).'</span>';
                        echo '<span><b>'.__('To: ','wt-smart-coupons-for-woocommerce-pro').'</b>'.get_post_meta($post->ID,'wt_credit_coupon_send_to',true).'</span>';
                    echo '</div>';
               }
                

                echo '<div class="wt-send-coupon">';
                    echo '<button order-id='.$post->ID.' class="btn wt-btn-resend-store-credit button-primary button-large" >'.__('Resend store credit','wt-smart-coupons-for-woocommerce-pro').' </button>';
                    echo '<div class="wt-send-status"> </div>';
               echo '</div>';

               echo '</div>';
               $order = new WC_Order( $post->ID );

            }
        }


        /**
         * Add store credit email class into woocommmerce email
         * @since 1.2.0
         */
        function add_store_credit_emails( $email_classes ) {
            require_once ( WT_SMARTCOUPON_MAIN_PATH.'admin/store-credit/class-wt-smart-coupon-store-credit-email.php');
            $email_classes['WT_smart_Coupon_Store_Credit_Email'] = new WT_Smart_Coupon_Store_Credit_Email();
            return $email_classes;
        }
        

        /**
         * Aajx action for sending store credit items from an order [ send now button on order]
         * @since 1.2.6
         */

        function send_store_credit_items() {
            $order_id = isset( $_POST['_wt_order_id'] ) ? $_POST['_wt_order_id'] : 0;
            if( $order_id < 1 ) {
                $return = array(
                    'error' =>  true,
                    'message' => __('Something went wrong','wt-smart-coupons-for-woocommerce-pro')
                );
                echo json_encode($return);
                die();
            }
            $enabled_customizing_store_Credit = Wt_Smart_Coupon_Customisable_Gift_Card::is_extended_store_credit_enebaled( );
            if( ! $enabled_customizing_store_Credit ) {
                $send_coupon = $this->send_store_credit_email_for_order_old( $order_id );

            } else {
                $send_coupon = $this->send_store_credit_email_for_order( $order_id );
            }

            if( $send_coupon  ) {
                $return = array(
                    'error' =>  false,
                    'message' => __('store credit send successfully','wt-smart-coupons-for-woocommerce-pro')
                );
                echo json_encode($return);
                die();
            }

            
            $return = array(
                'error' =>  true,
                'message' => __('Something went wrong','wt-smart-coupons-for-woocommerce-pro')
            );
            echo json_encode($return);
            die();
        }
    }
}

