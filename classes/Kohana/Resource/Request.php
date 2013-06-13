<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Resource_Request class
 *
 * @package    OpenBuildings/jam-resource
 * @author     Haralan Dobrev <hdobrev@despark.com>
 * @copyright  (c) 2012 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Resource_Request extends Kohana_Request {

	public static $http_overloading_param = '_method';

	public static function resolve_method($actual_method)
	{
		if ( ! Kohana::$config->load('jam-resource.rest_overloading'))
			return $actual_method;

		$overloaded_method = Request::$initial->query(
			Resource_Request::$http_overloading_param
		);

		if ( ! $overloaded_method)
			return $actual_method;

		$overloaded_method = strtoupper($overloaded_method);

		if ($actual_method !== Request::GET
		 OR ! in_array($overloaded_method, array(
		 	Request::POST,
		 	Request::PUT,
		 	Request::DELETE
		 )))
			return $overloaded_method;

		return $actual_method;
	}

	public function method($method = NULL)
	{
		if ( ! $method)
			return parent::method();

		return parent::method(Resource_Request::resolve_method($method));
	}

	/**
	 * The resource associated with the request
	 * @return Resource
	 */
	public function resource()
	{
		if ( ! $this->_route->resource_name())
			return NULL;

		return Resource::get($this->_route->resource_name())
			->param($this->param());
	}

	public function resource_name()
	{
		return $this->_route->resource_name();
	}
}
