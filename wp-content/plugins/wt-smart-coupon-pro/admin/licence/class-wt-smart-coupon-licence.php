<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'Wt_Smart_Coupon_licence' ) ) {
    class Wt_Smart_Coupon_licence {
        function __construct() {
            add_filter( 'wt_smart_coupon_admin_tab_items', array($this,'add_licence_tab'), 99, 1 );            
            add_action('wt_smart_coupon_tab_content_licence', array( $this, 'licence_tab_content'),10);
        }

        function add_licence_tab( $admin_tabs ) {
            $admin_tabs['licence'] =  __('License','wt-smart-coupons-for-woocommerce-pro');
            return $admin_tabs;
        }

        function licence_tab_content() {
            ?>


            <div id="normal-sortables-3" class="meta-box-sortables ui-sortable">
                <ul class="wt_sub_tab">
                            
                    <?php
                        $sections = $this->get_licenced_plugins();
                        $i = 0;
                        foreach( $sections as $id => $section ) {
                            $class = '';
                            if( $i++ == 0 ) {
                                $class="active";
                            }
                            ?>
                            <li class="<?php echo $class; ?>">
                                <a href = "#<?php echo $id; ?>" >
                                <?php  echo $section ; ?>
                                
                                </a>
                            </li>

                            <?php
                        }
                    
                    ?>
                    
                </ul>
                <div id="wt_licence_coupon_top" >
                    <div class="wt_licence_coupon_content">
                        <div class="wt_sub_tab_container">
                            
                            <div class="wt_sub_tab_content active" id="wtsmartcoupon" >
                                <?php
                                    $status_icon = '';
                                    $plugin_name = 'wtsmartcoupon';
                                    $status = get_option( $plugin_name.'_activation_status' );
                                    
                                    if( !$status ) {
                                        $status_icon = '( <span class="dashicons dashicons-warning"></span> )';
                                    } else {
                                        $status_icon = '( <span class="dashicons dashicons-yes"></span> )';
                                    }
                                    ?>
                                    <h2><?php _e('Smart Coupon','wt-smart-coupons-for-woocommerce-pro'); ?><?php echo $status_icon; ?></h2>
                                    <?php
                                    include(WT_SMARTCOUPON_MAIN_PATH.'/includes/wf_api_manager/html/html-wf-activation-window.php' );
                                ?>
                            </div>

                            <?php do_action('wt_smart_coupon_after_licence_tab_content'); ?>

                        </div>
                    </div>
                </div>
            </div>
            <?php
        }


        function get_licenced_plugins() {
            $plugins = array(
                'wtsmartcoupon' => __('smart coupon', 'wt-smart-coupons-for-woocommerce-pro')
            );

            return apply_filters( 'wt_smart_coupon_licenced_plugins_tab', $plugins );
        }

    }

    $licence =  new Wt_Smart_Coupon_licence();
}