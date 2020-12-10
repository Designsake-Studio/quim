<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpextend.io
 * @since      1.0.0
 *
 * @package    Minimum_Periods_For_Woocommerce_Subscriptions
 * @subpackage Minimum_Periods_For_Woocommerce_Subscriptions/admin/partials
 */

?>

<div class="options_group show_if_subscription allow_cancelling_group hidden">
	<p class="form-field mpws_allow_cancelling_field">
		<label for="mpws_allow_cancelling"><?php esc_html_e( 'Cancelling', 'minimum-periods-for-woocommerce-subscriptions' ); ?></label>
		<select class="select short mpws_allow_cancelling" id="mpws_allow_cancelling" name="mpws_allow_cancelling">
			<option value="use-storewide" <?php selected( 'use-storewide', get_post_meta( $post->ID, 'mpws_allow_cancelling', true ), true ); ?>><?php esc_html_e( 'Use storewide settings', 'minimum-periods-for-woocommerce-subscriptions' ); ?></option>
			<option value="override-storewide" <?php selected( 'override-storewide', get_post_meta( $post->ID, 'mpws_allow_cancelling', true ), true ); ?>><?php esc_html_e( 'Override storewide settings', 'minimum-periods-for-woocommerce-subscriptions' ); ?></option>
		</select>
	</p>

	<p class="form-field mpws_allow_cancelling_periods_field">
		<label for="mpws_allow_cancelling_periods"><?php esc_html_e( 'Minimum Period(s)', 'minimum-periods-for-woocommerce-subscriptions' ); ?>
			<span class="woocommerce-help-tip" data-tip="<?php esc_attr_e( 'Set 0 to allow subscribers to cancel immediately.', 'minimum-periods-for-woocommerce-subscriptions' ); ?>"></span>
		</label>
		<input type="number" class="wc_input_price short" name="mpws_allow_cancelling_periods" id="mpws_allow_cancelling_periods" min="0" step="1" placeholder="<?php esc_attr_e( 'e.g. 2', 'minimum-periods-for-woocommerce-subscriptions' ); ?>" value="<?php echo esc_attr( metadata_exists( 'post', $post->ID, 'mpws_allow_cancelling_periods' ) ? get_post_meta( $post->ID, 'mpws_allow_cancelling_periods', true ) : '0' ); ?>"/>
		<span class="description"><?php esc_html_e( 'periods/orders before cancellation is possible.' ); ?></span>
	</p>
</div>
