<?php
/**
 * WooCommerce Square
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@woocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Square to newer
 * versions in the future. If you wish to customize WooCommerce Square for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-square/
 *
 * @author    WooCommerce
 * @copyright Copyright: (c) 2019, Automattic, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace WooCommerce\Square\Gateway;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_0 as Framework;

class Card_Handler extends Framework\SV_WC_Payment_Gateway_Payment_Tokens_Handler {


	/**
	 * Tokenizes the current payment method and adds the standard transaction
	 * data to the order post record.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Order $order order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Create_Payment_Token_Response|null $response payment token API response, or null if the request should be made
	 * @param string $environment_id optional environment ID, defaults to the current environment
	 * @return \WC_Order order object
	 * @throws Framework\SV_WC_Plugin_Exception on transaction failure
	 */
	public function create_token( \WC_Order $order, $response = null, $environment_id = null ) {

		$order = parent::create_token( $order, $response, $environment_id );

		// remove the verification token that was used to store the card so it's not also sent in the payment request
		$order->payment->verification_token = null;

		return $order;
	}


	/**
	 * Determines if a token should be deleted locally after a failed API attempt.
	 *
	 * Checks the response code, and if Square indicates the card ID was not found then it's probably safe to delete.
	 *
	 * @since 2.0.0
	 *
	 * @param Framework\SV_WC_Payment_Gateway_Payment_Token $token
	 * @param Framework\SV_WC_Payment_Gateway_API_Response $response
	 * @return bool
	 */
	public function should_delete_token( Framework\SV_WC_Payment_Gateway_Payment_Token $token, Framework\SV_WC_Payment_Gateway_API_Response $response ) {

		return 'NOT_FOUND' === $response->get_status_code();
	}


}
