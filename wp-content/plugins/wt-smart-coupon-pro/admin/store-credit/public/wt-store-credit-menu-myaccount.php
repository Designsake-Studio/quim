<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'WT_MyAccount_StoreCredit' ) ) {
    class WT_MyAccount_StoreCredit  extends WC_Query {

        public static $endpoint = 'wt-store-credit';
        public static $endpoint1 = 'wt-view-store-credit';

        public function __construct() {
                add_filter('wc_get_template',array($this,'add_view_subscription_template'), 10, 5);


                // Actions used to insert a new endpoint in the WordPress.
                add_action('init', array($this, 'add_endpoints'));
                // Change the My Accout page title.
                add_filter('the_title', array($this, 'endpoint_title'));

                if ( !is_admin() ) {
                    add_filter('query_vars', array($this, 'add_query_vars'), 0);
                    // Insering your new tab/page into the My Account page.
                    add_filter('woocommerce_account_menu_items', array($this, 'wt_smartcoupon_menu'));
                    add_action('woocommerce_account_' . self::$endpoint . '_endpoint', array($this, 'endpoint_content'));
                    add_action('woocommerce_account_' . self::$endpoint1 . '_endpoint', array($this, 'endpoint_content1'));
                }
                $this->init_query_vars();
        }


        public function add_view_subscription_template($located_template, $template_name, $args, $template_path, $default_path){
        
        
            global $wp;
            if ('myaccount/my-account.php' == $template_name && !empty($wp->query_vars['view-store-credit']) ) {
                $located_template = wc_locate_template('views/my-account/view-store-credit.php', $template_path,'/');
                
            }
            
            return $located_template;
        }


        public function init_query_vars() {

            $this->query_vars = array(
                'view-store-credit' => get_option('woocommerce_myaccount_view_store_Credit_end_poit', 'wt-view-store-credit'),
            );
            $this->query_vars['store-credit'] = get_option('woocommerce_myaccount_store_credit_endpoint', 'wt-store-credit');
        }


        public function endpoint_title($title) {
            if (in_the_loop() && is_account_page()) {
                foreach ($this->query_vars as $key => $query_var) {
                    if ($this->is_query($query_var)) {
                        $title = $this->get_endpoint_title($key);
                        remove_filter('the_title', array($this, __FUNCTION__), 11);
                    }
                }
            }
            return $title;
        }


        public function get_endpoint_title($endpoint) {
            global $wp;
            switch ($endpoint) {
                case 'view-store-credit':
                    $coupon_id = $wp->query_vars['wt-view-store-credit'];
                    $title = ( $coupon_id ) ? sprintf(__('Store Credit #%s', 'wt-smart-coupon-for-woocommerce-pro'),$coupon_id ) : '';
                    break;
                case 'store-credit':
                        $title = __('My Store Credit', 'wt-smart-coupons-for-woocommerce-pro');
                    break;
                default:
                    $title = '';
                    break;
            }
            return $title;
        }


        protected function is_query($query_var) {
            global $wp;
    
            if (is_main_query() && is_page() && isset($wp->query_vars[$query_var])) {

                $is_coupon_query = true;
            } else {
                $is_coupon_query = false;
            }
            return apply_filters('is_wt_smart_coupon_query', $is_coupon_query, $query_var);
        }




        public function wt_smartcoupon_menu($items) {
            $logout = $items['customer-logout'];
            unset($items['customer-logout']);
            $items[self::$endpoint] = __('My Store Credits', 'wt-smart-coupons-for-woocommerce-pro');
            $items['customer-logout'] = $logout;
            return $items;
        }

        public function endpoint_content() {
            
            require_once ('views/my-account/my-store-credit.php');
        }

        public function endpoint_content1() {
            
            require_once ('views/my-account/view-store-credit.php');

        }

        public static function install() {
            flush_rewrite_rules();
        }

    }
}
new WT_MyAccount_StoreCredit();