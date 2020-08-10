<?php
if (!defined('WPINC')) {
    die;
}
if( ! class_exists ( 'Wt_Smart_Coupon_Store_Credit_Order' ) ) {
    class Wt_Smart_Coupon_Store_Credit_Order {

        protected $apply_before_tax,$store_credit_options;
        
        
        public function __construct( ) {
            $this->store_credit_options = Wt_Smart_Coupon_Admin::get_option('wt_store_credit_settings');
            if( isset( $this->store_credit_options ) && isset( $this->store_credit_options['apply_store_credit_before_tax'] )){
                $this->apply_before_tax = $this->store_credit_options['apply_store_credit_before_tax'];
            }
        }

        
        /**
		 * Whether store credit apply beofore shipping and tax
		 * @since 1.2.0
		 */
		public function apply_before_tax() {
			if ( is_null( $this->apply_before_tax ) ) {
				$this->apply_before_tax = true;
			}
			return $this->apply_before_tax;
        }
        
        /**
         * Get credit used for an order
         * @since 1.2.1
         */

        function get_credit_used_for_order( $order ) {
            if( is_int( $order ) ) {
                $order = wc_get_order( $order );
            }
            if( !is_object( $order) && ! is_a( $order,'WC_Order')  ) {
                return false;
            }
            $credit_amount = get_post_meta( $order->get_id(), 'wt_store_credit_used', true );

            if( is_array( $credit_amount ) || !empty( $credit_amount )  ) {
                return $credit_amount;
            }
            return false;

         }

        /**
         * Get total credit used for an order
        * @since 1.2.1
        */

        function get_total_credi_used_for_an_order( $order ) {
            $credit_used = $this->get_credit_used_for_order( $order );
            $credit =  0;
            if( $credit_used ) {
                foreach( $credit_used as $coupon => $amount  ) {
                    $credit+= $amount;
                }
            }

            return $credit;

        }

        /**
        * Update Credit used for an order
        * @since 1.2.1
        */

        function update_credit_used_for_order( $order, $credit_used ) {
            if( !is_object( $order) || ! is_a( $order,'WC_Order') || ! is_array( $credit_used ) ||  empty( $credit_used )   ) {
                return false;
            }
            
            update_post_meta( $order->get_id(), 'wt_store_credit_used', $credit_used );

        }

        /**
         * Reimburse Credit amount on for an order ( On failed, refund etc. )
         * @since 1.2.1
         */

         function reimburse_credit_value( $order ) {

            if( !is_object( $order) || ! is_a( $order,'WC_Order')  ) {
                return false;
            }

            $credit_used = $credit_amount = $this->get_credit_used_for_order( $order );
            $update = false;
            if( $credit_amount ) {
                foreach( $credit_amount as $coupon_code => $amount ) {

                    $coupon         = new WC_Coupon( $coupon_code );
                    if( ! is_object( $coupon ) || ! is_a( $coupon,'WC_Coupon') ) {
                        continue;
                    }
                    $coupon_id      = $coupon->get_id();
                    $discount_type  = $coupon->get_discount_type();
                    if( !$coupon_id || $discount_type != 'store_credit'  ) {
                        continue;
                    }

                    
                    $current_amount = $coupon->get_amount();
                    $usage_count    = $coupon->get_usage_count();
                    $usage_count    = ( $usage_count > 0 )? $usage_count - 1 : 0 ;
                    $new_amount     = $amount + $current_amount;

                    $coupon->set_usage_count( $usage_count );
                    $coupon->set_amount( $new_amount );
                    $coupon->save();

					$credit_history = get_post_meta( $coupon_id , 'wt_credit_history',true);
                    $credit_history_this_order = array(
						'order'				=>	$order->get_id(),
						'previous_credit' 	=>	$current_amount,
						'updated_credit' 	=>	$new_amount,
                        'credit_used'		=>	'-',
                        'reimbursed'        =>   $amount,
						'comments'			=>  __( 'reimburse credit value' , 'wt-smart-coupons-for-woocommerce-pro')
                    );
                    $time_stamp = current_time( 'timestamp' );
                    $credit_history[ "'".$time_stamp."'" ] = $credit_history_this_order;
                    
                    update_post_meta( $coupon_id, 'wt_credit_history', $credit_history );
                    unset( $credit_used[$coupon_code] );
                    $update = true;
                }
            }
            if( $updated ) {
                $this->update_credit_used_for_order( $order, $credit_used );
            }
        }

        /***
         * get the success statuses for an order ( managing credit coupon )
         * @since 1.2.1
         */
        function wt_success_order_status() {

            $success_status = array(
                'on-hold'       => 'On hold',
                'processing'    => 'Processing',
                'completed'     => 'Completed'
            );

            return apply_filters( 'wt_smart_coupon_success_order_statuses', $success_status );
        }

        /***
         * get the failed statuses for an order ( managing credit coupon )
         * @since 1.2.1
         */
        function wt_failed_order_status() {

            $failed_status = array(
                'refunded'      => 'Refunded',
                'cancelled'     => 'Cancelled',
                'failed'        => 'Failed'

            );

            return apply_filters( 'wt_smart_coupon_failed_order_statuses', $failed_status );
        }

        /**
         * Manage the credit refund on changing otder status.
         * @since 1.2.1
         */
        function manage_credit_coupon_on_order( $order_id, $old_status,$new_status,$order ) {
            
            $success_order_statuses = array_keys( $this->wt_success_order_status() );
            $failed_order_statuses  = array_keys( $this->wt_failed_order_status() );
            if( in_array( $old_status,$success_order_statuses) && in_array( $new_status, $failed_order_statuses ) ) {
                $this->reimburse_credit_value( $order );
            }
        }
        
        /**
         * Add storder credit used details into order item row
         * @since 1.2.1
         */
        public function add_order_item_totals_store_credit_row( $total_rows, $order ) {
            $credit = $this->get_total_credi_used_for_an_order( $order );
           
			if ( 0 < $credit ) {
                $offset = array_search( 'order_total', array_keys( $total_rows ), true );
                
				if ( false === $offset ) {
					$offset = count( $total_rows );
				}
	
				$total_rows = array_merge(
					array_slice( $total_rows, 0, $offset ),
					array(
						'store_credit' => array(
							'label' => __( 'Store Credit Used:', 'wt-smart-coupons-for-woocommerce-pro' ),
							'value' => wc_price( -$credit ),
						),
					),
					array_slice( $total_rows, $offset )
				);
			}
	
			return $total_rows;
        }
        

        /**
         * Add Store credit used in Admin order item dtails.
         * @since 1.2.1
         */
        public function admin_order_store_credit_used_details( $order_id ) {
            $credit = $this->get_total_credi_used_for_an_order( $order_id );
            
            if ( 0 === $credit ) {
                return;
            }
            ?>
            <tr>
                <td class="label"><?php echo esc_html__( 'Store Credit Used:', 'wt-smart-coupons-for-woocommerce-pro' ); ?></td>
                <td width="1%"></td>
                <td class="total"><?php echo wc_price( $credit ); // WPCS: XSS ok. ?></td>
            </tr>
            <?php
        }

        /**
         * Change Store Credit coupon label in cart and checkout.
         * @since 1.2.1
         */
        public function store_credit_cart_total_coupon_label( $label, $coupon ) {
			if ( Wt_Smart_Coupon_Store_Credit::is_store_credit( $coupon ) ) {
				$label = __( 'Store credit:', 'wt-smart-coupons-for-woocommerce-pro' );
			}

			return $label;
		}

        /**
         * Add the store credit used into discount total ( change the total amount text ).
         * @since 1.2.1
         */
		public function get_discount_total( $discount_total, $order ) {
            if ( $this->apply_before_tax()  ) {
                $credit = $this->get_total_credi_used_for_an_order( $order );
    
                if ( 0 < $credit ) {
                    $discount_total -= $credit;
                }
            }
    
            return $discount_total;
        }
    }
}