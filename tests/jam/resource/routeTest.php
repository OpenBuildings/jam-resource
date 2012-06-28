<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Unit tests for the Resource_Route class
 *
 * @group jam.resource
 * @group jam.resource.route
 */
class Resource_RouteTest extends Unittest_Resource_Testcase {

	public function test_set()
	{
		$route = Route::set('my_route', 'routing/<id>', array(
			'id' => '[1-9]\d*'
		), array(
			'resource' => 'some_resource',
			'method' => 'get',

		))
			->defaults(array(
				'controller' => 'routes',
				'action' => 'show'
			));

		$this->assertSame(array('my_route' => $route,), Route::all('my_route'));
		$this->assertEquals('some_resource', $route->resource_name());
		
		$this->setExpectedException('Kohana_Exception');
		$route->resource();

		$this->assertEquals('GET', $route->method());
	}

	public function provider_method()
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
	 * @dataProvider provider_method
	 */
	public function test_method($method, $expected)
	{
		$route = Route::set('route_method', 'uri', NULL, array(
			'method' => $method
		));
		$this->assertEquals($expected, $route->method());
	}

	public function provider_is_method_allowed()
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
	 * @dataProvider provider_is_method_allowed
	 */
	public function test_is_method_allowed($route_method, $requested_method, $expected_result)
	{
		$route = Route::set('route_check', 'route', NULL, array('method' => $route_method))
			->defaults(array(
				'controller' => 'route'
			));

		$this->assertEquals($expected_result, $route->is_method_allowed($requested_method));
		$this->assertEquals($expected_result, (bool) $route->matches('route', $requested_method));
	}

}
