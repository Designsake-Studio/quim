<?php

if (!defined('WPINC')) {
    die;
}
/**
 * Signup coupon
 *
 * @link       http://www.webtoffee.com
 * @since      1.2.8
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/signup-coupon
 */

if( ! class_exists ( 'Wt_Smart_Coupon_Signup_Coupon' ) ) {

    class Wt_Smart_Coupon_Signup_Coupon {

        protected $option;
        
        function __construct( ) {
            

        }

        /**
         * Create Action coupon subtab for Signup coupon
         * @since 1.2.8
         * @param $action_coupon_items - The tabs added currentley
         */
        function singup_coupon_tab( $action_coupon_items ) {
            $action_coupon_items['signup_coupon'] = __('Signup Coupon', 'wt-smart-coupons-for-woocommerce-pro');

            return $action_coupon_items;
        }
        /**
         * Add Signup coupon option into smart coupon option array.
         * @since 1.2.8
         * @param $default_options - array() -Default smart coupon options. 
         */
        function singup_coupon_options( $default_options ) {
            if(isset( $default_options['wt_signup_coupon_settings'] )) {
                return $default_options;
            }
            $dignup_coupon_settings = array(
                'enable_signup_coupon'      =>  false,
                'wt_signup_master_coupon'   =>  '',
                'use_master_coupon_as_is'   =>  true,
                'signup_coupon_prefix'      =>  '',
                'signup_coupon_suffix'      =>  '',
                'signup_coupon_length'      =>  12,

            );
            $default_options['wt_signup_coupon_settings'] = $dignup_coupon_settings;
            return $default_options;
        }
        /**
         * Helper function to getsignup coupon options
         * @since 1.2.8
         */
        function get_options() {
            
            if( ! empty( $this->option )) {
                return $this->option;
            } else {
                $this->option = Wt_Smart_Coupon_Admin::get_option('wt_signup_coupon_settings');
                return $this->option;
            }

        }
        /**
         * Update signup coupon options
         * @since 1.2.8
         */
        function update_option( $signup_coupon_options ) {
            if( empty($signup_coupon_options) ) {
                return;
            }
           $smart_coupon_option = get_option( 'wt_smart_coupon_options' );
           $smart_coupon_option['wt_signup_coupon_settings'] = $signup_coupon_options;
           update_option('wt_smart_coupon_options',$smart_coupon_option);
           $this->option = $signup_coupon_options;
        }

        /**
         * Signup coupon setting page content
         * @since 1.2.8
         */
        function signup_page_content() {
            $active_tab = get_transient( 'wt_active_tab_action_coupon' );
            $class = ' ';
            if( ! isset( $active_tab ) || '' == $active_tab || $active_tab == 'signup_coupon' ) {
                $class = 'active';
            }

            ?>

            <div class="wt_sub_tab_content <?php echo $class; ?>" id="signup_coupon" >
                <?php  $this->signup_coupon_settings_form(); ?>
            </div>

            <?php
        }
        
        /**
         * Signup coupon settings form
         * @since 1.2.8
         */
        function signup_coupon_settings_form() {
            // $updated = $this->save_signup_coupon_settings();
            $options  = $this->get_options();

            ?>

                <form name="wt_smart_coupons_signup_coupon_settings_form" method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>" >
                    <?php  wp_nonce_field('wt_smart_coupons_action_coupon_settings'); ?>
                    
                    
                    <table class="form-table">
                        <tbody>
                            <tr  valign="top">
                                <?php 
                                    $enable_signup_coupon =  ( isset( $options['enable_signup_coupon'] ) )? $options['enable_signup_coupon'] : true; 
                                    $checked = '';
                                    if( $enable_signup_coupon ) {
                                        $checked = 'checked = checked';
                                    }
                                ?>
                                <td scope="row" class="titledesc"> 
                                    <?php _e('Enable signup coupon','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                                    <?php echo wc_help_tip( __('Enable the option to create and assign coupons to customers automatically upon signup','wt-smart-coupons-for-woocommerce-pro') ); ?>
                                </td>
                                <td class="forminp forminp-checkbox">
                                    <label><input type="checkbox" id="_wt_enable_signup_coupon" name="_wt_enable_signup_coupon" <?php echo $checked; ?>>                         
                                </td>   
                            </tr>

                            <tr  valign="top">
                                <td scope="row" class="titledesc"> 
                                    <?php _e('Associate a master coupon ','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                                    <?php echo wc_help_tip( __('The signup coupon will be created based on the underlying master coupon. The coupon configuration(discount percentage and other related rules) will be created based on the selected master coupon.','wt-smart-coupons-for-woocommerce-pro') ); ?>
                                </td>
                                <td class="forminp">
                                    <?php
                                        $all_discount_types = wc_get_coupon_types();

                                        $coupon_id = ( isset( $options['wt_signup_master_coupon'] ) )? $options['wt_signup_master_coupon'] : '';
                                    ?>
                                    <select class="wt-coupon-search" style="width:300px;" id="_wt_signup_master_coupon" name="_wt_signup_master_coupon" data-placeholder="<?php echo esc_attr__( 'Search for a coupon&hellip;', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" data-action="wt_json_search_coupons" data-security="<?php echo esc_attr( wp_create_nonce( 'search-coupons' ) ); ?>" >
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
                                    <?php 
                                        $use_master_coupon_as_is =  ( isset( $options['use_master_coupon_as_is'] ) )? $options['use_master_coupon_as_is'] : true; 
                                        $checked = '';
                                        if( $use_master_coupon_as_is ) {
                                            $checked = 'checked = checked';
                                        }
                                    ?>
                                    <td scope="row" class="titledesc "> 
                                        <?php _e('Use master coupon code as is ','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                                    </td>
                                    <td class="forminp forminp-checkbox ">
                                        <label><input type="checkbox" id="_wt_use_master_coupon_as_is" name="_wt_use_master_coupon_as_is" <?php echo $checked; ?>>    
                                        <small><?php echo  __('When enabled the coupon code will be the same as the master coupon code. Upon successful signup the email ids of the corresponding users will be added to \'Allowed emails\' under Usage Restriction section of the master coupon. When unchecked a new coupon code will be generated for every new signup. These coupons will follow the same configuration as the master coupon, the difference being a unique coupon code. The coupon code can be formatted as per the prefix/suffix/length options from below. If not specified it will take the format as per the General Settings.','wt-smart-coupons-for-woocommerce-pro') ; ?></small>
                                    </td>   
                                </tr>
                               
                                <?php
                                    if( $use_master_coupon_as_is ) {
                                        $disabled = 'disabled';
                                        $class = "wt_disabled_form_item";
                                    } else {
                                        $disabled = '';
                                        $class = '';
                                    }
                                ?>

                                <tr  valign="top">
                                    <?php  $coupon_prefix = $options['signup_coupon_prefix']; ?>
                                    <td scope="row" class="titledesc wt_coupon_format <?php echo $class; ?>">
                                        <?php _e('Prefix','wt-smart-coupons-for-woocommerce-pro'); ?>
                                                
                                    </td>
                                    <td class="forminp forminp-text">
                                        <input type="text" id="_wt_signup_coupon_prefix" name="_wt_signup_coupon_prefix"  value="<?php echo $coupon_prefix ; ?>"  <?php echo $disabled; ?> />

                                    </td>
                                </tr>

                                <tr  valign="top">
                                    <?php  $coupon_suffix = $options['signup_coupon_suffix']; ?>
                                    <td scope="row" class="titledesc wt_coupon_format <?php echo $class; ?>">
                                        <?php _e('Suffix','wt-smart-coupons-for-woocommerce-pro'); ?> 
                                    </td>
                                    <td class="forminp forminp-text">
                                        <input type="text" id="_wt_signup_coupon_suffix" name="_wt_signup_coupon_suffix"  value="<?php echo $coupon_suffix ; ?>" <?php echo $disabled; ?> />
                                    </td>

                                </tr>

                                <tr  valign="top">
                                    <?php  $coupon_length = $options['signup_coupon_length']; ?>
                                                
                                    <td scope="row" class="titledesc wt_coupon_format <?php echo $class; ?>">
                                        <?php _e('Length of coupon code','wt-smart-coupons-for-woocommerce-pro'); ?> 
                                    </td>
                                    <td  class="forminp forminp-text">   
                                        <input type="number" id="_wt_signup_coupon_length" name="_wt_signup_coupon_length" min="7"   value="<?php echo $coupon_length ; ?>" <?php echo $disabled; ?>/>
                                    </td>
                                </tr>

                            </tr>


                            

                        </tbody>
                    </table>

                    <?php do_action('wt_smart_coupon_after_signup_coupon_settings_from'); ?>

                    <div class="wt_form_submit">
                        <div class="form-submit">
                            <button id="update_wt_smart_coupon_signup_coupon_settings" name="update_wt_smart_coupon_signup_coupon_settings" type="submit" class="button button-primary button-large"><?php _e( 'Save','wt-smart-coupons-for-woocommerce-pro'); ?></button>
                        </div>
                    </div>
            <?php
        }

        /**
         * Process settings form
         * @since 1.2.8
         */
        function save_signup_coupon_settings() {
            if(isset( $_POST['update_wt_smart_coupon_signup_coupon_settings']) ) {
                set_transient('wt_active_tab_action_coupon','signup_coupon',30);
                if ( ! Wt_Smart_Coupon_Security_Helper::check_write_access( 'smart_coupons', 'wt_smart_coupons_action_coupon_settings' ) ) {
                    wp_die(__('You do not have sufficient permission to perform this operation', 'wt-smart-coupons-for-woocommerce-pro'));
                }
                $signup_coupon_settings = $this->get_options();

                if( isset( $_POST['_wt_enable_signup_coupon'] ) && $_POST['_wt_enable_signup_coupon'] =='on' ) {
                    $signup_coupon_settings['enable_signup_coupon'] = true;
                } else {
                    $signup_coupon_settings['enable_signup_coupon'] = false;
                }
                
                if( isset( $_POST['_wt_signup_master_coupon'] ) ) {
                    $signup_coupon_settings['wt_signup_master_coupon'] = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_signup_master_coupon'], 'int' );
                }

                if( isset( $_POST['_wt_use_master_coupon_as_is'] ) && $_POST['_wt_use_master_coupon_as_is'] =='on' ) {
                    $signup_coupon_settings['use_master_coupon_as_is'] = true;
                } else {
                    $signup_coupon_settings['use_master_coupon_as_is'] = false;
                }
                if( isset( $_POST['_wt_signup_coupon_prefix'] ) ) {
                    $signup_coupon_settings['signup_coupon_prefix'] = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_signup_coupon_prefix'] );
                }
                if( isset( $_POST['_wt_signup_coupon_suffix'] ) ) {
                    $signup_coupon_settings['signup_coupon_suffix'] = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_signup_coupon_suffix'] );
                }
                if( isset( $_POST['_wt_signup_coupon_length'] ) ) {
                    $signup_coupon_settings['signup_coupon_length'] = Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['_wt_signup_coupon_length'], 'int' );
                }
                
                $this->update_option( $signup_coupon_settings );


                return true;
            }

            return false;

        }


        /**
         * Add coupon for newley registered user
         * @since 1.2.8
         * @param $user_id - newley register user ID
         */
        function add_coupon_for_new_user( $user_id ) {
            $options  = $this->get_options();
            
            if( !$user_id || !$options['enable_signup_coupon'] ) {
                return false;
            }
            $user = get_user_by('ID',$user_id);
            $user_email = $user->user_email;
            $coupon_id = $options['wt_signup_master_coupon'];
            if( !$coupon_id ) {
                return false;
            }

            if( isset( $options['use_master_coupon_as_is'] ) && $options['use_master_coupon_as_is'] ) { // no need to create random coupon.
                $coupon_obj = new WC_Coupon( $coupon_id );
                $coupon_code = $coupon_obj->get_code();
                $email_restrictions = $coupon_obj->get_email_restrictions();
                $email_restrictions[] = $user_email;
                $coupon_obj->set_email_restrictions( $email_restrictions );
                $coupon_obj->save();
                $randon_coupon = $coupon_id;
            } else {
                $coupon_prefix = ( isset( $options['signup_coupon_prefix'] ) )? $options['signup_coupon_prefix'] : '';
                $coupon_suffix = ( isset( $options['signup_coupon_suffix'] ) )? $options['signup_coupon_suffix'] : '';
                $coupon_length = ( isset( $options['signup_coupon_lenght'] ) )? $options['signup_coupon_lenght'] : 12;
                $randon_coupon = Wt_Smart_Coupon_Admin::clone_coupon( $coupon_id,$coupon_prefix,$coupon_suffix,$coupon_length );
                if( $randon_coupon ) {
                    $coupon_obj = new WC_Coupon( $randon_coupon );
                    $coupon_code = $coupon_obj->get_code();
                    $coupon_obj->set_email_restrictions(  $user_email );
                    $coupon_obj->save();
                }
            }
            WC()->mailer();
            do_action('wt_signup_coupon_created',$randon_coupon,$user,$coupon_obj);
        }



        function add_signup_coupon_email( $email_classes ) {
            
            require_once( WT_SMARTCOUPON_MAIN_PATH.'admin/signup-coupons/class-wt-smart-coupon-signup-coupon-email.php');
            $email_classes['WT_smart_Coupon_Signup_Coupon_Email'] = new WT_smart_Coupon_Signup_Coupon_Email();
            return $email_classes;
        }
    }

    $signup_coupon = new Wt_Smart_Coupon_Signup_Coupon();
}