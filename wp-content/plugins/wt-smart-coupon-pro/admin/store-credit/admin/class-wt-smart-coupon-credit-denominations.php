<?php
if (!defined('WPINC')) {
    die;
}

if(!class_exists('Wt_Smart_Coupon_Storecredit_Denominations') ) {
    class Wt_Smart_Coupon_Storecredit_Denominations  extends Wt_Smart_Coupon_Store_Credit {
        function __construct(){
            
        }
        /**
         * Add store credit denominations into  smart coupon optons
         * @since 1.2.9
         */
        function store_credit_denomination_option( $smart_coupon_options ) {
            if( isset( $smart_coupon_options['store_credit_denominaton_settings'] )) {
                return $smart_coupon_options;
            }

            $denominaton_settings = array(
                'display_option' => 'user_specific_only',
                'denominations'  => ''
            );
            $smart_coupon_options['store_credit_denominaton_settings'] = $denominaton_settings;
            return $smart_coupon_options;
        }
        /**
         * Get store credit denomination option
         * @since 1.2.9
         */
        function get_option() {
            return Wt_Smart_Coupon_Admin::get_option('store_credit_denominaton_settings');
        }
        /**
         * Update store credit denomination option
         * @since 1.2.9
         */
        function set_option( $option_value ) {
            if( empty( $option_value ) ) {
                return false;
            }
            $smart_coupon_option  = get_option( 'wt_smart_coupon_options' );
            $smart_coupon_option['store_credit_denominaton_settings'] =  $option_value;
            update_option( 'wt_smart_coupon_options', $smart_coupon_option );
        }
        /**
         * Store credit denomination settings form
         * @since 1.2.9
         */
        function add_denomination_settngs() {
            $denomination_options = $this->get_option();
            ?>
                <?php
                    $display_options = array(
                        'denominations_only'                => 'Predefined only',
                        'user_specific_only'                => 'Custom only',
                        'denominations_and_user_specific'   => 'Custom and predefined',

                    );
                    $store_credit_display_options = apply_filters( 'wt_smart_coupon_store_credit_display_options', $display_options );
                ?>
                    <tr  valign="top">
                        <td scope="row" class="titledesc">
                            <?php _e('Credit purchase options','wt-smart-coupons-for-woocommerce-pro'); ?>
                        </td>
                        <td class="forminp forminp-text">
                                <select id="store_credit_display_option" name="store_credit_display_option" style="width: 300px"  class="wc-enhanced-select" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                                    <?php 
                                    $option_selected = $denomination_options['display_option'];
                                    $disabled_credit_denominations = ( 'user_specific_only' == $option_selected ) ? 'disabled=disabled' :  '';
                                        foreach( $display_options  as $display_option => $display_option_text ) {
                                            $selected = '  ';
                                            if( $option_selected == $display_option  ) {
                                                $selected = ' selected';
                                            }
                                            echo '<option value="'.$display_option.'" '.$selected.'> '.$display_option_text.'</option>';
                                        }
                                    ?>
                                </select>
                            <?php echo wc_help_tip( __('Signifies purchase options for store credit. Predefined allows the admin to set specific amounts. Custom allows the customer to enter an amount of choice.','wt-smart-coupons-for-woocommerce-pro') ); ?>  
                        </td>
                    </tr>
                
                    <tr  valign="top">
                        <td scope="row" class="titledesc"> 
                            <?php _e('Set amount','wt-smart-coupons-for-woocommerce-pro'); ?>
                                    
                        </td>
                        <td class="forminp forminp-text">
                            <input type="text" name="store_crdit_denominations"  id="store_crdit_denominations" value="<?php echo $denomination_options['denominations']; ?>"  <?php echo $disabled_credit_denominations; ?>/>
                            <?php echo wc_help_tip( __('Specify the predefined denomination values that must appear at the user end while purchasing store credit.','wt-smart-coupons-for-woocommerce-pro') ); ?>
                        </td>
                    </tr>
            <?php
        }

        /**
         * Save store credit denominations
         * @since 1.2.9
         */
        function save_denomination_settings( ) {
            if( isset( $_POST['update_wt_smart_coupon_store_credit_settings'] ) ) {
                check_admin_referer('wt_smart_coupons_store_credit_settings');
                $denomination_option = array( 
                    'display_option'    => ( isset( $_POST['store_credit_display_option'] ) ) ? $_POST['store_credit_display_option'] : 'user_specific_only',
                    'denominations'     => ( isset( $_POST['store_crdit_denominations'] ) ) ? $_POST['store_crdit_denominations'] : '',
                );

                $this->set_option( $denomination_option );
            }
        }
        /**
         * helper function to check is need to display the denominations
         * @since 1.2.9
         */
        public  function is_need_to_display_denomination( ) {
            $settings = $this->get_option();
            if( 'user_specific_only' == $settings['display_option'] || '' == $settings['denominations'] ) {
                return false;
            }
            return true;
        }
        /**
         * helper function to check is need to display the storecredit amount form.
         * @since 1.2.9
         */
        public  function is_need_display_user_price_form() {
            $settings = $this->get_option();
            if( 'denominations_only' == $settings['display_option'] ) {
                return false;
            }
            return true;
        }



        /**
		 *   Add a custom price from before cart button.
		 *  @since 1.2.0
         * copied form storecredit admin
		 */
		function insert_credit_form() {
			global $product;
			if( ! $this->is_product_is_store_credit_purchase( $product->get_id() ) ) {
				return;
            }
            
            $settings = $this->get_option();
            $displayed_denominaton = false;
            if( 'user_specific_only' != $settings['display_option'] && '' != $settings['denominations'] ) {
                $denomination_items = explode(',',$settings['denominations']);

                array_walk($denomination_items, create_function('&$val','$val = trim($val);'));
                $denomination_items  = array_unique( array_filter( $denomination_items ) );

                $displayed_denominaton = true;
                ?>
                <div class="radio-toolbar wt_credit_denominations">
                    <?php 
                    foreach( $denomination_items as $denomination ) {
                        ?>
                        <input type="radio" id="denomination_<?php echo $denomination; ?>"  name="credit_denominaton" value="<?php echo trim( $denomination ); ?>" >
                        <label for="denomination_<?php echo $denomination; ?>"><?php echo Wt_Smart_Coupon_Admin::get_formatted_price( $denomination ); ?></label>

                        <?php
                    }
                    ?>
                    <?php  
                    if( 'denominations_and_user_specific' == $settings['display_option'] ) { 
						$currency_symbol = get_woocommerce_currency_symbol();
                        if( ! empty( $currency_symbol ) ) {
							$credit_amount_text = __( 'Other amount' ,'wt-smart-coupons-for-woocommerce-pro' ). ' (' . $currency_symbol . ')' ;
						} else{
							$credit_amount_text =  __( 'Other amount' ,'wt-smart-coupons-for-woocommerce-pro' );			
						}
                        ?>
                        
                        <div class="wt_enter_credit_amount">
                            <input id='wt_user_credit_amount' class="wt_enter_other_amount" step='1' type='number' min='1' name='wt_user_credit_amount' value='' autocomplete='off' placeholder="<?php echo $credit_amount_text; ?>" />
                            <?php
                            $credit_options = Wt_Smart_Coupon_Admin::get_option('wt_store_credit_settings');

                            $min_purchase = $credit_options['minimum_store_credit_purchase'];
                            $max_purchase = $credit_options['maximum_store_credit_purchase'];
                            $min_price_value = Wt_Smart_Coupon_Admin::get_formatted_price( $min_purchase );
                            $max_price_value = Wt_Smart_Coupon_Admin::get_formatted_price( $max_purchase );
                            if( $min_purchase > 0 || $max_purchase > 0 ) {
                                ?>
                                <div class="credit_instruction">
                                        <?php 

                                            
                                            if( $min_purchase > 0 ) {
                                                echo __('Minimum: ','wt-smart-coupons-for-woocommerce-pro').$min_price_value;
                                                echo '<br/>';
                                            }
                                            if( $max_purchase > 0 ) {
                                                echo __('Maximum: ','wt-smart-coupons-for-woocommerce-pro').$max_price_value;
                                                echo '<br/>';
                                            }
                                        ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php 
                    }  ?>
                    
                </div>

                <?php
            }

            if( 'user_specific_only' == $settings['display_option'] || '' == $settings['denominations'] ) { 
			
			?>
                <br />
				<div id="wt_store_credit">
					<?php
						$currency_symbol = get_woocommerce_currency_symbol();
					?>
					<p style="float: left">
					<?php
						if( ! empty( $currency_symbol ) ) {
							$credit_amount_text = __( stripslashes( 'Amount' ),'wt-smart-coupons-for-woocommerce-pro' ). ' (' . $currency_symbol . ')' ;
						} else{
							$credit_amount_text =  __( stripslashes( 'Amount' ),'wt-smart-coupons-for-woocommerce-pro' );			
						}
					echo '</p>';
					?>
					<div class="credit-purchase-field">
						<div class="form-item">
							<?php  echo "<input id='wt_user_credit_amount' step='1' type='number' min='1' name='wt_user_credit_amount' value='' autocomplete='off'  placeholder='".$credit_amount_text."' />"; ?>
							<div class="credit_instruction">
									<?php 

										$credit_options = Wt_Smart_Coupon_Admin::get_option('wt_store_credit_settings');

										$min_purchase = $credit_options['minimum_store_credit_purchase'];
										$max_purchase = $credit_options['maximum_store_credit_purchase'];
										$min_price_value = Wt_Smart_Coupon_Admin::get_formatted_price( $min_purchase );
										$max_price_value = Wt_Smart_Coupon_Admin::get_formatted_price( $max_purchase );
										if( $min_purchase > 0 ) {
											echo __('Minimum: ','wt-smart-coupons-for-woocommerce-pro').$min_price_value;
											echo '<br/>';
										}
										if( $max_purchase > 0 ) {
											echo __('Maximum: ','wt-smart-coupons-for-woocommerce-pro').$max_price_value;
											echo '<br/>';
										}
									?>
							</div>
						</div>
					</div>
					
                </div>
                
            <?php } ?>

				
            <input type="hidden" name="wt_credit_amount" id="wt_credit_amount" />
			<?php
		}
        
    }
}