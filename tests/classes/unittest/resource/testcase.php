<?php defined('SYSPATH') OR die('No direct script access.');

class Unittest_Resource_Testcase extends Unittest_Testcase {

	public function setUp()
	{
		parent::setUp();
		Test_Route::clear_routes();
		Test_Resource::clear_resources();
	}

	public function test_empty()
	{
		
	}

}
