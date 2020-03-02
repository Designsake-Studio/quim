<?php
/**
 * Register all actions and filters for WT_Giveaway_Free_Product
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'WT_Giveaway_Free_Product_Hooks' ) ) {
    class WT_Giveaway_Free_Product_Hooks extends Wt_Smart_Coupon_Hooks {
        

        function load_hooks() {

            $give_away_product	= new WT_Giveaway_Free_Product( );	
			$this->add_filter( 'woocommerce_coupon_is_valid', $give_away_product,  'add_give_away_products_with_coupon', 10, 2 );
			$this->add_action('woocommerce_removed_coupon', $give_away_product,'remove_free_product_from_cart',10,1  );
			$this->add_filter('woocommerce_cart_item_subtotal',$give_away_product, 'add_custom_cart_item_total',10,2  );
			$this->add_action('wp_ajax_update_variation_id',$give_away_product,'ajax_find_matching_product_variation_id');
			$this->add_action('wp_ajax_nopriv_update_variation_id',$give_away_product,'ajax_find_matching_product_variation_id');
			$this->add_action('wp_ajax_wt_choose_free_product',$give_away_product,'add_to_cart');
			$this->add_action('wp_ajax_nopriv_wt_choose_free_product',$give_away_product,'add_to_cart');
			$this->add_action('woocommerce_after_cart_item_name',$give_away_product,'display_give_away_product_description',10,1);

			$this->add_filter( 'woocommerce_calculated_total', $give_away_product,'discounted_calculated_total', 10, 2 );
			$this->add_action('woocommerce_cart_totals_before_shipping',$give_away_product,'add_give_away_product_discount',10,0);
			$this->add_action('woocommerce_review_order_before_shipping',$give_away_product,'add_give_away_product_discount',10,0);
			$this->add_action( 'woocommerce_checkout_create_order_line_item',$give_away_product, 'add_free_product_details_into_order', 10, 4 );			
			$this->add_filter('woocommerce_get_order_item_totals',$give_away_product,'woocommerce_get_order_item_totals',11,2);
			$this->add_filter( 'woocommerce_order_item_get_formatted_meta_data',$give_away_product, 'unset_free_product_order_item_meta_data', 10, 2);
			$this->add_filter( 'woocommerce_get_cart_item_from_session', $give_away_product, 'update_cart_item_in_session' , 15, 3 );
            $this->add_filter( 'woocommerce_add_cart_item', $give_away_product,'update_cart_item_values' , 15, 4 );
			$this->add_filter( 'woocommerce_cart_item_quantity',$give_away_product, 'update_cart_item_quantity_field' , 5, 3 );
			$this->add_filter('woocommerce_cart_item_price',$give_away_product,'update_cart_item_price',10,2);

			//$this->add_filter('wt_pre_applied_discount_into_sub_total',$give_away_product,'make_give_away_product_price_is_already_discounted',10,1);
			
			$give_away_product_admin = new WT_Giveaway_Free_Product_Admin( );	
			$this->add_filter('woocommerce_coupon_data_tabs', $give_away_product_admin, 'add_give_way_coupon_data_tab', 21, 1);
			$this->add_action('woocommerce_coupon_data_panels', $give_away_product_admin, 'give_away_free_product_tab_content', 10, 1);
			$this->add_action('woocommerce_process_shop_coupon_meta', $give_away_product_admin, 'process_shop_coupon_meta_give_away', 10, 2);
			$this->add_action( 'wp_ajax_woocommerce_json_search_products_and_variations_without_parent', $give_away_product_admin, 'wt_products_and_variations_no_parent'  );

        }

    }

    $give_away_products = new  WT_Giveaway_Free_Product_Hooks();
    $give_away_products->run();

  
}