<?php
/**
 * Test Two Factor Sms.
 */

class Tests_Two_Factor_Sms extends WP_UnitTestCase {

	/**
	 * Set up a test case.
	 *
	 * @see WP_UnitTestCase::setup()
	 */
	function setUp() {
		parent::setUp();
	}

	/**
	 * Check that the plugin is active.
	 */
	function test_is_plugin_active() {

		$this->assertTrue( is_plugin_active( 'two-factor-sms/two-factor-sms.php' ) );

	}

	/**
	 * Check that the TWO_FACTOR_PHONE_DIR constant is defined.
	 */
	function test_constant_defined() {

		$this->assertTrue( defined( 'TWO_FACTOR_PHONE_DIR' ) );

	}

	/**
	 * Check that the files were included.
	 */
	function test_classes_exist() {

		$this->assertTrue( class_exists( 'Two_Factor_Phone' ) );

	}

	/**
	 * Add provider to the list.
	 * @covers ::two_factor_sms_init
	 */
	function test_two_factor_sms_init() {
		$this->assertSame(
			array( 'Two_Factor_Sms' => TWO_FACTOR_SMS_DIR . 'class.two-factor-sms.php' ),
			two_factor_sms_init( array() )
		);
	}
}