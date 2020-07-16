<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin
 * @author     markhf <info@webtoffee.com>
 */
if( ! class_exists ( 'Wt_Smart_Coupon_Admin' ) ) {
    class Wt_Smart_Coupon_Admin {

        private $plugin_name;
        private $version;

        public function __construct($plugin_name, $version) {

            $this->plugin_name = $plugin_name;
            $this->version = $version;
        }


        /**
         * Save Custom meata fields added in coupon 
         * @since 1.0.0
         */
        public function process_shop_coupon_meta($post_id, $post) {

            if (!current_user_can('manage_woocommerce')) 
            {
                wp_die(__('You do not have sufficient permission to perform this operation', 'wt-smart-coupons-for-woocommerce-pro'));
            }
            if (!empty($_POST['_wt_sc_shipping_methods'])) {
                $wt_sc_shipping_methods = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_sc_shipping_methods'], 'text_arr' );
                update_post_meta($post_id, '_wt_sc_shipping_methods', implode(',', $wt_sc_shipping_methods ) );
            } else {
                update_post_meta($post_id, '_wt_sc_shipping_methods', '');
            }

            if (!empty($_POST['_wt_sc_payment_methods'])) {
                $wt_sc_payment_methods = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_sc_payment_methods'], 'text_arr' );
                update_post_meta($post_id, '_wt_sc_payment_methods', implode(',', $wt_sc_payment_methods ));
            } else {
                update_post_meta($post_id, '_wt_sc_payment_methods', '' );
            }

            // Location restriction 

            if (!empty($_POST['_wt_need_check_location_in'])) {
                $_wt_need_check_location_in = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_need_check_location_in'] );
                update_post_meta($post_id, '_wt_need_check_location_in', $_wt_need_check_location_in );
            } else {
                update_post_meta($post_id, '_wt_need_check_location_in', 'billing');
            }

            if (!empty($_POST['_wt_coupon_available_location'])) {
                $wt_coupon_available_location = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_coupon_available_location'], 'text_arr' );
                update_post_meta($post_id, '_wt_coupon_available_location', implode(',', $wt_coupon_available_location ));
            } else {
                update_post_meta($post_id, '_wt_coupon_available_location', '' );
            }


            if (!empty($_POST['_wt_sc_user_roles'])) {
                $_wt_sc_user_roles = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_sc_user_roles'], 'text_arr' );
                update_post_meta($post_id, '_wt_sc_user_roles', implode(',',$_wt_sc_user_roles ) );
            } else {
                update_post_meta($post_id, '_wt_sc_user_roles', '');
            }

            // Save Usage Restriction items.

            if( isset( $_POST['_wt_category_condition'] ) && !empty( $_POST['_wt_category_condition'] ) ) {
                $_wt_category_condition = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_category_condition'] );
                update_post_meta( $post_id, '_wt_category_condition', $_wt_category_condition );
            } else {
                update_post_meta($post_id, '_wt_category_condition', 'or');
            }

            if( isset( $_POST['_wt_product_condition'] ) && !empty( $_POST['_wt_product_condition'] ) ) {
                $_wt_product_condition = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_product_condition'] );
                update_post_meta( $post_id, '_wt_product_condition', $_wt_product_condition );
            } else {
                update_post_meta($post_id, '_wt_product_condition','or');
            }

            
            // Matching products
            
            if( isset($_POST['_wt_min_matching_product_qty']) && $_POST['_wt_min_matching_product_qty']!='' ) {
                $_wt_min_matching_product_qty = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_min_matching_product_qty'], 'int' );
                update_post_meta($post_id, '_wt_min_matching_product_qty', $_wt_min_matching_product_qty );
            } else {
                update_post_meta($post_id, '_wt_min_matching_product_qty', '');
            }

            if( isset($_POST['_wt_max_matching_product_qty']) && $_POST['_wt_max_matching_product_qty']!='' ) {
                $_wt_max_matching_product_qty = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_max_matching_product_qty'], 'int' );
                update_post_meta($post_id, '_wt_max_matching_product_qty', $_wt_max_matching_product_qty );
            } else {
                update_post_meta($post_id, '_wt_max_matching_product_qty', '');
            }

            if( isset($_POST['_wt_min_matching_product_subtotal']) && $_POST['_wt_min_matching_product_subtotal']!='' ) {
                $_wt_min_matching_product_subtotal = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_min_matching_product_subtotal'],'float');
                update_post_meta($post_id, '_wt_min_matching_product_subtotal', $_wt_min_matching_product_subtotal );
            } else {
                update_post_meta($post_id, '_wt_min_matching_product_subtotal', '');
            }

            if( isset($_POST['_wt_max_matching_product_subtotal']) && $_POST['_wt_max_matching_product_subtotal']!='' ) {
                $_wt_max_matching_product_subtotal = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_max_matching_product_subtotal'], 'float' );
                update_post_meta($post_id, '_wt_max_matching_product_subtotal', $_wt_max_matching_product_subtotal );
            } else {
                update_post_meta($post_id, '_wt_max_matching_product_subtotal', '');
            }

            if( isset($_POST['_wt_valid_for_number']) && $_POST['_wt_valid_for_number']!='' ) {
                $_wt_valid_for_number = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_valid_for_number']);
                update_post_meta($post_id, '_wt_valid_for_number', $_wt_valid_for_number );
                
                if ( isset( $_POST['_wt_valid_for_type'] ) && '' != $_POST['_wt_valid_for_type']  ) {
                    $wt_valid_for_type = $_POST['_wt_valid_for_type'];
                } else {
                    $wt_valid_for_type = 'days';
                }
                update_post_meta($post_id, '_wt_valid_for_type', $wt_valid_for_type );

            }


            if( isset( $_POST['_wc_make_coupon_available'] ) && $_POST['_wc_make_coupon_available']!='' ) {
                
                $_wc_make_coupon_available = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wc_make_coupon_available'], 'text_arr' );
                update_post_meta($post_id, '_wt_make_coupon_available_in_myaccount', '' );

                update_post_meta($post_id, '_wc_make_coupon_available', implode(',', $_wc_make_coupon_available ) );
            } else {
                update_post_meta($post_id, '_wc_make_coupon_available',  '' );
                update_post_meta($post_id, '_wt_make_coupon_available_in_myaccount', '' );
            }

            
            
        }

        /**
         * Enqueue Admin styles.
         * @since 1.0.0
         */
        public function enqueue_styles() {
            $screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';
            
            if ( function_exists('wc_get_screen_ids') && in_array( $screen_id, wc_get_screen_ids() ) ) {
                wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wt-smart-coupon-admin.css', array(), $this->version, 'all');
                wp_enqueue_style( 'wp-color-picker' );
            }
        }
        /**
         * Enqueue Admin Scripts.
         * @since 1.0.0
         */
        public function enqueue_scripts() {

            $screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';

            $script_parameters['ajaxurl'] = admin_url( 'admin-ajax.php' ) ;
            $script_parameters['coupon_styles'] = self::coupon_styles();
            $script_parameters['nonce'] = wp_create_nonce( 'wt_smart_coupons_admin_nonce' );
            if ( function_exists('wc_get_screen_ids') && in_array( $screen_id, wc_get_screen_ids() ) ) {
                wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wt-smart-coupon-admin.js', array('jquery','wp-color-picker'), $this->version, false);
                wp_localize_script($this->plugin_name,'WTSmartCouponAdminOBJ',$script_parameters );
            }
        }

        /**
         * Add  Smart coupon pages into woocommcerce screen ID's
         * @since 1.0.0
         */
        public function add_wc_screen_id( $screen_ids ) {
            $screen_ids[] = 'admin_page_wt-smart-coupon';
            $screen_ids[] = 'dashboard_page_wt-smart-coupon';
            $screen_ids[] = '_page_wt-smart-coupon';
            return $screen_ids;
        }

        /**
         * Add tabs to the coupon option page.
         * @since 1.0.0
         */
        public function admin_coupon_options_tabs($tabs) {

            $tabs['wt_coupon_checkout_options'] = array(
                'label' => __('Checkout Options', 'wt-smart-coupons-for-woocommerce-pro'),
                'target' => 'webtoffee_coupondata_checkout1',
                'class' => 'webtoffee_coupondata_checkout1',
            );

            return $tabs;
        }

        /**
         * wt_coupon_checkout_options Page content
         * @since 1.0.0
         */
        public function admin_coupon_options_panels() {

            global $thepostid, $post;
            $thepostid = empty($thepostid) ? $post->ID : $thepostid;
            ?>
            <div id="webtoffee_coupondata_checkout1" class="panel woocommerce_options_panel">
            <?php
            do_action('webtoffee_coupon_metabox_checkout', $thepostid, $post);
            ?>
            </div>

            <?php
        }

        /**
         * Checkout tab form elements
         * @since 1.0.0
         */
        public function admin_coupon_metabox_checkout2($thepostid, $post) {


            $wc_help_icon_uri = WC()->plugin_url() . "/assets/images/help.png";

            $coupon_shipping_method_id_s = get_post_meta($thepostid, '_wt_sc_shipping_methods',true);
            if( '' !=  $coupon_shipping_method_id_s &&  !is_array( $coupon_shipping_method_id_s ) ) {
                $coupon_shipping_method_id_s = explode(',',$coupon_shipping_method_id_s);
            }

            // $coupon_shipping_method_ids = isset($coupon_shipping_method_id_s[0]) ? $coupon_shipping_method_id_s[0] : array();
            ?>

            <!-- Shipping methods -->
            <p class="form-field">
                <label for="_wt_sc_shipping_methods"><?php _e('Shipping methods', 'wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <select id="_wt_sc_shipping_methods" name="_wt_sc_shipping_methods[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e('Any shipping method', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                    <?php
                    $shipping_methods = WC()->shipping->load_shipping_methods();

                    if (!empty($shipping_methods)) {

                        foreach ($shipping_methods as $shipping_method) {

                            if ( !empty(  $coupon_shipping_method_id_s ) && in_array($shipping_method->id, $coupon_shipping_method_id_s)) {
                                echo '<option value="' . esc_attr($shipping_method->id) . '" selected>' . esc_html($shipping_method->method_title) . '</option>';
                            } else {
                                echo '<option value="' . esc_attr($shipping_method->id) . '">' . esc_html($shipping_method->method_title) . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
            <?php echo wc_help_tip( __('Coupon will be applicable only if any of these shipping methods are opted', 'wt-smart-coupons-for-woocommerce-pro') ); ?>

            </p>

            <!-- Payment methods -->
            <p class="form-field"><label for="_wt_sc_payment_methods"><?php _e('Payment methods', 'wt-smart-coupons-for-woocommerce-pro'); ?></label>

                <select id="webtoffee_payment_methods" name="_wt_sc_payment_methods[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e('Any payment method', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                    <?php
                    $coupon_payment_method_id_s = get_post_meta($thepostid, '_wt_sc_payment_methods',true);
                    if( '' !=  $coupon_payment_method_id_s && !is_array( $coupon_payment_method_id_s ) ) {
                        $coupon_payment_method_id_s = explode(',',$coupon_payment_method_id_s);
                    }
                    // $coupon_payment_method_ids = isset($coupon_payment_method_id_s[0]) ? $coupon_payment_method_id_s[0] : array();

                    $payment_methods = WC()->payment_gateways->payment_gateways();

                    if (!empty($payment_methods)) {

                        foreach ($payment_methods as $payment_method) {

                            if ('yes' === $payment_method->enabled) {
                                if ( !empty( $coupon_payment_method_id_s ) && in_array($payment_method->id, $coupon_payment_method_id_s)) {
                                    echo '<option value="' . esc_attr($payment_method->id) . '" selected>' . esc_html($payment_method->title) . '</option>';
                                } else {
                                    echo '<option value="' . esc_attr($payment_method->id) . '">' . esc_html($payment_method->title) . '</option>';
                                }
                            }
                        }
                    }
                    ?>
                </select>
                <?php echo wc_help_tip( __('Coupon will be applicable only if any of these payment methods are opted', 'wt-smart-coupons-for-woocommerce-pro') ); ?>
            </p>


            <p class="form-field"><label for="_wt_sc_user_roles"><?php _e('Applicable Roles', 'wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <?php
                    $_wt_sc_user_roles_s = get_post_meta($thepostid, '_wt_sc_user_roles',true);
                    if( !is_array( $_wt_sc_user_roles_s ) &&  '' != $_wt_sc_user_roles_s  ) {
                        $_wt_sc_user_roles_s = explode(',',$_wt_sc_user_roles_s);
                    }
                    $available_roles = array_reverse(get_editable_roles());

                ?>
                <select id="_wt_sc_user_roles" name="_wt_sc_user_roles[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e('Any role', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                    <?php
                    if( !empty( $available_roles) ) {
                        foreach ($available_roles as $role_id => $role) {
                            if( !empty( $_wt_sc_user_roles_s )  && in_array($role_id, $_wt_sc_user_roles_s ) ) {
                                $selected = 'selected = selected';
                            } else {
                                $selected = '';
                            }
                       
                            $role_name = translate_user_role($role['name']);
    
                            echo '<option value="' . esc_attr($role_id) . '" '.$selected.'>'
                            . esc_html($role_name) . '</option>';
                        }
                    }
                    
                    ?>
                </select> 
                <?php echo wc_help_tip( __('Coupon will be applicable only if the user belongs to any of these roles', 'wt-smart-coupons-for-woocommerce-pro') ); ?>
            </p>

            <p class="form-field wt_need_check_location_in  ">
            <?php

                $wt_need_check_location_in  = get_post_meta($thepostid, '_wt_need_check_location_in',true);
                $wt_need_check_location_in = ( '' == $wt_need_check_location_in )? 'billing' : $wt_need_check_location_in;

                woocommerce_wp_radio(
                    array(
                        'id'      => '_wt_need_check_location_in',
                        'value'     => $wt_need_check_location_in,
                        'class'     => 'wt_need_check_location_in',
                        'label'     => __('Restrict by country', 'wt-smart-coupons-for-woocommerce-pro'),
                        'options'   => array
                            (
                                'billing' => __('Billing Address.', 'wt-smart-coupons-for-woocommerce-pro'),
                                'shipping' => __('Shipping Address.', 'wt-smart-coupons-for-woocommerce-pro')
                            ),
                        'description' => __( 'Look for country from the selected option', 'wt-smart-coupons-for-woocommerce-pro' ),
                        'desc_tip'    => true,
                    )
                );
                $_wt_coupon_available_location  = get_post_meta($thepostid, '_wt_coupon_available_location',true);
                if( !is_array( $_wt_coupon_available_location ) &&  '' != $_wt_coupon_available_location  ) {
                    $_wt_coupon_available_location = explode(',',$_wt_coupon_available_location);
                }
                ?>

            </p>
            <p class="form-field">
            <?php
            $countries_obj   = new WC_Countries();
            $countries   = $countries_obj->__get('countries');

            ?>
            
            <label for="_wt_coupon_available_location">
                <?php _e('Country', 'wt-smart-coupons-for-woocommerce-pro'); ?>
               
            </label>

            
            
                <select id="_wt_coupon_available_location" name="_wt_coupon_available_location[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e('Any Location', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                    <?php

                        foreach( $countries as $country_code => $country ) {
                            $selected = '';
                            if( is_array( $_wt_coupon_available_location ) &&  in_array( $country_code , $_wt_coupon_available_location)  ) {
                                $selected = 'selected=selected';
                            }

                            echo '<option value="'.$country_code.'" '.$selected.'>';
                                echo $country;
                            echo '</option>';


                        }
                    ?>
                </select> 
                <?php echo wc_help_tip( __('Coupon will be applicable only for the selected country from either the shipping/billing address', 'wt-smart-coupons-for-woocommerce-pro') ); ?>
            </p>

            <?php
        }
        

        /**
         * Plugin action link.
         * @since 1.0.0
         */
        public function add_plugin_links_wt_smartcoupon($links) {


            $plugin_links = array(
                '<a target="_blank" href="https://www.webtoffee.com/support/">' . __('Support', 'wt-smart-coupons-for-woocommerce-pro') . '</a>',
                '<a href="'.admin_url('admin.php?page=wt-smart-coupon&tab=settings').'">' . __('Settings', 'wt-smart-coupons-for-woocommerce-pro') . '</a>',
            );
            
            return array_merge($plugin_links, $links);
        }
        
        /**
         * Extra Options on usage restrictions.
         * @since 1.0.0
         */
        public function admin_coupon_usage_restrictions( $post ){
            $coupon    = new WC_Coupon( $post );

            $wt_product_condition = ( get_post_meta( $post, '_wt_product_condition', true ) )? get_post_meta( $post, '_wt_product_condition', true ) : 'or';


            woocommerce_wp_radio(
                array(
                    'id'      => '_wt_product_condition',
                    'value'     => $wt_product_condition,
                    'class'     => 'wt_product_restrictions',
                    'label'     => __('Product condition:', 'wt-smart-coupons-for-woocommerce-pro'),
                    'options'   => array
                        (
                            'or' => __('Any', 'wt-smart-coupons-for-woocommerce-pro'),
                            'and' => __('All', 'wt-smart-coupons-for-woocommerce-pro')
                        ),
                    'description' => __( 'Coupon will be applied if any of the products from the below is available in the cart; all of option requires that the cart contains all of the listed products.', 'wt-smart-coupons-for-woocommerce-pro' ),
                    'desc_tip'    => true,
                )
            );


            // Categories

            $wt_category_condition = ( get_post_meta( $post, '_wt_category_condition', true ) )? get_post_meta( $post, '_wt_category_condition', true ) : 'or' ;
    
            woocommerce_wp_radio(
                array(
                    'id'      => '_wt_category_condition',
                    'value'     => $wt_category_condition,
                    'class'     => 'wt_category_condition',
                    'label'     => __('Category condition:', 'wt-smart-coupons-for-woocommerce-pro'),
                    'options'   => array
                        (
                            'or' => __('Any', 'wt-smart-coupons-for-woocommerce-pro'),
                            'and' => __('All', 'wt-smart-coupons-for-woocommerce-pro')
                        ),

                    'description' => __( 'Coupon will be applied if any of the categories from the below is available in the cart; all of option requires that the cart contains all of the listed categories.', 'wt-smart-coupons-for-woocommerce-pro' ),
                    'desc_tip'    => true,
                )
            );

            echo '<h3>' . esc_html( __( 'Matching products', 'wt-smart-coupons-for-woocommerce-pro' ) ) . "</h3>\n";


            // Minimum quantity of matching products (product/category)
                woocommerce_wp_text_input(
                    array(
                        'id'          => '_wt_min_matching_product_qty',
                        'label'       => __( 'Minimum quantity of matching products', 'wt-smart-coupons-for-woocommerce-pro' ),
                        'placeholder' => __( 'No minimum', 'woocommerce' ),
                        'description' => __( 'Minimum quantity of the products that match the given product or category restrictions. If no product or category restrictions are specified, the total number of products is used.', 'wt-smart-coupons-for-woocommerce-pro' ),
                        'data_type'   => 'decimal',
                        'desc_tip'    => true,
                    )
                );

                // Maximum quantity of matching products (product/category)
                woocommerce_wp_text_input(
                    array(
                        'id'          => '_wt_max_matching_product_qty',
                        'label'       => __( 'Maximum quantity of matching products', 'wt-smart-coupons-for-woocommerce-pro' ),
                        'placeholder' => __( 'No maximum', 'woocommerce' ),
                        'description' => __( 'Maximum quantity of the products that match the given product or category restrictions. If no product or category restrictions are specified, the total number of products is used.', 'wt-smart-coupons-for-woocommerce-pro' ),
                        'data_type'   => 'decimal',
                        'desc_tip'    => true,
                    )
                );

                // Minimum subtotal of matching products (product/category)
                woocommerce_wp_text_input(
                    array(
                        'id'          => '_wt_min_matching_product_subtotal',
                        'label'       => __( 'Minimum subtotal of matching products', 'wt-smart-coupons-for-woocommerce-pro' ),
                        'placeholder' => __( 'No minimum', 'woocommerce' ),
                        'description' => __( 'Minimum price subtotal of the products that match the given product or category restrictions.', 'wt-smart-coupons-for-woocommerce-pro' ),
                        'data_type'   => 'price',
                        'desc_tip'    => true,
                    )
                );

                // Maximum subtotal of matching products (product/category)
                woocommerce_wp_text_input(
                    array(
                        'id'          => '_wt_max_matching_product_subtotal',
                        'label'       => __( 'Maximum subtotal of matching products', 'wt-smart-coupons-for-woocommerce-pro' ),
                        'placeholder' => __( 'No maximum', 'woocommerce' ),
                        'description' => __( 'Maximum price subtotal of the products that match the given product or category restrictions.', 'wt-smart-coupons-for-woocommerce-pro' ),
                        'data_type'   => 'price',
                        'desc_tip'    => true,
                    )
                );
        }

        /**
         * Add smart Coupon tabs into Coupon page.
         * @since 1.0.0
         */
        public function smart_coupons_views_row( $views = null ) {

            global $typenow;

            if ( $typenow == 'shop_coupon' ) {
                do_action( 'smart_coupons_display_views' );
            }

            return $views;

        }

        /**
         * Create Smart Coupon admin pages
         * @since 1.0.0
         */
        public function wt_smart_coupon_admin_page() {
            add_submenu_page(
                '',
                'WT Smart Coupon - options',
                'WT Smart Coupon',
                'manage_options',
                'wt-smart-coupon',
                array($this,'wt_smart_coupon_admin_page_callback') );
        }

        /**
         * Tab items
         * @since 1.2.1
         */
        function smart_coupon_admin_tab_items ( ) {
            $tab_items = array(
                'coupons' => __('Coupon','wt-smart-coupons-for-woocommerce-pro'),
                'settings' => __('Settings','wt-smart-coupons-for-woocommerce-pro')
            );
    
            return apply_filters( 'wt_smart_coupon_admin_tab_items',  $tab_items );
        }

        /**
         * smart Coupon tabs
         * @since 1.2.1
         */
        function smart_coupon_admin_tabs() {
            $coupon_tabs = $this->smart_coupon_admin_tab_items();
            $active_tab = ( isset( $_GET['tab'] ) )? $_GET['tab'] : '';
            $actual_link = get_admin_url().'admin.php?page=wt-smart-coupon';
            $coupon_page = get_admin_url().'edit.php?post_type=shop_coupon';
    
            $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
            do_action('wt_smart_coupon_before_admin_tabs',$active_tab);
    
            if( is_array( $coupon_tabs ) && !empty( $coupon_tabs ) ) {
                
                echo '<nav class="nav-tab-wrapper smart-coupon-tabs">';
                foreach( $coupon_tabs as $coupon_tab => $tab_name ) {
                    $class = ' ';
                    if( $coupon_tab == $active_tab || ( $current_url == $coupon_page && $coupon_tab == 'coupons'  )  ) {
                        $class = ' nav-tab-active';
                    }
    
                    $tab_link = $actual_link.'&tab='.$coupon_tab;
                    if( $coupon_tab == 'coupons' ) {
                        $tab_link = $coupon_page;
                    }
                    
                    
    
                    ?>
                    <a class="nav-tab <?php echo $class; ?>" href="<?php echo $tab_link; ?>">
                        <?php echo $tab_name ;  ?>
                    </a>
                    <?php
                }
                echo '</nav>';
            }
        }

        /**
         * admin page callback
         * @since 1.0.0
         */
        public function wt_smart_coupon_admin_page_callback() {


            ?>
            <div class="wrap">
                <h1 class="wp-heading-inline"><?php _e('Coupons','woocommerce') ?></h1>

                <a href="<?php echo get_admin_url().'/post-new.php?post_type=shop_coupon'; ?>" class="page-title-action"><?php _e('Add coupon','wt-smart-coupons-for-woocommerce-pro');  ?> </a>
                <hr class="wp-header-end">
            <?php
            $this->smart_coupon_admin_tabs();
            

            if( isset($_GET['tab']) ) {
                $tab = $_GET['tab'];

                $tab_items = array_keys( $this->smart_coupon_admin_tab_items() );
                if( !in_array($tab, $tab_items ) ) {
                    wp_safe_redirect( admin_url('edit.php?post_type=shop_coupon'), 303 );
                    exit;
                
                }
                do_action('wt_smart_coupon_tab_content_'.$tab);
            }
        echo '</div>';
        }

        /**
         * Add other coupon genral options.
         * @since 1.1.0
         */
        function add_new_coupon_options( $coupon_id, $coupon ) {
            
            $wt_make_coupon_available_in_myaccount = get_post_meta($coupon_id , '_wt_make_coupon_available_in_myaccount', true );
            $wc_make_coupon_available = get_post_meta($coupon_id , '_wc_make_coupon_available', true );
            if( $wc_make_coupon_available =='' && $wt_make_coupon_available_in_myaccount  ) {
                $wc_make_coupon_available = 'my_account';
            }
            if( $wc_make_coupon_available ) {
                $wc_make_coupon_available = explode( ',',$wc_make_coupon_available );

            } else {
                $wc_make_coupon_available = array();
            }

                $make_coupon_available = array(
                        'my_account'    => __('My Account','wt-smart-coupons-for-woocommerce-pro'),
                        'checkout'      => __('Checkout','wt-smart-coupons-for-woocommerce-pro'),
                        'cart'          => __('Cart','wt-smart-coupons-for-woocommerce-pro')
                    );
            ?>
            <p class="form-field"><label for="_wc_make_coupon_available"><?php _e('Make coupon available in', 'wt-smart-coupons-for-woocommerce-pro'); ?></label>
            <select id="_wc_make_coupon_available" name="_wc_make_coupon_available[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                    <?php
                    if( !empty( $make_coupon_available) ) {
                        foreach ($make_coupon_available as $section => $name ) {
                            if( !empty( $wc_make_coupon_available )  && in_array($section, $wc_make_coupon_available ) ) {
                                $selected = 'selected = selected';
                            } else {
                                $selected = '';
                            }
    
                            echo '<option value="' . esc_attr($section) . '" '.$selected.'>'
                            . esc_html($name) . '</option>';
                        }
                    }
                    
                    ?>
                </select> 
                <?php echo wc_help_tip( __('Make coupon available in the selected pages', 'wt-smart-coupons-for-woocommerce-pro') ); ?>

            </p>

            <?php
        }
        

        /**
         * Ajax function for populating Coupon on multiselect field.
         * @since 1.1.0
         */
        public function wt_json_search_coupons( ) {


            global $wpdb;
            if ( ! Wt_Smart_Coupon_Security_Helper::check_write_access( 'smart_coupons', 'search-coupons' ) ) {

                wp_die(__('You do not have sufficient permission to perform this operation', 'wt-smart-coupons-for-woocommerce-pro'));

            }
            $term = (string) wc_clean( wp_unslash( $_GET['term'] ) ); 
            $post_id =(int) wc_clean( wp_unslash( $_GET['post_id'] ) );

			if ( empty( $term ) ) {
				die();
			}
            $posts = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}posts
                        WHERE post_type = %s
                            AND post_title LIKE %s
                            AND post_status = %s",
                    'shop_coupon',
                    $wpdb->esc_like( $term ) . '%',
                    'publish'
                )
            );


			$found_products = array();

			$all_discount_types = wc_get_coupon_types();

			if ( $posts ) {
				foreach ( $posts as $post_item ) {
                    if( $post_id== $post_item->ID){
                        continue;
                    }

					$discount_type = get_post_meta( $post_item->ID, 'discount_type', true );

					if ( ! empty( $all_discount_types[ $discount_type ] ) ) {
						$discount_type                       = ' (' . __( 'Type', 'wt-smart-coupons-for-woocommerce-pro' ) . ': ' . $all_discount_types[ $discount_type ] . ')';
                        $found_products[ $post_item->ID ] = $post_item->post_title . $discount_type;
					}
				}
            }

			wp_send_json( $found_products );

		}

       


        public static function coupon_styles() {
    
            $coupon_styles = array(
                'stitched_padding'  => array(
                    'name'      => 'Style 1',
                    'colors'    =>  array( '#2890a8','#ffffff','#ffffff')
                ),
                'stitched_edge'     => array( 
                    'name'      => 'Style 2',
                    'colors'    => array( '#f7f7f7','#e9e9eb','#000000') 
                ),
                'ticket_style'      => array(
                    'name'      => 'Style 3',
                    'colors'    => array( '#fffae6','#fc7400','#000000' )
                ),
                'plane_coupon'      => array(
                    'name'      => 'Style 4',
                    'colors'    => array( '#c8f1c0','#30900c' )
                ),
            );
    
            return apply_filters( 'wt_smart_coupon_styles',$coupon_styles );
        }


        /**
         * get Smartcoupon Settings options
         * @since 1.1.0
         */
        public static function get_options() {
            $smart_coupon_options = apply_filters('wt_smart_coupon_default_options',array(
                'wt_coupon_styles'  => array(
                    'available_coupon' => array(
                        'style' => 'stitched_padding',
                        'color' => array('#2890a8','#ffffff','#ffffff')
                    ),
                    'used_coupon'  => array(
                        'style' => 'stitched_edge',
                        'color' => array('#f7f7f7','#e9e9eb','#0b0b0b')
                    ),
                    'expired_coupon' => array(
                        'style' => 'ticket_style',
                        'color' => array('#fffae6','#fc7400','#fc7400')
                    ),
                ),
                'wt_copon_general_settings' => array(
                    'display_used_coupons_my_acount'        => true,
                    'display_expired_coupons_my_acount'     => false,
                    'wt_coupon_prefix'                      => '',
                    'wt_coupon_suffix'                      => '',
                    'wt_coupon_lenght'                      => 12,
                    'no_of_characters_for_bulk_generate'    => 12,
                    'email_coupon_for_order_status'         => 'completed'
                )
            ));
            $smart_coupon_saved_option = get_option('wt_smart_coupon_options');
            if ( !empty($smart_coupon_saved_option) ) {
                foreach ( $smart_coupon_saved_option as $key => $option ) {
                    $smart_coupon_options[$key] = $option;
                }
            }
            update_option("wt_smart_coupon_options", $smart_coupon_options);
            return $smart_coupon_options;
        }

        public static function get_option ( $option_name ) {
            $smart_coupon_options = self::get_options();

            if( isset( $smart_coupon_options[ $option_name ] ) ) {
                return $smart_coupon_options[ $option_name ] ;
            }

            foreach(  $smart_coupon_options as $smart_coupon_option  ) {
               if( isset( $smart_coupon_option[ $option_name ] ) ){
                   return $smart_coupon_option[ $option_name ];
               }
            }
            return false;
        }

       

        /**
         * Add Coupon Styles into Woocommerce Email Style.
         * @since 1.1.0
         */
        function coupon_inline_style( $style ) {
            $css = '
            .wt-single-coupon{
                background-color: #2890a8 ;
                border: 2px dashed #fff;
                text-align: center;
                margin-bottom: 30px;
                cursor: pointer;
                transition: all .3s;
                position: relative;
                vertical-align: top;
                color: #fff;
                margin-right: 30px;
                float: left;
                align-items: center;
                height: 100px;
                width: 300px;
                padding: 5px;
                display: -ms-flexbox;
                display: -webkit-flex;
                display: flex;
                -ms-flex-item-align: center;
                align-items: center;
              }
              .wt-single-coupon, .wt-single-coupon span, .wt-single-coupon div, .wt-single-coupon code {
                font-family: "Poppins", sans-serif;
              }
              .wt-coupon-amount span.amount {
                font-size: 30px;
                margin-right: 5px;
                font-weight: 500;
              }
              .ticket_style .wt-coupon-amount span.amount{
                margin-right: 0px;
              }
              .wt-coupon-amount {
                float: left;
                width: 100%;
                font-size: 20px;
                line-height: 25px;
                font-weight: 500;
              }
              
              .wt-coupon-code {
                float: left;
                width: 100%;
                font-size: 19px;
                line-height: 25px;
              }
              .wt-single-coupon.used-coupon {
                background-color: #eee ;
                color: #000;
                border: 2px dashed #000;
              }
              .wt-coupon-code code{
                background: none;
                font-size: 15px;
                opacity: 0.7;
              }
              .wt-single-coupon.used-coupon.expired {
                background-color: #f3dfdf;
                color: #a96b6b;
                border: 2px dashed #eccaca;
              }
              .wt-coupon-content {
                padding: 10px 0px;
                float: left;
                width: 100%;
              }
              .ticket_style .wt-coupon-content {
                padding: 10px;
                float: left;
                width: 100%;
                display: -ms-flexbox;
                display: -webkit-flex;
                display: flex;
                -ms-flex-item-align: center;
                align-items: center;
              }
              .wt-single-coupon.stitched_padding {
                box-shadow: 0 0 0 4px #2890a8, 2px 1px 6px 4px rgba(10, 10, 0, 0);
                height: 92px;
                width: 292px;
                margin-top: 4px;
                background-color:#2890a8;
                border: 2px dashed #fff;
              }
              .wt-single-coupon.stitched_edge {
                border: 2px dashed #e9e9eb;
                background: #f7f7f7;
                color: #000;
              }
              .wt-single-coupon.plane_coupon {
                border: none;
                background: #c8f1c0;
                color: #30900c;
              }
              .wt-single-coupon.ticket_style{
                background: #fffae6;
                border: 1px dotted #fc7400;
                color: #000;
              }
              .wt-single-coupon.ticket_style .discount_type{
                font-weight: 500;
                font-size: 22px;
              }
              .wt-single-coupon.ticket_style .wt-coupon-amount {
                color: #fc7400;
                padding-top: 12px;
                padding-right: 10px;
                border-right: 1px dotted;
                padding-bottom: 12px;
              }
              .ticket_style .wt-coupon-amount {
                float: left;
                width: auto;
              }
              .ticket_style .wt-coupon-code {
                float: left;
                padding-left: 10px;
                text-align: left;
              }
              .ticket_style .wt-coupon-code span {
                float: left;
                width: 100%;
              }
            .coupon-desc-wrapper:hover .coupon-desc {
                display: block;
            }
            .wt-single-coupon i.info {
                position: absolute;
                top: 6px;
                right: 10px;
                font-size: 13px;
                font-weight: bold;
                border-radius: 100%;
                width: 20px;
                height: 20px;
                background: #fff;
                text-shadow: none;
                color: #2890a8;
                font-style: normal;
                cursor: pointer;
            }
            .wt_gift_coupon_preview_caption {
                float: left;
                width: 100%;
                padding: 20px 0px;
                text-align: center;
                color: #fff;
              }
              .wt_gift_coupon_preview_image img{
                float: left;
                width: 100%;
                height: auto;
              }
              
              .wt_coupon-code-block {
                float: left;
                width: 100%;
                background: #ffffff;
                padding: 20px 0px;
              }
              
              .wt_coupon-code-block .coupon-code {
                float: left;
                text-align: left;
                background: #0e0b0d;
                padding: 10px;
                color: #fff;
                border-radius: 6px;
                margin-left:20px;
              }
              
              .wt_coupon-code-block .coupon_price {
                float: right;
                text-align: right;
                font-size: 32px;
                font-weight: 700;
                color: #0e0b0d;
                padding: 0px;
                line-height: 36px;
                margin-right:20px;
              }
              
              .coupon-message-block {
                float: left;
                width: 100%;
                color: #fff;
                padding: 20px 0px;
              }
              
              .coupon-message-block .coupon-message {
                float: left;
                text-align: left;
                margin-left:20px;
              }
              
              .coupon-message-block .coupon-from {
                float: right;
                text-align: right;
                margin-right:20px;
              }
              .wt_store_credit_email_wrapper{
                  margin:0 auto;
                  max-width:600px;
                  background:#ffffff;
              }
            
            ';

            $store_credit_templates = Wt_Smart_Coupon_Customisable_Gift_Card::get_tempate_images();
            $store_credit_template_css = '';
            foreach( $store_credit_templates  as $template => $template_details ) {
                $store_credit_template_css .= '
                .wt_gift_coupon_preview_caption.'.$template.'{
                    background-color:'.$template_details['top_bg_color'].'
                }
                .coupon-message-block.'.$template.'{
                    background-color:'.$template_details['bottom_bg_color'].'
                }
                ';
            }
            
            return $style.$css.$store_credit_template_css;
        }


        /**
         * Coupon send order status
         * @since 1.1.0
         */

        public static function success_order_statuses() {
            $order_statuses = array(
                'completed'=>__('Completed','wt-smart-coupons-for-woocommerce-pro'),
                'processing'=>__('Processing','wt-smart-coupons-for-woocommerce-pro'),
            );

            return apply_filters( 'wt_coupon_success_order_statuses', $order_statuses  );
        }

        /**
         * Get Coupon Properties
         * @since 1.2.0
         */
        public static function wt_get_coupon_properties( $coupon, $key ) {
            switch ( $key ) {
                case 'type':
                    $need_to_get = array( $coupon, 'get_discount_type' );
                    break;
                default:
                    $need_to_get = array( $coupon, 'get_' . $key );
                    break;
            }
        
            return ( is_callable( $need_to_get ) ? call_user_func( $need_to_get ) : $coupon->{ $key } );
        }


        /**
         * Genrate a random Coupon code.
         * @since 1.0.0
         * moved into admin common function on 1.2.0
         */
        public static function generate_random_coupon( $prefix,$suffix,$length = 12 ) {
            global $wpdb;
            $random_coupon = '';
            $charset       = apply_filters( 'wt_allowed_characters_for_random_coupon', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' );
            $count         = strlen( $charset );
            
            while ( $length-- ) :
                $random_coupon .= $charset[ mt_rand( 0, $count-1 ) ];
            endwhile;
        
            // $random_coupon = implode( '-', str_split( strtoupper( $random_coupon ), 4 ) );
            $coupon_code = $prefix.$random_coupon.$suffix;
            $query =  "SELECT ID FROM $wpdb->posts WHERE post_type ='shop_coupon' AND post_status = 'publish' AND post_title = %s ";
            while( $wpdb->get_var( $wpdb->prepare($query,$coupon_code ) ) ) {
            return generate_random_coupon( $prefix,$suffix );
            }
        
            return $coupon_code;
        
        }


        /**
         * Clone the coupon 
         * @since 1.2.6
         */
        public static function clone_coupon( $coupon,$prefix='',$suffix='',$coupon_length='' ) {
            global $wpdb;
            $coupon_obj = get_post($coupon);

            if (isset( $coupon_obj ) && $coupon_obj != null ) {
                
                $general_settings_options = Wt_Smart_Coupon_Admin::get_option('wt_copon_general_settings');
                

                if( $prefix == '') {
                    $prefix         = isset( $general_settings_options['wt_coupon_prefix'] )? $general_settings_options['wt_coupon_prefix'] : '';
                }
                if( $suffix == '') {
                    $suffix         = isset( $general_settings_options['wt_coupon_suffix'] )? $general_settings_options['wt_coupon_suffix'] : '';
                }
                if( $coupon_length == '') {
                    $coupon_length  = isset( $general_settings_options['wt_coupon_length'] )? $general_settings_options['wt_coupon_length'] : 12 ;
                }

                $coupon_title = Wt_Smart_Coupon_Admin::generate_random_coupon($prefix,$suffix,$coupon_length);
               

                $args = json_decode(json_encode($coupon_obj), true);
                $args['post_title'] = $coupon_title;
                $should_change_fields = array('ID','post_date','post_date_gmt','post_name','post_modified','post_modified_gmt','guid','comment_count');
                foreach( $should_change_fields as $field ) {
                    unset( $args[ $field ] );
                }

                $new_post_id = wp_insert_post( $args );
                $new_coupon  = new WC_Coupon($new_post_id );

                $taxonomies = get_object_taxonomies($coupon_obj->post_type); 
                foreach ($taxonomies as $taxonomy) {
                    $post_terms = wp_get_object_terms($coupon, $taxonomy, array('fields' => 'slugs'));
                    wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
                }

                $post_meta_data = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$coupon");
                if (count($post_meta_data) != 0) {

                    $meta_no_need_to_clone = apply_filters( 'wt_smart_coupon_meta_no_need_to_clone',  array('_wp_old_slug','wt_credit_history','_wt_smart_coupon_initial_credit') );
                    foreach ($post_meta_data as $meta_info) {
                        $meta_key = $meta_info->meta_key;

                        if ( ! in_array( $meta_key,$meta_no_need_to_clone ) ) {
                            $meta_value = addslashes($meta_info->meta_value);
                            update_post_meta( $new_post_id, $meta_key, $meta_value );
                        }
                    }
                    /**
                     * Special case for category meta
                     */
                    $categories = get_post_meta( $coupon, 'product_categories', true );
                    update_post_meta( $new_post_id, 'product_categories', $categories );
                    $new_coupon->save();
                }

                return $new_post_id;
            }

            return false;
        }


        /**
         * helper function for getting formatted price
         * @since 1.2.9
         */
        public static function get_formatted_price( $amount ) {
            $currency = get_woocommerce_currency_symbol();
            $currentcy_positon = get_option('woocommerce_currency_pos');
    
            switch( $currentcy_positon ) {
                case 'left' : 
                    return $currency.$amount;
                case 'left_space' : 
                    return $currency.' '.$amount;
                case 'right_space' : 
                    return $amount.' '.$currency;
                default  : 
                    return $amount.$currency;
            }
        }
        /**
         * Callback for changing user role in Webtoffee security helper
         * @since 1.3.0
         */
        public static function wt_sc_alter_user_roles(  ) {
            return array('manage_woocommerce');
        }

    }
}