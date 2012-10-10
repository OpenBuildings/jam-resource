<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Resource_Request class
 *
 * @package    OpenBuildings/jam-resource
 * @author     Haralan Dobrev
 * @copyright  (c) 2012 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Resource_Request extends Kohana_Request {

	public static $allowed_methods = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'TRACE', 'OPTIONS', 'CONNECT');

	/**
	 * The name of the resource for the request
	 * @var string
	 */
	public $resource = NULL;

	public static function factory($uri = TRUE, HTTP_Cache $cache = NULL, $injected_routes = array())
	{
		$request = parent::factory($uri, $cache, $injected_routes);

		$request->method(Resource_Request::_method($request->method()));

		if ( ! in_array($request->method(), Request::$allowed_methods))
			throw new HTTP_Exception_405("Method :method is not allowed", array(
					':method' => $request->method(),
				), $request->method());

		if ( ! $request->is_external() AND $request->route() AND $request->route()->resource_name())
		{
			$request->resource_name($request->route()->resource_name());
		}

		return $request;
	}

	public function resource_name($resource_name = NULL)
	{
		if ($resource_name)
		{
			$this->_resource_name = $resource_name;
			return $this;
		}

		return $this->_resource_name;
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
			$actual_method = Resource_Request::_method(Request::$current ? Request::$current->method() : (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET'));

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
		if (Kohana::$config->load('jam-resource.rest_overloading') AND ($overloaded_method = Arr::get($_GET, 'method')) AND ($actual_method !== 'GET' OR ! in_array($overloaded_method, array('PUT', 'POST', 'DELETE'))))
		{
			$actual_method = strtoupper($overloaded_method);
		}

		return $actual_method;
	}

}
