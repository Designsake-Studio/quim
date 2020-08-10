<?php
/**
 * Register all actions and filters for Combo Coupon
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/combo_coupon
 */


// If this file is called directly, abort.
if (!defined('WPINC')) { 
    die;
}

if( ! class_exists ( 'Wt_Smart_Coupon_Store_Credit_Hooks' ) ) {
    class Wt_Smart_Coupon_Store_Credit_Hooks extends Wt_Smart_Coupon_Hooks {
       

        function load_hooks() {

			$enabled_customizing_store_Credit = Wt_Smart_Coupon_Customisable_Gift_Card::is_extended_store_credit_enebaled( );

            $store_credit_admin = new Wt_Smart_Coupon_Store_Credit_Admin();
			$this->add_filter('wt_smart_coupon_default_options',$store_credit_admin,'store_credit_options',10,1);
			$this->add_action('wp_ajax_wt_send_credit_coupon', $store_credit_admin, 'send_store_credit' );
			$this->add_action('woocommerce_order_status_changed',$store_credit_admin,'send_credit_coupon_email', 10, 4);
			$this->add_action( 'add_meta_boxes', $store_credit_admin, 'add_store_credit_details_into_order' );
			$this->add_action('woocommerce_email_classes', $store_credit_admin,'add_store_credit_emails', 11, 1);
			$this->add_action('wp_ajax_wt_send_store_credit_coupon', $store_credit_admin,'send_store_credit_items', 11, 1);
			$this->add_action( 'admin_enqueue_scripts', $store_credit_admin, 'enqueue_scripts',11,0 );
			
			
            $store_credit_order = new Wt_Smart_Coupon_Store_Credit_Order();
			
			$this->add_action('woocommerce_order_status_changed',$store_credit_order,'manage_credit_coupon_on_order', 10, 4);
			$this->add_filter( 'woocommerce_get_order_item_totals', $store_credit_order, 'add_order_item_totals_store_credit_row' , 10, 2 );
			$this->add_action( 'woocommerce_admin_order_totals_after_tax', $store_credit_order, 'admin_order_store_credit_used_details' );
			$this->add_filter( 'woocommerce_order_get_discount_total', $store_credit_order, 'get_discount_total' , 10, 2 );
			$this->add_filter( 'woocommerce_cart_totals_coupon_label', $store_credit_order, 'store_credit_cart_total_coupon_label' , 10, 2 );


            
            // @since 1.2.0
            $store_credit = new Wt_Smart_Coupon_Store_Credit();
			$this->add_filter( 'woocommerce_coupon_discount_types', $store_credit,'add_store_credit_discount_type'  );
			$this->add_filter( 'woocommerce_coupon_is_valid_for_cart', $store_credit, 'is_valid_for_cart' , 10, 2 );
			$this->add_action( 'woocommerce_applied_coupon', $store_credit, 'apply_coupon_last'  );
			$this->add_filter( 'woocommerce_coupon_sort', $store_credit, 'set_coupon_priority' , 10, 2 );
			$this->add_filter( 'woocommerce_coupon_get_discount_amount', $store_credit, 'get_discount_amount' , 10, 5 );
			$this->add_filter( 'woocommerce_coupon_custom_discounts_array', $store_credit, 'get_discounts_array' , 10, 2 );
            $this->add_action( 'woocommerce_after_calculate_totals',$store_credit, 'update_before_tax' , 5 );
			$this->add_action( 'woocommerce_after_calculate_totals', $store_credit,'after_calculate_totals' );
			$this->add_action( 'woocommerce_new_order', $store_credit, 'update_credit_amount' , 8 );
			$this->add_action( 'woocommerce_after_shop_loop_item', $store_credit, 'remove_add_to_cart_button_from_shop_page' );
			$this->add_filter( 'woocommerce_is_purchasable',  $store_credit, 'make_product_purchasable', 10, 2 );
			$this->add_filter( 'woocommerce_get_price_html',  $store_credit, 'remove_price_html_for_store_credit' , 10, 2 );
			// $this->add_action( 'woocommerce_before_add_to_cart_button', $store_credit, 'insert_credit_form',4 );
			$this->add_action( 'woocommerce_before_calculate_totals',     $store_credit, 'make_price_dynamic' , 10, 1 );
			// add_filter( 'woocommerce_product_supports',           array( $this, 'disable_add_to_cart_ajax' ), 10, 3 );
			


			$this->add_filter( 'woocommerce_add_to_cart_validation',  $store_credit, 'validate_store_credit_on_add_to_cart' , 10, 2 );
			$this->add_filter( 'woocommerce_add_cart_item_data',  $store_credit, 'add_store_credit_details_to_cart_item_data' , 10, 3 );
			if( ! $enabled_customizing_store_Credit ) { 
				$this->add_action( 'woocommerce_checkout_after_customer_details', $store_credit, 'store_credit_receiver_detail_form'  );
				$this->add_action('woocommerce_new_order_item', $store_credit,'update_credit_coupon_into_order_old',1,2);
			} else {
				$this->add_action('woocommerce_new_order_item', $store_credit,'update_credit_coupon_into_order',1,2);

			}

			$this->add_action( 'woocommerce_add_to_cart',  $store_credit, 'save_credit_details_in_session' , 10, 6 );
			$this->add_filter( 'woocommerce_cart_item_price',  $store_credit, 'cart_item_price_for_credit_purchase' , 10, 3 );

			if( ! $enabled_customizing_store_Credit ) { 
				$this->add_action( 'woocommerce_checkout_update_order_meta',  $store_credit, 'save_called_credit_details_in_order_old' , 10, 2 );
			} else {
				$this->add_action( 'woocommerce_checkout_update_order_meta',  $store_credit, 'save_called_credit_details_in_order' , 10, 2 );
			}
			$this->add_filter( 'wt_coupon_added_email_restriction_to_the_user',  $store_credit, 'exclude_store_credit_from_user_specific_coupons' , 10, 2 );
			$this->add_filter( 'wt_smart_coupon_used_coupons',  $store_credit, 'exclude_store_credit_from_user_specific_coupons' , 10, 2 );
			$this->add_action( 'wt_my_store_credit',  $store_credit, 'display_store_credit_in_my_account' , 10, 1 );
			$this->add_action( 'wt_my_store_credit',  $store_credit, 'display_expired_storecredit' , 11, 1 );
			$this->add_action( 'wt_store_credit_history',  $store_credit, 'display_credit_history_table' , 10, 1 );

			/**
			 * Allow store credit coupons with restricted couopons.
			 * @since 1.2.6
			 */
			$this->add_filter( 'woocommerce_apply_with_individual_use_coupon',  $store_credit, 'allow_store_credit_with_all_coupons' , 10, 3 );
			$this->add_filter( 'woocommerce_apply_individual_use_coupon',  $store_credit, 'keep_the_store_credit_coupon_applied' , 10, 3 );

			$this->add_action( 'wp_enqueue_scripts', $store_credit, 'enqueue_store_credit_public_styles' );
            $this->add_action( 'wp_enqueue_scripts', $store_credit, 'enqueue_store_credit_public_scripts' );



            $schedule_credit = new Wt_Smart_Coupon_Schedule_Store_Credit();
			$this->add_action( 'wt_smart_coupon_schedule_credit', $schedule_credit,'schedule_send_credit_coupon' ,10,1 );

			if( $enabled_customizing_store_Credit ) {
				$this->add_action( 'wt_send_coupon_email_as_per_schedule', $schedule_credit,'send_credit_coupon',10,6 );

			} else {
				$this->add_action( 'wt_send_coupon_email_as_per_schedule', $schedule_credit,'send_credit_coupon_old',10,3 );

			}
			
			if( $enabled_customizing_store_Credit ) { 
				$this->add_action('woocommerce_before_add_to_cart_button',$schedule_credit,'add_schedule_date_for_storecredit',11  );
			}
			$this->add_action('wt_smart_coupon_after_credit_gift_to_friend_form',$schedule_credit,'add_schedule_date_for_storecredit'  );
			
			if( ! $enabled_customizing_store_Credit ) {
				$this->add_action('woocommerce_checkout_update_order_meta', $schedule_credit,'update_store_credit_schedule_into_order',10,2);
			}

            $this->add_action( 'wp_enqueue_scripts',$schedule_credit,'enabling_date_picker');
            if( $enabled_customizing_store_Credit ) {
				$this->add_action('woocommerce_thankyou', $schedule_credit,'add_store_credit_schedule_for_cart_item',10,1);
				$this->add_filter('wt_send_credit_coupon_on_order_success_status',$schedule_credit,'disable_sending_store_credit_immediately',10,3);

			} else {
				$this->add_filter('wt_send_credit_coupon_on_order_success_status',$schedule_credit,'disable_sending_store_credit_immediately_old',10,2);

			}


			

			/**
			 *  Customisable gift coupon
			 * @since 1.2.8
			 */
			$customisable_gift_card = new Wt_Smart_Coupon_Customisable_Gift_Card();

			$this->add_filter('wt_smart_coupon_default_options',$customisable_gift_card,'add_customisable_gift_coupon_option',10,1);


			if( $enabled_customizing_store_Credit ) {
				
				$this->add_action( 'woocommerce_before_single_product_summary', $customisable_gift_card,'shop_single_page_design' ,10,0);
				$this->add_filter( 'woocommerce_is_sold_individually',$customisable_gift_card,'remove_quantity_selction_for_gift_card',10,2);
				$this->add_filter( 'woocommerce_is_virtual',$customisable_gift_card,'make_the_sore_credit_product_virtual',10,2);
				
				$this->add_action('woocommerce_before_add_to_cart_button',$customisable_gift_card,'add_store_credit_fields_on_product_page',10);
				$this->add_filter( 'woocommerce_add_cart_item_data',  $customisable_gift_card, 'add_store_credit_template_details_to_cart_item_data' , 10, 3 );
				$this->add_filter( 'woocommerce_get_item_data',$customisable_gift_card, 'display_credit_details_into_cart_item', 10, 2 );
				$this->add_filter( 'woocommerce_add_to_cart_validation',  $customisable_gift_card, 'validate_store_credit_for_email_address' , 10, 2 );
				
			}

			$this->add_action( 'wt_store_credit_before_settigns_form_items', $customisable_gift_card,'add_customizable_store_credit_option', 10, 1 );
            $this->add_action( 'wt_smart_coupon_before_store_credit_settings_from', $customisable_gift_card,'customizable_store_credit_warning', 10, 1 );
            $this->add_action( 'wp_ajax_wt_store_credit_try_now', $customisable_gift_card,'try_store_credit_now'  );
			
			// remove_action( 'woocommerce_checkout_after_customer_details', array( $store_credit, 'store_credit_receiver_detail_form' ) );
			
			$store_credit_denominations = new Wt_Smart_Coupon_Storecredit_Denominations();

			$this->add_filter( 'wt_smart_coupon_default_options', $store_credit_denominations,'store_credit_denomination_option', 18, 1 );            
            $this->add_action( 'wt_after_associate_product_field_store_credit',$store_credit_denominations,'add_denomination_settngs', 10,0 );
            $this->add_action( 'wt_store_credit_settings_updated', $store_credit_denominations,'save_denomination_settings', 10, 0 );
            $this->add_action( 'woocommerce_before_add_to_cart_button', $store_credit_denominations,'insert_credit_form', 4, 0 );
            
        }

    }

    $store_credit = new  Wt_Smart_Coupon_Store_Credit_Hooks();
    $store_credit->run();

  
}