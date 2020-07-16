<?php
if (!defined('WPINC')) {
    die;
}
if( ! class_exists ( 'Wt_Smart_Coupon_Store_Credit' ) ) {
    class Wt_Smart_Coupon_Store_Credit {


        protected $discounts = array();
		protected $apply_before_tax;
		protected $total_discounts;
		protected $Loader;
		protected $store_credit_options;




        public function __construct( ) {
			$this->store_credit_options = Wt_Smart_Coupon_Admin::get_option('wt_store_credit_settings');
			if( isset( $this->store_credit_options ) && isset( $this->store_credit_options['apply_store_credit_before_tax'] ) ) {
				$this->apply_before_tax = $this->store_credit_options['apply_store_credit_before_tax'];
			}
		}

		/**
         * Add required Scripts
         * @since 1.2.9
         * updated with seperate js file
         */
        function enqueue_store_credit_public_scripts() {
			wp_enqueue_script('wt-smart-coupon-store-credit', plugin_dir_url(__FILE__) . 'js/wt-smart-coupon-store-credit.js', array('jquery'), WEBTOFFEE_SMARTCOUPON_VERSION, false);
		}
		
		/**
		 * Add required style
		 * @since 1.2.9
		 * Moved into seprate file in 1.2.9
		 */

		 function enqueue_store_credit_public_styles() {
            wp_enqueue_style( 'wt-smart-coupon-store-credit', plugin_dir_url(__FILE__) . 'css/wt-smart-coupon-store-credit.css', array(), WEBTOFFEE_SMARTCOUPON_VERSION, 'all');

		 }

		

		/**
		 *  Validate the Dynamic Store Cedit value.
		 *  @since 1.2.0
		 */
		function validate_store_credit_on_add_to_cart( $passed, $product_id ) {
			$_product = wc_get_product( $product_id );

			if( !empty( $this->store_credit_options ) ) {
				$store_credit_options =  $this->store_credit_options;
			} else {
				$store_credit_options = $this->store_credit_options = Wt_Smart_Coupon_Admin::get_option('wt_store_credit_settings');
			}

			if ( $this->is_product_is_store_credit_purchase( $product_id ) ) {
				$min_price = $store_credit_options['minimum_store_credit_purchase'];
				$max_price = $store_credit_options['maximum_store_credit_purchase'];

				if ( ! isset( $_REQUEST['wt_credit_amount'] ) || '' === $_REQUEST['wt_credit_amount'] ) {
					wc_add_notice( 
						__( 'Store credit value is required!', 'wt-smart-coupons-for-woocommerce-pro' ) , 'error' );
					return false;
				}
				
				$min_price_value = Wt_Smart_Coupon_Admin::get_formatted_price( $min_price );
				$max_price_value = Wt_Smart_Coupon_Admin::get_formatted_price( $max_price );
				

				if ( ( $min_price > 0 &&  $max_price > 0 ) && ( $_REQUEST['wt_credit_amount'] < $min_price ||  $_REQUEST['wt_credit_amount'] > $max_price )  ) {
					wc_add_notice( 
						sprintf(  __( 'The credit value should be in between %s and %s ', 'wt-smart-coupons-for-woocommerce-pro' ) ,$min_price_value,$max_price_value ), 'error' );
					return false;
				}

				if ( $min_price > 0 ) {
					
					if ( $_REQUEST['wt_credit_amount'] < $min_price ) {
						wc_add_notice( 
							sprintf( __(  'The credit value should be greater than %s', 'wt-smart-coupons-for-woocommerce-pro' ) ,$min_price_value ), 'error' );
						return false;
					}
				}
				if (  $max_price > 0 ) {
					if ( isset( $_REQUEST['wt_credit_amount'] ) && $_REQUEST['wt_credit_amount'] > $max_price ) {
						wc_add_notice( 
							sprintf( __(   'The credit value should be less than %s', 'wt-smart-coupons-for-woocommerce-pro' ),$max_price_value ) , 'error' );
						return false;
					}
				}
			}
			return $passed;
		}
		/**
		 * Santize Price Field
		 * @since 1.2.0
		 */
		function sanitize_price( $price ) {
			return filter_var( sanitize_text_field( $price ), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
		}

		/**
		 * Add Store Credit details into Cart item data.
		 * @since 1.2.0
		 */
		function add_store_credit_details_to_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
			if ( $this->is_product_is_store_credit_purchase( $product_id ) &&  isset( $_REQUEST['wt_credit_amount'] ) ) {
				$cart_item_data['wt_credit_amount'] = $this->sanitize_price( $_REQUEST['wt_credit_amount'] );
			}
		
			return $cart_item_data;
		}
		
		function add_open_price_to_cart_item( $cart_item_data, $cart_item_key ) {
			if ( isset( $cart_item_data['wt_credit_amount'] ) ) {
				$cart_item_data['data']->wt_credit_amount = $cart_item_data['wt_credit_amount'];
			}
			return $cart_item_data;
		}


		
		/**
		 * Create new Coupon Type
		 * @since 1.2.0
		 */
        function add_store_credit_discount_type( $discount_types ) {
            $discount_types['store_credit'] = __( 'Store Credit', 'wt-smart-coupons-for-woocommerce-pro' );
			return $discount_types;
        }
		
		/**
		 * Check whether the specified coupon is store credit.
		 * @since 1.2.0
		 */
		public static function is_store_credit( $coupon ) {
            return $coupon->is_type('store_credit');
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
		 * Make the coupon valid for cart
		 * @since 1.2.0
		 */
		function is_valid_for_cart( $valid, $coupon ) {
			if ( self::is_store_credit( $coupon ) ) {
				$valid = true;
			}
			return $valid;
		}

		/**
		 * Make the store credit coupon applied at last
		 * @since 1.2.0
		 */
        public function apply_coupon_last( $coupon_code ) {
			$coupon = new WC_Coupon( $coupon_code );

			if ( self::is_store_credit( $coupon ) ) {
				return;
			}

			$applied_coupons = WC()->cart->get_applied_coupons();

			if ( empty( $applied_coupons ) ) {
				return;
			}

			$codes_to_add_back = array();

			foreach ( $applied_coupons as $applied_coupon_index => $applied_coupon_code ) {
				$applied_coupon = new WC_Coupon( $applied_coupon_code );

				if ( self::is_store_credit( $applied_coupon ) ) {
					WC()->cart->remove_coupon( $applied_coupon_code );
					$codes_to_add_back[] = $applied_coupon_code;
				}
			}

			add_filter( 'woocommerce_coupon_message', '__return_empty_string' );

			foreach ( $codes_to_add_back as $code_to_add_back ) {
				WC()->cart->add_discount( $code_to_add_back );
			}

			remove_filter( 'woocommerce_coupon_message', '__return_empty_string' );
		}

		/**
		 * Get coupon discount if cached.
		 * @since 1.2.0
		 */
		public function get_discounts_for_coupon( $the_coupon ) {
			$coupon_code = ( $the_coupon instanceof WC_Coupon ? Wt_Smart_Coupon_Admin::wt_get_coupon_properties( $the_coupon, 'code' ) : $the_coupon );

			return ( isset( $this->discounts[ $coupon_code ] ) ? $this->discounts[ $coupon_code ] : array() );
		}
		
		/**
		 * Store the calculated discount values into cache.
		 * @since 1.2.0
		 */
        public function set_discount_for_item( $the_coupon, $item_key, $discount ) {
			$coupon_code = ( $the_coupon instanceof WC_Coupon ? Wt_Smart_Coupon_Admin::wt_get_coupon_properties( $the_coupon, 'code' ) : $the_coupon );
			$discounts   = $this->get_discounts_for_coupon( $the_coupon );
			$discounts[ $item_key ] = $discount;
			$this->discounts[ $coupon_code ] = $discounts;
		}
		
		/**
		 *  get discount for specieid item
		 * @since 1.2.0
		 */
		public function get_discount_for_item( $the_coupon, $item_key ) {
			$discounts = $this->get_discounts_for_coupon( $the_coupon );

			if ( isset( $discounts[ $item_key ] ) ) {
				return $discounts[ $item_key ];
			}

			return false;
        }

		/**
		 *  Get the dicounting amount for the store credit coupon
		 * @since 1.2.0
		 */
		public function get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
			
           
            if ( ! self::is_store_credit( $coupon ) || ! $this->apply_before_tax()  ) {
				return $discount;
			}


			if ( version_compare( WC()->version, '3.4', '>=' ) ) {
				return $discounting_amount;
			}

			$cart_item_key = $cart_item['key'];

			if ( false === $cart_item_key ) {
				return $discounting_amount;
			}

			// Checks if the discount was calculated previously.
			$discount = $this->get_discount_for_item( $coupon, $cart_item_key );

			if ( false !== $discount ) {
				return $discount;
			}

			$coupon_code = Wt_Smart_Coupon_Admin::wt_get_coupon_properties( $coupon, 'code' );


			if ( isset( $this->total_discounts[ $coupon_code ] ) ) {
				$total_discount = $this->total_discounts[ $coupon_code ];
				$item_quantity  = ( isset( $cart_item['quantity'] ) ? (int) $cart_item['quantity'] : 1 );

				if ( version_compare( WC()->version, '3.2', '<' ) ) {
					$discounting_amount = ( $discounting_amount * $item_quantity );
				}

				$coupon_amount = Wt_Smart_Coupon_Admin::wt_get_coupon_properties( $coupon, 'amount' );
				$coupon_amount = min( $coupon_amount, $total_discount );

				$discount_percent = ( $discounting_amount / $total_discount );

				$discount = ( (float) $coupon_amount * $discount_percent );
				$discount = ( min( $discount, $discounting_amount ) / $item_quantity );
				$this->set_discount_for_item( $coupon, $cart_item_key, $discount );

				return $discount;
			}

			return $discounting_amount;
		}

		/**
		 * Get coupon Discont array
		 * @since 1.2.0
		 */
		public function get_discounts_array( $discounts, $coupon ) {
			if ( ! self::is_store_credit( $coupon ) || ! $this->apply_before_tax() ) {
				return $discounts;
            }
            $subtotal      = array_sum( $discounts );
            $coupon_amount = Wt_Smart_Coupon_Admin::wt_get_coupon_properties( $coupon, 'amount' );
		
			if( ! $subtotal ) {
				return $discounts;
			}

			foreach ( $discounts as $cart_item_key => $discounting_amount ) {
				// get discount from cache.
                $discount = $this->get_discount_for_item( $coupon, $cart_item_key );

				if ( false === $discount ) {
					$discount_percent = $discounting_amount / $subtotal;

					$discount = wc_add_number_precision( (float) $coupon_amount * $discount_percent );
					$discount = min( $discount, $discounting_amount );

					// Cache the discount.
					$this->set_discount_for_item( $coupon, $cart_item_key, wc_remove_number_precision( $discount ) );
				} else {
					$discount = wc_add_number_precision( $discount );
				}

				$discounts[ $cart_item_key ] = $discount;
			}

			return $discounts;
        }
		
		/**
		 *  Get Coupon Discount total from cart session.
		 *  @since 1.2.0
		 */
		function get_coupon_discount_totals() {
            if ( method_exists( WC()->cart, 'get_coupon_discount_totals' ) ) {
                $coupon_discount_totals = WC()->cart->get_coupon_discount_totals();
            } else {
                $coupon_discount_totals = ( isset( WC()->cart->coupon_discount_amounts ) ? WC()->cart->coupon_discount_amounts : array() );
            }
        
            return $coupon_discount_totals;
        }
        
		/**
		 * Calculate Discount for applying store credit befor taxt and shipping calculation
		 * @since 1.2.0
		 */
        public function update_before_tax() {

			if ( version_compare( WC()->version, '3.4', '>=' ) || ! $this->apply_before_tax() ) {
				return;
			}

			$discounts = $this->get_coupon_discount_totals();

			add_filter( 'woocommerce_coupon_message', '__return_empty_string' );

			foreach ( $discounts as $coupon_code => $discount ) {
				$coupon = new WC_Coupon( $coupon_code );

				if ( ! self::is_store_credit( $coupon ) ) {
					continue;
				}

				$this->total_discounts[ $coupon_code ] = $discount;

				$coupon_discounts = $this->get_discounts_for_coupon( $coupon );

				if ( empty( $coupon_discounts ) ) {
					WC()->cart->remove_coupon( $coupon_code );
					WC()->cart->add_discount( $coupon_code );
					break;
				}
			}

			remove_filter( 'woocommerce_coupon_message', '__return_empty_string' );
        }
		
		
		/**
		 * Update cart object ( Update coupon discount total ).
		 * @since 1.2.0
		 */
		public function update_coupon_discount_total( $coupon, $discount ) {
			$coupon_code = Wt_Smart_Coupon_Admin::wt_get_coupon_properties( $coupon, 'code' );

			$coupon_discount_totals = $this->get_coupon_discount_totals();

			if ( empty( $coupon_discount_totals[ $coupon_code ] ) ) {
				$coupon_discount_totals[ $coupon_code ] = $discount;
			} else {
				$coupon_discount_totals[ $coupon_code ] += $discount;
			}

			if ( method_exists( WC()->cart, 'set_coupon_discount_totals' ) ) {
                WC()->cart->set_coupon_discount_totals( $coupon_discount_totals );
            } else {
                WC()->cart->coupon_discount_amounts = $coupon_discount_totals;
            }
		}
        

		/**
		 * Set cart total ( When apply before tax is disabled ).
		 * @since 1.2.0
		 */
        public function after_calculate_totals( $cart ) {
			if ( $this->apply_before_tax() ) {
				return;
			}

			if ( is_null( $cart ) ) {
                $cart = WC()->cart;
			}
			if ( version_compare( WC()->version, '3.1.2', '>' ) ) {
                $cart_total = $cart->get_total( 'edit' );
            } else {
                $cart_total = $cart->total;
			}

			if ( empty( $cart_total ) ) {
				return;
			}

			$total           = $cart_total;
			$applied_coupons = WC()->cart->get_applied_coupons();

			foreach ( $applied_coupons as $coupon_code ) {
				$coupon = new WC_Coupon( $coupon_code );

				if ( ! $coupon || ! self::is_store_credit( $coupon ) ) {
					continue;
				}

				$coupon_amount = Wt_Smart_Coupon_Admin::wt_get_coupon_properties( $coupon, 'amount' );
				$discount      = min( $total, $coupon_amount );

				$total -= $discount;

				$this->update_coupon_discount_total( $coupon, $discount );
			}

			if ( method_exists( $cart, 'set_total' ) ) {
                $cart->set_total( $total );
            } else {
                $cart->total = $total;
            }
		}

		/**
		 * Set the store credit coupon priotity
		 * @since 1.2.0
		 */
		// @may check if other custom coupons.
		public function set_coupon_priority( $priority, $coupon ) {
			if ( self::is_store_credit( $coupon ) ) {
				$priority = 4;
			}

			return $priority;
		}

		/**
		 * Update the Credit value on successful order.
		 * @since 1.2.0
		 */
        public function update_credit_amount( $order_id ) {
			if ( empty( WC()->cart ) ) {
				return;
			}

			$applied_coupons = WC()->cart->get_applied_coupons();

			if ( empty( $applied_coupons ) ) {
				return;
			}

			$store_credit_used      = array();
			$coupon_discount_totals = $this->get_coupon_discount_totals();

			foreach ( $applied_coupons as $coupon_code ) {
				$coupon = new WC_Coupon( $coupon_code );

				if ( ! $coupon || ! self::is_store_credit( $coupon ) ) {
					continue;
				}

				$discount = ( isset( $coupon_discount_totals[ $coupon_code ] ) ? $coupon_discount_totals[ $coupon_code ] : 0 );

				if ( $discount > 0 ) {

					$store_credit_used[ $coupon_code ] = $discount;

					$coupon_amount_now = Wt_Smart_Coupon_Admin::wt_get_coupon_properties( $coupon, 'amount' );
					$coupon_id = Wt_Smart_Coupon_Admin::wt_get_coupon_properties( $coupon, 'id' );

					$credit_remaining = max( 0, ( $coupon_amount_now  - $discount ) );

					$coupon->set_amount( wc_format_decimal( $credit_remaining, 2 ) );
					$coupon->save();

					// update_post_meta( $coupon_id, 'coupon_amount', wc_format_decimal( $credit_remaining, 2 ) );
					
					$credit_history = get_post_meta( $coupon_id, 'wt_credit_history',true);
					if( '' == $credit_history ) {
						$credit_history = array();
					}
					// if(!is_serialized( $credit_history )) { $credit_history = maybe_serialize($credit_history); }

					$credit_history_this_order = array(
						'order'				=>	$order_id,
						'previous_credit' 	=>	$coupon_amount_now,
						'updated_credit' 	=>	$credit_remaining,
						'credit_used'		=>	$discount,
						'comments'			=>  __('-', 'wt-smart-coupons-for-woocommerce-pro')
					);
					$time_stamp = current_time( 'timestamp' );
					$credit_history[ "'".$time_stamp."'" ] = $credit_history_this_order;
					update_post_meta( $coupon_id, 'wt_credit_history', $credit_history );

					if( $credit_remaining <= 0 && apply_filters( 'wt_smart_coupon_delete_store_credit_after_use', false ) ) {
						// $coupon->set_date_expires( ( current_time( 'timestamp' ) - 60 )  );
						wp_trash_post( $coupon_id );
					}
					
				}
			}

			if ( ! empty( $store_credit_used ) ) {
				update_post_meta( $order_id, 'wt_store_credit_used', $store_credit_used );
			}
		}



		/** Store Credit Purchase */
		/**
		 * Whether the given product is used for Purchasing Store Credit
		 * @since 1.2.0
		 */
		function is_product_is_store_credit_purchase( $product_id ) {
			
			if( !empty( $this->store_credit_options ) ) {
				$store_credit_options =  $this->store_credit_options;
			} else {
				$store_credit_options = $this->store_credit_options = Wt_Smart_Coupon_Admin::get_option('wt_store_credit_settings');
			}
			$store_credit_product = ( isset( $store_credit_options['store_credit_purchase_product'] ) )? $store_credit_options['store_credit_purchase_product'] : '';
			if( $product_id == $store_credit_product ) {
				return true;
			}
			return false;
		}

		/**
		 *  Remove Add to cart button ( From Shop Page )  for Credit Purchase Product.
		 *  @since 1.2.0
		 */

		function remove_add_to_cart_button_from_shop_page(  ) {
			global $product, $woocommerce;
			if( $this->is_product_is_store_credit_purchase( $product->get_id() ) ) {
				$js = " jQuery('a[data-product_id=".  $product->get_id() ."]').remove(); ";

				if ( version_compare( WC()->version, '2.0.20', '>=' ) ) {
					wc_enqueue_js( $js );
				} else {
					$woocommerce->add_inline_js( $js );
				}
				?>
			 	<a href="<?php echo the_permalink(); ?>" class="button"><?php echo  __( 'Select options', 'wt-smart-coupons-for-woocommerce-pro' ); ?></a>
			 	<?php
			}
		}
		/**
		 *  Make the Store Credit Product Purchasable ( Without setting any Price ).
		 * @since 1.2.0
		 */
		function make_product_purchasable( $purchasable, $product ) {

			if( $this->is_product_is_store_credit_purchase(  $product->get_id() ) ) {
				return true;
			}

			return $purchasable;
		}

		/**
		 * Remove Product Price HTML.
		 * @since 1.2.0
		 */
		function remove_price_html_for_store_credit( $price = null, $product = null ) {
			if( $this->is_product_is_store_credit_purchase(  $product->get_id() ) ) {
				return '';
			}
			return $price;
		}

		

		/**
		 * Update the Product Price for Credit Purchase.
		 * @since 1.2.0
		 */
		function make_price_dynamic( $cart_obj ) {
			if ( is_admin() ) {
				return;
			}
			foreach ( $cart_obj->get_cart() as $key => $item ) {
				if ( ! isset( $item['wt_credit_amount'] ) ) {
					continue;
				}
				
				$credit_price = $item['wt_credit_amount'];
				$item['data']->set_price( $credit_price );
				$item['wt_credit_amount'] = $credit_price;
			}
		}



		/**
		 * Create Random Coupon and update the Credit details into Order meta
		 * @since 1.2.0
		 */
		function update_credit_coupon_into_order_old( $item_id, $values ) {

			$product = ( is_callable( array( $values, 'get_product' ) ) ) ? $values->get_product() : '';

			if ( ! is_object( $product ) || ! is_a( $product, 'WC_Product' ) ) {
				return;
			}

			$product_short_desc = $product->get_short_description();
			$qty	= ( is_callable( array( $values, 'get_quantity' ) ) ) ? $values->get_quantity() : 1;
			$qty	= ( ! empty( $qty ) ) ? $qty : 1;
			$legacy_values = $values->legacy_values;
			if( ! isset( $legacy_values['wt_credit_amount'] ) || intval( $legacy_values['wt_credit_amount'] )  <=  0 ) {
				return;
			}
			$credit_value  = $legacy_values['wt_credit_amount'];
			if( isset(  $_REQUEST['wt_credit_coupon_to_do'] )) {
				if( $_REQUEST['wt_credit_coupon_to_do']  == 'wt_credit_send_to_me' ) {
					$email =  $_REQUEST['billing_email'];
				} else {
					$email = $_REQUEST['wt_credit_coupon_send_to'];
					$message = $_REQUEST['wt_credit_coupon_send_to_message'];
				}
				$coupons_genrated = array();

				if( !empty( $this->store_credit_options ) ) {
					$store_credit_options =  $this->store_credit_options;
				} else {
					$store_credit_options = $this->store_credit_options = Wt_Smart_Coupon_Admin::get_option('wt_store_credit_settings');
				}
	
				$prefix	= $store_credit_options['store_credit_coupon_prefix'];
				$suffix	= $store_credit_options['store_credit_coupon_suffix'];
				$coupn_length = $store_credit_options['store_credit_coupon_length'];
				if( !$coupn_length  ) {
					$coupn_length  = 12;
				}

				for( $i = 0; $i < $qty; $i++ ) {
					$coupon_code =  Wt_Smart_Coupon_Admin::generate_random_coupon( $prefix,$suffix,$coupn_length );
					
					$coupon_args = array(
						'post_title'    => strtolower( $coupon_code ),
						'post_content'  => '',
						'post_status'   => 'publish',
						'post_author'   => 1,
						'post_type'     => 'shop_coupon'
					);

					$coupon_id  = wp_insert_post( $coupon_args );
					$coupons_genrated[] = array(
						'coupon_id' => $coupon_id,
						'credited_amount' => $credit_value
					);
					update_post_meta( $coupon_id, 'wt_auto_genrated_store_credit_coupon', true );

					$coupon_obj = new WC_Coupon( $coupon_id );
					$coupon_obj->set_email_restrictions(  $email );
					$coupon_obj->set_amount(  $credit_value );
					$coupon_obj->set_discount_type('store_credit');
					if( $store_credit_options['make_coupon_individual_use_only'] ) {
                        $coupon_obj->set_individual_use(true );
					}
					$coupon_obj->set_description($product_short_desc);
					$coupon_obj->save();
					do_action('wt_smart_coupon_purchased_store_credit_coupon_added',$coupon_obj);
				}
				wc_add_order_item_meta( $item_id, 'wt_credit_coupon_generated', $coupons_genrated );

			}

		}


		/**
		 * Create Random Coupon and update the Credit details into Order meta
		 * @since 1.2.0
		 */
		function update_credit_coupon_into_order( $item_id, $values ) {

			$product = ( is_callable( array( $values, 'get_product' ) ) ) ? $values->get_product() : '';

			if ( ! is_object( $product ) || ! is_a( $product, 'WC_Product' ) ) {
				return;
			}

			$product_short_desc = $product->get_short_description();
			$qty	= ( is_callable( array( $values, 'get_quantity' ) ) ) ? $values->get_quantity() : 1;
			$qty	= ( ! empty( $qty ) ) ? $qty : 1;
			$cart_item_legency = $values->legacy_values;
			if( ! isset( $cart_item_legency['wt_credit_amount'] ) || intval( $cart_item_legency['wt_credit_amount'] )  <=  0 ) {
				return;
			}
			$credit_value  = $cart_item_legency['wt_credit_amount'];
			
			if( isset(  $cart_item_legency['wt_store_crdit_template'] ) && !empty( $cart_item_legency['wt_store_crdit_template'] ) )  {
	
				$store_credit_data = $cart_item_legency['wt_store_crdit_template'];
				if( isset( $store_credit_data['wt_credit_coupon_send_to'] )) {
					$email =  $store_credit_data['wt_credit_coupon_send_to'];
				} else {
					$email =  $_REQUEST['billing_email'];
				}
				$message = $store_credit_data['wt_credit_coupon_send_to_message'];

				$coupons_genrated = array();

				if( !empty( $this->store_credit_options ) ) {
					$store_credit_options =  $this->store_credit_options;
				} else {
					$store_credit_options = $this->store_credit_options = Wt_Smart_Coupon_Admin::get_option('wt_store_credit_settings');
				}
	
				$prefix	= $store_credit_options['store_credit_coupon_prefix'];
				$suffix	= $store_credit_options['store_credit_coupon_suffix'];
				$coupn_length = $store_credit_options['store_credit_coupon_length'];
				if( !$coupn_length  ) {
					$coupn_length  = 12;
				}

				for( $i = 0; $i < $qty; $i++ ) {
					$coupon_code =  Wt_Smart_Coupon_Admin::generate_random_coupon( $prefix,$suffix,$coupn_length );
					
					$coupon_args = array(
						'post_title'    => strtolower( $coupon_code ),
						'post_content'  => '',
						'post_status'   => 'publish',
						'post_author'   => 1,
						'post_type'     => 'shop_coupon'
					);

					$coupon_id  = wp_insert_post( $coupon_args );
					$coupons_genrated[] = array(
						'coupon_id' => $coupon_id,
						'credited_amount' => $credit_value
					);
					update_post_meta( $coupon_id, 'wt_auto_genrated_store_credit_coupon', true );

					$coupon_obj = new WC_Coupon( $coupon_id );
					$coupon_obj->set_email_restrictions(  $email );
					$coupon_obj->set_amount(  $credit_value );
					$coupon_obj->set_discount_type('store_credit');
					if( $store_credit_options['make_coupon_individual_use_only'] ) {
                        $coupon_obj->set_individual_use(true );
					}
					$coupon_obj->set_description($product_short_desc);
					$coupon_obj->save();
					do_action('wt_smart_coupon_purchased_store_credit_coupon_added',$coupon_obj);
				}

				$store_credit_data = array();
				$store_credit_data[ $coupon_id ] = $cart_item_legency['wt_store_crdit_template'];
				$store_credit_data[ $coupon_id ]['coupon_id'] = $coupon_id; 
				
				wc_add_order_item_meta( $item_id, 'wt_credit_coupon_generated', $coupons_genrated );
				wc_add_order_item_meta( $item_id, 'wt_credit_coupon_template_details', $store_credit_data );


			}

		}


		/**
		 * Set Credit amount session on cart.
		 * @since 1.2.0
		 */
		public function save_credit_details_in_session( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
			if ( ! empty( $variation_id ) && $variation_id > 0 ) {  // Variable Product.
				return;
			}
			if ( ! isset( $cart_item_data['wt_credit_amount'] ) || empty( $cart_item_data['wt_credit_amount'] ) ) {
				return;
			}

			$_product = wc_get_product( $product_id );
			if( $this->is_product_is_store_credit_purchase( $product_id ) ) {
				$wt_credit_amount = WC()->session->get( 'wt_credit_amount' );
				if ( empty( $wt_credit_amount ) || ! is_array( $wt_credit_amount ) ) {
					$wt_credit_amount = array();
				}
				$wt_credit_amount[ $cart_item_key ] = $cart_item_data['wt_credit_amount'];
				WC()->session->set( 'wt_credit_amount', $wt_credit_amount );
			}

		}
		/**
		 * Save Credit details into order meta data ( used for Gift Email ).
		 * @since 1.2.0
		 */
		function save_called_credit_details_in_order( $order_id, $posted ) {
			
			$order       = wc_get_order( $order_id );
			$order_items = $order->get_items();

			$credit_coupons = array();
			$coupons_template_genrated = array();
			foreach ( $order_items as $item_id => $order_item ) {
				$credit_coupon_generated = $order_item->get_meta( 'wt_credit_coupon_generated' );
				if( !empty( $credit_coupon_generated ) ) {
					$credit_coupons  = array_merge( $credit_coupons,$credit_coupon_generated );
					update_post_meta($order_id, 'wt_credit_coupons',maybe_serialize( $credit_coupons ) );
				}
				$credit_coupon_template = $order_item->get_meta( 'wt_credit_coupon_template_details' );

				if( !empty( $credit_coupon_template ) ) {
					$coupons_template_genrated  = array_merge( $coupons_template_genrated,$credit_coupon_template );
					update_post_meta($order_id, 'wt_credit_coupon_template_details',maybe_serialize( $coupons_template_genrated ) );
				}

			}
		}


		/**
		 * Save Credit details into order meta data ( used for Gift Email ).
		 * @since 1.2.0
		 */
		function save_called_credit_details_in_order_old( $order_id, $posted ) {
			
			if( ! isset( $_POST['wt_credit_coupon_to_do'] ) ) {
				return;
			}
			
			$order       = wc_get_order( $order_id );
			$order_items = $order->get_items();
			$credit_coupons = array();
			$coupons_genrated = array();
			foreach ( $order_items as $item_id => $order_item ) {
				$credit_coupon_generated = $order_item->get_meta( 'wt_credit_coupon_generated' );
				if( !empty( $credit_coupon_generated ) ) {
					$credit_coupons  = array_merge( $credit_coupons,$credit_coupon_generated );
					update_post_meta($order_id, 'wt_credit_coupons',maybe_serialize( $credit_coupons ) );
				}
			}
			if(  isset( $_POST['wt_credit_coupon_to_do'] ) && $_POST['wt_credit_coupon_to_do'] == 'credit_gift_to_a_friend' ) {  
				$coupon_email = isset( $_POST['wt_credit_coupon_send_to'] )? Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['wt_credit_coupon_send_to'] ) : '';
				$coupon_message = isset( $_POST['wt_credit_coupon_send_to_message'] )? Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['wt_credit_coupon_send_to_message'] ) : '';
				update_post_meta($order_id, 'wt_credit_coupon_send_to', sanitize_email( $coupon_email ));
				update_post_meta($order_id, 'wt_credit_coupon_send_to_message', sanitize_text_field( $coupon_message ));
				update_post_meta( $order_id, 'wt_credit_coupon_send_from', sanitize_email( $order->get_billing_email() ) );	
			} elseif(  isset( $_POST['wt_credit_coupon_to_do'] ) ) {
				update_post_meta( $order_id, 'wt_credit_coupon_send_to', sanitize_email( $order->get_billing_email() ) );
				update_post_meta( $order_id, 'wt_credit_coupon_send_from', sanitize_email( $order->get_billing_email() ) );
			}
			
		}
		
		/**
		 * Update cart item price for  Credit Purchase.
		 * @since 1.2.0
		 */
		public function cart_item_price_for_credit_purchase( $product_price, $cart_item, $cart_item_key ) {

			$gift_certificate = ( is_object( WC()->session ) && is_callable( array( WC()->session, 'get' ) ) ) ? WC()->session->get( 'wt_credit_amount' ) : array();

			if ( ! empty( $gift_certificate ) && isset( $gift_certificate[ $cart_item_key ] ) && ! empty( $gift_certificate[ $cart_item_key ] ) ) {
				return wc_price( $gift_certificate[ $cart_item_key ] );
			} elseif ( ! empty( $cart_item['credit_amount'] ) ) {
				return wc_price( $cart_item['credit_amount'] );
			}

			return $product_price;

		}
		
		/**
		 * 
		 */
		function disable_add_to_cart_ajax( $supports, $feature, $_product ) {
			if ( $this->is_product_is_store_credit_purchase( $_product ) && 'ajax_add_to_cart' === $feature ) {
				$supports = false;
			}
			return $supports;
		}


		/**
         * Form in checkout for specifying gift coupon reciever.
         * @since 1.2.0
         */
        function store_credit_receiver_detail_form() {
            
            $free_coupons = array();

            $cart_contains_credit_purchase = false;
            $store_credit =  new Wt_Smart_Coupon_Store_Credit();
            
            foreach ( WC()->cart->cart_contents as $product ) {
                if ( empty( $product['product_id'] ) ) {
					$product['product_id'] = ( ! empty( $product['variation_id'] ) ) ? wp_get_post_parent_id( $product['variation_id'] ) : 0;
				}

				if ( empty( $product['product_id'] ) ) {
					continue;
				}
                
                
                if( $this->is_product_is_store_credit_purchase( $product['product_id'] ) ) {
                    $cart_contains_credit_purchase = true;
                }

            }
            if( $cart_contains_credit_purchase ) {
                $this->store_credit_form_htnl();
            }
           
		}

		/**
		 *  HTML form for store credit purchase
		 */
		function store_credit_form_htnl() {
			?>
			<div class="wt_smart_coupon_send_coupon_wrap">
			<h4><?php _e('Congrats, Your order contains Store Credits ','wt-smart-coupons-for-woocommerce-pro'); ?></h4>
			<p> <?php _e('What would you like to do?','wt-smart-coupons-for-woocommerce-pro') ?></p>
			<ul>


				<li class="wt_send_to_me"> 
					<label> <input type="radio" value="wt_credit_send_to_me" name="wt_credit_coupon_to_do" id="wt_send_to_me" checked/>
					 <?php _e('Send to me','wt-smart-coupons-for-woocommerce-pro' ) ?> </label>
				</li>
				<li class="credit_gift_to_a_friend"> 

					<label> <input type="radio" value="credit_gift_to_a_friend"  name="wt_credit_coupon_to_do" id="wt_send_to_me" />
				   <?php _e('Gift to a friend','wt-smart-coupons-for-woocommerce-pro' ) ?> </label>
				</li>

				
				<div class="credit_gift_to_friend_form" style="display:none">
					<div  class="wt-form-item">
						<input type="email" name="wt_credit_coupon_send_to" id="wt_credit_coupon_send_to" placeholder="<?php _e('Email to send coupon','wt-smart-coupons-for-woocommerce-pro'); ?>" />
					</div>
					<div  class="wt-form-item">
						<textarea  name="wt_credit_coupon_send_to_message" id="wt_credit_coupon_send_to_message" placeholder="<?php _e('Message','wt-smart-coupons-for-woocommerce-pro'); ?>"></textarea>
					</div>
					<?php  do_action('wt_smart_coupon_after_credit_gift_to_friend_form');  ?>

				</div>
	
				<?php  do_action('wt_smart_coupon_after_send_credit_form');  ?>
			</ul>

		</div>
			
			<?php
		}

		/**
		 * Exclude StoreCredit from user specific Coupons.
		 * @since 1.2.0
		 */

		function exclude_store_credit_from_user_specific_coupons( $coupons, $user ) {
			if( !empty( $coupons ) ) {
				foreach( $coupons as $key => $coupon ) {
					$coupon_obj = new WC_coupon( $coupon);
					if( self::is_store_credit( $coupon_obj ) ) {
						unset($coupons[$key] );
					}
				}
			}
			return $coupons;

		}

		/**
		 * display StoreCredits available in My Account
		* @since 1.2.0
		*/

		function display_store_credit_in_my_account( $user ) {
			$store_credits = $this->get_store_credit_for_a_user( $user );
			echo '<div class="wt_store_credit">';
			
			if( !empty( $store_credits ) ) {
				echo '<h4>';
					_e("Store Credits","wt-smart-coupons-for-woocommerce-pro"); 
				echo '</h4>';
				foreach( $store_credits as $coupon_id ) {
					$coupon_obj = new WC_Coupon( $coupon_id );
					$is_activated = get_post_meta( $coupon_id, '_wt_smart_coupon_credit_activated', true );					
					if( $coupon_obj->get_amount() <= 0 || ! $is_activated  ) {
						continue;
					}
					
					$coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon_obj );
					$coupon_data['display_on_page'] = 'my_account';
					echo Wt_Smart_Coupon_Public::get_coupon_html( $coupon_obj,$coupon_data );
				}
				
				
			} else {

				_e("Sorry, you don't have any available Store Credits.","wt-smart-coupons-for-woocommerce-pro");
			}

			echo '</div>';

			
		}

		/**
		 * get store credit for an user.
		 * @since 1.2.0
		 */
		function get_store_credit_for_a_user( $user,$expired = false ) {
			if( !$user  ) {
				$user = wp_get_current_user();
			}
			$email = $user->user_email;
			$user_credits = array();
			if( $user && $email ) {

				$args = array (
					'post_type' => 'shop_coupon',
					'meta_query' => array (
						array (
							'key' => 'customer_email',
							'value' => $email,
							'compare' => 'LIKE'
						),
						array (
							'key' => 'discount_type',
							'value' => 'store_credit',
							'compare' => 'LIKE'
						),
					),
				);
				$the_query = new WP_Query( $args );
				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) {
						$the_query->the_post();

						$user_credits[] = get_the_ID();
					}
				}

			}
			return $user_credits ;
		}

		/**
		 * Display the Store Credit history
		 * @since 1.2.1
		 */
		public function display_credit_history_table( $coupon ) {
			if(  ! is_object( $coupon ) ) {
				$coupon  = new WC_coupon( $coupon ); 
			}
			
			if ( ! is_object( $coupon ) ||  !is_a( $coupon, 'WC_Coupon' ) || ! $this->is_store_credit( $coupon ) ) {
				return false;
			}

			$coupon_id =  $coupon->get_id();
			$credithistories = get_post_meta( $coupon_id, 'wt_credit_history', true );
			$credit_history ='<table class="woocommerce-table shop_table shop_table_responsive wt_store_credit_history">
					<tr>
						<th>'.__('Date','wt-smart-coupons-for-woocommerce-pro').'</th>
						<th>'.__('Order ID','wt-smart-coupons-for-woocommerce-pro').'</th>
						<th>'.__('Credit / Debit','wt-smart-coupons-for-woocommerce-pro').'</th>
						<th>'.__('Balance','wt-smart-coupons-for-woocommerce-pro').'</th>
					</tr>';
					$created_on = $coupon->get_date_created();
					$date_created =  $created_on->date( 'Y-m-d H:i') ;
					$coupon_initial_amount = get_post_meta( $coupon_id,'_wt_smart_coupon_initial_credit', true );
					if( ! $coupon_initial_amount ) {
						$coupon_initial_amount = $this->get_credit_initial_amount( $coupon );
					}
					$credit_history .='<tr>
						<td>'.$date_created.' </td>
						<td>'.__('Recieved Credit','wt-smart-coupons-for-woocommerce-pro').'</td>
						<td>'.'<span class="wt-credited">'.Wt_Smart_Coupon_Admin::get_formatted_price( $coupon_initial_amount )  .'</span></td>
						<td>'.Wt_Smart_Coupon_Admin::get_formatted_price( $coupon_initial_amount ).' </td>
					</tr>';
					
			if( !empty( $credithistories ) ) {

				foreach( $credithistories as $date => $credithistory ) {
					$order = isset( $credithistory['order'] )? '#'.$credithistory['order'] : '-';
					$credit_used = ( isset( $credithistory['credit_used'] ) && $credithistory['credit_used'] !='-' )? '<span class="wt-debited">'. Wt_Smart_Coupon_Admin::get_formatted_price( $credithistory['credit_used'] ).'</span>' : '';
					if( $credit_used == '' ) {
						$credit_used = isset( $credithistory['reimbursed'] )? '<span class="wt-credited">'. Wt_Smart_Coupon_Admin::get_formatted_price( $credithistory['reimbursed'] ).'</span>' : '-';

					}
					$updated_credit = isset( $credithistory['updated_credit'] )? Wt_Smart_Coupon_Admin::get_formatted_price( $credithistory['updated_credit'] ) : '-';
					$credit_history .='<tr>
						<td>'.date_i18n('Y-m-d H:i ',intval(trim( $date, "'") ) ).'</td>
						<td>' . $order.'</td>
						<td>'.$credit_used.'</td>
						<td>'.$updated_credit.'</td>
					</tr>';
				}

			} else {
				// this credit not yet used.
			}
			$credit_history .='</table>';

			echo $credit_history;
		}

		/**
		 * Helper function to get the credit initial amount and set the value into meta
		 * @since 1.2.1
		 */
		function get_credit_initial_amount( $coupon ) {
			$coupon_id =  $coupon->get_id();
			$credit_balance	= $coupon->get_amount();
			$credithistories = get_post_meta( $coupon_id, 'wt_credit_history', true );
			if( !empty( $credithistories ) ) {
				$credit_used_so_far  = 0;
				foreach( $credithistories as $credit_history ) {
					if( is_numeric($credit_history['credit_used'] ) ) {
						$credit_used_so_far += $credit_history['credit_used'];
					}
				}
				$initial_amount = $credit_used_so_far + $credit_balance;
			} else {
				$initial_amount = $credit_balance;
			}
			update_post_meta( $coupon_id, '_wt_smart_coupon_initial_credit', $initial_amount );
			return $initial_amount;
		}

		/**
		 * display expired coupon in myStoreCredit
		 * @since 1.2.1 
		 */
		function display_expired_storecredit( $user ) {
			$store_credits = $this->get_store_credit_for_a_user( $user );
			echo '<div class="wt_store_credit">';
			
			if( !empty( $store_credits ) ) {
				echo '<h4>';
					_e("Expired Credits","wt-smart-coupons-for-woocommerce-pro"); 
				echo '</h4>';
				foreach( $store_credits as $coupon_id ) {
					$coupon_obj = new WC_Coupon( $coupon_id );
					$is_activated = get_post_meta( $coupon_id, '_wt_smart_coupon_credit_activated', true );
					if( $coupon_obj->get_amount() <= 0   ) {
						$coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon_obj );
						$coupon_data['display_on_page'] = 'my_account';
						echo Wt_Smart_Coupon_Public::get_coupon_html( $coupon_obj,$coupon_data,'expired_coupon' );
					}
				}
				
				
			} 
			echo '</div>';

			
		}

		/**
		 * Allow Store credit coupons with all coupons
		 * @since 1.2.6
		 */
		function allow_store_credit_with_all_coupons( $allow_coupon,$the_coupon,$check_coupon ) {
			if( $the_coupon->is_type('store_credit')) {
				return true;
			} 
			return $allow_coupon;
		}

		/**
		 * Make the store credit coupon allowed.
		 * @since 1.2.6
		 */
		function keep_the_store_credit_coupon_applied( $allowed_coupons, $the_coupon, $applied_coupons ) {
			foreach( $applied_coupons as $coupon_code ) {
				$coupon = new WC_Coupon( $coupon_code );
				
				if( $coupon->is_type('store_credit')) {
					$allowed_coupons[] = $coupon_code;
				} 
			}

			return $allowed_coupons;
		}

    }
}