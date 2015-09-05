<?php
/**
 * Test Two Factor Sms Class.
 */

class Tests_Class_Two_Factor_Sms extends WP_UnitTestCase {

	protected $provider;

	/**
	 * Set up a test case.
	 *
	 * @see WP_UnitTestCase::setup()
	 */
	public function setUp() {
		parent::setUp();

		$this->provider = Two_Factor_Sms::get_instance();
	}

	/**
	 * Verify an instance exists.
	 * @covers Two_Factor_Sms::get_instance
	 */
	function test_get_instance() {
		global $wp_actions;

		unset( $wp_actions['plugins_loaded'] );

		$this->assertInstanceOf( 'Two_Factor_Sms', $this->provider->get_instance() );
	}

	/**
	 * Verify an instance exists.
	 * @covers Two_Factor_Sms::get_instance
	 */
	function test_get_instance_did_action() {
		global $wp_actions;

		$wp_actions['plugins_loaded'] = 1;

		$this->assertInstanceOf( 'Two_Factor_Sms', $this->provider->get_instance() );
	}

	/**
	 * Verify the label value.
	 * @covers Two_Factor_Sms::get_label
	 */
	function test_get_label() {
		$this->assertContains( 'SMS (Twilio)', $this->provider->get_label() );
	}

	/**
	 * Verify that validate_token validates a generated token.
	 * @covers Two_Factor_Sms::generate_token
	 * @covers Two_Factor_Sms::validate_token
	 */
	function test_generate_token_and_validate_token() {
		$user_id = 1;

		$token = $this->provider->generate_token( $user_id );

		$this->assertTrue( $this->provider->validate_token( $user_id, $token ) );
	}

	/**
	 * Show that validate_token fails for a different user's token.
	 * @covers Two_Factor_Sms::generate_token
	 * @covers Two_Factor_Sms::validate_token
	 */
	function test_generate_token_and_validate_token_false_different_users() {
		$user_id = 1;

		$token = $this->provider->generate_token( $user_id );

		$this->assertFalse( $this->provider->validate_token( $user_id + 1, $token ) );
	}

	/**
	 * Show that a deleted token can't validate for a user.
	 * @covers Two_Factor_Sms::generate_token
	 * @covers Two_Factor_Sms::validate_token
	 * @covers Two_Factor_Sms::delete_token
	 */
	function test_generate_token_and_validate_token_false_deleted() {
		$user_id = 1;

		$token = $this->provider->generate_token( $user_id );
		$this->provider->delete_token( $user_id );

		$this->assertFalse( $this->provider->validate_token( $user_id, $token ) );
	}

	/**
	 * Verify messaged tokens can be validated.
	 * @covers Two_Factor_Sms::generate_and_sms_token
	 */
	function test_generate_and_sms_token() {
		$user = new WP_User( $this->factory->user->create() );

		update_user_meta( $user->ID, Two_Factor_Sms::ACCOUNT_SID_META_KEY,     'AC6de23fc078bf6a68766cb71396bd909f' );
		update_user_meta( $user->ID, Two_Factor_Sms::AUTH_TOKEN_META_KEY,      'e89ae308710c53982fad1d6795a6c75b' );
		update_user_meta( $user->ID, Two_Factor_Sms::SENDER_NUMBER_META_KEY,   '+15005550006' );
		update_user_meta( $user->ID, Two_Factor_Sms::RECEIVER_NUMBER_META_KEY, '+15005550005' );

		$this->assertTrue( $this->provider->generate_and_sms_token( $user ) );
	}

	/**
	 * Verify messaged tokens can be validated.
	 * @covers Two_Factor_Sms::generate_and_sms_token
	 */
	function test_generate_and_sms_token_invalid_data() {
		$user = new WP_User( $this->factory->user->create() );

		update_user_meta( $user->ID, Two_Factor_Sms::ACCOUNT_SID_META_KEY,     'dummydummy' );
		update_user_meta( $user->ID, Two_Factor_Sms::AUTH_TOKEN_META_KEY,      'WordPress!' );
		update_user_meta( $user->ID, Two_Factor_Sms::SENDER_NUMBER_META_KEY,   '+100000000000' );
		update_user_meta( $user->ID, Two_Factor_Sms::RECEIVER_NUMBER_META_KEY, '+810000000000' );

		$this->assertFalse( $this->provider->generate_and_sms_token( $user ) );
	}

