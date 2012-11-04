<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package Jam Resource
 * @group jam-resource
 * @groupjam-resource.request
 */
class Jam_Resource_RequestTest extends Unittest_Resource_Testcase {

	public function test_allowed_methods()
	{
		$this->assertSame(array(
			'GET',
			'POST',
			'PUT',
			'DELETE',
			'HEAD',
			'TRACE',
			'OPTIONS',
			'CONNECT'
		), Request::$allowed_methods);
	}

}
