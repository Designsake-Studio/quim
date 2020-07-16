<?php

if (!defined('WPINC')) {
    die;
}
/**
 * abandonment coupon
 *
 * @link       http://www.webtoffee.com
 * @since      1.2.8
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/cart-abandonment/
 * 
 */

if( ! class_exists ( 'Wt_Smart_Coupon_Cart_Checkout_Abandonment' ) ) {

    class Wt_Smart_Coupon_Cart_Checkout_Abandonment {

        protected $option;
        protected $plugin_db_version;
        
        function __construct( ) {
            $this->plugin_db_version = 1.0;

            


        }
        
        /**
         * Create Abandonment coupon tables
         * @since 1.2.8
         */

        function add_abandonment_coupon_tables( ) {
            global $wpdb;

            
                $table_name = $wpdb->prefix . "wt_abandonment_coupon";
                $charset_collate = '';
                if ( $wpdb->has_cap( 'collation' ) ) {
                    $charset_collate = $wpdb->get_charset_collate();
                }
                $sql = "CREATE TABLE `$table_name` (
                    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                    `user_id` mediumint(9),
                    `time` int(11)  NOT NULL,
                    `cart_info` text COLLATE utf8_unicode_ci NOT NULL,
                    `is_cart_ignored` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
                    `is_cart_recovered` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
                    `session_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
                    PRIMARY KEY  (id)
                ) $charset_collate;";
                
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
                update_option( 'wt_abandonment_coupon_table_version', $this->plugin_db_version );
        }

        /**
         * Check the table for plugin upgrade
         * @since 1.2.8
         */

         function check_for_db_upgrade() {
            $installed_ver = get_option( "wt_abandonment_coupon_table_version" );
            if( $installed_ver !==  $this->plugin_db_version ) {
                $this->add_abandonment_coupon_tables();
            }
         }



        /**
         * Create Action coupon subtab for Abandonment coupon
         * @since 1.2.8
         * @param $action_coupon_items - The tabs added currentley
         */

        function abandonment_coupon_tab( $action_coupon_items ) {
            $action_coupon_items['abandonment_coupon'] =  __('Cart/Checkout Abandonment', 'wt-smart-coupons-for-woocommerce-pro');

            return $action_coupon_items;
        }

        /**
         * Abandonment coupon setting page content
         * @since 1.2.8
         */
        function abandonment_coupon_tab_content() {
            $active_tab = get_transient( 'wt_active_tab_action_coupon' );
            $class = ' ';
            if( isset( $active_tab ) &&  $active_tab == 'abandonment_coupon' ) {
                $class = 'active';
            }
            ?>
             <div class="wt_sub_tab_content <?php echo $class; ?>" id="abandonment_coupon" >
                <?php $this->abandonment_coupon_settings_form();  ?>
            </div>

            <?php
        }

        
        /**
         * Add Abandonment coupon option into smart coupon option array.
         * @since 1.2.8
         * @param $default_options - array() -Default smart coupon options. 
         */
        function abandonment_coupon_coupon_options( $default_options ) {
            
            $abandonment_coupon_settings = array(
                'enable_abandonment_coupon'     =>  true,
                'abandonment_master_coupon'     =>  '',
                'cut_of_time'                   =>  20, // in minutes
                'email_send_after'              =>  60, // in minutes
                'use_master_coupon_as_is'       =>  true,
                'abandonment_coupon_prefix'     =>  '',
                'abandonment_coupon_suffix'     =>  '',
                'abandonment_coupon_length'     =>  12,

            );
            if(isset( $default_options['wt_abandonment_coupon_settings'] )) {
                return array_merge( $default_options,$abandonment_coupon_settings );
            }
            $default_options['wt_abandonment_coupon_settings'] = $abandonment_coupon_settings;
            return $default_options;
        }
        /**
         * Helper function to get abandonment coupon options
         * @since 1.2.8
         */
        function get_options() {
            
            if( ! empty( $this->option )) {
                return $this->option;
            } else {
                $this->option = Wt_Smart_Coupon_Admin::get_option('wt_abandonment_coupon_settings');
                return $this->option;
            }

        }

        /**
         * Update Abandonment coupon options
         * @since 1.2.8
         */

        function update_option( $abandonment_coupon_settings ) {
            if( empty($abandonment_coupon_settings) ) {
                return;
            }
           $smart_coupon_option = get_option( 'wt_smart_coupon_options' );
           $smart_coupon_option['wt_abandonment_coupon_settings'] = $abandonment_coupon_settings;
           update_option('wt_smart_coupon_options',$smart_coupon_option);
           $this->option = $abandonment_coupon_settings;
        }

        /**
         * Abandonment coupon settings form
         * @since 1.2.8
         */
        function abandonment_coupon_settings_form() {
            // $updated = $this->save_abandonment__coupon_settings();
            $options  = $this->get_options();

            ?>

                <form name="wt_smart_coupons_abandonment_coupon_settings" method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"].'/test'); ?>" >
                    <?php wp_nonce_field('wt_smart_coupons_action_coupon_settings'); ?>
                    
                    
                    <table class="form-table">
                        <tbody>
                            <tr  valign="top">
                                <?php 
                                    $enable_abandonment_coupon =  ( isset( $options['enable_abandonment_coupon'] ) )? $options['enable_abandonment_coupon'] : true; 
                                    $checked = '';
                                    if( $enable_abandonment_coupon ) {
                                        $checked = 'checked = checked';
                                    }
                                ?>
                                <td scope="row" class="titledesc"> 
                                    <?php _e('Enable abandonment coupon','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                                    <?php echo wc_help_tip( __('Enable the option to create and assign coupons to customers automatically upon cart/checkout abandonment.','wt-smart-coupons-for-woocommerce-pro') ); ?>
                                </td>
                                <td class="forminp forminp-checkbox">
                                    <label><input type="checkbox" id="_wt_enable_abandonment_coupon" name="_wt_enable_abandonment_coupon" <?php echo $checked; ?>>                         
                                </td>   
                            </tr>

                            <tr  valign="top">
                                <td scope="row" class="titledesc"> 
                                    <?php _e('Associate a master coupon','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                                    <?php echo wc_help_tip( __('The abandonment coupon will be created based on the underlying master coupon. The coupon configuration(discount percentage and other related rules) will be created based on the selected master coupon.','wt-smart-coupons-for-woocommerce-pro') ); ?>
                                </td>
                                <td class="forminp">
                                    <?php
                                        $all_discount_types = wc_get_coupon_types();

                                        $coupon_id = ( isset( $options['wt_abandonment_master_coupon'] ) )? $options['wt_abandonment_master_coupon'] : '';
                                    ?>
                                    <select class="wt-coupon-search" style="width:300px;" id="_wt_abandonment_master_coupon" name="_wt_abandonment_master_coupon" data-placeholder="<?php echo esc_attr__( 'Search for a coupon&hellip;', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" data-action="wt_json_search_coupons" data-security="<?php echo esc_attr( wp_create_nonce( 'search-coupons' ) ); ?>" >
                                        <?php
                                        
                                        if( '' != $coupon_id ) {
                                            $coupon_title = get_the_title( $coupon_id );
                                            $coupon = new WC_Coupon( $coupon_id );

                                            $discount_type = $coupon->get_discount_type();

                                            if ( ! empty( $discount_type ) ) {
                                                $discount_type = sprintf( __( ' ( %1$s: %2$s )', 'wt-smart-coupons-for-woocommerce-pro' ), __( 'Type', 'wt-smart-coupons-for-woocommerce-pro' ), $all_discount_types[ $discount_type ] );
                                            }
                                            if( $coupon_title && $discount_type ) {
                                                echo '<option value="' . esc_attr( $coupon_id ) . '"' . selected( true, true, false ) . '>' . esc_html( $coupon_title . $discount_type ) . '</option>';
                                            }
                                        }
                                        
                                        ?>

                                    </select>                         
                                </td>  

                                

                                

                            <tr  valign="top">
                                <?php  $cut_of_time = ( isset( $options['cut_of_time'] ))? $options['cut_of_time'] : 20; ?>
                                <td scope="row" class="titledesc">
                                    <?php _e('Idle time','wt-smart-coupons-for-woocommerce-pro'); ?>
                                    <?php echo wc_help_tip( __('Minimum time(in mins) that the item/s should remain in cart for the customer to be eligible for the coupon.','wt-smart-coupons-for-woocommerce-pro') ); ?>
                                </td>
                                <td class="forminp forminp-text">
                                    <input type="text" id="_wt_abandonment_coupon_cut_of_time" name="_wt_abandonment_coupon_cut_of_time"  value="<?php echo $cut_of_time ; ?>" />

                                </td>
                            </tr>

                            <tr  valign="top">
                                <?php  $email_send_after = ( isset( $options['email_send_after'] ))? $options['email_send_after'] : 3; ?>
                                <td scope="row" class="titledesc">
                                    <?php _e('Email coupon interval','wt-smart-coupons-for-woocommerce-pro'); ?>
                                    <?php echo wc_help_tip( __('Specify the duration(in mins) after which the coupon will be mailed to the eligible customers.','wt-smart-coupons-for-woocommerce-pro') ); ?>                                            
                                </td>
                                <td class="forminp forminp-text">
                                    <input type="text" id="_wt_abandonment_coupon_email_send_after" name="_wt_abandonment_coupon_email_send_after"  value="<?php echo $email_send_after ; ?>" />
                                </td>
                            </tr>
                            
                            <tr  valign="top">
                                <?php 
                                    $use_master_coupon_as_is =  ( isset( $options['use_master_coupon_as_is'] ) )? $options['use_master_coupon_as_is'] : true; 
                                    $checked = '';
                                    if( $use_master_coupon_as_is ) {
                                        $checked = 'checked = checked';
                                    }
                                ?>
                                <td scope="row" class="titledesc"> 
                                    <?php _e('Use master coupon code as is','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                                </td>
                                <td class="forminp forminp-checkbox ">
                                    <label><input type="checkbox" id="_wt_use_master_coupon_as_is_abandonment" name="_wt_use_master_coupon_as_is_abandonment" <?php echo $checked; ?>>         
                                    <small><?php _e('When enabled the coupon code will be the same as the master coupon code. The email ids of the eligible customers will be added to \'Allowed emails\' under Usage Restriction section of the master coupon. When unchecked a new coupon code will be generated for every eligible customer. These coupons will follow the same configuration as the master coupon, the difference being a unique coupon code. The coupon code can be formatted as per the prefix/suffix/length options from below. If not specified it will take the format as per the General Settings.','wt-smart-coupons-for-woocommerce-pro') ; ?></small>
                                </td>   
                            </tr>


                                <?php
                                    if( $use_master_coupon_as_is ) {
                                        $disabled   = 'disabled';
                                        $class      = "wt_disabled_form_item";
                                    } else {
                                        $disabled = '';
                                        $class = '';
                                    }
                                ?>

                                <tr  valign="top">
                                    <?php  $coupon_prefix = ( isset( $options['abandonment_coupon_prefix'] ) ) ? $options['abandonment_coupon_prefix'] :'' ; ?>
                                    <td scope="row" class="titledesc wt_coupon_format <?php echo $class; ?>">
                                        <?php _e('Prefix','wt-smart-coupons-for-woocommerce-pro'); ?>
                                                
                                    </td>
                                    <td class="forminp forminp-text">
                                        <input type="text" id="_wt_abandonment_coupon_prefix" name="_wt_abandonment_coupon_prefix"  value="<?php echo $coupon_prefix ; ?>" <?php echo $disabled; ?> />

                                    </td>
                                </tr>

                                <tr  valign="top">
                                    <?php  $coupon_suffix = ( isset( $options['abandonment_coupon_suffix'] ) )? $options['abandonment_coupon_suffix'] : '' ; ?>
                                    <td scope="row" class="titledesc wt_coupon_format <?php echo $class; ?>">
                                        <?php _e('Suffix','wt-smart-coupons-for-woocommerce-pro'); ?> 
                                    </td>
                                    <td class="forminp forminp-text">
                                        <input type="text" id="_wt_abandonment_coupon_suffix" name="_wt_abandonment_coupon_suffix"  value="<?php echo $coupon_suffix ; ?>"  <?php echo $disabled; ?> />
                                    </td>

                                </tr>

                                <tr  valign="top">
                                    <?php  $coupon_length = ( isset( $options['abandonment_coupon_length'] ) )? $options['abandonment_coupon_length'] : ''; ?>
                                                
                                    <td scope="row" class="titledesc wt_coupon_format <?php echo $class; ?>">
                                        <?php _e('Length of coupon code','wt-smart-coupons-for-woocommerce-pro'); ?> 
                                    </td>
                                    <td  class="forminp forminp-text">   
                                        <input type="number" id="_wt_abandonment_coupon_length" name="_wt_abandonment_coupon_length" min="7"   value="<?php echo $coupon_length ; ?>"  <?php echo $disabled; ?> />
                                    </td>
                                </tr>

                            </tr>

                        </tbody>
                    </table>

                    <?php do_action('wt_smart_coupon_after_abandonment_coupon_settings_from'); ?>

                    <div class="wt_form_submit">
                        <div class="form-submit">
                            <button id="update_wt_smartabandonment_coupon_settings" name="update_wt_smartabandonment_coupon_settings" type="submit" class="button button-primary button-large"><?php _e( 'Save','wt-smart-coupons-for-woocommerce-pro'); ?></button>
                        </div>
                    </div>
                </form>
            <?php
        }

        /**
         * Process settings form
         * @since 1.2.8
         */
        function save_abandonment__coupon_settings(){
            if(isset( $_POST['update_wt_smartabandonment_coupon_settings']) ) {
                if ( ! Wt_Smart_Coupon_Security_Helper::check_write_access( 'smart_coupons', 'wt_smart_coupons_action_coupon_settings' ) ) {
                    wp_die(__('You do not have sufficient permission to perform this operation', 'wt-smart-coupons-for-woocommerce-pro'));
                }
                set_transient('wt_active_tab_action_coupon','abandonment_coupon',30);
                $abandonment_coupon_settings = $this->get_options();


                if( isset( $_POST['_wt_enable_abandonment_coupon'] ) && $_POST['_wt_enable_abandonment_coupon'] =='on' ) {
                    $abandonment_coupon_settings['enable_abandonment_coupon'] = true;
                } else {
                    $abandonment_coupon_settings['enable_abandonment_coupon'] = false;
                }

                if( isset( $_POST['_wt_abandonment_coupon_cut_of_time'] ) ) {
                    $abandonment_coupon_settings['cut_of_time'] = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['_wt_abandonment_coupon_cut_of_time'], 'int');
                }

                if( isset( $_POST['_wt_abandonment_coupon_email_send_after']  ) ) {
                    $abandonment_coupon_settings['email_send_after'] =   Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_abandonment_coupon_email_send_after'], 'int' );
                }

                if( isset( $_POST['_wt_abandonment_master_coupon'] ) ) {
                    $abandonment_coupon_settings['wt_abandonment_master_coupon'] = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_abandonment_master_coupon'], 'int' );
                }

                if( isset( $_POST['_wt_use_master_coupon_as_is_abandonment'] ) && $_POST['_wt_use_master_coupon_as_is_abandonment'] =='on' ) {
                    $abandonment_coupon_settings['use_master_coupon_as_is'] = true;
                } else {
                    $abandonment_coupon_settings['use_master_coupon_as_is'] = false;
                }


                if( isset( $_POST['_wt_abandonment_coupon_prefix'] ) ) {
                    $abandonment_coupon_settings['abandonment_coupon_prefix'] = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_abandonment_coupon_prefix'] );
                }

                if( isset( $_POST['_wt_abandonment_coupon_suffix'] ) ) {
                    $abandonment_coupon_settings['abandonment_coupon_suffix'] = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_abandonment_coupon_suffix'] );
                }
                if( isset( $_POST['_wt_abandonment_coupon_length'] ) ) {
                    $abandonment_coupon_settings['abandonment_coupon_length'] = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_abandonment_coupon_length'], 'int' );
                }
                
                
                $this->update_option( $abandonment_coupon_settings  );


                return true;
            }

            return false;
        }

        /**
         * insert abandoment data into table
         * @since 1.2.8
         */
        function wt_insert_abandoment_data() {

            global $wpdb,$woocommerce;
            $coupon_options = $this->get_options();
            if( ! isset( $coupon_options['enable_abandonment_coupon'] ) || ! $coupon_options['enable_abandonment_coupon'] ) {
                return false;
            }

            $current_time                    = current_time( 'timestamp' );
            $abandoment_cart_options        = $this->get_options();
            $cart_ignored                    = 0;
            $recovered_cart                  = 0;

            $cut_off_time = $abandoment_cart_options['cut_of_time']; 

            if ( isset( $cut_off_time ) ) {
                $cart_cut_off_time = intval( $cut_off_time ) * 60;
            }

            $compare_time = $current_time - $cart_cut_off_time;


            if( is_user_logged_in() ) {
                $user_id = get_current_user_id();                
                $cart_data = array();
                $cart_data['cart'] = WC()->session->cart;
                $cart_data_str = json_encode( $cart_data );
                $table_name = $wpdb->prefix . "wt_abandonment_coupon";


                $user_abandoment_data = $this->get_abandoment_data_by_user( $user_id );

                if ( ! $user_abandoment_data || ! is_object( $user_abandoment_data )  ) {
                    $inserted = $this->insert_abandoment_data( $cart_data_str, 0,$user_id );
                } elseif( $user_abandoment_data->time && $compare_time > $user_abandoment_data->time  )  {
                    //  check cart item data updated.
                    if( $this->check_is_cart_item_updated( $user_id, $user_abandoment_data->cart_info ) ) {
                        $data = array(
                            'is_cart_ignored' => 1
                        );
    
                        $where = array(
                            'id' => $user_abandoment_data->id
                        );
                        $wpdb->update( $table_name,$data,$where );
                        $user = get_user_by('ID',$user_id);
                        $arguments = array(
                            'user_id'       => (int) $user_id,
                            'abandoment_id' => (int) $user_abandoment_data->id,
                            'email'         => $user->user_email
                        );
                        as_unschedule_action( 'wt_check_and_create_abandonment_item', $arguments,'wt-smart-coupon-abandonment-cart' );
    
                        $inserted = $this->insert_abandoment_data( $cart_data_str, 0,$user_id );

                    } else {

                        // no need to update the cart item.
                    }
                    

                } else {  // update the cart item data
                    $data = array(
                        'cart_info' => $cart_data_str 
                    );

                    $where = array(
                        'id' => $user_abandoment_data->id
                    );
                    $wpdb->update( $table_name,$data,$where );

                }
            }

        }
        /**
         * Get abandoment data of a user
         * @since 1.2.8
         * @param $user_id - user id to featch data
         */
        function get_abandoment_data_by_user(  $user_id = '') {
            global $wpdb;
            if( ! $user_id ) {
                $user_id = get_current_user_id();
            }
            if( ! $user_id ) {
                return false;
            }
            $table_name = $wpdb->prefix . "wt_abandonment_coupon";
            return $wpdb->get_row( $wpdb->prepare  (  "SELECT * FROM {$table_name}  WHERE user_id = %d AND is_cart_ignored = %s AND is_cart_recovered  = %s " ,$user_id,0,0 ) );
        }

        /**
         * Insert Abandoment Data into table
         * @since 1.2.8
         * @param $cart_data - 
         * @param $is_cart_ignored - 
         * @param $user_id - 
         * @param $cart_session_id - 
         */
        function insert_abandoment_data( $cart_data,$is_cart_ignored = 0 ,$user_id = '',$cart_session_id='' ) {
            global $wpdb;
            if( !$user_id && ! $cart_session_id ) {
                return false;
            }
            if( !$user_id ) {
                $user_id = 0;
            }

            $current_time   = strtotime( 'now' );

            $data = array(
                'user_id'           => $user_id,
                'time'              => $current_time,
                'is_cart_ignored'   => $is_cart_ignored,
                'cart_info'         =>  $cart_data
            );
            $table_name = $wpdb->prefix . "wt_abandonment_coupon";
            if( $wpdb->insert( $table_name,$data ) ) {
                
                $abandoment_cart_options = $this->get_options();
                $email_send_on = ( isset( $abandoment_cart_options['email_send_after'] ) )? $abandoment_cart_options['email_send_after'] : 120;
                $cut_of_time = ( isset( $abandoment_cart_options['cut_of_time'] ) )? $abandoment_cart_options['cut_of_time'] : 60;

                $crone_schedule_time = $current_time + ( 60 *  ( absint( $cut_of_time )  +  absint( $email_send_on ) )  ) ;
                $user = get_user_by('ID',$user_id);

                if( ! $is_cart_ignored ) {
                    $arguments = array(
                        'user_id' => $user_id,
                        'abandoment_id' => $wpdb->insert_id,
                        'email' => $user->user_email
                    );
                    as_schedule_single_action( $crone_schedule_time, 'wt_check_and_create_abandonment_item',$arguments,'wt-smart-coupon-abandonment-cart' );
    
                } else {
                    // exception on table insertion.
                }
                
                return $wpdb->insert_id;
            }
            return false;
        }

        /**
         * Check is customer updated the cart
         * @since 1.2.8
         */
        function check_is_cart_item_updated( $user_id, $cart_info ) {
            global $woocommerce;
            
            
            $abd_cart_info = json_decode( $cart_info,true );

            $abd_cart_info = ( isset( $abd_cart_info['cart'] ) ) ? $abd_cart_info['cart'] : ''; 
            if( '' == $abd_cart_info  ) {
                return true;
            }

            $woocommerce_persistent_cart = version_compare( $woocommerce->version, '3.1.0', ">=" ) ? '_woocommerce_persistent_cart_' . get_current_blog_id() : '_woocommerce_persistent_cart' ;
            $current_cart_info   = get_user_meta( $user_id, $woocommerce_persistent_cart, true );
            if( isset($current_cart_info['cart'] ) &&  !empty( $current_cart_info['cart'] )) {
                foreach( $current_cart_info['cart'] as $key => $cart_item ) {
                    if( 
                            !isset( $abd_cart_info[$key]['product_id'] ) || $abd_cart_info[$key]['product_id'] != $cart_item['product_id']
                        ||  !isset( $abd_cart_info[$key]['variation_id'] ) || $abd_cart_info[$key]['variation_id'] != $cart_item['variation_id']
                        ||  !isset( $abd_cart_info[$key]['quantity'] ) || $abd_cart_info[$key]['quantity'] != $cart_item['quantity']
                    ) {
                        return true;
                    }
                }
            }
            return false;
        }

        /**
         * Get abandoment from ID
         * @since 1.2.8
         */
        function get_abandoment_data( $abandoment_id ) {
            global $wpdb;
            $table_name = $wpdb->prefix . "wt_abandonment_coupon";
            return $wpdb->get_row( $wpdb->prepare  (  "SELECT * FROM {$table_name}  WHERE id = %d " ,$abandoment_id  ) );
 
        }

        /**
         * Call back on Run the scheluded action for creating abadoment coupon
         * @since 1.2.8
         */
        function create_abadoment_coupon( $user_id,$abandoment_id,$email ) {
            $coupon_options = $this->get_options();
            if( ! isset( $coupon_options['enable_abandonment_coupon'] ) || ! $coupon_options['enable_abandonment_coupon'] ) {
                die();
            }

            $cart_items = $this->get_abandoment_data( $abandoment_id );

            if( $cart_items->is_cart_ignored || $cart_items->is_cart_recovered	 ) {
            // nothing to do cart ignored or recovered.
                return false;
            }
            // create and send coupon - update status into email send
            $user_id = $cart_items->user_id;
            $user = get_user_by('ID',$user_id);
            $user_email = $user->user_email;

            $master_coupon = ( isset( $coupon_options['wt_abandonment_master_coupon'] ) ) ? $coupon_options['wt_abandonment_master_coupon'] :  false ;
            if( !$master_coupon ) {
                return;
            }

            if ( isset( $coupon_options['use_master_coupon_as_is'] ) && $coupon_options['use_master_coupon_as_is']  ) {
                // no need to create a coupon update allowed email and send the coupon.
                $coupon_obj = new WC_Coupon( $master_coupon );
                $coupon_code = $coupon_obj->get_code();
                $email_restrictions = $coupon_obj->get_email_restrictions();
                $email_restrictions[] = $user_email;
                $coupon_obj->set_email_restrictions( $email_restrictions );
                $coupon_obj->save();
                $randon_coupon = $master_coupon;

            } else {

               
                // Create send the coupon.
                $coupon_prefix = ( isset( $coupon_options['abandonment_coupon_prefix'] ) )? $coupon_options['abandonment_coupon_prefix'] : '';
                $coupon_suffix = ( isset( $coupon_options['abandonment_coupon_suffix'] ) )? $coupon_options['abandonment_coupon_suffix'] : '';
                $coupon_length = ( isset( $coupon_options['abandonment_coupon_length'] ) )? $coupon_options['abandonment_coupon_length'] : 12;
                $randon_coupon = Wt_Smart_Coupon_Admin::clone_coupon( $master_coupon,$coupon_prefix,$coupon_suffix,$coupon_length );
                if( $randon_coupon ) {
                    $coupon_obj = new WC_Coupon( $randon_coupon );
                    $coupon_code = $coupon_obj->get_code();
                    $coupon_obj->set_email_restrictions(  $user_email );
                    $coupon_obj->save();
                }
            }
            add_user_meta($user_id,'wt_send_abandonment_coupon',true);
            
            WC()->mailer();
            do_action( 'wt_abandonment_coupon_created',$randon_coupon,$user,$coupon_obj );
            
        }

        /**
         * Add Abandoment Email Class into woocommerce Email Class
         * @since 1.2.8
         */
        function add_abandonment_coupon_email( $email_classes ) {
            require_once( WT_SMARTCOUPON_MAIN_PATH.'admin/cart-abandonment/class-wt-smart-coupon-abandonment-coupon-email.php');
            $email_classes['WT_smart_Coupon_Abandonment_Coupon_Email'] = new WT_smart_Coupon_Abandonment_Coupon_Email();
            return $email_classes;
        }

        /**
         * check cart is reccovered or customer compled the checkout before sending abd coupon
         * @since 1.2.8
         */
        function check_successfull_order_is_recovered( $order_id, $old_status,$new_status ,$order) {

            $successful_statuses = apply_filters('wt_abandonment_coupon_order_success_statuses',array('completed','Processing') );

            if( !in_array( $new_status,$successful_statuses ) ) {
                // nothing to do
                return;
            }

            $user_id = $order->get_user_id();
            if( !$user_id  ) {
                // guest chekcout will handle later version.
                return false;
            }
            
            $abd_info = $this->get_abandoment_data_by_user( $user_id );
            if( !$abd_info  || ! $abd_info->id ) {
                // the order have no curresponding abd data. nothing to do
                return false;
            }
            $user = get_user_by('ID',$user_id);
            $arguments = array(
                'user_id'       => (int)$user_id,
                'abandoment_id' => (int)$abd_info->id,
                'email'         => $user_email
            );
            // un schedule on sucessfull order.
            as_unschedule_action( 'wt_check_and_create_abandonment_item', $arguments,'wt-smart-coupon-abandonment-cart' );

        }
    }

    $cart_ab = new Wt_Smart_Coupon_Cart_Checkout_Abandonment();
}

