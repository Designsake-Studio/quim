<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists ( 'WT_Smart_Coupon_Store_Credit_Email' ) ) {
	class WT_Smart_Coupon_Store_Credit_Email extends WC_Email{

		
		function __construct() {
			$this->id             = 'wt_smart_coupon_store_credit';
			$this->title          = __( 'You have received a Store Credit ', 'wt-smart-coupons-for-woocommerce-pro' );
			$this->description    = __( 'This email will be sent to customers when they purchase the store credit or when the admin mails a store credit manually.', 'wt-smart-coupons-for-woocommerce-pro' );
			$this->customer_email = true;

			$this->heading        = __( 'You have received a Store Credit', 'wt-smart-coupons-for-woocommerce-pro' );
			$this->subject        = sprintf(_x('You have received a Store credit coupon', 'default email subject for active emails sent to the customer', 'wt-smart-coupons-for-woocommerce-pro') );
			$enabled_customizing_store_Credit =  Wt_Smart_Coupon_Customisable_Gift_Card::is_extended_store_credit_enebaled( );
			if( ! $enabled_customizing_store_Credit ) {
				$this->template_html  = 'email/send-store-credit-coupon.php';
				$this->template_plain = 'email/plain/send-store-credit.php';
			} else {
				$this->template_html  = 'email/send-store-credit-coupon-template.php';
				$this->template_plain = 'email/plain/send-store-credit-coupon.php';
			}
				
			
			$this->template_base = WT_SMARTCOUPON_MAIN_PATH . 'admin/store-credit/';

			// Triggers for this email
			add_action( 'wt_send_store_credit_coupon_to_customer', array( $this, 'trigger' ),10,1);

			// We want all the parent's methods, with none of its properties, so call its parent's constructor
			WC_Email::__construct();
		}

		
		function trigger( $credit_email_args ) {

			$this->recipient =$credit_email_args['send_to'];
			$this->object = $credit_email_args;
			if (!$this->is_enabled() || !$this->get_recipient()) {
				return;
			}
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		}

		
		
		
		function get_content_html() {
			ob_start();
			wc_get_template(
				$this->template_html,
				array(
					'credit_email_args'	=> $this->object,
					'email_heading' 	=> $this->get_heading(),
					'sent_to_admin' 	=> false,
					'plain_text'    	=> false,
					'email'         	=> $this,
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
					'credit_email_args'	=> $this->object,
					'email_heading' 	=> $this->get_heading(),
					'sent_to_admin' 	=> false,
					'plain_text'    	=> true,
					'email'         	=> $this,
				),
				'',
				$this->template_base
			);
			return ob_get_clean();
		}
	}
}