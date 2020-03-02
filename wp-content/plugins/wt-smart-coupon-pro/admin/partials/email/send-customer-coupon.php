<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p> <?php  _e( 'Hi there'  ,'wt-smart-coupons-for-woocommerce-pro') ; ?> </p>			
	<p> <?php  printf( __('Congratulations! Here\'s A Coupon! To redeem your discount use coupon code <b>%s</b> during checkout.', 'wt-smart-coupons-for-woocommerce-pro' ), esc_html( $coupon->get_code() ) );?>
</p>
<p><?php _e('What are you waiting for?','wt-smart-coupons-for-woocommerce-pro'); ?> </p>
<p>
	<?php 
  $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon );
  

	echo Wt_Smart_Coupon_Public::get_coupon_html( $coupon,$coupon_data,'email_coupon' );
	?>

</p>




<?php do_action( 'woocommerce_email_footer', $email ); ?>
