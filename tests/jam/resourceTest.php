<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package Jam Resource
 * @group jam-resource
 * @groupjam-resource.resource
 */
class Jam_ResourceTest extends Unittest_Resource_Testcase {

	public function test_actions_map()
	{
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
