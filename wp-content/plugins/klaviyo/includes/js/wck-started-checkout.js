/**
 * WCK Started Checkout
 *
 * Incoming event object
 * @typedef {object} kl_checkout
 *   @property {string} email - Email of current logged in user
 *   
 *   @property {object} event_data - Data for started checkout event
 *     @property {object} $extra - Event data
 *     @property {string} $service - Value will always be "woocommerce"
 *     @property {int} $value - Total value of checkout event
 *     @property {array} Categories - Product categories (array of strings)
 *     @property {string} Currency - Currency type
 *     @property {string} CurrencySymbol - Currency type symbol
 *     @property {array} ItemNames - List of items in the cart
 *
 */


/**
 * Attach event listeners to save billing fields.
 */
function klIdentifyBillingField() {
  var billingFields = ["first_name", "last_name"];
  for (var i=0; i<billingFields.length; i++) {
    (function() {
      var nameType = billingFields[i];
      jQuery('input[name="billing_' + nameType + '"]').change(function () {
        var email = jQuery('input[name="billing_email"]').val();
        if (email) {
          var elem = jQuery(this),
              nameValue = jQuery.trim(elem.val());
          _learnq.push(["identify", {[nameType]: nameValue}]);
        }
      })
    })();
  }
}

window.addEventListener("load", function() {
  // Custom checkouts/payment platforms may still load this file but won't
  // fire woocommerce_after_checkout_form hook to load checkout data.
  if (typeof kl_checkout === 'undefined') {
    return;
  }

  var _learnq = window._learnq || [];

  var WCK = WCK || {};
  WCK.trackStartedCheckout = function () {
    _learnq.push(["track", "$started_checkout", kl_checkout.event_data ]);
  };

  // Priority of emails for syncing Started Checkout event: Logged-in user,
  // cookied, billing email address
  if (kl_checkout.email !== "") {
    _learnq.push(["identify", {"$email" : kl_checkout.email}]);
    WCK.trackStartedCheckout();
  } else if (_learnq.identify().$email !== undefined) {
    WCK.trackStartedCheckout();
  } else {
    if (jQuery) {
      jQuery('input[name="billing_email"]').change(function () {
        var elem = jQuery(this),
        email = jQuery.trim(elem.val());

        if (email && /@/.test(email)) {
          var params = {"$email" : email};
          var first_name = jQuery('input[name="billing_first_name"]').val();
          var last_name = jQuery('input[name="billing_last_name"]').val();
          if (first_name) {
            params["first_name"] = first_name;
          }
          if (last_name) {
            params["last_name"] = last_name;
          }

          _learnq.push(["identify", params]);
          WCK.trackStartedCheckout();
        }
      });

      // Save billing fields
      klIdentifyBillingField();
    }
  }
});