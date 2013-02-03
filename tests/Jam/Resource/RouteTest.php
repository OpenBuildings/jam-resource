<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Unit tests for the Resource_Route class
 *
 * @group jam-resource
 * @group jam-resource.route
 */
class Jam_Resource_RouteTest extends Unittest_TestCase {

	public function test_set_without_resource()
	{
		$this->markTestSkipped();
		$route = Route::set('route_without_resource', 'routing/<id>', array(
			'id' => '[1-9]\d*'
		), array(
			'resource' => 'some_resource',
			'method' => 'get',

		))
			->defaults(array(
				'controller' => 'routes',
				'action' => 'show'
			));

		$this->assertSame(array(
			'route_without_resource' => $route,
		), Route::all());
		$this->assertEquals('some_resource', $route->resource_name());

		$this->setExpectedException('Kohana_Exception');
		$route->resource();

		$this->assertEquals('GET', $route->method());
	}

	public function data_methods()
	{
		return array(
			array('get', 'GET'),
			array('GET', 'GET'),
			array('post', 'POST'),
			array('put', 'PUT'),
			array('delete', 'DELETE'),
			array(array('get', 'post'), array('GET', 'POST')),
			array(array('get', 'POST'), array('GET', 'POST')),
			array(array('GET', 'put', 'post'), array('GET', 'PUT', 'POST')),
		);
	}

	/**
	 * @dataProvider data_methods
	 */
	public function test_set_and_get_method($method, $expected)
	{
		$route = Route::set('route_method', 'uri', NULL, array(
			'method' => $method
		));

		$this->assertEquals($expected, $route->method());
	}

	public function data_is_method_allowed()
	{
		return array(
			array('get', 'get', TRUE),
			array('post', 'post', TRUE),
			array('post', 'get', FALSE),
			array('get', 'post', FALSE),
			array(array('post', 'get'), 'get', TRUE),
			array(array('get', 'post'), 'get', TRUE),
			array(array('get', 'post'), 'post', TRUE),
			array(array('get', 'post'), 'put', FALSE),
		);
	}

	/**
	 * @dataProvider data_is_method_allowed
	 */
	public function test_is_method_allowed($route_method, $requested_method, $expected_result)
	{
		$this->markTestSkipped();

		$route = Route::set('route_check', 'uri', NULL, array(
			'method' => $route_method
		))
			->defaults(array(
				'controller' => 'uri'
			));

		$request = new Request('uri');
		$request->method($requested_method);

		$this->assertEquals($expected_result, $route->is_method_allowed(
			$route,
			array(),
			$request
		));
	}

}
