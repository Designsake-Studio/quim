(function( $ ) {
    'use strict';

    $('document').ready(function(){
        $('.wt_dismissable').on('click',function( e ) {
            e.preventDefault();
            e.stopPropagation();
            var item = $(this).parents('.wt_banner');
            item.hide();
        });
        

        $('.wt_apply_coupon_banner').on('click',function( e ){
            e.preventDefault();
            var coupon = $(this).attr('coupon');
            var redirect = $(this).attr('redirect');
            if( typeof(redirect) != "undefined" && '' != redirect ) {
                window.location.href = redirect;
                return;
            }
            var data = {
                'action'        : 'apply_coupon_on_click',
                'coupon_code'   : coupon,
                '_wpnonce'      : WTSmartCouponBannerOBJ.nonce
            };

    
            $.ajax({
                type: "POST",
                async: true,
                url: WTSmartCouponBannerOBJ.ajaxurl,
                data: data,
                success: function ( response ) {
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
    
})( jQuery );




function wt_get_timer_content( expiry_date,style,timer_divition_json,selector ) {
    
    var timer_divitions = JSON.parse( timer_divition_json );
    // Update the count down every 1 second
    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = expiry_date * 1000 - now;
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));

        var days_string = `
            <div class=" wt_timer timer-day">
                <div class="wt_time_entry">`;
        var sNumber = days.toString();
        if( sNumber.length == 1 ) {
            days_string += `<span style= ` + style + `" >0</span>` ;

        }
        for (var i = 0, len = sNumber.length; i < len; i += 1) {
            days_string += `<span   style="` + style + `" >` +sNumber.charAt(i) + `</span>` ;
        }
        days_string += `
                </div>
                <div class="wt_time_details">
                    <small>` + timer_divitions.days + `</small>
                </div>
            </div>`;

        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

        var hours_string = `
            <div class=" wt_timer timer-hours">
                <div class="wt_time_entry">`;
        var sNumber = hours.toString();
        if( sNumber.length == 1 ) {
            hours_string += `<span style=" ` + style + ` " >0</span>` ;

        }
        for (var i = 0, len = sNumber.length; i < len; i += 1) {
            hours_string += `<span  style=" ` + style + ` "  >` + sNumber.charAt(i) + `</span>` ;
        }
        hours_string += `
                </div>
                <div class="wt_time_details">
                    <small>` + timer_divitions.hour + `</small>
                </div>
            </div>`;


        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

        var minutes_string = `
            <div class=" wt_timer timer-minutes">
                <div class="wt_time_entry">`;
        var sNumber = minutes.toString();
        if( sNumber.length == 1 ) {
            minutes_string += `<span   style="` + style + `" >0</span>` ;

        }
        for (var i = 0, len = sNumber.length; i < len; i += 1) {
            minutes_string += `<span  style="` + style + ` " >` + sNumber.charAt(i) + `</span>` ;
        }
        minutes_string += `
                </div>
                <div class="wt_time_details">
                    <small>` + timer_divitions.minutes + `</small>
                </div>
            </div>`;
            
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        var seconds_string = `
            <div class=" wt_timer timer-seconds">
                <div class="wt_time_entry">`;
        var sNumber = seconds.toString();
        if( sNumber.length == 1 ) {
            seconds_string += `<span  style=" ` + style + `" >0</span>` ;

        }
        for (var i = 0, len = sNumber.length; i < len; i += 1) {
            seconds_string += `<span  style=" ` + style + ` " >` + sNumber.charAt(i) + `</span>` ;
        }
        seconds_string += `
                </div>
                <div class="wt_time_details">
                    <small>` + timer_divitions.seconds + `</small>
                </div>
            </div>`;
        document.getElementById( selector ).innerHTML = days_string + hours_string + minutes_string + seconds_string;

        if (distance < 0) {
            clearInterval(x);
            document.getElementById( selector ).innerHTML =  timer_divitions.expired ;
        }
    }, 1000 );
}