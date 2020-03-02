<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p> <?php  _e( 'Hi there'  ,'wt-smart-coupons-for-woocommerce-pro') ; ?> </p>	
<p>
	<?php
	// translators: placeholder is the name of the site
	printf( __( 'We noticed you left something behind. No need to worry - we have saved the items in your %s. Get them before they are gone! ', 'wt-smart-coupons-for-woocommerce-pro' ),'<a href="'.wc_get_cart_url().'">'.__('cart','wt-smart-coupons-for-woocommerce-pro').'</a>'  );
  
?>
</p>

<p>
<?php
	printf( __( 'Use the following coupon code to avail an offer on your order.', 'wt-smart-coupons-for-woocommerce-pro' )  );

?>
</p>


<?php


    $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon );?>
    <p><?php  echo Wt_Smart_Coupon_Public::get_coupon_html( $coupon,$coupon_data,'email_coupon' ); ?></p>


<?php do_action( 'woocommerce_email_footer', $email ); ?>
