<?php
/**
 * Plugin Name: Two Factor SMS
 * Plugin URI: https://github.com/shield-9/two-factor-sms
 * Description: Add SMS support to "Two Factor" feature as a plugin
 * Author: Daisuke Takahashi (Extend Wings)
 * Version: 0.1.1
 * Author URI: https://www.extendwings.com
 * Text Domain: two-factor-sms
 * Domain Path: /languages
 */

define( 'TWO_FACTOR_SMS_DIR', plugin_dir_path( __FILE__ ) );

function Two_Factor_Sms_init( $providers ) {
	$providers['Two_Factor_Sms'] = TWO_FACTOR_SMS_DIR . 'class.two-factor-sms.php';

	return $providers;
}

add_filter( 'two_factor_providers', 'Two_Factor_Sms_init' );
