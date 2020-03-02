jQuery(function ($) {
    "use strict";

    /** design store credit */
   
    $('document').ready( function() {
        $('.wt_gift_coupn_designs li img').on('click',function(){
            var image = $(this).attr('src');
            var design = $(this).attr('design');
            var top_bg_colog = $(this).attr('top_bg');
            var bottom_bg_colog = $(this).attr('bottom_bg');
            $('.wt_gift_coupn_designs li').removeClass('active');
            $(this).parent('li').addClass('active');

            $('.wt_gift_coupon_preview_image img').attr('src',image);
            $('.wt_gift_coupon_preview_image img').attr('alt',design);
            $('.wt_gift_coupon_preview_caption').css('background-color',top_bg_colog);
            $('.coupon-message-block').css('background-color',bottom_bg_colog);
            $('#wt_credit_coupon_image').val(design);
        });

        $('#wt_credit_amount').on('change',function (){
            $('.wt_coupon-code-block .coupon_price span').text( $(this).val());
        });

        $('#wt_credit_coupon_send_to_message').on('change',function(){
            $('.coupon-message-block .coupon-message').text( $(this).val());
        });
        $('#wt_credit_coupon_send_to_message').on('change',function(){
            $('.coupon-message-block .coupon-message').text( $(this).val());
        });
        $('#wt_credit_coupon_from').on('change',function(){
            $('.coupon-message-block .coupon-from span').text( $(this).val());
        });

    });

    $('input[name="credit_denominaton"], #wt_user_credit_amount').on('change',function(){
        var val = $(this).val();
        $('#wt_credit_amount').val(val);
        $("#wt_credit_amount").trigger("change");
    });


});