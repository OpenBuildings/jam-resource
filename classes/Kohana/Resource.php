<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Resource class
 *
 * @package    OpenBuildings/jam-resource
 * @author     Haralan Dobrev <hdobrev@despark.com>
 * @copyright  (c) 2012 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Resource {

	/**
	 * @var  bool Indicates whether resources are cached
	 */
	public static $cache = FALSE;

	public static $actions_map = array(
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
	);

	public static $options_available = array(
		'model',
		'sluggable',
		'singular',
		'controller',
		'path',
		'field',
		'only',
		'except',
		'with',
		'format',
		'formats',
		'is_format_required',
		'slug_regex',
		'positive_integer_regex',
	);

	/**
	 * All the created resources.
	 * @var array
	 */
	protected static $_resources = array();

	/**
	 * Set routes and nested resources based on the models
	 *
	 * @param string|arrau $model the name of the resource or an array of names
	 * @param array $options array of options - see the usage description
	 * @param string $parent the name of the parent route
	 */
	public static function set($name, array $options = array(), $parent = NULL)
	{
		if (Arr::is_array($name))
		{
			$resources = array();
			foreach ($name as $resource_name)
			{
				$resources[] = Resource::set($resource_name, $options, $parent);
			}
			return $resources;
		}

		$resource = new Resource($name, $options, $parent);
		return Resource::$_resources[$resource->name()] = $resource;
	}

	/**
	 * Get a single resource by name.
	 * @param  string $resource_name the resource name
	 * @return Resource
	 * @throws Resource_Exception_Notfound If the resource is not found
	 */
	public static function get($resource_name)
	{
		if ( ! ($resource = Arr::get(Resource::all(), $resource_name)))
			throw new Kohana_Exception('Resource :resource is not defined!', array(
				':resource' => $resource_name
			));

		return $resource;
	}

	/**
	 * Retrieves all named resources.
	 *
	 *     $resources = Resource::all();
	 *
	 * @return  array  resources by name
	 */
	public static function all()
	{
		return Resource::$_resources;
	}

	/**
	 * Generate an URL for the specified arguments.
	 * Arguments could be model objects, strings, arrays or booleans.
	 * Model objects are resolved to strings and route params are set from them - slugs or primary keys.
	 * Arrays are used as additional route params.
	 * Booleans are used for protocols. Protocols could be defined with 'protocol' key in arrays too.
	 *
	 * Usage:
	 * 		Resource::url($user, 'images', array('action' => 'more'), TRUE); // /users/some-user-123/images/more
	 *
	 * 	If model objects are given as arguments their name is used in singular form.
	 * 	If collection objects are given as arguments their name is used in plural form.
	 *
	 * 	The point is to generate a correct route name, then select the route and get the resource associated with the route.
	 *
	 * @return string
	 */
	public static function url()
	{
		$arguments = func_get_args();
		$protocol = NULL;
		$params = array();
		$strings = array();

		foreach ($arguments as $i => $argument)
		{
			if (is_string($argument))
			{
				$strings[] = $argument;
				Resource::_set_key($params);
				if (Inflector::singular($argument) == $argument)
				{
					$params['action'] = 'show';
				}
			}
			elseif (is_object($argument))
			{
				$strings[] = Resource::_parse_object($argument, $params);
			}
			elseif (is_array($argument))
			{
				$params = Arr::merge($params, $argument);
			}
			elseif (is_bool($argument) OR is_null($argument))
			{
				$protocol = $argument;
			}
		}
		$protocol = Arr::get($params, 'protocol', $protocol);
		$strings[] = Arr::get($params, 'action', 'index');
		return Route::url(implode('_', $strings), $params, $protocol);
	}

	/**
	 * Set a key in the params and if it has been already set move the old one to the parent key
	 *
	 * @static
	 * @param array &$params a reference to the params array on which to operate on
	 * @param int $key     The key which to be set if provided
	 */
	protected static function _set_key(&$params, $key = NULL)
	{
		if (isset($params['id']))
		{
			$params['parent_id'] = $params['id'];
			unset($params['id']);
		}

		if ($key)
		{
			$params['id'] = $key;
		}
		else
		{
			$params['action'] = 'index';
		}
	}

	/**
	 * Parse an object or a collection and returns the name of the model
	 *
	 * @static
	 * @param  mixed $object a model object depending on the drivers; it could be Jam_Collection, Jam_Builder, Jam_Object, ORM or Database_Result
	 * @param  array $params if the object is loaded it would set id in params to the key of the object
	 * @return string
	 */
	protected static function _parse_object($object, &$params)
	{
		if ($object instanceof Jam_Model AND $object->loaded())
		{
			$key = Resource_Jam::is_sluggable($object) ?
			 ($object->slug ?: $object->id()) :
			 $object->id();
			$params['action'] = 'show';
			Resource::_set_key($params, $key);
		}

		return Inflector::plural($object->meta()->model());
	}

	/**
	 * Saves or loads the resource cache. If your resources will remain the same for
	 * a long period of time, use this to reload the resources from the cache
	 * rather than redefining them on every page load.
	 *
	 *     if ( ! Resource::cache())
	 *     {
	 *         // Set resources here
	 *         Resource::cache(TRUE);
	 *     }
	 *
	 * @param   boolean   cache the current resources
	 * @return  void      when saving resources
	 * @return  boolean   when loading resources
	 * @uses    Kohana::cache
	 */
	public static function cache($save = FALSE)
	{
		if ($save === TRUE)
		{
			// Cache all defined resources
			Kohana::cache('Resource::cache()', Resource::$_resources);
		}
		else
		{
			if ($resources = Kohana::cache('Resource::cache()'))
			{
				Resource::$_resources = $resources;

				// Resources were cached
				return Resource::$cache = TRUE;
			}
			else
			{
				// Resources were not cached
				return Resource::$cache = FALSE;
			}
		}
	}

	/**
	 * The name of the resource. It is used for identifying resources.
	 * It is required and must be unique.
	 * If you use the same name the resource would be overwritten.
	 * @var string
	 */
	protected $_name;

	protected $_options = array();

	/**
	 * The parent resource name
	 * @var string
	 */
	protected $_parent;

	protected $_routes = array();

	protected $_child_options = array();

	/**
	 * The childrend for the resource
	 * @var array
	 */
	protected $_children = array();

	/**
	 * Route params for the resource.
	 * Used when selecting collection or an object for the resource.
	 * @var array
	 */
	protected $_params = array();

	protected $_actions = array(
		'collection' => array(),
		'member'     => array()
	);

	/**
	 * Constructor for the Resource class.
	 * @param string $name    the name of the resource. It is combined with the parent resource name when it's available.
	 * @param array  $options the options for the resource
	 * @param Resource $parent  the parent object for the resource
	 */
	public function __construct($name, array $options = array(), $parent = NULL)
	{
		$this->_name = $name;

		$this->_options['field'] = $this->option('field', $this->name());

		$this->_options = array_filter(
			Arr::merge(
				(array) Kohana::$config->load('jam-resource'),
				Arr::extract($options, Resource::$options_available)
			), function($value)
			{
				return $value !== NULL;
			}
		);

		$model = $this->option('model', Inflector::singular($name));

		if ($model !== FALSE)
		{
			if ( ! (bool) Jam::meta($model))
				throw new Kohana_Exception('The model :model does not exist', $model);

			$this->_options['model'] = $model;
		}

		if ($this->option('singular'))
		{
			$this->_options['sluggable'] = FALSE;
		}

		$this->_options['path'] = $this->option('path', $this->option('singular') ?
			Inflector::singular($name) :
			$name
		);

		$path_base = basename($this->option('path'));

		$this->_options['controller'] = $this->option('controller', $this->option('singular') ?
			$path_base :
			Inflector::singular($path_base, 2)
		);

		$this->_options['formats'] = $this->option('formats', array_keys(array_filter(Kohana::$config->load('jam-resource.formats'))));

		if ($only = $this->option('only'))
		{
			foreach (Resource::$actions_map as $actions_type => $actions_group)
			{
				if ($actions_group)
				{
					if ($actions_type != 'member' AND $this->option('singular'))
					{
						$actions_type = 'member';
					}

					$this->_actions = Arr::merge($this->_actions, array(
						$actions_type => array_filter(
							Arr::extract($actions_group, (array) $only)
						)
					));
				}
			}
		}
		elseif ($except = $this->option('except'))
		{
			foreach (Resource::$actions_map as $actions_type => $actions_group)
			{
				if ($actions_group)
				{
					$this->_actions[$actions_type] = array_filter(
						Arr::extract($actions_group, array_diff(
							array_keys($actions_group),
							(array) $except
						))
					);
				}
			}
		}
		elseif ( ! array_key_exists('only', $this->_options))
		{
			$this->_actions = Resource::$actions_map;
		}

		$this->_add_actions( (array) $this->option('with'));

		if ($parent)
		{
			$this->_parent = $parent;
			$this->_name = $this->_parent->name().'_'.$this->name();
			$this->_child_options = array();
		}
		else
		{
			foreach (Resource::$options_available as $option)
			{
				unset($options[$option]);
			}

			$this->_child_options = array_filter($options);
		}

		$this->_set_routes();
	}

	public function option($option = NULL, $default = NULL)
	{
		if ($option === NULL)
			return $this->_options;

		return Arr::path($this->_options, $option, $default);
	}

	/**
	 * Get the collection  object for the resource
	 * @return Jam_Collection
	 */
	public function collection()
	{
		return Resource_Jam::builder($this);
	}

	/**
	 * Get the model object for the resource.
	 * @return mixed depending on the driver - Jam_Model or ORM
	 */
	public function object()
	{
		return Resource_Jam::object($this);
	}

	/**
	 * Get the model object for the parent resource of the resource.
	 * @return mixed depending on the driver - Jam_Model or ORM
	 */
	public function parent_object()
	{
		return $this
			->parent()
			->param(array('id' => $this->param('parent_id')))
			->object();
	}

	/**
	 * Get or set route params to the resource object to be used when selecting a collection or an object.
	 * It could act as:
	 * 		getter for all of the params - if no arguments are given
	 * 		getter for a single param - if a string is given
	 * 		setter - if an array is given; It merges the previously set params with the new ones.
	 * @param  NULL|string|array $param
	 * @return $this|array|mixed if it acts as a setter return $this; otherwise array of all the params or a single param
	 * @throws Resource_Exception If it acts as a getter of a single param and it's not set
	 * @uses Arr::merge to merge the previously set params with the new ones provided
	 */
	public function param($param = NULL)
	{
		if ($param === NULL)
			return $this->_params;

		if (is_array($param))
		{
			$this->_params = Arr::merge($this->_params, $param);
			return $this;
		}

		if ( ! isset($this->_params[$param]))
			throw new Kohana_Exception('The :param param is missing from the :resource resource!', array(
				':resource' => $this->name(),
				':param' => $param
			));

		return Arr::get($this->_params, $param);
	}

	public function actions()
	{
		return $this->_actions;
	}

	/**
	 * Get an array of the child resource for the resource
	 * @param  string $child_name the name of a single child resource to be returned
	 * @return array array of isntances of the Resource class
	 */
	public function children($child_name = NULL)
	{
		if ($child_name)
			return (bool) Arr::get($this->_children, $child_name);

		return $this->_children;
	}

	/**
	 * Get the resource name
	 * @return string
	 */
	public function name()
	{
		return $this->_name;
	}

	/**
	 * Get the parent resource
	 * @return Resource
	 */
	public function parent()
	{
		return $this->_parent;
	}

	/**
	 * Return the routes of the resource
	 * @return array the routes
	 */
	public function routes()
	{
		return $this->_routes;
	}

	public function _add_actions($with)
	{
		if ( ! $with)
			return $this;

		foreach ($with as $type => $actions)
		{
			if ( ! $actions)
				continue;

			if (is_numeric($type))
			{
				$type = 'member';
			}
			elseif (is_string($actions))
			{
				$this->_actions['member'][$type] = $actions;
			}

			if (is_string($actions))
			{
				$this->_actions['member'][$actions] = 'get';
			}
			elseif (is_array($actions))
			{
				$actions_final = array();

				foreach ($actions as $action_name => $action_method)
				{
					if (is_numeric($action_name))
					{
						$action_name = $action_method;
						$action_method = 'get';
					}

					$actions_final[$action_name] = $action_method;
				}
				$this->_actions[$type] += $actions_final;
			}
		}

		return $this;
	}

	/**
	 * Set the child resource.
	 * It adjust the child resource options, set the resource and add it to the child resources of the parent.
	 * @param string $child_name    the child resource name
	 * @param array $child_options the child resource options
	 */
	protected function _set_child($child_name, $child_options)
	{
		$child_options['path'] = $this->option('path')
			.'/'
			.( ! $this->option('singular') ? '<parent_id>/' : '')
			.Arr::get(
				$child_options,
				'path',
				Arr::get($child_options, 'singular') ?
				 Inflector::singular($child_name) :
				 $child_name
			);

		$child_resource = Resource::set($child_name, $child_options, $this);

		return $this->_children[] = $child_resource->name();
	}

	/**
	 * Set the children resources and the routes for the resource depending on the options and the type provided.
	 * @return $this
	 * @uses Resource_Routes for the routes
	 * @uses $this->_set_child for the child resources
	 */
	protected function _set_routes()
	{
		foreach ($this->_child_options as $child_name => $child_options)
		{
			$this->_set_child($child_name, $child_options);
		}

		foreach ($this->_actions as $actions_group => $actions)
		{
			foreach ($actions as $action => $method)
			{
				if ( ! ($this->option('singular') AND $action == 'index'))
				{
					$route_name = $this->_set_route(
						$actions_group,
						$action,
						$method
					);

					$this->_routes[] = $route_name;
				}
			}
		}

		return $this;
	}

	/**
	 * Set a route from the given type.
	 * @param string $type 'member' or 'collection'
	 * @param string $action
	 * @param string $method the HTTP method for this route
	 * @return Route the created Resource_Route object
	 */
	protected function _set_route($type, $action, $method)
	{
		$route_name = implode('_', array($this->name(), $action));
		$format_string = $id_string = $action_string = '';
		$route_regex = array();
		$route_defaults = array(
			'controller'  => $this->option('controller'),
			'action'      => $action
		);

		if (($parent = $this->parent()))
		{
			if ( ! $parent->option('singular'))
			{
				if ($parent->option('sluggable'))
				{
					$route_regex['parent_id'] = Kohana::$config
						->load('jam-resource.slug_regex');
				}
				else
				{
					$route_regex['parent_id'] = Kohana::$config
						->load('jam-resource.positive_integer_regex');
				}
			}
		}

		if (! $this->option('singular') AND $type == 'member')
		{
			$id_string = '/<id>';

			if ($this->option('sluggable'))
			{
				$route_regex['id'] = Kohana::$config
					->load('jam-resource.slug_regex');
			}
			else
			{
				$route_regex['id'] = Kohana::$config
					->load('jam-resource.positive_integer_regex');
			}
		}

		if ( ! in_array($action, array(
			'index',
			'create',
			'show',
			'update',
			'destroy'
		)))
		{
			$action_string = '/'.$action;
		}

		if ($this->option('format') !== FALSE)
		{
			$route_regex['format'] = count($this->option('formats')) > 1 ?
			 '('.implode('|', $this->option('formats')).')' :
			  $this->option('formats.0', '');

			$route_defaults['format'] = $this->option('format');
			$format_string = '.<format>';

			if ( ! $this->option('is_format_required'))
			{
				$format_string = '('.$format_string.')';
			}
		}

		Route::set(
			$route_name,
			$this->option('path')
			 .$id_string
			 .$action_string
			 .$format_string,
			$route_regex,
			array(
			'resource' => $this->name(),
			'method' => $method
		))
			->defaults($route_defaults);

		return $route_name;
	}
}
