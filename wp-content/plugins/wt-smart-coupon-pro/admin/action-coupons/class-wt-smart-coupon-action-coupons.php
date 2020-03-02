<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'Wt_Smart_Coupon_Action_coupons' ) ) {
    class Wt_Smart_Coupon_Action_coupons {

        function __construct() {
            add_filter( 'wt_smart_coupon_admin_tab_items', array($this,'add_action_coupon_tab'), 20, 1 );            
            add_action('wt_smart_coupon_tab_content_action-coupon', array($this,'action_coupon_page_content'),10);
        }

        function add_action_coupon_tab( $admin_tabs ) {
            $admin_tabs['action-coupon'] =__('Action Coupon','wt-smart-coupons-for-woocommerce-pro');
            return $admin_tabs;
        }


        function action_coupon_page_content() {
            ?>
            <div  class="meta-box-sortables ui-sortable">
                <ul class="wt_sub_tab">
                            
                    <?php
                        do_action('wt_before_action_coupon_tabs');
                        $sections = $this->get_action_coupon_items();
                        $active_tab = get_transient( 'wt_active_tab_action_coupon' );
                        $i = 0;
                        foreach( $sections as $id => $section ) {
                            $class = '';
                            if( isset( $active_tab ) && $active_tab ) {
                                if( $active_tab == $id ) {
                                    $class="active";
                                }
                            } elseif( $i++ == 0 ) {
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
                <div id="wt-action-coupon-page">
                    <div class="wt_action_coupon_content">
                        <div class="wt_sub_tab_container">
                            <?php
                                do_action('wt_action_coupon_page_content');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }


        function get_action_coupon_items(  ) {
            $action_coupon_items = array(
                
            );

            return apply_filters( 'wt_smart_coupon_action_coupon_items', $action_coupon_items );
        }
    }

    $action_coupons  = new Wt_Smart_Coupon_Action_coupons();
}