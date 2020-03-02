<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo $email_heading . "\n\n";

// translators: placeholder is the name of the site
$coupon_message =$credit_email_args['message'] ;
$coupons = $credit_email_args['coupon_id'];
$coupons = maybe_unserialize( $coupons  );
$coupon_code_to_display = '';
if( is_array( $coupons ) && !empty($coupons )) {
    foreach( $coupons as $coupon_id ) {
    
        $coupon_title = get_the_title( $coupon_id );
        $coupon = new WC_Coupon( $coupon_title );
        $coupon_code_to_display .=  $coupon->get_code().'  ';
    

    }
} else { 
    $coupon_title = get_the_title( $coupons );
    $coupon = new WC_Coupon( $coupon_title );
    $coupon_code_to_display .=  $coupon->get_code();
    
}


$amount = Wt_Smart_Coupon_Admin::get_formatted_price( $coupon->get_amount() );


printf( __( 'You have just received a new store credit for %s from %s ', 'wt-smart-coupons-for-woocommerce-pro' ), $amount, get_site_url() );


    if( $coupon_message ) {
        _e( $coupon_message );
    } else {
        _e('What are you waiting for?','wt-smart-coupons-for-woocommerce-pro');
    }

    echo  $coupon_code_to_display;

   __e('*T&C apply','wt-smart-coupons-for-woocommerce-pro');

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
