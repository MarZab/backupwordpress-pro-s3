<?php

class Test_Admin_Functions extends WP_UnitTestCase {

	protected $admin;

	function setUp() {

		parent::setUp();

		$this->admin = new \HM\BackUpWordPress\CheckLicense( 'hmbkpp_aws_settings', 'BackUpWordPress To Amazon S3', 'hmbkp_license_data_s3', new HM\BackUpWordPress\Addon( '2.0.4', '3.1.4', 'HM\\BackUpWordPress\\S3BackUpService', HM\BackUpWordPress\Plugin::get_instance() ), new \HM\BackUpWordPress\PluginUpdater() );

	}

	function test_license_is_invalid() {

		$this->assertTrue( $this->admin->is_license_invalid( 'invalid' ) );
	}

	function test_license_is_expired() {
		$this->assertTrue( $this->admin->is_license_expired( 'expired' ) );
	}

	function test_fetch_license_data_invalid() {

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => 'invalidkey',
			'item_name'  => urlencode( $this->admin->edd_download_file_name )
		);

		add_filter( 'pre_http_request', $this->get_http_request_overide( $this->admin->get_api_url( $api_params ),file_get_contents( __DIR__ . '/data/invalid_license.json' )
			), 10, 3 );

		$license_data = $this->admin->fetch_license_data( 'invalidkey' );

		$this->assertInstanceOf( 'stdClass', $license_data );

		$this->assertTrue( $this->admin->is_license_invalid( $license_data->license ) );

	}

	function test_fetch_license_data_valid() {

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => 'validkey',
			'item_name'  => urlencode( $this->admin->edd_download_file_name )
		);

		add_filter( 'pre_http_request', $this->get_http_request_overide( $this->admin->get_api_url( $api_params ),file_get_contents( __DIR__ . '/data/valid_license.json' )
		), 10, 3 );

		$license_data = $this->admin->fetch_license_data( 'validkey' );

		$this->assertInstanceOf( 'stdClass', $license_data );

		$this->assertTrue( ! $this->admin->is_license_invalid( $license_data->license ) );

	}

	function test_fetch_license_data_expired() {

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => 'expiredkey',
			'item_name'  => urlencode( $this->admin->edd_download_file_name )
		);

		add_filter( 'pre_http_request', $this->get_http_request_overide( $this->admin->get_api_url( $api_params ),file_get_contents( __DIR__ . '/data/expired_license.json' )
		), 10, 3 );

		$license_data = $this->admin->fetch_license_data( 'expiredkey' );

		$this->assertInstanceOf( 'stdClass', $license_data );

		$this->assertTrue( $this->admin->is_license_expired( $license_data->license ) );

	}

	private function get_http_request_overide( $matched_url, $response_body ) {

		$func = null;

		return $func = function ( $return, $request, $url ) use ( $matched_url, $response_body, &$func ) {

			remove_filter( 'pre_http_request', $func );

			if ( $url !== $matched_url ) {
				return $return;
			}

			$response = array(
				'headers'  => array(),
				'body'     => $response_body,
				'response' => 200,
			);

			return $response;
		};

	}

}

