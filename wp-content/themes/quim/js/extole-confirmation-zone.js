/* Start Extole */
(function(c,e,k,l,a){c[e]=c[e]||{};for(c[e].q=c[e].q||[];a<l.length;)k(l[a++],c[e])})
(window,"extole",function(c,e){e[c]=e[c]||function(){e.q.push([c,arguments])}},["createZone"],0);
/* End Extole */


/* From Extole Directory - conversion requires user specific and order specific data */
extole.createZone({
name: 'conversion',
  data: {
    "first_name": orderDetails.customerFirstName, 
    "last_name": orderDetails.customerLastName, 
    "email": orderDetails.customerEmail, 
    "partner_user_id": orderDetails.customerUserId,
    "partner_conversion_id": orderDetails.customerOrderId,
    "cart_value": orderDetails.customerCartTotal, 
    "coupon_code": orderDetails.customerCouponCode 
  }

});

/* From Extole Directory - confirmation requires user specific data only */
extole.createZone({
name: 'confirmation',
  data: {
    "first_name": orderDetails.customerFirstName, 
    "last_name": orderDetails.customerLastName, 
    "email": orderDetails.customerEmail, 
    "partner_user_id": orderDetails.customerUserId
  }

});