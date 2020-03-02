(function( $ ) {
    'use strict';
    
    // send stroe credit
	$(document).ready(function(  ){
		$('#wt_send_credit_submit').on('click',function( e ) {
			e.preventDefault();
			$('div.wt_smart_coupon_credit_message').hide().removeClass('notice-error').removeClass('notice-success');
			var email	= $('#_wt_send_credit_email').val();
			var amount	= $('#_wt_send_credit_amount').val();
			var message	= $('#_wt_send_credit_message').val();
			var individual_use = 0;
			if( $('#_wt_make_coupon_individual_use_only').prop("checked") == true)  {
				individual_use = 1;
			}
			$(this).attr('disabled',true);
			
			var data = {
				'action'        			: 'wt_send_credit_coupon',
				'_wt_send_credit_email'		: email,
				'_wt_send_credit_amount'	: amount,
				'_wt_send_credit_message'	: message,
				'_wt_make_coupon_individual_use_only'	: individual_use,
			};

			
			jQuery.ajax({
				type: "POST",
				url: WTSmartCouponOBJ.ajaxurl,
				data: data,
				success: function (response) {
					$('#wt_send_credit_submit').removeAttr('disabled');

					var result = JSON.parse( response );
					if( result.error == false ) {
						$('div.wt_smart_coupon_credit_message').html( '<p>' + result.message + '</p>'  );
						$('div.wt_smart_coupon_credit_message').addClass('notice-success');
						$('div.wt_smart_coupon_credit_message').show();
						$('#wt_send_credit').trigger("reset");
					} else {
						$('div.wt_smart_coupon_credit_message').html( '<p>' + result.message + '</p>'  );
						$('div.wt_smart_coupon_credit_message').addClass(' notice-error');
						$('div.wt_smart_coupon_credit_message').show();
					}
					$('html, body').animate({ scrollTop: 0 }, 'slow');
					
				},
				error : function(error){ console.log(error) }
			});

		});

		$('#wt_send_credit_preview').on('click',function( e ) {
			e.preventDefault();
			
			var amount = $('#_wt_send_credit_amount').val();
			var message = $('#_wt_send_credit_message').val();
			var currency = $('#_wt_woo_currency').val();
			if( '' == amount  ) {
				$('#_wt_send_credit_amount').focus();
				return false;
			}
			

			$('.credit_amount').html( currency + amount );
			$('.wt-coupon-amount .amount').html( currency + amount);
			if( '' != $.trim( message )) {
				$('.wt_credit_message').html( message );
			}
			$('.wt-coupon-code code').html('coupon code');
			$('.wt_coupon_peview').fadeIn();
			$("html, body").animate({ scrollTop: $(document).height() }, 1000);	
			
		});
    });
    
    // Send store credit

	$('document').ready(function() {
		$('.wt-btn-resend-store-credit').on('click',function(e){
			e.preventDefault();
			var order_id = $(this).attr('order-id');

			var data = {
				'action'		: 'wt_send_store_credit_coupon',
				'_wt_order_id'	: order_id
			};

			jQuery.ajax({
				type: "POST",
				url: WTSmartCouponOBJ.ajaxurl,
				data: data,
				success: function (response) {
					$('.wt-send-status').removeClass('wt_error').removeClass('wt_success');

					var result = JSON.parse( response );
					if( result.error == false ) {
						$('.wt-send-status').addClass('wt_success').html( result.message );
					} else {
						$('.wt-send-status').addClass('wt_error').html( result.message );

					}
				}
			});

		});
    });
    

    /** Store credit notice */
	$('document').ready(function() {
		$('#wt_try_store_credit_now').on('click',function(){
			var data = {
				'action'		: 'wt_store_credit_try_now'
			};

			var parent_wt_notice = $(this).parent('.wt_notice');

			jQuery.ajax({
				type: "POST",
				url: WTSmartCouponOBJ.ajaxurl,
				data: data,
				success: function (response) {
					parent_wt_notice.html( response );
					parent_wt_notice.removeClass('wt_warning').addClass('wt_success')
				}
			});
		});
	});

	$('document').ready(function() {
		$('#store_credit_display_option').on('change',function(){
			var val = $(this).val();
			
			switch( val ) {
				case  'denominations_and_user_specific' : 
					$('input[name="_wt_minimum_store_credit_purchase"]').removeAttr('disabled');
					$('input[name="_wt_maximum_store_credit_purchase"]').removeAttr('disabled');
					$('#store_crdit_denominations').removeAttr('disabled');
					break;
				case 'user_specific_only' : 
					$('#store_crdit_denominations').attr('disabled','disabled');
					$('input[name="_wt_minimum_store_credit_purchase"]').removeAttr('disabled');
					$('input[name="_wt_maximum_store_credit_purchase"]').removeAttr('disabled');
					break;
				case 'denominations_only' :
					$('input[name="_wt_minimum_store_credit_purchase"]').attr('disabled','disabled');
					$('input[name="_wt_maximum_store_credit_purchase"]').attr('disabled','disabled');
					$('#store_crdit_denominations').removeAttr('disabled');
					break;
			}
		});

		$('#store_credit_display_option').trigger('change');
	});


})( jQuery );