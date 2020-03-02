<?php
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'Wt_Smart_Coupon_Schedule_Store_Credit' ) ) {
    class Wt_Smart_Coupon_Schedule_Store_Credit {

        function __construct() {
        }

        /**
         * Created action scheduler on specified time 
         * @since 1.2.1
         */
        function schedule_send_credit_coupon( $coupon_email_args ) {
            $email_args =  $coupon_email_args;
            unset( $email_args['schedule'] );
            as_schedule_single_action( $coupon_email_args['schedule'], 'wt_send_coupon_email_as_per_schedule',$email_args, 'wt-smart-coupon-store-credit' );
        }


        /**
         * Send Credit coupon on Run action scheduler.
         * @since 1.2.1
         */
        function send_credit_coupon( $send_to,$coupon_ids,$message,$order_id,$template,$from_name ) {
            $credit_email_args = array(
                'send_to'   => $send_to,
                'coupon_id' => $coupon_ids,
                'message'   => $message,
                'template'  => $template,
                'from_name' => $from_name
            );
            WC()->mailer();
            do_action( 'wt_send_store_credit_coupon_to_customer',$credit_email_args );
        }


        /**
         * Send Credit coupon on Run action scheduler.
         * @since 1.2.1
         * for smart coupon verison < 1.2.8
         */
        function send_credit_coupon_old( $send_to,$coupon_ids,$message ) {
            $credit_email_args = array(
                'send_to'   => $send_to,
                'coupon_id' => $coupon_ids,
                'message'   => $message
            );
            WC()->mailer();
            do_action( 'wt_send_store_credit_coupon_to_customer',$credit_email_args );
        }

        /**
         * Create a field on checkout for choosing Scheduld date.
         * @since 1.2.1
         */
        function add_schedule_date_for_storecredit() {
            $enabled_customizing_store_Credit = Wt_Smart_Coupon_Customisable_Gift_Card::is_extended_store_credit_enebaled( );
            if( $enabled_customizing_store_Credit ) {
                global $product;
                $store_credit = new Wt_Smart_Coupon_Store_Credit();
                if( ! $store_credit->is_product_is_store_credit_purchase( $product->get_id() ) ) {
                    return;
                }
            }
           
            echo '<li class="wt_smart_schedule_field">';

            echo '
            <script>
                jQuery(function($){
                    $("#wt_smart_coupon_schedule").datepicker(  {
                        minDate : 0
                        }
                    );
                });
            </script>';

            woocommerce_form_field( 'wt_smart_coupon_schedule', array(
                'type'          => 'text',
                'class'         => array('my-field-class form-row-wide'),
                'id'            => 'wt_smart_coupon_schedule',
                'required'      => false,
                'label'         => '',
                'placeholder'   => __('Gift on this date','wt-smart-coupons-for-woocommerce-pro'),
                ));

            echo '</li>';
                
        }
        
        /**
         * include date picker js on checkout.
         * @since 1.2.1
         */
        function enabling_date_picker() {
            if( is_admin() || ! is_checkout()   ) return;
            wp_enqueue_script( 'jquery-ui-datepicker' );
            
        }

        /**
         * Disable the email of store credit when a schedule exist.
         * @since 1.2.1
         */
        function disable_sending_store_credit_immediately( $enable,$order_id,$coupon_details ) {
            
            $coupon_schedule = $coupon_details['wt_smart_coupon_schedule'];
            if( isset( $coupon_schedule ) && '' != $coupon_schedule ) {
                return false;
            }
            return $enable;

        }

        function disable_sending_store_credit_immediately_old( $enable,$order_id ) {
            $coupon_schedule = get_post_meta( $order_id, 'wt_smart_coupon_schedule', true );
            if( isset( $coupon_schedule ) && '' != $coupon_schedule ) {
                return false;
            }
            return $enable;

        }


         /**
         * Add store credit schedule details into order.
         * @since 1.2.1
         */
        function update_store_credit_schedule_into_order( $order_id, $posted ) {
            if(  isset( $_POST['wt_credit_coupon_to_do'] )   && $_POST['wt_credit_coupon_to_do'] == 'credit_gift_to_a_friend' && isset( $_POST['wt_smart_coupon_schedule'] ) && '' != $_POST['wt_smart_coupon_schedule'] ) {  
                $coupon_schedule = sanitize_text_field( $_POST['wt_smart_coupon_schedule']  );
                update_post_meta( $order_id, 'wt_smart_coupon_schedule',  $coupon_schedule );
                update_post_meta( $order_id, 'wt_smart_coupon_schedule_timestamp',  strtotime( $coupon_schedule ) );

                $coupon_email = isset( $_POST['wt_credit_coupon_send_to'] )? $_POST['wt_credit_coupon_send_to'] : '';
				$coupon_message = isset( $_POST['wt_credit_coupon_send_to_message'] )? $_POST['wt_credit_coupon_send_to_message'] : '';

                $order       = wc_get_order( $order_id );
                $order_items = $order->get_items();
                $credit_coupons = array();
                $coupons_genrated = array();
                foreach ( $order_items as $item_id => $order_item ) {
                    $credit_coupon_generated = $order_item->get_meta( 'wt_credit_coupon_generated' );
                    if( !empty( $credit_coupon_generated ) ) {
                        $credit_coupons  = array_merge( $credit_coupons,$credit_coupon_generated );
                    }
                }
                foreach( $credit_coupons as $coupon_item ) {
                    $coupon_ids[] = $coupon_item['coupon_id'];
                }
                $credit_email_args = array(
                    'send_to'   => $coupon_email,
                    'coupon_id' => $coupon_ids,
                    'message'   => $coupon_message,
                    'schedule'  => strtotime( $coupon_schedule )
                );

                do_action('wt_smart_coupon_schedule_credit',$credit_email_args);
            }
        }
        
        
        /**
         * Update Schedule items into order item.
         * @since 1.2.6
         */
        function add_store_credit_schedule_for_cart_item( $order_id ) {
            
            $template_details = maybe_unserialize( get_post_meta( $order_id, 'wt_credit_coupon_template_details', true ) );
            if( !is_array($template_details ) || empty ( $template_details ) ) {
                return;
            }
            foreach( $template_details as $coupon_template_item ) {
                $coupon_schedule =  $coupon_template_item['wt_smart_coupon_schedule'];
                if( $coupon_schedule  ) {
                    $coupon_email = isset( $coupon_template_item['wt_credit_coupon_send_to'] )? $coupon_template_item['wt_credit_coupon_send_to'] : '';
                    $coupon_message = isset( $coupon_template_item['wt_credit_coupon_send_to_message'] )? $coupon_template_item['wt_credit_coupon_send_to_message'] : '';
                    $template       = isset( $coupon_template_item['wt_smart_coupon_template_image'] )? $coupon_template_item['wt_smart_coupon_template_image'] : '';
                    $from_name       = isset( $coupon_template_item['wt_credit_coupon_from'] )? $coupon_template_item['wt_credit_coupon_from'] : '';
                    $credit_email_args = array(
                        'send_to'   => $coupon_email,
                        'coupon_id' => $coupon_template_item['coupon_id'],
                        'message'   => $coupon_message,
                        'order_id'  => $order_id,
                        'template'  => $template,
                        'from_name' => $from_name,
                        'schedule'  => strtotime( $coupon_schedule ),
                    );
        
                    do_action('wt_smart_coupon_schedule_credit',$credit_email_args );
                }
               

            }
            
        }



        


       

    }
}



