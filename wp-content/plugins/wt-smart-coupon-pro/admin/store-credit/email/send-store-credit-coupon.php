<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p> <?php  _e( 'Hi there'  ,'wt-smart-coupons-for-woocommerce-pro') ; ?> </p>	

	<?php
    // translators: placeholder is the name of the site
    $credit_amount = 0;
    $coupon_html = array();
    $coupon_message =$credit_email_args['message'] ;
    $coupons = $credit_email_args['coupon_id'];
    $coupons = maybe_unserialize( $coupons  );


    if( is_array( $coupons ) && !empty($coupons )) {
       
        foreach( $coupons as $coupon_id ) {
		
            $coupon_title = get_the_title( $coupon_id );
            $coupon = new WC_Coupon( $coupon_title );
            $credit_amount+= $coupon->get_amount();
            $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon );
            $coupon_html[] =  Wt_Smart_Coupon_Public::get_coupon_html( $coupon,$coupon_data,'email_coupon' );


        }
    } else { 
        $coupon_title = get_the_title( $coupons );
        

        $amount = Wt_Smart_Coupon_Admin::get_formatted_price( 10 );

        if( ! $coupon_title && $coupons ==0 ) { // for email preview
            $coupon = -1;
            $credit_amount = 10;
            $coupon_data = array(
                'coupon_type'           => __('Store Credit','wt-smart-coupons-for-woocommerce-pro'),
                'coupon_amount'         => $amount,
                'coupon_expires'        => '',
                'email_restriction'     => ''
            );
        } else {
            $coupon = new WC_Coupon( $coupon_title );
            $credit_amount+= $coupon->get_amount();
            $coupon_data  = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon );

        }
        
        $coupon_html[] =  Wt_Smart_Coupon_Public::get_coupon_html( $coupon,$coupon_data,'email_coupon' ); 
    }

   
    $amount = Wt_Smart_Coupon_Admin::get_formatted_price( $credit_amount );
    
    echo '<p>'; 
        printf( __( 'You have just received a new store credit for <span class="credit_amount">%s</span> from %s ', 'wt-smart-coupons-for-woocommerce-pro' ), $amount, get_site_url() );
    echo '</pre>';

    
    

    if( $coupon_message ) {
        ?>
        <p class="wt_credit_message">  <?php  echo  $coupon_message; ?> </p>
        <?php
    } else { ?>
        <p class="wt_credit_message"><?php _e('What are you waiting for?','wt-smart-coupons-for-woocommerce-pro'); ?> </p>
    <?php
    } ?>

    <p>
    <?php
    foreach( $coupon_html as $coupon_item ) {
        echo $coupon_item;
    }
    ?>
    </p>


    <p> <?php  //_e('*T&C apply','wt-smart-coupons-for-woocommerce-pro'); ?></p>

    
<?php do_action( 'woocommerce_email_footer', $email ); ?>
