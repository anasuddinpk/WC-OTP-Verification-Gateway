<?php
/**
 * Plugin Name: OTP Verification Gateway
 * Plugin URI: https://www.linkedin.com/in/anasuddinpk/
 * Description: Made for verifying orders by sending OTPs via emails.
 * Version: 1.1.1.0
 * Author: Anas Uddin
 * Author URI: https://www.linkedin.com/in/anasuddinpk/
 * Text Domain: otp-verification
 *
 * @package otp-verification-gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'OVG_PLUGIN_DIR' ) ) {
	define( 'OVG_PLUGIN_DIR', __DIR__ );
}

if ( ! defined( 'OVG_PLUGIN_DIR_URL' ) ) {
	define( 'OVG_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'OVG_ABSPATH' ) ) {
	define( 'OVG_ABSPATH', dirname( __FILE__ ) );
}

require_once OVG_ABSPATH . '/includes/class-ovg-loader.php';
