<?php
/**
 * Generate Random 6-Digit OTP to customer.
 *
 * @package otp-verification-gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'OVG_OTP_Processing' ) ) {

	/**
	 * Class OVG_OTP_Processing
	 */
	class OVG_OTP_Processing {

		/**
		 * Returning OTP
		 *
		 * @return int $rand;
		 */
		public function generates_otp() {
			$rand = '';

			for ( $i = 0; $i < 6; $i++ ) {
				$rand .= wp_rand( 0, 9 );
			}
			return $rand;
		}

	}

}
