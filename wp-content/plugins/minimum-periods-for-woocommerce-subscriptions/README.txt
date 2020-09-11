=== Minimum Periods for WooCommerce Subscriptions ===
Contributors: wpextend
Tags: WooCommerce, WC, Subscriptions, WooCommerce Subscriptions, ecommerce, wordpress ecommerce
Requires at least: 5.0
Tested up to: 5.5
Stable tag: 5.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 4.0
WC tested up to: 4.5

Configure minimum periods before customers can cancel their WooCommerce Subscription products.

== Description ==

Minimum Periods for WooCommerce Subscriptions allows store owners to disable cancelling of subscriptions that are powered by WooCommerce Subscriptions. This can be done either for on a storewide basis or for each subscription product individually.

== Installation ==

1. Download the .zip file from your WooCommerce account.
2. Go to: WordPress Admin > Plugins > Add New and Upload Plugin with the file you downloaded with Choose File.
3. Install Now and Activate the extension.

== Frequently Asked Questions ==

= Can I configure minimum periods for variable subscriptions products? =

This is possible using the storewide settings. The ability to configure minimum periods on a per variation basis is on our roadmap to develop

= When activating the Minimum Periods for WooCommerce Subscriptions extension on my store, will it affect historical subscriptions as well? =

Yes - The minimum periods are applied to the subscription product and checks whether the orders/renewals up to that point matches with the periods configured.

= Is the initial order counted towards the minimum period? =

Yes - Minimum Periods takes into account the initial order. This means that if you'd like a customer to renew their subscription at least once before being able to cancel their subscription, the minimum period should be set to "2".

== Changelog ==

= 1.1 =
* New: Added support for variable subscriptions
* Fix: PHP Warning if period set to 0
* Tweak: WooCommerce 4.5 Compatibility 

= 1.0.2 =
* Fix: Remove redundant script/style enqueues
* Fix: Ensure plugin action links return on dependency check
* Tweak: Updated WP coding standards

= 1.0.1 =
* Fix: Possible fatal error on WC_Subscriptions_Admin class check
* Tweak: Wordpress 5.5 compatibility
* Tweak: WooCommerce 4.4 compatibility

= 1.0.0 =
* Initial release