	/**
	 * Verify the contents of the authentication page.
	 * @covers Two_Factor_Sms::authentication_page
	 */
	function test_authentication_page() {
		$this->expectOutputRegex('/^\s*<p>A verification code has been sent to the phone number associated with your account\.<\/p>/s');
		$this->expectOutputRegex('/<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Log In"  \/><\/p>\s*$/s');

		$user = new WP_User( $this->factory->user->create() );

		update_user_meta( $user->ID, Two_Factor_Sms::ACCOUNT_SID_META_KEY,     'AC6de23fc078bf6a68766cb71396bd909f' );
		update_user_meta( $user->ID, Two_Factor_Sms::AUTH_TOKEN_META_KEY,      'e89ae308710c53982fad1d6795a6c75b' );
		update_user_meta( $user->ID, Two_Factor_Sms::SENDER_NUMBER_META_KEY,   '+15005550006' );
		update_user_meta( $user->ID, Two_Factor_Sms::RECEIVER_NUMBER_META_KEY, '+15005550005' );

		$this->provider->authentication_page( $user );
	}

	/**
	 * Verify the contents of the authentication page when invalid data are provided.
	 * @covers Two_Factor_Sms::authentication_page
	 */
	function test_authentication_page_invalid_data() {
		$this->expectOutputRegex('/<p>An error occured while sending SMS\.<\/p>/');

		$user = new WP_User( $this->factory->user->create() );

		update_user_meta( $user->ID, Two_Factor_Sms::ACCOUNT_SID_META_KEY,     'dummydummy' );
		update_user_meta( $user->ID, Two_Factor_Sms::AUTH_TOKEN_META_KEY,      'WordPress!' );
		update_user_meta( $user->ID, Two_Factor_Sms::SENDER_NUMBER_META_KEY,   '+100000000000' );
		update_user_meta( $user->ID, Two_Factor_Sms::RECEIVER_NUMBER_META_KEY, '+810000000000' );

		$this->provider->authentication_page( $user );
	}

	/**
	 * Verify the contents of the authentication page when no user is provided.
	 * @covers Two_Factor_Sms::authentication_page
	 */
	function test_authentication_page_no_user() {
		$this->expectOutputString('');

		$this->provider->authentication_page( false );
	}

	/**
	 * Verify that message validation with no user returns false.
	 * @covers Two_Factor_Sms::validate_authentication
	 */
	function test_validate_authentication_no_user_is_false() {
		$this->assertFalse( $this->provider->validate_authentication( false ) );
	}

	/**
	 * Verify that message validation with no user returns false.
	 * @covers Two_Factor_Sms::validate_authentication
	 */
	function test_validate_authentication() {
		$user = new WP_User( $this->factory->user->create() );

		$token = $this->provider->generate_token( $user->ID );
		$_REQUEST['two-factor-sms-code'] = $token;

		$this->assertTrue( $this->provider->validate_authentication( $user ) );

		unset( $_REQUEST['two-factor-sms-code'] );
	}

	/**
	 * Verify that availability returns true.
	 * @covers Two_Factor_Sms::is_available_for_user
	 */
	function test_is_available_for_user() {
		$user = new WP_User( $this->factory->user->create() );

		update_user_meta( $user->ID, Two_Factor_Sms::ACCOUNT_SID_META_KEY,     'AC6de23fc078bf6a68766cb71396bd909f' );
		update_user_meta( $user->ID, Two_Factor_Sms::AUTH_TOKEN_META_KEY,      'e89ae308710c53982fad1d6795a6c75b' );
		update_user_meta( $user->ID, Two_Factor_Sms::SENDER_NUMBER_META_KEY,   '+15005550000' );
		update_user_meta( $user->ID, Two_Factor_Sms::RECEIVER_NUMBER_META_KEY, '+15005550005' );

		$this->assertTrue( $this->provider->is_available_for_user( $user ) );
	}

