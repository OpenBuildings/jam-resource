<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Resource_Route class
 *
 * @package    OpenBuildings/jam-resource
 * @author     Haralan Dobrev <hdobrev@despark.com>
 * @copyright  (c) 2012 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Resource_Route extends Kohana_Route {

	/**
	 * Stores a named route and returns it. The "action" will always be set to
	 * "index" if it is not defined.
	 *
	 *     Route::set('default', '(<controller>(/<action>(/<id>)))')
	 *         ->defaults(array(
	 *             'controller' => 'welcome',
	 *         ));
	 *
	 * @param   $name         string  route name
	 * @param   $uri string  URI pattern
	 * @param   $regex array  regex patterns for route keys
	 * @param   $resource     string  the nameresource for this route
	 * @return  Route
	 */
	public static function set($name, $uri = NULL, $regex = NULL, array $options = array())
	{
		$route = parent::set($name, $uri, $regex);

		$route->_resource_name = Arr::get($options, 'resource');
		$route->method(Arr::get($options, 'method'));

		if ($route->_method)
		{
			$route->filter(array('Resource_Route', 'is_method_allowed'));
		}

		return $route;
	}

	public static function is_method_allowed($route, $params, $request)
	{
		if ( ! $route->_method)
			return TRUE;

		$method = strtoupper($request->method());

		if (is_string($route->_method))
			return $method == $route->_method;

		return in_array($method, $route->_method);
	}

	/**
	 * The name of the resource which holds the route
	 * @var string
	 */
	protected $_resource_name;

	protected $_method;

	public function method($method = NULL)
	{
		if ($method === NULL)
			return $this->_method;

		if (is_string($method))
		{
			$this->_method = strtoupper($method);
		}
		elseif (is_array($method))
		{
			$this->_method = array_map(function($value)
			{
				return strtoupper($value);
			}, $method);
		}

		return $this;
	}

	/**
	 * Get the resource for the route
	 * @return Resource
	 */
	public function resource()
	{
		if ( ! $this->_resource_name)
			return NULL;

		return Resource::get($this->resource_name());
	}

	public function resource_name()
	{
		return $this->_resource_name;
	}

}
