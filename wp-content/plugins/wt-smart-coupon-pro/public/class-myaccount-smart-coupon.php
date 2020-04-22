<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'WT_MyAccount_SmartCoupon' ) ) {
    class WT_MyAccount_SmartCoupon {

        public static $endpoint = 'wt-smart-coupon';

        public function __construct() {


                // Actions used to insert a new endpoint in the WordPress.
                add_action('init', array($this, 'add_endpoints'));
                add_filter('query_vars', array($this, 'add_query_vars'), 0);

                // Change the My Accout page title.
                add_filter('the_title', array($this, 'endpoint_title'));

                // Insering your new tab/page into the My Account page.
                add_filter('woocommerce_account_menu_items', array($this, 'wt_smartcoupon_menu'));
                add_action('woocommerce_account_' . self::$endpoint . '_endpoint', array($this, 'endpoint_content'));
            
        }

        public function add_endpoints() {
            add_rewrite_endpoint(self::$endpoint, EP_ROOT | EP_PAGES);
            flush_rewrite_rules();
        }

        public function add_query_vars($vars) {
            $vars[] = self::$endpoint;

            return $vars;
        }

        public function endpoint_title($title) {

            global $wp_query;
            
            $smartcoupon_title = __('My Coupons', 'wt-smart-coupons-for-woocommerce-pro');
            $is_endpoint = isset($wp_query->query_vars[self::$endpoint]);
            if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
                $title = __($smartcoupon_title, 'woocommerce');
                remove_filter('the_title', array($this, 'endpoint_title'));
            }
            return $title;
        }

        public function wt_smartcoupon_menu($items) {

            $logout = $items['customer-logout'];
            unset($items['customer-logout']);
            $items[self::$endpoint] = __('My Coupons', 'wt-smart-coupons-for-woocommerce-pro');
            $items['customer-logout'] = $logout;
            return $items;
        }

        public function endpoint_content() {
            
            require_once ('partials/my-account/my-account-coupon-view.php');
        }

        public static function install() {
            flush_rewrite_rules();
        }

    }
}
new WT_MyAccount_SmartCoupon();