<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpextend.io
 * @since      1.0.0
 *
 * @package    Minimum_Periods_For_Woocommerce_Subscriptions
 * @subpackage Minimum_Periods_For_Woocommerce_Subscriptions/admin
 */
class Minimum_Periods_For_Woocommerce_Subscriptions_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version           The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Add plugin action links to settings area
	 *
	 * @since    1.0.0
	 * @param    array $links       The array of plugin actions links.
	 */
	public function mpws_add_action_links( $links ) {
		if ( class_exists( 'WC_Subscriptions_Admin' ) && class_exists( 'WC_Subscriptions' ) ) {
			$settings_link = array(
				'<a href="' . WC_Subscriptions_Admin::settings_tab_url() . '#mpws_cancelling_heading">' . __( 'Settings', 'minimum-periods-for-woocommerce-subscriptions' ) . '</a>',
			);
			return array_merge( $settings_link, $links );
		} else {
			return $links;
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/minimum-periods-for-woocommerce-subscriptions-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/minimum-periods-for-woocommerce-subscriptions-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Check that WooCommerce and WooCommerce Subscriptions are installed
	 *
	 * @since    1.0.0
	 */
	public function mpws_plugins_loaded() {

		if ( true !== $this->mpws_plugin_dependencies_met() ) {
			return;
		}
	}

	/**
	 * Display admin notice when WooCommerce or WooCommerce Subscriptions are not active.
	 *
	 * @since    1.0.0
	 */
	public function mpws_plugin_dependencies_notice() {
		$return = $this->mpws_plugin_dependencies_met( true );

		if ( true !== $return && current_user_can( 'activate_plugins' ) ) {
			$dependency_notice = $return;
			printf( '<div class="error"><p>%s</p></div>', wp_kses_post( $dependency_notice ) );
		}
	}

	/**
	 * Check whether the plugin dependencies met.
	 *
	 * @since    1.0.0
	 * @param    boolean $return_dep_notice       The state of dependent plugin installations/activations.
	 */
	private function mpws_plugin_dependencies_met( $return_dep_notice = false ) {
		$return = false;

		if ( ! class_exists( 'WC_Subscriptions' ) ) {
			if ( $return_dep_notice ) {
				/* translators: %1$s: Opening Strong Tag, %2$s: Closing Strong Tag, %3$s: Opening WooCommerce Subscriptions link tag, %4$s: Closing link tag */
				$return = sprintf( esc_html__( '%1$sMinimum Periods for WooCommerce Subscriptions is inactive.%2$s The %3$sWooCommerce Subscriptions plugin%4$s must be active for Minimum Periods to work.', 'minimum-periods-for-woocommerce-subscriptions' ), '<strong>', '</strong>', '<a href="http://woocommerce.com/products/woocommerce-subscriptions/" target="_blank">', '</a>' );
			}

			return $return;
		}

		if ( ! function_exists( 'WC' ) ) {
			if ( $return_dep_notice ) {
				$install_url = wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'install-plugin',
							'plugin' => 'woocommerce',
						),
						admin_url( 'update.php' )
					),
					'install-plugin_woocommerce'
				);
				/* translators: %1$s: Opening Strong Tag, %2$s: Closing Strong Tag, %3$s: Opening WooCommerce link tag, %4$s: Closing link tag */
				$return = sprintf( esc_html__( '%1$sMinimum Periods for WooCommerce Subscriptions is inactive.%2$s The %3$sWooCommerce plugin%4$s must be active for Minimum Periods to work.', 'minimum-periods-for-woocommerce-subscriptions' ), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/" target="_blank">', '</a>' );
			}

			return $return;
		}
		return true;
	}

	/**
	 * Register the new "Cancelling" section in WooCommerce Subscriptions Options
	 *
	 * @since    1.0.0
	 * @param    array $settings       The settings array in WooCommerce Subscriptions.
	 */
	public static function mpws_subscription_settings( $settings ) {

		$misc_section_start = wp_list_filter(
			$settings,
			array(
				'id'   => 'woocommerce_subscriptions_miscellaneous',
				'type' => 'title',
			)
		);

		array_splice(
			$settings,
			key( $misc_section_start ),
			0,
			array(
				array(
					'name' => _x( 'Cancelling', 'options section heading', 'minimum-periods-for-woocommerce-subscriptions' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'mpws_cancelling',
				),
				array(
					'name'    => __( 'Enable Cancelling', 'minimum-periods-for-woocommerce-subscriptions' ),
					'id'      => 'mpws_allow_cancelling',
					'default' => 'yes',
					'type'    => 'checkbox',
					'desc'    => __( 'Allow subscribers to cancel their subscriptions', 'minimum-periods-for-woocommerce-subscriptions' ),
				),
				array(
					'name'              => __( 'Default Minimum Period(s)', 'minimum-periods-for-woocommerce-subscriptions' ),
					'id'                => 'mpws_allow_cancelling_periods',
					'default'           => '1',
					'type'              => 'number',
					'placeholder'       => __( 'e.g. 2', 'woocommerce-subscriptions-advanced-options' ),
					'desc_tip'          => __( 'The minimum number of periods/orders will be used store-wide for all subscription products.', 'minimum-periods-for-woocommerce-subscriptions' ),
					'desc'              => __( 'The number of periods/orders to be completed before a customer can cancel their subscription on their my-account page. Leave blank if this will be configured on a per-product basis.', 'minimum-periods-for-woocommerce-subscriptions' ),
					'custom_attributes' => array(
						'step' => '1',
						'min'  => '0',
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'mpws_cancelling',
				),
			)
		);

		return $settings;
	}

	/**
	 * Register the new "Cancelling" section in each Subscription product
	 */
	public static function mpws_admin_edit_product_fields() {
		if ( 'yes' === get_option( 'mpws_allow_cancelling', 'yes' ) ) {
			global $post;
			require_once plugin_dir_path( __FILE__ ) . 'partials/minimum-periods-for-woocommerce-subscriptions-admin-display.php';
		}
	}

	/**
	 * Save subscription meta.
	 *
	 * @since    1.0.0
	 * @param    integer $product_id       The id of the subscription where cancellation is set.
	 */
	public static function mpws_save_subscription_meta( $product_id ) {
		if ( empty( $_POST['_wcsnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wcsnonce'] ) ), 'wcs_subscription_meta' ) ) {
			return;
		}

		if ( isset( $_POST['mpws_allow_cancelling_periods'] ) ) {
			update_post_meta( $product_id, 'mpws_allow_cancelling_periods', is_numeric( $_POST['mpws_allow_cancelling_periods'] ) ? absint( wp_unslash( $_POST['mpws_allow_cancelling_periods'] ) ) : '1' );
		}

		if ( isset( $_POST['mpws_allow_cancelling'] ) ) {
			update_post_meta( $product_id, 'mpws_allow_cancelling', sanitize_key( wp_unslash( $_POST['mpws_allow_cancelling'] ) ) );
		}
	}

	/**
	 * Adds the feature settings in the product page (when it is a variable subscription product)
	 *
	 * @param int     $loop - The existing settings.
	 * @param array   $variation_data - The variation data.
	 * @param WP_Post $variation - The current variation post object.
	 *
	 * @since 1.1
	 */
	public static function mpws_variable_subscription_pricing_fields( $loop, $variation_data, $variation ) {
		woocommerce_wp_select(
			array(
				'id'            => 'mpws_allow_cancelling[' . $loop . ']',
				'class'         => 'mpws_allow_cancelling select',
				'wrapper_class' => 'form-row mpws_allow_cancelling_field',
				'label'         => __( 'Cancelling', 'woocommerce-subscriptions' ),
				'options'       => array(
					'use-storewide'      => __( 'Use storewide settings' ),
					'override-storewide' => __( 'Override storewide settings' ),
				),
				'value'         => get_post_meta( $variation->ID, 'mpws_allow_cancelling', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'                => "mpws_allow_cancelling_periods[{$loop}]",
				'class'             => 'short',
				'label'             => __( 'Minimum Periods', 'woocommerce-subscriptions-advanced-options' ),
				'placeholder'       => __( 'e.g. 2', 'woocommerce-subscriptions-advanced-options' ),
				'description'       => __( 'The "Cancel" button will disappear from the subscription page until the number of periods have been reached. Leave blank to ignore this feature.', 'woocommerce-subscriptions-advanced-options' ),
				'desc_tip'          => true,
				'type'              => 'number',
				'wrapper_class'     => 'form-row form-row-full mpws_allow_cancelling_periods_variation',
				'value'             => get_post_meta( $variation->ID, 'mpws_allow_cancelling_periods', true ),
				'data_type'         => 'any',
				'custom_attributes' => array(
					'step' => '1',
					'min'  => '0',
				),
			)
		);
	}

	/**
	 * Save meta data for simple subscription product type when the "Edit Product" form is submitted.
	 *
	 * @param int $variation_id - The ID of the current variation.
	 * @param int $index        - The index of the current variation.
	 *
	 * @since 1.1
	 */
	public static function mpws_save_variable_subscription_meta( $variation_id, $index ) {
		// Security check.
		if ( empty( $_POST['_wcsnonce_save_variations'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_wcsnonce_save_variations'] ), 'wcs_subscription_variations' ) || ! isset( $_POST['mpws_allow_cancelling_periods'][ $index ] ) || ! isset( $_POST['mpws_allow_cancelling'][ $index ] ) ) {
			return;
		}

		// Get variation ID to save.
		$product = wc_get_product( $variation_id );

		// Ensure it's a product being saved.
		if ( ! $product ) {
			return;
		}

		// Check whether we should be using variation override or storewide.
		$mpws_allow_cancelling_variation = wc_clean( wp_unslash( $_POST['mpws_allow_cancelling'][ $index ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$product->update_meta_data( 'mpws_allow_cancelling', is_string( $mpws_allow_cancelling_variation ) ? $mpws_allow_cancelling_variation : 'use-storewide' );

		// Save variation minimum period.
		$mpws_allow_cancelling_periods_variation = wc_clean( wp_unslash( $_POST['mpws_allow_cancelling_periods'][ $index ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$product->update_meta_data( 'mpws_allow_cancelling_periods', is_numeric( $mpws_allow_cancelling_periods_variation ) ? absint( $mpws_allow_cancelling_periods_variation ) : '0' );

		// Save the product.
		$product->save();
	}

}
