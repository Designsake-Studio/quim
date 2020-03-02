<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists ( 'WT_smart_Coupon_Gift' ) ) {
	class WT_smart_Coupon_Gift extends WC_Email{

		
		function __construct() {

			$this->id             = 'wt_smart_coupon_gift';
			$this->title          = __( 'You have received a Gift Coupon ', 'wt-smart-coupons-for-woocommerce-pro' );
			$this->description    = __( 'This email will be sent to customers upon completing an order containing a product with a associated gift coupon.', 'wt-smart-coupons-for-woocommerce-pro' );
			$this->customer_email = true;

			$this->heading        = __( 'You have received gift coupons', 'wt-smart-coupons-for-woocommerce-pro' );
			$this->subject        = sprintf(_x('You have received a Gift Coupon', 'default email subject for active emails sent to the customer', 'wt-smart-coupons-for-woocommerce-pro') );
			$this->template_html  = 'email/send-gift-coupon.php';
			$this->template_plain = 'email/plain/send-gift-coupon.php';
			$this->template_base = WT_SMARTCOUPON_MAIN_PATH . 'admin/gift-coupon/';

			// Triggers for this email
			add_action( 'wt_send_gift_coupon_to_customer', array( $this, 'trigger' ),9,2);
			

			// We want all the parent's methods, with none of its properties, so call its parent's constructor
			WC_Email::__construct();
		}

		
		function trigger( $order,$coupons ) {
            $this->recipient = get_post_meta( $order->get_id(), 'wt_coupon_send_to', true );
            
            
            $this->object = $order;
			if (!$this->is_enabled() || !$this->get_recipient()) {
				return;
			}
			$this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
		}

		
		
		
		function get_content_html() {
			ob_start();
			wc_get_template(
				$this->template_html,
				array(
					'order'       	=> $this->object,
					'email_heading' => $this->get_heading(),
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $this,
				),
				'',
				$this->template_base
			);
			return ob_get_clean();
		}

		
		function get_content_plain() {
			ob_start();
			wc_get_template(
				$this->template_plain,
				array(
					'order'       	=> $this->object,
					'email_heading' => $this->get_heading(),
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $this,
				),
				'',
				$this->template_base
			);
			return ob_get_clean();
		}
	}
}