jQuery(function ($) {
    "use strict";
    $('form.checkout').on('change', 'input[name="payment_method"]', function () {
        
        var t = {updateTimer: !1, dirtyInput: !1,
            reset_update_checkout_timer: function () {
                clearTimeout(t.updateTimer)
            }, trigger_update_checkout: function () {
                t.reset_update_checkout_timer(), t.dirtyInput = !1,
                        $(document.body).trigger("update_checkout");
            }
        };
        t.trigger_update_checkout();
    });


    $('.wt_give_away_product_attr').on('change',function(){
        var attributes = {};
        var parent = $(this).closest('.wt_get_away_product');
        $( parent.find( '.wt_give_away_product_attr') ).each( function( index ) {
            attributes[$(this).attr('data-attribute_name')] = $( this ).val();
        });

        var product_id =parent.find('.wt_choose_free_product').attr('prod-id');

        var data = {
            'action'        : 'update_variation_id',
            'attributes'    : attributes,
            'product'       : product_id,
            '_wpnonce'      : WTSmartCouponOBJ.nonces.public
        };

        jQuery.ajax({
			type: "POST",
			async: true,
			url: WTSmartCouponOBJ.ajaxurl,
			data: data,
			success: function (response) {
                parent.find('input[name="variation_id"]').val(response);
                parent.find('input[name="wt_variation_options"]').val(JSON.stringify( attributes ));
            }
        });


    });


    $(document).on('click','.wt_choose_free_product',function( e ) {
        e.preventDefault();
       
        var parent = $(this).closest('.wt_get_away_product');
        var super_parent = $(this).closest('.wt_give_away_products').attr('coupon');
        if( ! super_parent) {
            super_parent = '';
        }
        
        var data = {
            'action'        : 'wt_choose_free_product',
            '_wpnonce'      : WTSmartCouponOBJ.nonces.public,
            'product_id'    : $(this).attr('prod-id'),
            'variation_id'  : ( parent.find('input[name="variation_id"]').val() )? parent.find('input[name="variation_id"]').val() : 0 ,
            'attributes'    : ( parent.find('input[name="wt_variation_options"]').val() )? parent.find('input[name="wt_variation_options"]').val() : '',
            'applied_coupon' : super_parent
        };

        jQuery.ajax({
			type: "POST",
			async: true,
			url: WTSmartCouponOBJ.ajaxurl,
			data: data,
			success: function (response) {
                if( response == 'success') {
                    location.reload();

                } else {
                    alert( response );
                }
            }
        });
    });

    $('document').ready(function(){
        
        $('input[name="wt_coupon_to_do"]').on('change',function() {
            var val = $(this).val();
            if( val == 'gift_to_a_friend' ) {
                $('.gift_to_friend_form').show()
            } else {
                $('.gift_to_friend_form').hide()
            }
        });

        $(document).on("click",'.cart_page.wt-single-coupon.active-coupon, .checkout_page.wt-single-coupon.active-coupon, .my_account.wt-single-coupon.active-coupon',function(){
            var coupon_code = $(this).find('code').text();
            wt_block_node( $( 'div.wt_coupon_wrapper' ) );
            wt_block_node( $( 'div.wt-mycoupons' ) );
            wt_block_node( $( 'div.wt_store_credit' ) );
            
            
            var data = {
                'action'        : 'apply_coupon_on_click',
                'coupon_code'   : coupon_code,
                '_wpnonce'      : WTSmartCouponOBJ.nonces.apply_coupon
            };
    
            $.ajax({
                type: "POST",
                async: true,
                url: WTSmartCouponOBJ.ajaxurl,
                data: data,
                success: function (response) {
                    if ( $( '.woocommerce-cart-form' ).length != 0 ) {
                        update_cart(true);  // need only for cart page
                    }
                    
                    wt_unblock_node( $( 'div.wt_coupon_wrapper' ) );
                    wt_unblock_node($("div.wt-mycoupons"));
                    wt_unblock_node($("div.wt_store_credit"));

                    $( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
                    show_notice( response );
                    $(document.body).trigger("update_checkout");
                    $( document.body ).trigger("applied_coupon");

                    $('html, body').animate({
                        scrollTop: $(".woocommerce").offset().top
                    }, 1000);
                }
            });
        });

        $('.credit_history').on('click',function( e ){ e.stopPropagation(); })

    });


    var show_notice = function( html_element, $target ) {
		if ( ! $target ) {
			$target = $( '.woocommerce-notices-wrapper:first' ) || $( '.cart-empty' ).closest( '.woocommerce' ) || $( '.woocommerce-cart-form' );
		}
		$target.prepend( html_element );
    };
    
    /**
     * Function directly using from cart.js by woocommmerce
     * @param {bool} preserve_notices 
     */
    var update_cart = function( preserve_notices ) {
        var $form = $( '.woocommerce-cart-form' );
        wt_block_node( $form );
        wt_block_node( $( 'div.cart_totals' ) );
        
        

        // Make call to actual form post URL.
        $.ajax( {
            type:     $form.attr( 'method' ),
            url:      $form.attr( 'action' ),
            data:     $form.serialize(),
            dataType: 'html',
            success:  function( response ) {
                update_wc_div( response, preserve_notices );
            },
            complete: function() {
                wt_unblock_node( $form );
                wt_unblock_node( $( 'div.cart_totals' ) );
            }
        } );
    }

    /**
     * function directley used from cart.js by wooocommerce
     * @param { jQuery object } node 
     */
    var wt_block_node = function( node ) {
        node.addClass( 'processing' ).block( {
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        } );
    }
    /**
     * function directley used from cart.js by wooocommerce
     * @param {jQuery object} $node 
     */
    var wt_unblock_node = function( $node ) {
		$node.removeClass( 'processing' ).unblock();
    };
    
    /**
     * 
     * @param {string} html_str 
     * @param {bool} preserve_notices 
     */
    var update_wc_div = function( html_str, preserve_notices ) {
		var $html       = $.parseHTML( html_str );
		var $new_form   = $( '.woocommerce-cart-form', $html );
		var $new_totals = $( '.cart_totals', $html );
		var $notices    = $( '.woocommerce-error, .woocommerce-message, .woocommerce-info', $html );

		// No form, cannot do this.
		if ( $( '.woocommerce-cart-form' ).length === 0 ) {
			window.location.href = window.location.href;
			return;
		}

		// Remove errors
		if ( ! preserve_notices ) {
			$( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
		}

		if ( $new_form.length === 0 ) {
			// If the checkout is also displayed on this page, trigger reload instead.
			if ( $( '.woocommerce-checkout' ).length ) {
				window.location.href = window.location.href;
				return;
			}

			// No items to display now! Replace all cart content.
			var $cart_html = $( '.cart-empty', $html ).closest( '.woocommerce' );
			$( '.woocommerce-cart-form__contents' ).closest( '.woocommerce' ).replaceWith( $cart_html );

			// Display errors
			if ( $notices.length > 0 ) {
				show_notice( $notices );
			}
		} else {
			// If the checkout is also displayed on this page, trigger update event.
			if ( $( '.woocommerce-checkout' ).length ) {
				$( document.body ).trigger( 'update_checkout' );
			}

			$( '.woocommerce-cart-form' ).replaceWith( $new_form );
			$( '.woocommerce-cart-form' ).find( ':input[name="update_cart"]' ).prop( 'disabled', true );

			if ( $notices.length > 0 ) {
				show_notice( $notices );
			}

			update_cart_totals_div( $new_totals );
		}

		$( document.body ).trigger( 'updated_wc_div' );
    };
    /**
     * Function directley using from woocmmerce cart.js
     * @param {string} html_str 
     */
    var update_cart_totals_div = function( html_str ) {
		$( '.cart_totals' ).replaceWith( html_str );
		$( document.body ).trigger( 'updated_cart_totals' );
    };
    

    $('document').ready(function(){
        
        $('input[name="wt_credit_coupon_to_do"]').on('change',function() {
            var val = $(this).val();
            if( val == 'credit_gift_to_a_friend' ) {
                $('.credit_gift_to_friend_form').show();
            } else {
                $('.credit_gift_to_friend_form').hide();
            }
        });
    });
    
});


