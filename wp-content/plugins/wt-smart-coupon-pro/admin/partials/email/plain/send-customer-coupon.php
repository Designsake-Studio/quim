<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo $email_heading . "\n\n";

// translators: placeholder is the name of the site
printf( __( 'Hi there to redeem your discount use coupon code %s during checkout.', 'wt-smart-coupons-for-woocommerce-pro' ), esc_html( $coupon->get_code() ) );
	

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
