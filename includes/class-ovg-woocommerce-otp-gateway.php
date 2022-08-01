<?php
/**
 * Add OTP Verification Gateway to WC payment gateways.
 *
 * @package otp-verification-gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_payment_gateways', 'adds_otp_verification_gateway_class' );

/**
 * Add OVG_Woocommerce_OTP_Gateway class to Gateway classes.
 *
 * @param Array $gateways Payment Gateway classes.
 * @return Array $gateways Payment Gateway classes.
 */
function adds_otp_verification_gateway_class( $gateways ) {
	$gateways[] = 'OVG_Woocommerce_OTP_Gateway';
	return $gateways;
}

add_action( 'plugins_loaded', 'plugin_init_gateway_class' );

	/**
	 * Define OVG_Woocommerce_OTP_Gateway Class.
	 */
function plugin_init_gateway_class() {

	if ( ! class_exists( 'OVG_Woocommerce_OTP_Gateway' ) ) {
		/**
		 * Class OVG_Woocommerce_OTP_Gateway
		 */
		class OVG_Woocommerce_OTP_Gateway extends WC_Payment_Gateway {

			/**
			 * Constructor.
			 */
			public function __construct() {

				$this->id                 = 'otp_verification_gateway';
				$this->has_fields         = true;
				$this->method_title       = 'OTP Verification Gateway';
				$this->method_description = 'Send OTP to customer email on order\' place for verification.';
				// Method with all the options fields.
				$this->init_form_fields();

				// Load the settings.
				$this->init_settings();
				$this->title       = $this->get_option( 'title' );
				$this->description = $this->get_option( 'description' );
				$this->enabled     = $this->get_option( 'enabled' );
				$this->shop_id     = $this->get_option( 'shop_id' );

				// Action hook saves the OTP Gateway settings.
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

			}

			/**
			 * Adds OTP Gateway setting fields.
			 */
			public function init_form_fields() {

				$this->form_fields = array(
					'enabled'     => array(
						'title'   => 'Enable/Disable',
						'label'   => 'Enable OTP Verification Gateway',
						'type'    => 'checkbox',
						'default' => 'no',
					),
					'title'       => array(
						'title'       => 'Title',
						'type'        => 'text',
						'description' => __( 'This controls the title which the user sees during checkout.', 'otp-verification-gateway' ),
						'default'     => __( 'OTP Verification', 'otp-verification-gateway' ),
						'desc_tip'    => true,
					),
					'description' => array(
						'title'       => __( 'Description', 'otp-verification-gateway' ),
						'type'        => 'textarea',
						'description' => __( 'This controls the description which the user sees during checkout.', 'otp-verification-gateway' ),
						'default'     => __( 'Verify OTP on order place.', 'otp-verification-gateway' ),
					),
				);
			}

			/**
			 * Creates OTP email field on OTP Checkout.
			 */
			public function payment_fields() {

				woocommerce_form_field(
					'Email_for_OTP',
					array(
						'type'  => 'email',
						'class' => array( 'transaction_type form-row-wide' ),
						'label' => __( 'Enter your email', 'otp-verification' ),
					),
					''
				);

			}

			/**
			 * Validates OTP email value on OTP Checkout.
			 *
			 * @return boolean
			 */
			public function validate_fields() {
				global $woocommerce;

				if ( ! isset( $_POST['Email_for_OTP'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wc_add_notice( __( 'Email for OTP verification is a required field.', 'otp-verification' ), 'error' );
					return false;
				} else {
					if ( ! filter_var( wp_unslash( $_POST['Email_for_OTP'] ), FILTER_VALIDATE_EMAIL ) ) {  // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						wc_add_notice( __( 'Invalid email address for OTP Verification.', 'otp-verification' ), 'error' );
						return false;
					}
				}

				return true;
			}

			/**
			 * Sets order on on-hold status, updates OTD code & email on OTP Gateway Checkout.
			 *
			 * @param String $order_id Order's ID.
			 */
			public function process_payment( $order_id ) {

				$order = wc_get_order( $order_id );

				$order->update_status( 'on-hold', __( 'Awaiting for OTP Verification,', 'otp-verification-gateway' ) );

				$order->reduce_order_stock();

				WC()->cart->empty_cart();

				// Instantiating OVG_OTP_Processing class.
				$otp_processing_obj = new OVG_OTP_Processing();
				// Updating orders meta with OTP.
				update_post_meta( $order_id, 'OTP (Sent)', $otp_processing_obj->generates_otp() );

				// Updating orders meta with customer's OTP email.
				update_post_meta( $order_id, 'Email_for_OTP', sanitize_email( wp_unslash( $_POST['Email_for_OTP'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				// Sending OTP on checkout to customer.
				$mail_class_obj = new OVG_Mail_OTP_On_Checkout();
				$mail_class_obj->sends_otp_to_customer( $order_id );

				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				);
			}

		}
	}
}
