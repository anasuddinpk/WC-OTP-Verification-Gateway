<?php
/**
 * Main Loader
 *
 * @package otp-verification-gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'OVG_Loader' ) ) {
	/**
	 * Class OVG_Loader
	 */
	class OVG_Loader {

		/**
		 *  Constructor.
		 */
		public function __construct() {
			$this->includes();
		}

		/**
		 * Includes files depend on platform
		 */
		public function includes() {
			include 'class-ovg-woocommerce-otp-gateway.php';
			include 'class-ovg-mail-otp-on-checkout.php';
			include 'class-ovg-otp-processing.php';
			include 'class-ovg-otp-checking.php';
			include 'class-ovg-checkout-redirection.php';
		}
	}

	new OVG_Loader();
}

