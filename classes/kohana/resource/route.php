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
	 * @param   $uri_callback string  URI pattern
	 * @param   $regex array  regex patterns for route keys
	 * @param   $resource     string  the nameresource for this route
	 * @return  Route
	 */
	public static function set($name, $uri_callback = NULL, $regex = NULL, array $options = array())
	{
		$route = parent::set($name, $uri_callback, $regex);

		$route->_resource_name = Arr::get($options, 'resource');
		$route->_method = Arr::get($options, 'method');
		if (is_string($route->_method))
		{
			$route->_method = strtoupper($route->_method);
		}
		elseif (is_array($route->_method)) {
			$route->_method = array_map(function($value) {
				return strtoupper($value);
			}, $route->_method);
		}

		return $route;
	}

	/**
	 * The name of the resource which holds the route
	 * @var string
	 */
	protected $_resource_name = NULL;

	protected $_method = NULL;

	public function method()
	{
		return $this->_method;
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

	public function is_method_allowed($method)
	{
		if ( ! $this->_method)
			return TRUE;

		$method = strtoupper($method);

		if (is_string($this->_method))
			return strtoupper($method) == $this->_method;

		return in_array($method, $this->_method);
	}

	/**
	 * Tests if the route matches a given URI. A successful match will return
	 * all of the routed parameters as an array. A failed match will return
	 * boolean FALSE.
	 *
	 *     // Params: controller = users, action = edit, id = 10
	 *     $params = $route->matches('users/edit/10');
	 *
	 * This method should almost always be used within an if/else block:
	 *
	 *     if ($params = $route->matches($uri))
	 *     {
	 *         // Parse the parameters
	 *     }
	 *
	 * @param   string  URI to match
	 * @param   string $method the method against the uri is matched
	 * @return  array   on success
	 * @return  FALSE   on failure
	 */
	public function matches($uri, $method = NULL)
	{
		$params = parent::matches($uri);

		if ($params === FALSE)
			return FALSE;

		if ( $method AND ! $this->is_method_allowed($method))
			return FALSE;

		return $params;
	}
}
