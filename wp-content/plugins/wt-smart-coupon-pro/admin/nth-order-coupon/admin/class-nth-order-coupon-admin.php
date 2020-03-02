<?php

if (!defined('WPINC')) {
    die;
}
/**
 * Add coupon for nth Order
 *
 * @link       http://www.webtoffee.com
 * @since      1.2.4
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/nth-order-coupon
 */


if( !class_exists( 'Wt_Smart_Coupon_nth_Order_Couopon') ) {
    class Wt_Smart_Coupon_nth_Order_Couopon {

        public function __construct() {
            add_filter('woocommerce_coupon_data_tabs', array($this, 'add_nth_coupon_tab'), 22, 1);
            add_action('woocommerce_coupon_data_panels',array($this,'nth_coupon_tab_content'), 10, 1);
			add_action('woocommerce_process_shop_coupon_meta', array($this, 'process_shop_coupon_meta_nth_product'), 10, 2);
            add_action('woocommerce_coupon_is_valid',array($this,'valiate_nth_order_coupon'),16,2);
            add_action('woocommerce_thankyou', array($this,'check_nth_coupon_already_awarded'), 10, 1);


        }

        /**
         * Add new tab into woocmmerce coupon meta
         * @since 1.2.8
         */
        function add_nth_coupon_tab( $tabs ) {
            $tabs['wt_nth_coupon_tab'] = array(
                'label'  => __( 'Purchase History', 'wt-smart-coupons-for-woocommerce-pro' ),
                'target' => 'wt_nth_order_coupon',
                'class'  => '',
            );

            return $tabs;
        }
        /**
         * Add nth coupon tab content 
         * @since 1.2.8
         */
        function nth_coupon_tab_content( $post_id ) {
            ?>
            <div id="wt_nth_order_coupon" class="panel woocommerce_options_panel">
                <div class="options_group">
                    <?php
                        $number_of_orders = get_post_meta( $post_id, 'wt_nth_order_no_of_orders', true );
                    ?>
                    <div class="form-field">
                        <div class="no_of_orders">
                        
                            <label><?php _e( 'Number of orders', 'wt-smart-coupons-for-woocommerce-pro' ); ?></label>

                            <?php
                                $nth_coupon_no_of_coupon_condition = get_post_meta( $post_id, 'nth_coupon_no_of_coupon_condition', true );
                                if( $nth_coupon_no_of_coupon_condition =='equals' ) {
                                    $equals = 'selected';
                                    $greater_or_equal = '';
                                } elseif( $nth_coupon_no_of_coupon_condition =='greater_or_equal' ) {
                                    $greater_or_equal = 'selected';
                                    $equals = '';
                                } else {
                                    $greater_or_equal = '';
                                    $equals = '';
                                }
                            ?>
                            
                                    <select id="nth_coupon_no_of_coupon_condition" name="nth_coupon_no_of_coupon_condition" style="width: 190px"  class="wc-enhanced-select" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                                        <option vaue="please_select"><?php _e('- Select -','wt-smart-coupons-for-woocommerce-pro'); ?></option>
                                        <option value="equals"  <?php echo $equals; ?>  > <?php _e('equals','wt-smart-coupons-for-woocommerce-pro'); ?></option>
                                        <option value="greater_or_equal" <?php echo $greater_or_equal; ?> > <?php _e('greater than or equal to','wt-smart-coupons-for-woocommerce-pro'); ?> </option>
                                    </select>
                                    <?php //echo wc_help_tip( __(' The number of past orders on which the coupon is to be validated.','wt-smart-coupons-for-woocommerce-pro') ); ?>
                            
                            </div>
                            <div class="order_number_condition">
                                <input type="number" step="1" min="1" name="wt_nth_order_no_of_orders" value="<?php echo ( '' !== $number_of_orders ) ? esc_attr( $number_of_orders ) : '1'; ?>" placeholder="<?php echo esc_attr__( '', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" style="width: 5em;"> 
                                <?php echo wc_help_tip( __( 'The number of past orders on which the coupon is to be validated.', 'wt-smart-coupons-for-woocommerce-pro' ) ); ?>
                        
                            </div>
                        
                       
                     </div>
                    <?php
                        $coupon_statuses = wc_get_order_statuses();
                        $status_selected = get_post_meta( $post_id, 'wt_order_Status_need_to_count', true );
        
                    ?>

                    <p class="form-field">
                        <label><?php _e('Order status', 'wt-smart-coupons-for-woocommerce-pro'); ?></label>
                        <select id="wt_order_Status_need_to_count" name="wt_order_Status_need_to_count[]" multiple="multiple" style="width: 300px"  class="wc-enhanced-select" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                            <?php 
                                foreach( $coupon_statuses  as $coupon_status => $status_text ) {
                                    $selected = '  ';
                                    if( is_array( $status_selected ) && in_array( $coupon_status,$status_selected ) ) {
                                        $selected = ' selected';
                                    }
                                    
                                    echo '<option value="'.$coupon_status.'" '.$selected.'> '.$status_text.'</option>';
                                }
                            ?>
                        </select>
                        <?php echo wc_help_tip( __('The status of all identified orders(as per the number specified) should match the chosen for eligibility.','wt-smart-coupons-for-woocommerce-pro') ); ?>
                    </p>
                    <?php 
                        $wt_nth_order_order_total = get_post_meta( $post_id, 'wt_nth_order_order_total', true );
                    ?>
                    <p class="form-field">
                        <label><?php _e( 'Total amount', 'wt-smart-coupons-for-woocommerce-pro' ); ?></label>
                            <input type="number"  step="0.01" name="wt_nth_order_order_total" value="<?php echo $wt_nth_order_order_total;  ?>" placeholder="<?php echo esc_attr__( 'Order total', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" > 

                            <?php echo wc_help_tip( __( 'The minimum total amount of all identified orders together(as per the number specified) should match the provided for eligibility.', 'wt-smart-coupons-for-woocommerce-pro' ) ); ?>
                    </p>

                    <?php
                        $exclude_if_already_awarded = get_post_meta( $post_id, 'nth_coupon_exclude_already_awarded', true );
                    
                     woocommerce_wp_checkbox(
                        array(
                            'id'          => 'nth_coupon_exclude_already_awarded',
                            'label'       => __( 'Excluded customers already awarded', 'wt-smart-coupons-for-woocommerce-pro' ),
                            'description' =>  __( 'Enabling this option excludes customers who have already been awarded this coupon previously.', 'wt-smart-coupons-for-woocommerce-pro' ),
                            'value'       => wc_bool_to_string( $exclude_if_already_awarded  ),
                            'desc_tip'    => true,
                        )
                    );
                    ?>
                </div>
            </div>

            <?php
        }


        /**
         * Save nth coupon meta values
         * @since 1.2.8
         */
        function process_shop_coupon_meta_nth_product( $post_id, $post ) {
            
            if( isset($_POST['wt_nth_order_no_of_orders']) && $_POST['wt_nth_order_no_of_orders'] != '' ) {
                update_post_meta($post_id, 'wt_nth_order_no_of_orders', $_POST['wt_nth_order_no_of_orders'] );
            } else {
                update_post_meta($post_id, 'wt_nth_order_no_of_orders', '');
            }

            if( isset($_POST['nth_coupon_no_of_coupon_condition']) && $_POST['nth_coupon_no_of_coupon_condition']!='' ) {

                update_post_meta($post_id, 'nth_coupon_no_of_coupon_condition', $_POST['nth_coupon_no_of_coupon_condition'] );
            } else {
                update_post_meta($post_id, 'nth_coupon_no_of_coupon_condition', '');
            }

            if( isset($_POST['wt_order_Status_need_to_count']) && $_POST['wt_order_Status_need_to_count']!='' ) {
                update_post_meta($post_id, 'wt_order_Status_need_to_count', $_POST['wt_order_Status_need_to_count'] );
            } else {
                update_post_meta($post_id, 'wt_order_Status_need_to_count', 'processing');
            }

            if( isset($_POST['wt_nth_order_order_total']) && $_POST['wt_nth_order_order_total']!='' ) {
                update_post_meta($post_id, 'wt_nth_order_order_total', $_POST['wt_nth_order_order_total'] );
            } else {
                update_post_meta($post_id, 'wt_nth_order_order_total', '');
            }

            if( isset($_POST['nth_coupon_exclude_already_awarded']) && $_POST['nth_coupon_exclude_already_awarded']!='' ) {

                update_post_meta($post_id, 'nth_coupon_exclude_already_awarded', $_POST['nth_coupon_exclude_already_awarded'] );
            } else {
                update_post_meta($post_id, 'nth_coupon_exclude_already_awarded', false );
            }


        }

        /**
         * Helper function to get nth coupon values
         * @since 1.2.8
         */
        function get_nt_order_meta( $coupon_id,$meta_key ) {
            if( '' == $meta_key || '' == $coupon_id ) return false;

            return get_post_meta( $coupon_id, $meta_key, true ); 
        }

        /**
         * Helper fucntion to get all the orders of an user with specified status
         * @since 1.2.8
         */

         function get_success_order_details( $user_id, $order_status ) {
            global $wpdb;
            $customer_orders = get_posts( array(
                'numberposts' => -1,
                'meta_key'    => '_customer_user',
                'meta_value'  => $user_id,
                'post_type'   => wc_get_order_types(),
                'post_status' => $order_status,
            ) );
            
            $order_details = array();
            $order_total  = 0;
            foreach( $customer_orders as $order ) {
                $order_obj = wc_get_order( $order->ID );
                $order_details[ $order_obj->get_id() ] = $order_obj->get_total();
                $order_total +=  $order_obj->get_total();
            }

            return array (
                'total'         =>  $order_total,
                'order_details' => $order_details
            );
            
         }


        /**
         * helper function to check is an nth order coupon
        * @since 1.2.8
        */

        public  function is_a_nth_order_coupon( $coupon_id ) {
            if( ! $coupon_id ) {
                return false;
            }
            $no_of_orders = $this->get_nt_order_meta( $coupon_id,'wt_nth_order_no_of_orders');
            if( $no_of_orders ) {
                return true;
            }
            return false;
        }


        /**
         * Validate coupon for nth order coupon
         * @since 1.2.8
         */

         function valiate_nth_order_coupon( $is_valid,$coupon ) {

        //     global $woocommerce;
        //    echo '<pre>'; var_dump($woocommerce->cart); echo '</pre>';
        //     die();
           
            if( ! $is_valid ) {
                return $is_valid;
            }
            $exception_message = '';
            
            

            $coupon_id = $coupon->get_id();
            $no_of_orders = $this->get_nt_order_meta( $coupon_id,'wt_nth_order_no_of_orders');
            $nth_coupon_no_of_coupon_condition  = $this->get_nt_order_meta( $coupon_id,'nth_coupon_no_of_coupon_condition');
            
            $user_id = get_current_user_id();
            if( '' == $no_of_orders || '- Select -' == $nth_coupon_no_of_coupon_condition || 'please_select' == $nth_coupon_no_of_coupon_condition ) {
                return $is_valid;
            }

            if( ! $user_id ) {
                
                throw new Exception( __('Coupon applicable only for customers', 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
                return false;
            }

            $wt_order_Status_need_to_count      = $this->get_nt_order_meta( $coupon_id,'wt_order_Status_need_to_count');
            $wt_nth_order_order_total           = $this->get_nt_order_meta( $coupon_id,'wt_nth_order_order_total');
            $nth_coupon_exclude_already_awarded = $this->get_nt_order_meta( $coupon_id,'nth_coupon_exclude_already_awarded');
            
            $success_order_details              = $this->get_success_order_details( $user_id,$wt_order_Status_need_to_count );
            $nth_order_count_to_check           = count( $success_order_details['order_details'] );
            $no_of_orders_need_to_check_with    = $no_of_orders-1;

            $is_nth_coupon_awarded_to_user      = get_user_meta( $user_id, 'wt_awarded_nth_coupon_'.$coupon_id, true );
            $nth_coupon_condition = ( $nth_coupon_no_of_coupon_condition == 'equals'   )? ( $no_of_orders_need_to_check_with == $nth_order_count_to_check    ) : ( $no_of_orders_need_to_check_with <= $nth_order_count_to_check  ) ;

            if( ( $nth_coupon_no_of_coupon_condition == 'equals'   ) && ( $no_of_orders_need_to_check_with != $nth_order_count_to_check    ) ) {
                throw new Exception( __( sprintf('Coupon is applicable only on %s successful order',$this->number_format($no_of_orders)  ), 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
                return false;   
            }
            
            if(  ( $nth_coupon_no_of_coupon_condition == 'greater_or_equal'   )  && ( $no_of_orders_need_to_check_with > $nth_order_count_to_check  )  ) {
                throw new Exception( __( sprintf('Coupon is applicable only %s successful order onwards',$this->number_format($no_of_orders)  ), 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
                return false;                
            }

            if( ( $wt_nth_order_order_total && $success_order_details['total'] <  $wt_nth_order_order_total  ) ) {
                throw new Exception( __( 'Total order amount of your previous purchases doesn\'t qualify for the coupon', 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
                return false;    
            }

            if(  ( $nth_coupon_exclude_already_awarded &&  $is_nth_coupon_awarded_to_user ) ) {
                throw new Exception( __( 'Coupon redeemed already', 'wt-smart-coupons-for-woocommerce-pro' ), 109 );
                return false;    
            }

            return $is_valid;
         }

         /**
          * helper function for creating number for mar list 1st 2nd 3rd nth etc.
          * @since 1.2.8
          * @param $num - Given nmuber
          */
         function number_format( $num ) {
            if ( ($num / 10) % 10 != 1 )
            {
                switch( $num % 10 )
                {
                    case 1: return $num . 'st';
                    case 2: return $num . 'nd';
                    case 3: return $num . 'rd'; 
                }
            }
            return $num . 'th';
        }

         /**
          * Add user meta details 
          * @since 1.2.8
          */
         function check_nth_coupon_already_awarded( $order ) {
            $order_obj = wc_get_order( $order );
             $user_id = get_current_user_id();
             $coupons = $order_obj->get_coupon_codes( );
             if( is_array( $coupons ) ) {
                 foreach( $coupons as $coupon ) {
                     $coupon_obj = new WC_coupon( $coupon );
                     if( $this->is_a_nth_order_coupon( $coupon_obj->get_id() ) ) {
                         update_user_meta( $order_obj->get_customer_id(), 'wt_awarded_nth_coupon_'.$coupon_obj->get_id(),1 );
                     }
                 }
             }
         }

    }

    $nth_coupon = new Wt_Smart_Coupon_nth_Order_Couopon();
}