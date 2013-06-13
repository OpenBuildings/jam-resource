<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package Jam Resource
 * @group jam-resource
 * @group jam-resource.resource
 */
class Jam_ResourceTest extends PHPUnit_Framework_TestCase {

	public function test_actions_map()
	{
		$this->markTestSkipped();

		$this->assertSame(array(
			'collection' => array(
				'index'   => 'get',
				'new'     => 'get',
				'create'  => 'post',
			),
			'member'     => array(
				'show'    => 'get',
				'edit'    => 'get',
				'update'  => 'put',
				'destroy' => 'delete'
			)
		), Resource::$actions_map);
	}
}
