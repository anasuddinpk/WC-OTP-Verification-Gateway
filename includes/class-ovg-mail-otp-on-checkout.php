<?php
/**
 * Set, Validate & Update OTP Verification Email field.
 *
 * @package otp-verification-gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'OVG_Mail_OTP_On_Checkout' ) ) {
	/**
	 * Class OVG_Mail_OTP_On_Checkout
	 */
	class OVG_Mail_OTP_On_Checkout {

		/**
		 * Sends random generated OTP to customer.
		 *
		 * @param String $order_id Order's ID.
		 */
		public function sends_otp_to_customer( $order_id ) {

			$test = new OVG_OTP_Processing();

			$to = get_post_meta( $order_id, 'Email_for_OTP', true );

			$subject = 'OTP for Verification for order #' . $order_id;

			$body = 'Your order\'s OTP for verification is: ' . get_post_meta( $order_id, 'OTP (Sent)', true );

			wp_mail( $to, $subject, $body, '' );
		}

	}
}
