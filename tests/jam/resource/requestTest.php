<?php defined('SYSPATH') OR die('No direct script access.');

class Jam_Resource_RequestTest extends Unittest_Resource_Testcase {

	public function test_allowed_methods()
	{
		$this->assertSame(array(
			'GET',
			'POST',
			'PUT',
			'DELETE'
		), Request::$allowed_methods);
	}

}
