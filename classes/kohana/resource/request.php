<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Resource_Request class
 *
 * @package    OpenBuildings/jam-resource
 * @author     Haralan Dobrev
 * @copyright  (c) 2012 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Kohana_Resource_Request extends Kohana_Request {

	public static $allowed_methods = array('GET', 'POST', 'PUT', 'DELETE');

	/**
	 * The name of the resource for the request
	 * @var string
	 */
	public $resource = NULL;

	public function __construct($uri, HTTP_Cache $cache = NULL, $injected_routes = array())
	{
		parent::__construct($uri, $cache, $injected_routes);

		if ( ! in_array($this->method(), Request::$allowed_methods))
			throw new HTTP_Exception_405("Method :method is not allowed", array(
					':method' => $request->method(),
				), $this->_method);

		if ( ! $this->_external AND $this->_route AND $this->_route->resource_name())
		{
			$this->_resource_name = $this->_route->resource_name();
		}
	}

	public function resource_name()
	{
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
			// We found something suitable
			if ($params = $route->matches($uri, Request::$current ? Request::$current->method() : (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET')))
				return array(
					'params' => $params,
					'route' => $route,
				);
		}

		return NULL;
	}

}
