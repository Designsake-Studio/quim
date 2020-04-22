<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


global $current_user, $woocommerce, $wpdb; 





$current_user = wp_get_current_user(); 

$coupons = Wt_Smart_Coupon_Public::get_available_coupons_for_user( $current_user, 'my_account' );


do_action('wt_smart_coupon_before_my_coupons');
if( $coupons ) {


?>
<div class="wt-mycoupons">
        <?php
        $expired_coupon = array();
        foreach ($coupons as $coupon ) {

            $coupon_obj = new WC_Coupon( $coupon->post_title );
            $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon_obj );
            
            
            if( $coupon_data['coupon_expires'] ) {

                $exp_date =  $coupon_data['coupon_expires']->getTimestamp();
                $expire_text = Wt_Smart_Coupon_Public::get_expiration_date_text( $exp_date );
                if( $expire_text == 'expired') {
                    array_push( $expired_coupon,$coupon->post_title );
                    continue;
                }
            }
            if( !empty( $coupon_data['email_restriction'] ) && ( ! is_user_logged_in() || ! in_array( strtolower( $current_user->user_email ),$coupon_data['email_restriction'] ) )  ) {
                continue;
            }
            $coupon_data['display_on_page'] = 'my_account';
            echo Wt_Smart_Coupon_Public::get_coupon_html( $coupon,$coupon_data );

            ?>
               

            <?php
        }
        ?>
</div>
<?php
    
} else {
    ?>
    <div class="wt-mycoupons">
        <?php _e('Sorry, you don\'t have any available coupons' ,'wt-smart-coupons-for-woocommerce-pro'); ?>
    </div>
    <?php
}

do_action('wt_smart_coupon_after_my_coupons',$current_user);
//  Display used Coupons.

$coupon_used  = Wt_Smart_Coupon_Public::get_coupon_used_by_a_customer( $current_user  );

$general_settings_options = Wt_Smart_Coupon_Admin::get_option('wt_copon_general_settings');
$display_used_coupon = true;
 if(   empty( $coupon_used ) ||  ! $general_settings_options['display_used_coupons_my_acount']  ) {
    $display_used_coupon = false;
 }
 $display_expired_coupon = true;
 if(   empty( $expired_coupon ) ||  ! $general_settings_options['display_expired_coupons_my_acount']  ) {
    $display_expired_coupon = false;
 }

?>

    <div class="wt-used-coupons">
        <?php  if(  $display_expired_coupon  || $display_used_coupon ) { ?>
            <h4>  <?php _e("Used / Expired Coupons","wt-smart-coupons-for-woocommerce-pro"); ?></h4>
        <?php } ?>
        <?php
        if( $display_used_coupon ) {
            foreach ($coupon_used as $coupon ) {
                $coupon_post    = get_page_by_title( $coupon,'OBJECT','shop_coupon' );
                if( !$coupon_post || $coupon_post->post_status != 'publish') {
                    continue;
                }

                $coupon_obj = new WC_Coupon( $coupon );
                
                $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon_obj );
                $coupon_data['display_on_page'] = 'my_account';
                echo Wt_Smart_Coupon_Public::get_coupon_html( $coupon_post,$coupon_data,'used_coupon' );

               
            }
        }
        if( $display_expired_coupon  ) {
            foreach ($expired_coupon as $coupon ) {
                $coupon_post    = get_page_by_title( $coupon,'OBJECT','shop_coupon' );
                if( !$coupon_post || $coupon_post->post_status != 'publish') {
                    continue;
                }

                $coupon_obj = new WC_Coupon( $coupon );
                
                $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon_obj );
                $coupon_data['display_on_page'] = 'my_account';
                echo Wt_Smart_Coupon_Public::get_coupon_html( $coupon_post,$coupon_data,'expired_coupon' );
               
            }
        }
        ?>
        <?php do_action('wt_smart_coupon_after_expired_coupons'); ?>
    </div>
<?php
do_action('wt_smart_coupon_after_used_coupons');