	/**
	 * Verify that availability returns false when no user provided.
	 * @covers Two_Factor_Sms::is_available_for_user
	 */
	function test_is_available_for_user_no_user() {
		$this->assertFalse( $this->provider->is_available_for_user( false ) );
	}

	/**
	 * Verify that availability returns false when user is not configured.
	 * @covers Two_Factor_Sms::is_available_for_user
	 */
	function test_is_available_for_user_no_setup_user() {
		$user = new WP_User( $this->factory->user->create() );

		delete_user_meta( $user->ID, Two_Factor_Sms::ACCOUNT_SID_META_KEY );
		delete_user_meta( $user->ID, Two_Factor_Sms::AUTH_TOKEN_META_KEY );
		delete_user_meta( $user->ID, Two_Factor_Sms::SENDER_NUMBER_META_KEY );
		delete_user_meta( $user->ID, Two_Factor_Sms::RECEIVER_NUMBER_META_KEY );

		$this->assertFalse( $this->provider->is_available_for_user( $user ) );
	}

	/**
	 * Verify that user profile is displaying.
	 * @covers Two_Factor_Sms::show_user_profile
	 */
	function test_show_user_profile() {
		global $wp_actions;

		$this->expectOutputRegex('/^\s*<div class="twilio" id="twilio-section">\s*<h3>Twilio<\/h3>\s*<table class="form-table">/s');
		$this->expectOutputRegex('/<\/table>\s*<\/div>\s*$/s');

		unset( $wp_actions['user_profile_twilio'] );
		$user = new WP_User( $this->factory->user->create() );

		$this->assertNull( $this->provider->show_user_profile( $user ) );
	}

	/**
	 * Verify that user profile returns null.
	 * @covers Two_Factor_Sms::show_user_profile
	 */
	function test_show_user_profile_did_action() {
		global $wp_actions;

		$wp_actions['user_profile_twilio'] = 1;
		$user = new WP_User( $this->factory->user->create() );

		$this->assertNull( $this->provider->show_user_profile( $user ) );
	}

	/**
	 * Verify that twilio item at user profile is updated.
	 * @covers Two_Factor_Sms::catch_submission
	 */
	function test_catch_submission() {
		$this->markTestIncomplete( 'This test is not implemented yet.' );

		$current_user = wp_get_current_user();
		$new_user = new WP_User( $this->factory->user->create() );
		$new_user->add_cap( 'edit_users' );

		wp_set_current_user( $new_user->ID );

		$_POST['twilio-sms-sid']      = 'dummydummy';
		$_POST['twilio-sms-token']    = 'WordPress!';
		$_POST['twilio-sms-sender']   = '+100000000000';
		$_POST['twilio-sms-receiver'] = '+810000000000';

		$this->provider->catch_submission( $current_user->ID );

		$this->assertSame( $_POST['twilio-sms-sid'],      get_user_meta( $current_user->ID, Two_Factor_Sms::ACCOUNT_SID_META_KEY, true ) );
		$this->assertSame( $_POST['twilio-sms-token'],    get_user_meta( $current_user->ID, Two_Factor_Sms::AUTH_TOKEN_META_KEY, true ) );
		$this->assertSame( $_POST['twilio-sms-sender'],   get_user_meta( $current_user->ID, Two_Factor_Sms::SENDER_NUMBER_META_KEY, true ) );
		$this->assertSame( $_POST['twilio-sms-receiver'], get_user_meta( $current_user->ID, Two_Factor_Sms::RECEIVER_NUMBER_META_KEY, true ) );

		wp_set_current_user( $current_user->ID );
	}

	/**
	 * Verify that submission catcher returns null.
	 * @covers Two_Factor_Sms::catch_submission
	 */
	function test_catch_submission_no_cap() {
		$current_user = wp_get_current_user();
		$new_user = new WP_User( $this->factory->user->create( array(
			'role' => 'subscriber',
		) ) );

		wp_set_current_user( $new_user->ID );

		$this->assertNull( $this->provider->catch_submission( $current_user->ID ) );

		wp_set_current_user( $current_user->ID );
	}
}
