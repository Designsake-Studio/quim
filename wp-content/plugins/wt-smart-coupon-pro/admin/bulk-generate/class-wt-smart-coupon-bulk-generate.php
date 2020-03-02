<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * 
 * This file included basic functionality required  for bulk generating coupon
 */
if( ! class_exists ( 'Wt_Smart_Coupon_Bulk_Generate' ) ) {
    class Wt_Smart_Coupon_Bulk_Generate {

        public function __construct( ) {
            if( ( isset( $_GET['tab'] ) && $_GET['tab'] == 'create-coupon' ) ) {
                add_filter( 'wt_smart_coupon_admin_tab_items', array($this,'add_create_coupon_tab'), 100, 1 );
           }
        }
        /**
         * Add Store Credit tabs into Smart Coupon settings.
         * @since 1.2.1
         */
        function add_admin_tab( $admin_tabs ) {
            $admin_tabs['bulk-generate'] =__('Bulk Generate','wt-smart-coupons-for-woocommerce-pro');
            return $admin_tabs;
        }

        function add_create_coupon_tab( $admin_tabs  ) {
            $admin_tabs['create-coupon'] =__('Create Coupon','wt-smart-coupons-for-woocommerce-pro');
            return $admin_tabs;

        }

        /**
         * Bulk Genrate Tab content
         * @since 1.2.1
         */

         function bulk_generate_tab_content() {
             $this->bulk_genrate_coupon_tab();

         }

         /**
          * Bulk Genrate Settings in general settings
          * @since 1.2.1
          */

          function bulk_generate_settings() {
            $general_settings_options = Wt_Smart_Coupon_Admin::get_option('wt_copon_general_settings');

              ?>
              <div class="wt_section_title">
                    <h2>
                        <?php _e('Bulk generate','wt-smart-coupons-for-woocommerce-pro') ?>
                       
                    </h2>
                </div>

                <table class="form-table">
                    <tbody>
                        
                        <tr  valign="top">
                            <?php 
                                $no_of_coupons = (isset( $general_settings_options['no_of_characters_for_bulk_generate'] ) )? $general_settings_options['no_of_characters_for_bulk_generate'] : 12;
                            ?>
                            <td scope="row" class="titledesc"> 
                                <label> <?php _e("Length of the coupon code",'wt-smart-coupons-for-woocommerce-pro');  ?></label>
                            </td>
                            <td class="forminp forminp-text">
                                <p>
                                    <input type="number" name="_wt_no_of_characters_for_bulk_generate" min="7" value="<?php echo $no_of_coupons ; ?>" />
                                    <?php echo wc_help_tip( __('By default the plugin generates a 12 character coupon code minus the Prefix and Suffix.e.g,CRAZY-RTG23TYWE45Q-50, where CRAZY- is the prefix, RTG23TYWE45Q the autogenerated coupon code and -50 the suffix. ','wt-smart-coupons-for-woocommerce-pro') ); ?>
                                </p>
                            </td>
                        </tr>

                    </tbody>
                </table>

              <?php
          }

        /**
         * Add smart coupon email classes into wC_email.
         * @since 1.0.0
         */
        public  function add_wt_smart_coupon_emails( $email_classes ) {
            require_once( WT_SMARTCOUPON_MAIN_PATH.'admin/email/class-wt-smart-coupon-email.php');
            $email_classes['WT_smart_Coupon_Email'] = new WT_smart_Coupon_Email();
            return $email_classes;


        }

        /**
         * Add Bulk Smart coupon pages into woocommcerce screen ID's
         * @since 1.0.0
         */
        public function add_wc_screen_id( $screen_ids ) {
            $screen_ids[] = 'admin_page_wt-smart-coupon';
            $screen_ids[] = 'dashboard_page_wt-smart-coupon';
            $screen_ids[] = '_page_wt-smart-coupon';
            return $screen_ids;

        }

        /**
         * WP hooks only need to run for bulk generate Coupon
         * @since 1.0.0
         */
        public function add_bulk_genrate_specific_hooks() {

            add_action('woocommerce_coupon_options',array($this,'bulk_generate_option'),10,2);

        }

        /**
         *  Display mete fields required for bulk coupon.
         * @since 1.0.0
         */

        public function bulk_generate_option( $coupon_id,$coupon ) {
            ?>

            <p class="form-field">
                <label for="_wt_coupon_format"><?php _e('Coupon format', 'wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input type="text" name="_wt_coupon_prefix" id="_wt_coupon_prefix" placeholder="Prefix" />
                <code>coupon_code</code>
                <input type="text" name="_wt_coupon_suffix" id="_wt_coupon_suffix" placeholder="Suffix" />

            </p>
            <?php

        }
        

        /**
         * Include Woocommcerce Styles on bulk import page.
         * @since 1.0.0
         */
        public function generate_coupon_styles_and_scripts() {
            global $pagenow, $wp_scripts,$woocommerce;
            if( !$woocommerce || empty( $pagenow ) || 'admin.php' !== $pagenow || !isset( $_GET['page'] ) ||  $_GET['page'] !='wt-smart-coupon' || !isset( $_GET['tab'] ) ||  $_GET['tab'] != 'bulk-generate'  ) {
                return;
            }
            
            $suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

            $locale  = localeconv();
            $decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

            wp_enqueue_style( 'woocommerce_admin_menu_styles', $woocommerce->plugin_url() . '/assets/css/menu.css', array(), $woocommerce->version );
            wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', array(), $woocommerce->version );
            wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );

            $woocommerce_admin_params = array(
                'i18n_decimal_error'                => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'woocommerce' ), $decimal ),
                'i18n_mon_decimal_error'            => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'woocommerce' ), wc_get_price_decimal_separator() ),
                'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'woocommerce' ),
                'i18_sale_less_than_regular_error'  => __( 'Please enter in a value less than the regular price.', 'woocommerce' ),
                'decimal_point'                     => $decimal,
                'mon_decimal_point'                 => wc_get_price_decimal_separator(),
                'strings'                           => array(
                    'import_products' => __( 'Import', 'woocommerce' ),
                    'export_products' => __( 'Export', 'woocommerce' ),
                ),
                'nonces'                            => array(
                    'gateway_toggle' => wp_create_nonce( 'woocommerce-toggle-payment-gateway-enabled' ),
                ),
                'urls'                              => array(
                    'import_products' => current_user_can( 'import' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ) : null,
                    'export_products' => current_user_can( 'export' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ) : null,
                ),
            );

            $woocommerce_admin_meta_boxes_params = array(
                'remove_item_notice'            => __( 'Are you sure you want to remove the selected items? If you have previously reduced this item\'s stock, or this order was submitted by a customer, you will need to manually restore the item\'s stock.', 'woocommerce' ),
                'i18n_select_items'             => __( 'Please select some items.', 'woocommerce' ),
                'i18n_do_refund'                => __( 'Are you sure you wish to process this refund? This action cannot be undone.', 'woocommerce' ),
                'i18n_delete_refund'            => __( 'Are you sure you wish to delete this refund? This action cannot be undone.', 'woocommerce' ),
                'i18n_delete_tax'               => __( 'Are you sure you wish to delete this tax column? This action cannot be undone.', 'woocommerce' ),
                'remove_item_meta'              => __( 'Remove this item meta?', 'woocommerce' ),
                'remove_attribute'              => __( 'Remove this attribute?', 'woocommerce' ),
                'name_label'                    => __( 'Name', 'woocommerce' ),
                'remove_label'                  => __( 'Remove', 'woocommerce' ),
                'click_to_toggle'               => __( 'Click to toggle', 'woocommerce' ),
                'values_label'                  => __( 'Value(s)', 'woocommerce' ),
                'text_attribute_tip'            => __( 'Enter some text, or some attributes by pipe (|) separating values.', 'woocommerce' ),
                'visible_label'                 => __( 'Visible on the product page', 'woocommerce' ),
                'used_for_variations_label'     => __( 'Used for variations', 'woocommerce' ),
                'new_attribute_prompt'          => __( 'Enter a name for the new attribute term:', 'woocommerce' ),
                'calc_totals'                   => __( 'Calculate totals based on order items, discounts, and shipping?', 'woocommerce' ),
                'calc_line_taxes'               => __( 'Calculate line taxes? This will calculate taxes based on the customers country. If no billing/shipping is set it will use the store base country.', 'woocommerce' ),
                'copy_billing'                  => __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
                'load_billing'                  => __( 'Load the customer\'s billing information? This will remove any currently entered billing information.', 'woocommerce' ),
                'load_shipping'                 => __( 'Load the customer\'s shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
                'featured_label'                => __( 'Featured', 'woocommerce' ),
                'prices_include_tax'            => esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
                'round_at_subtotal'             => esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
                'no_customer_selected'          => __( 'No customer selected', 'woocommerce' ),
                'plugin_url'                    => $woocommerce->plugin_url(),
                'ajax_url'                      => admin_url( 'admin-ajax.php' ),
                'order_item_nonce'              => wp_create_nonce( 'order-item' ),
                'add_attribute_nonce'           => wp_create_nonce( 'add-attribute' ),
                'save_attributes_nonce'         => wp_create_nonce( 'save-attributes' ),
                'calc_totals_nonce'             => wp_create_nonce( 'calc-totals' ),
                'get_customer_details_nonce'    => wp_create_nonce( 'get-customer-details' ),
                'search_products_nonce'         => wp_create_nonce( 'search-products' ),
                'grant_access_nonce'            => wp_create_nonce( 'grant-access' ),
                'revoke_access_nonce'           => wp_create_nonce( 'revoke-access' ),
                'add_order_note_nonce'          => wp_create_nonce( 'add-order-note' ),
                'delete_order_note_nonce'       => wp_create_nonce( 'delete-order-note' ),
                'calendar_image'                => $woocommerce->plugin_url().'/assets/images/calendar.png',
                'post_id'                       => '',
                'base_country'                  => $woocommerce->countries->get_base_country(),
                'currency_format_num_decimals'  => wc_get_price_decimals(),
                'currency_format_symbol'        => get_woocommerce_currency_symbol(),
                'currency_format_decimal_sep'   => esc_attr( wc_get_price_decimal_separator() ),
                'currency_format_thousand_sep'  => esc_attr( wc_get_price_decimal_separator() ),
                'currency_format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
                'rounding_precision'            => WC_ROUNDING_PRECISION,
                'tax_rounding_mode'             => WC_TAX_ROUNDING_MODE,
                'product_types'                 => array_map( 'sanitize_title', get_terms( 'product_type', array( 'hide_empty' => false, 'fields' => 'names' ) ) ),
                'i18n_download_permission_fail' => __( 'Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.', 'woocommerce' ),
                'i18n_permission_revoke'        => __( 'Are you sure you want to revoke access to this download?', 'woocommerce' ),
                'i18n_tax_rate_already_exists'  => __( 'You cannot add the same tax rate twice!', 'woocommerce' ),
                'i18n_product_type_alert'       => __( 'Your product has variations! Before changing the product type, it is a good idea to delete the variations to avoid errors in the stock reports.', 'woocommerce' )
            );
            if ( ! wp_script_is( 'wc-admin-coupon-meta-boxes' ) ) {
                wp_enqueue_script( 'wc-admin-coupon-meta-boxes', $woocommerce->plugin_url() . '/assets/js/admin/meta-boxes-coupon' . $suffix . '.js', array( 'woocommerce_admin', 'wc-enhanced-select', 'wc-admin-meta-boxes' ), $woocommerce->version );
                wp_localize_script(
					'wc-admin-coupon-meta-boxes',
					'woocommerce_admin_meta_boxes_coupon',
					array(
						'generate_button_text' => esc_html__( 'Generate coupon code', 'woocommerce' ),
						'characters'           => apply_filters( 'woocommerce_coupon_code_generator_characters', 'ABCDEFGHJKMNPQRSTUVWXYZ23456789' ),
						'char_length'          => apply_filters( 'woocommerce_coupon_code_generator_character_length', 8 ),
						'prefix'               => apply_filters( 'woocommerce_coupon_code_generator_prefix', '' ),
						'suffix'               => apply_filters( 'woocommerce_coupon_code_generator_suffix', '' ),
					)
				);
                wp_localize_script( 'wc-admin-meta-boxes', 'woocommerce_admin_meta_boxes', $woocommerce_admin_meta_boxes_params );
                wp_enqueue_script( 'woocommerce_admin', $woocommerce->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), $woocommerce->version );
                wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $woocommerce_admin_params );
            }
        }

        /**
         * Function for rendering Contents  into bulk genrate tab
         * @since 1.0.0
         */

        public function bulk_genrate_coupon_tab() {
            $this->add_bulk_genrate_specific_hooks();
            ?> 
            <form id="generate-coupon" class="form bulk-generate-coupon" action="admin.php?page=wt-smart-coupon&tab=create-coupon" method="POST">

            <div id="bulk-create-message"><p></p></div>
            
        
                <div id="normal-sortables-4" class="meta-box-sortables ui-sortable">
                    <div id="wt_bulk_create_top" class="postbox ">
                        <div class="wt_bulk_create_top_content">
                            <div class="wt_settings_section">
                                <h2><?php _e('Bulk Generate Coupon','wt-smart-coupons-for-woocommerce-pro'); ?></h2>
                                <p> <?php _e('Store owners can use this option to create coupon in bulk with unique codes and a preset criteria. You can add these coupons to your store, share to customers directly by mail or simply export into a CSV for a later import.','wt-smart-coupons-for-woocommerce-pro'); ?></p>
                            </div>
                            <p><?php _e(' Specified number of coupons are generated as per the matching criteria from the Coupon data section below.','wt-smart-coupons-for-woocommerce-pro'); ?></p>
                            
                            <div class="generate-coupon-wrapper">
                                <div class="wt_bulk_section_title">
                                    <?php _e('Action','wt-smart-coupons-for-woocommerce-pro') ?>
                                </div>
                                <?php wp_nonce_field(  'wt_bulk_generate_coupon' ); ?>
                                <div class="form-group">
                                    <label> <?php _e('No of coupons to be generated','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                                    <input min="1" step="1" type="number" class="form-item" name="_wt_no_of_coupons" id="_wt_no_of_coupons"  placeholder="0" />
                                </div>
                                <div class="form-group">
                                    <label><?php _e( 'Generate coupons and,', 'wt-smart-coupons-for-woocommerce-pro') ?></label>
                                    <div class="form-fields">
                                        <p><label><input type="radio" name="wt_generate_coupon_and" value="add_to_store" checked /><?php _e('Add to Store','wt-smart-coupons-for-woocommerce-pro') ?></label></p>
                                        <p><label><input type="radio" name="wt_generate_coupon_and" value="export_as_csv_store"/><?php _e('Export as CSV','wt-smart-coupons-for-woocommerce-pro'); ?></label></p>
                                        <p><label><input type="radio" name="wt_generate_coupon_and" value="email_to_recipients"/><?php _e('Email to the recipients','wt-smart-coupons-for-woocommerce-pro') ?></label></p>
                                    </div>
                                </div>
                                <div class="bulk-generate-desc">  
                                    <p>
                                        <?php _e('Email recipients option works in combination with Allowed emails. If email restriction is applied under allowed emails option, the application generates only enough no. of coupons depending on whichever is the lowest value, either the coupon number or the number of emails.','wt-smart-coupons-for-woocommerce-pro') ?>
                                        <a target="_blank" href="https://www.webtoffee.com/setup-smart-coupons-for-woocommerce"> <?php _e('Read more','wt-smart-coupons-for-woocommerce-pro'); ?></a>
                                    </p>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    global $woocommerce, $woocommerce_smart_coupon, $post;
                    
                    $reference_post_id = get_option('wt_auto_draft_smart_coupons');
                    if( !$reference_post_id ) {
                        $args = array(
                            'post_status' => 'auto-draft',
                            'post_type' => 'shop_coupon'
                        );
                        $reference_post_id = wp_insert_post( $args );
                        update_option('wt_auto_draft_smart_coupons',$reference_post_id );

                    }

                    $post = get_post( $reference_post_id );
                    if ( empty( $post ) ) {
                        $args = array(
                                    'post_status' => 'auto-draft',
                                    'post_type' => 'shop_coupon'
                                );
                        $reference_post_id = wp_insert_post( $args );
                        update_option( 'wt_auto_draft_smart_coupons', $reference_post_id );
                        $post = get_post( $reference_post_id );
                    }

                ?>
    
                <div id="wt-coupon-meta-box" class="postbox-container">
                    <div id="normal-sortables-5" class="meta-box-sortables ui-sortable">
                        <div id="woocommerce-coupon-data" class="postbox ">
                            <h2 class="hndle ui-sortable-handle"><span><?php _e('Coupon data','wt-smart-coupons-for-woocommerce-pro'); ?></span></h2>
                            <div class="inside">
                                <?php WC_Meta_Box_Coupon_Data::output( $post ); ?>
                            </div>

                            
                        </div>
                    </div>
                </div>

                <div class="wt-button-wrapper">
                    <input class="button button-primary button-large" type="submit" name="create_bulk_coupon" id="create_bulk_coupon" value="Generate Coupon" />
                </div>

                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        jQuery('#generate-coupon').on('submit', function(event){

                            if( jQuery('input#_wt_no_of_coupons').val() == "" ){
                                event.preventDefault();

                                jQuery("div#bulk-create-message").removeClass("updated fade").addClass("error fade").show();
                                jQuery('div#bulk-create-message p').html( "<?php _e('Please enter a valid value for Number of Coupons to Generate', 'wt-smart-coupons-for-woocommerce-pro'); ?>" );
                                jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
                                return false;
                            } else {
                                jQuery("div#bulk-create-message").removeClass("error fade").addClass("updated fade").hide();
                                return true;
                            }
                        });
                    });

                </script>

            </form>
        <?php

        }

        
        /**
         * Action on genrating Bulk coupon
         * @since 1.0.0.
         */

        public function generate_bulk_coupon_action() {
            check_admin_referer( 'wt_bulk_generate_coupon' );

                if( isset( $_POST['_wt_no_of_coupons']) && $_POST['_wt_no_of_coupons'] > 0 ) {
                    
                    $allowed_email = ( isset($_POST['customer_email']) )? $_POST['customer_email'] : '';
                    if( $allowed_email!='' ) {
                        $emails = explode(',',$allowed_email);
                    }
                    $coupon_need_to_genrate = $_POST['_wt_no_of_coupons'];

                    if( isset($emails) && sizeof($emails) > 0 && sizeof($emails) < $_POST['_wt_no_of_coupons']  ) {
                        $coupon_need_to_genrate = sizeof($emails) ;
                    }
                    if( isset( $_POST['wt_generate_coupon_and']) && ( $_POST['wt_generate_coupon_and'] == 'email_to_recipients' ||  $_POST['wt_generate_coupon_and'] == 'add_to_store' ) ) {

                        for( $i = 0 ; $i < $coupon_need_to_genrate; $i++  ) {

                            $prefix = $_POST['_wt_coupon_prefix'];
                            $suffix = $_POST['_wt_coupon_suffix'];

                            $coupon_length = Wt_Smart_Coupon_Admin::get_option('no_of_characters_for_bulk_generate');

                            $coupon_code = Wt_Smart_Coupon_Admin::generate_random_coupon($prefix,$suffix,$coupon_length);

                        
                            
                            $coupon_args = array(
                                'post_title'    => strtolower( $coupon_code ),
                                'post_content'  => '',
                                'post_status'   => 'publish',
                                'post_author'   => 1,
                                'post_type'     => 'shop_coupon'
                            );

                            $coupon_id  = wp_insert_post( $coupon_args );
                            update_post_meta( $coupon_id, 'wt_bulk_genrated_coupon', true );
                            $coupon     = get_post($coupon_id);

                            do_action('woocommerce_process_shop_coupon_meta',$coupon_id,$coupon);
                            $coupon_obj = new WC_Coupon( $coupon_id );
                            if( isset( $emails ) && !empty($emails)) {
                                $coupon_obj->set_email_restrictions(  $emails[$i] );

                            }
                            $coupon_obj->save();

                            if( $_POST['wt_generate_coupon_and'] == 'email_to_recipients' ) {
                                WC()->mailer();
                                do_action('wt_send_coupon_to_customer',$coupon_obj,strtolower( $coupon_code ),$emails[$i]  );

                            }
                        }
                        
                    } elseif( $_POST['wt_generate_coupon_and'] && ( $_POST['wt_generate_coupon_and'] == 'export_as_csv_store' ) ) {
                            
                        $this->export_coupon( $_POST,$coupon_need_to_genrate );

                    } else {
                        header('Location: ' . $_SERVER['HTTP_REFERER']);
                    }
                    wp_safe_redirect( get_admin_url().'edit.php?post_type=shop_coupon' );

                } else {
                    ?>
                    <div class="postbox" id="">
                        <div class="error notice">
                            <p> <?php _e('Please enter a valid value for Number of Coupons to Generate', 'wt-smart-coupons-for-woocommerce-pro'); ?></p>
                        </div>
                    
                    </div>
                    <?php
                }
        }

        /**
         * Create CSV header and Content Data and Download the CSV created.
         * @since 1.0.0
         * @param $post => $_POST Object from the form submition Type=> array
         * @param $no_of_coupon_need_to_genrate 
         */
        public function export_coupon( $post,$no_of_coupon_need_to_genrate ) {

            $prefix = $_POST['_wt_coupon_prefix'];
            $suffix = $_POST['_wt_coupon_suffix'];

            $coupon_posts_headers = array ( 
                'post_title'    => __( 'Coupon Code','wt-smart-coupons-for-woocommerce-pro' ),
                'post_excerpt'  => __( 'Post Excerpt','wt-smart-coupons-for-woocommerce-pro' ),
                'post_status'   => __( 'Post Status','wt-smart-coupons-for-woocommerce-pro' ),
                'post_parent'   => __( 'Post Parent','wt-smart-coupons-for-woocommerce-pro' ),
                'menu_order'    => __( 'Menu Order','wt-smart-coupons-for-woocommerce-pro' ),
                'post_date'     => __( 'Post Date','wt-smart-coupons-for-woocommerce-pro')
            );
            $default_coupon_meta_fields = array(
                '_wt_sc_shipping_methods'           => __( '_wt_sc_shipping_methods','wt-smart-coupons-for-woocommerce-pro' ),
                '_wt_sc_payment_methods'            => __( '_wt_sc_shipping_methods','wt-smart-coupons-for-woocommerce-pro' ),
                '_wt_sc_user_roles'                 => __( '_wt_sc_shipping_methods','wt-smart-coupons-for-woocommerce-pro' ),
                '_wt_category_condition'            => __( '_wt_sc_shipping_methods','wt-smart-coupons-for-woocommerce-pro' ),
                '_wt_category_condition'            => __( '_wt_category_condition','wt-smart-coupons-for-woocommerce-pro' ),
                '_wt_product_condition'             => __( '_wt_product_condition','wt-smart-coupons-for-woocommerce-pro' ),
                '_wt_free_product_ids'              => __( '_wt_free_product_ids','wt-smart-coupons-for-woocommerce-pro' ),
                '_wt_min_matching_product_qty'      => __( '_wt_min_matching_product_qty','wt-smart-coupons-for-woocommerce-pro' ),
                '_wt_max_matching_product_qty'      => __( '_wt_max_matching_product_qty','wt-smart-coupons-for-woocommerce-pro' ),
                '_wt_min_matching_product_subtotal' => __( '_wt_min_matching_product_subtotal','wt-smart-coupons-for-woocommerce-pro' ),
                '_wt_max_matching_product_subtotal' => __( '_wt_max_matching_product_subtotal','wt-smart-coupons-for-woocommerce-pro' ),
                'discount_type'                     => __( 'discount_type','wt-smart-coupons-for-woocommerce-pro' ),
                'coupon_amount'                     => __( 'coupon_amount','wt-smart-coupons-for-woocommerce-pro' ),
                'individual_use'                    => __( 'individual_use','wt-smart-coupons-for-woocommerce-pro' ),
                'product_ids'                       => __( 'product_ids','wt-smart-coupons-for-woocommerce-pro' ),
                'exclude_product_ids'               => __( 'exclude_product_ids','wt-smart-coupons-for-woocommerce-pro' ),
                '_wt_valid_for_number'              => __( '_wt_valid_for_number','wt-smart-coupons-for-woocommerce-pro' ),
                'minimum_amount'                    => __( 'minimum_amount','wt-smart-coupons-for-woocommerce-pro' ),
                'maximum_amount'                    => __( 'maximum_amount','wt-smart-coupons-for-woocommerce-pro' ),
                'customer_email'                    => __( 'customer_email','wt-smart-coupons-for-woocommerce-pro' ),
                'usage_limit'                       => __( 'usage_limit','wt-smart-coupons-for-woocommerce-pro' ),
                'limit_usage_to_x_items'            => __( 'limit_usage_to_x_items','wt-smart-coupons-for-woocommerce-pro' ),
                'usage_limit_per_user'              => __( 'usage_limit_per_user','wt-smart-coupons-for-woocommerce-pro' ),
            );

            $coupon_meta_headers  = array();
            if( isset($post['customer_email'] ) && '' != $post['customer_email']  ) {
                $coupon_emails = explode(',',$post['customer_email']);
            }

            foreach( $post as $key => $value ) {
                if(    $key == '_wpnonce' 
                    || $key =='_wp_http_referer' 
                    || $key ==  '_wt_no_of_coupons' 
                    || $key == 'wt_generate_coupon_and' 
                    || $key == 'woocommerce_meta_nonce' 
                    || $key == 'create_bulk_coupon' 
                    || $key == '_wt_coupon_prefix' 
                    || $key == '_wt_coupon_suffix' ) {
                
                    continue;
                }

                $coupon_meta_headers[$key]  = $key;
                if( is_array($value) ) {
                    $value = implode(',',$value);
                }
                $coupon_meta_values[$key]   =  $value ;
                

            }
            $coupon_meta_headers = array_unique(array_merge($default_coupon_meta_fields,$coupon_meta_headers) );

            $coupon_csv_header = array_merge($coupon_posts_headers,$coupon_meta_headers);
            $coupon_csv_data = array();

            for( $i =0 ; $i < $no_of_coupon_need_to_genrate; $i++ ) {

                $coupon_length = Wt_Smart_Coupon_Admin::get_option('no_of_characters_for_bulk_generate');
                $coupon_meta_values['post_title'] = Wt_Smart_Coupon_Admin::generate_random_coupon($prefix,$suffix,$coupon_length);
                
                if( isset( $coupon_emails ) && !empty($coupon_emails)) {
                    $coupon_meta_values['customer_email'] = $coupon_emails[$i];
                }
                
                $coupon_csv_data[$i] = $coupon_meta_values;
                

            }

            $file_data = $this->export_coupon_csv( $coupon_csv_header, $coupon_csv_data );
            
            if ( ob_get_level() ) {
                $levels = ob_get_level();
                for ( $i = 0; $i < $levels; $i++ ) {
                    @ob_end_clean();
                }
            } else {
                @ob_end_clean();
            }
            nocache_headers();
            header( "X-Robots-Tag: noindex, nofollow", true );
            header( "Content-Type: text/x-csv; charset=UTF-8" );
            header( "Content-Description: File Transfer" );
            header( "Content-Transfer-Encoding: binary" );
            header( "Content-Disposition: attachment; filename=\"" . sanitize_file_name( $file_data['file_name'] ) . "\";" );

            echo $file_data['file_content'];
            exit;
        }

        /**
         * Create CSV and get the .csv file
         * @since 1.0.0
         * @param $coupon_csv_header CSV header.
         * @param $coupon_csv_data CSV data.
         */
        public function export_coupon_csv( $coupon_csv_header, $coupon_csv_data ){

            $getfield = '';

            foreach ( $coupon_csv_header as $key => $value ) {
                    $getfield .= $key . ',';
            }

            $fields = substr_replace($getfield, '', -1);

            $each_field = array_keys( $coupon_csv_header );

            $csv_file_name = 'wt_smart_coupons_' . gmdate('d_m_Y_H_i_s') . ".csv";

            foreach( (array) $coupon_csv_data as $row ){
                for($i = 0; $i < count ( $coupon_csv_header ); $i++){
                    if($i == 0) $fields .= "\n";

                    if( array_key_exists($each_field[$i], $row) ){
                        $row_each_field = $row[$each_field[$i]];
                    } else {
                        $row_each_field = '';
                    }

                    $array = str_replace( array("\n", "\n\r", "\r\n", "\r"), "\t", $row_each_field);

                    $array = str_getcsv ( $array , ";" , "\"" , "\\");
                    $array = str_getcsv ( $row_each_field , ";", "\"" , "\\");

                    $str = ( $array && is_array( $array ) ) ? implode( ', ', $array ) : '';
                    $fields .= '"'. $str . '",';

                    // $fields .= $row_each_field;
                }
                $fields = substr_replace($fields, '', -1);
            }

            $upload_dir = wp_upload_dir();

            $file_data = array();
            $file_data['wp_upload_dir'] = $upload_dir['path'] . '/';
            $file_data['file_name'] = $csv_file_name;
            $file_data['file_content'] = $fields;

            return $file_data;
        }

        /**
         * Update Bulk genrate settings
         * @since 1.2.1
         * 
         */
         function update_bulk_generate_settings( ) {

            if( isset( $_POST['update_wt_smart_coupon_general_settings'] ) ) {
                check_admin_referer('wt_smart_coupons_general_settings');
                $smart_coupon_options = Wt_Smart_Coupon_Admin::get_options();
                if( isset( $_POST['_wt_no_of_characters_for_bulk_generate'] ) && '' != $_POST['_wt_no_of_characters_for_bulk_generate']  ) {
                    $no_charectors = sanitize_text_field( $_POST['_wt_no_of_characters_for_bulk_generate'] );                    
                } else {
                    $no_charectors = 12; 
                }
                $general_settings = $smart_coupon_options['wt_copon_general_settings'];
                $general_settings['no_of_characters_for_bulk_generate'] = $no_charectors;
                $smart_coupon_options[ 'wt_copon_general_settings'] = $general_settings;
                update_option("wt_smart_coupon_options", $smart_coupon_options);

            }
        }
        
        
    }
}