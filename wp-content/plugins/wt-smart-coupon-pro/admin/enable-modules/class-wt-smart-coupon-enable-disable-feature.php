<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * The admin-Settings functionality of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      1.2.1
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin
 * 
 */

if( ! class_exists ( 'WT_smart_Coupon_Enable_Module' ) ) {

    
	class WT_smart_Coupon_Enable_Module {


        public function __construct( ) {

           if( isset( $_GET['enable_features'] ) || ( isset( $_GET['tab'] ) && $_GET['tab'] == 'enable_disable_features' ) ) {
                add_filter( 'wt_smart_coupon_admin_tab_items', array($this,'add_admin_tab'), 100, 1 );
           }

           add_action('wt_smart_coupon_tab_content_enable_disable_features',array($this,'enable_disable_features_content' ));
        }

        /**
         * Add Enable Module tab into Smart Coupon settings.
         * @since 1.2.1
         */
        function add_admin_tab( $admin_tabs ) {
            
            $admin_tabs['enable_disable_features'] = __('Modules','wt-smart-coupons-for-woocommerce-pro');
            return $admin_tabs;
        }

        /**
         * Default smart coupon features
         * @since .1.2.1
         */
        public static function wt_smart_coupon_features( ) {
            $wt_smart_coupon_features = array (
                
                'bulk_genrate' => array (
                    'file_to_include' => array(
                        'admin/bulk-generate/class-wt-smart-coupon-bulk-generate.php',
                        'admin/bulk-generate/class-wt-smart-coupon-bulk-generate-hooks.php',

                    ),
                    'status'    => true
                ),
                'import_coupon' => array(
                    'file_to_include' => array(
                        'admin/import/class-import-smart-coupon.php',
                        'admin/import/class-import-smart-coupon-hooks.php'
                    ),
                    'status'    => true
                ),
                'store_credit' => array(
                    'file_to_include' => array(
                        'admin/store-credit/admin/class-smart-coupon-store-credit.php',
                        'admin/store-credit/public/class-smart-coupon-store-credit.php',
                        'admin/store-credit/public/class-smart-coupon-customisable-gift-card.php',
                        'admin/store-credit/public/wt-store-credit-menu-myaccount.php',
                        'admin/store-credit/class-wt-smart-coupon-store-credit-order.php',
                        'admin/store-credit/admin/wt-smart-coupon-schedule-store-credit.php',
                        'admin/store-credit/admin/class-wt-smart-coupon-credit-denominations.php',
                        'admin/store-credit/class-smart-coupon-store-credit-hooks.php',
                    ),
                    'status'    => true
                ),
                'combo_coupon' => array(
                    'file_to_include' => array(
                        'admin/combo_coupon/class-wt-smart-coupon-combo-coupon.php',
                        'admin/combo_coupon/class-wt-smart-coupon-combo-coupon-hooks.php',
                    ),
                    
                    'status'    => true
                ),
                'auto_coupon' => array(
                    'file_to_include' => array(
                        'public/auto-coupon/class-wt-smart-coupon-auto-coupon.php',
                        'public/auto-coupon/class-wt-smart-coupon-auto-coupon-hooks.php'
                    ),
                    'status'    => true
                ),
                
                'give_away_products' => array(
                    'file_to_include' => array(
                        'public/give-away-product/public/class-wt-giveaway-free-products.php',
                        'public/give-away-product/admin/class-wt-giveaway-free-product-admin.php',
                        'public/give-away-product/class-wt-giveaway-free-products-hooks.php'
                    ),
                    'status'    => true
                ),
                'gift_coupon' => array(
                    'file_to_include' => array(
                        'admin/gift-coupon/admin/class-wt-smartcoupon-gift-coupon-admin.php',
                        'admin/gift-coupon/public/class-wt-smartcoupon-gift-coupon.php',
                        'admin/gift-coupon/class-wt-smartcoupon-gift-coupon-hooks.php'
                    ),
                    'status'    => true
                ),
                'exclude_product' => array(
                    'file_to_include' => array(
                        'admin/exclude-product/class-wt-exclude-product-for-coupon.php',
                    ),
                    'status'    => true
                    
                ),
                'wt_short_Codes'    => array(
                    'file_to_include' => array(
                        'public/shortcodes/class-wt-smart-coupon-shortcodes.php'
                    ),
                    'status'    => true
                ),
                'coupon_start_date'    => array(
                    'file_to_include' => array(
                        'admin/coupon-start-date/class-wt-smart-coupon-start-date.php'
                    ),
                    'status'    => true
                ),
                'url_coupon'    => array(
                    'file_to_include' => array(
                        'admin/url-coupon/class-wt-smart-coupon-url-coupon.php',
                        'admin/url-coupon/class-smart-coupon-url-coupon-hooks.php'
                        
                    ),
                    'status'    => true
                ),
                'limit_max_discount'    => array(
                    'file_to_include' => array(
                        'admin/limit-max-discount/class-wt-smart-coupon-limit-discount.php'
                    ),
                    'status'    => true
                ),
                'action_couppns'    => array(
                    'file_to_include' => array(
                        'admin/action-coupons/class-wt-smart-coupon-action-coupons.php'
                    ),
                    'status'    => true
                ),
                'signup_coupons'    => array(
                    'file_to_include' => array(
                        'admin/signup-coupons/admin/class-wt-smartcoupon-signup-coupon.php',
                        'admin/signup-coupons/class-wt-smart-coupon-signup-coupon-hooks.php'
                    ),
                    'status'    => true
                ),
                'cart_checkout_abandonment'    => array(
                    'file_to_include' => array(
                        'admin/cart-abandonment/admin/class-wt-smartcoupon-cart-abandonment.php',
                        'admin/cart-abandonment/class-smart-coupon-cart-abandonment-hooks.php'

                    ),
                    'status'    => true
                ),
                'nth_order_coupon'    => array(
                    'file_to_include' => array(
                        'admin/nth-order-coupon/admin/class-nth-order-coupon-admin.php'
                    ),
                    'status'    => true
                ),
                'coupon_banner'    => array(
                    'file_to_include' => array(
                        'admin/coupon-banner/admin/class-wt-coupon-banner.php',
                        'admin/coupon-banner/public/class-wt-coupon-banner-shortcode.php',
                        'admin/coupon-banner/class-wt-smart-coupon-banner-hooks.php'
                    ),
                    'status'    => true
                ),



            );

            return apply_filters( 'wt_smart_coupon_features', $wt_smart_coupon_features );
        }

        /**
         * Gell all the modules in system
         * @since 1.2.1
         */
        function get_all_modules() {
            $all_features = $this->wt_smart_coupon_features();
            return array_keys($all_features);
        } 

        /**
         * Get the features enabled/ disabled ( Stored in DB )
         * @since 1.2.1
         */
        public static function get_features_option() {

            $wt_smart_coupon_features   = self::wt_smart_coupon_features();
            $option_saved               = get_option('wt_smart_coupon_features');
            if( is_array($option_saved ) && !empty( $option_saved ) ) {
                foreach( $option_saved as $feature => $option ) {
                    $wt_smart_coupon_features[ $feature ]['status'] = $option['status'];
                }
            } else{
                update_option('wt_smart_coupon_features',$wt_smart_coupon_features);
            }

            return  $wt_smart_coupon_features;
        }

        /**
         * Update the features enabled
         * @since 1.2.1
         */
        function update_features_option( $options ) {

            $option_features = array_keys(  $options );

            $wt_smart_coupon_features   = self::wt_smart_coupon_features();
            foreach( $wt_smart_coupon_features as $feature => $propeties ) {
                if( in_array($feature,$option_features ) ) {
                    $status = $options[ $feature ];

                    $wt_smart_coupon_features[ $feature ]['status'] = $status;
                }
            }

            update_option('wt_smart_coupon_features',$wt_smart_coupon_features);

           
        }

        /**
         * Enable modules
         * @since 1.2.1
         */
        function enable_modules( $modules ) {
            if( !is_array( $modules ) || empty( $modules) ) {
                $modules = array();
            }
            $all_modules = $this->get_all_modules();

            $options = array();

            foreach( $all_modules as $module ) {
                if( in_array( $module,$modules ) ) {
                    $options[ $module ] = true;
                } else {
                    $options[ $module ] = false;
                }
            }

            if( !empty ($options) ) {
                $this->update_features_option( $options );
            }

        }

        /**
         * Manage the modules enabled/disables ( Tab content )
         * @since 1.2.1
         */
        function enable_disable_features_content() {

            if( isset( $_POST['update_wt_smart_coupon_enabled_modules'] )  ) {
                $enabled_modules = isset( $_POST['wt_enable_feature'] ) ? Wt_Smart_Coupon_Security_Helper::sanitize_item( $_POST['wt_enable_feature'], 'text_arr' ) : array();
                if( is_array( $enabled_modules )  ) {
                    $this->enable_modules( $enabled_modules );

                    header('Location: '.$_SERVER['REQUEST_URI']);
                }
            }
            ?>

                <div id="enable_disable_features_content" class="meta-box-sortables ui-sortable">
                    <div id="wt_enable_feature" >
                        <div class="wt-enable-module-content">
                            <form name="wt-enable-modules" method="POST">
                                <div class="wt_section_title">
                                    <h2>
                                        <?php _e('Enable Modules','wt-smart-coupons-for-woocommerce-pro') ?>
                                    </h2>
                                </div>

                                <table class="form-table" style="width:375px;">
                                    <tbody>
                                        <?php
                                            $wt_smart_coupon_features = self::get_features_option();
                                            
                                            foreach( $wt_smart_coupon_features as $wt_smart_coupon_feture => $properties ) {
                                                ?>
                                                <tr  valign="top">
                                                    <?php 
                                                        $status =  ( isset( $properties['status'] ) )? $properties['status'] : false; 
                                                        $checked = '';
                                                        if( $status ) {
                                                            $checked = 'checked = checked';
                                                        }
                                                    ?>
                                                    <td scope="row" class="titledesc"> 
                                                        <?php echo $wt_smart_coupon_feture; ?>
                                                    </td>
                                                    <td class="forminp forminp-checkbox">
                                                        <input value="<?php echo $wt_smart_coupon_feture; ?>" type="checkbox" id="wt_enable_feature" name="wt_enable_feature[]" <?php echo $checked; ?>>

                                                    </td>
                                                </tr>

                                                <?php 
                                            }
                                        ?>
                                    </tbody>
                                </table>
                                <div class="wt_form_submit">
                                    <div class="form-submit">
                                        <button id="update_wt_smart_coupon_enabled_modules" name="update_wt_smart_coupon_enabled_modules" type="submit" class="button button-primary button-large"><?php _e( 'Update','wt-smart-coupons-for-woocommerce-pro'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


            <?php
        }
    }
}

$WT_smart_Coupon_Enable_Module = new WT_smart_Coupon_Enable_Module();