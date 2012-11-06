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

	public static function factory(
		$uri = TRUE,
		HTTP_Cache $cache = NULL,
		$injected_routes = array())
	{
		$request = parent::factory($uri, $cache, $injected_routes);

		if ( ! $request->is_external()
		 AND $request->route()
		 AND $request->route()->resource_name())
		{
			$request->_resource_name = $request->route()->resource_name();
		}

		return $request;
	}

	/**
	 * Process URI
	 *
	 * @param   string  $uri     URI
	 * @param   array   $routes  Route
	 * @return  array
	 */
	public static function process_uri($uri, $routes = NULL)
	{
		// Load routes
		$routes = (empty($routes)) ? Route::all() : $routes;
		$params = NULL;

		foreach ($routes as $name => $route)
		{
			$actual_method = Resource_Request::_method(
				Request::$current ?
				 Request::$current->method() :
				 Arr::get($_SERVER, 'REQUEST_METHOD', 'GET')
			);

			// We found something suitable
			if ($params = $route->matches($uri, $actual_method))
				return array(
					'params' => $params,
					'route' => $route,
				);
		}

		return NULL;
	}

	protected static function _method($actual_method)
	{
		if ( ! Kohana::$config->load('jam-resource.rest_overloading'))
			return $actual_method;

		$overloaded_method = strtoupper(Arr::get(
			Request::initial() ? Request::initial()->query() : $_GET,
			'method'
		));

		if ( ! $overloaded_method)
		 	return $actual_method;
		 
		if ($actual_method !== 'GET'
		 OR ! in_array($overloaded_method, array('PUT', 'POST', 'DELETE')))
			return $overloaded_method;

		return $actual_method;
	}

	/**
	 * The name of the resource for the request
	 * @var string
	 */
	protected $_resource_name = NULL;

	public function method($method = NULL)
	{
		if ( ! $method)
			return parent::method();

		return parent::method(Resource_Request::_method($method));
	}
	
	/**
	 * The resource associated with the request
	 * @return Resource
	 */
	public function resource()
	{
		if ( ! $this->_resource_name)
			return NULL;

		return Resource::get($this->_resource_name)->param($this->param());
	}

	public function resource_name()
	{
		return $this->_resource_name;
	}
}
