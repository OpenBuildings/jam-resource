<?php

/**
 * Prints out a map of the defined resources.
 *
 * You can pass a comma-separated list of resource names you'd like listed.
 * If you omit it all the resources and their children would be listed.
 *
 * --actions flag controls whether to list actions and HTTP methods.
 *
 * Example:
 *
 *     ./minion resources::index users,images --actions
 *
 * @author Haralan Dobrev <hdobrev@despark.com>
 * @copyright (c) 2013 Despark Ltd.
 * @license http://www.opensource.org/licenses/isc-license.txt
 */
class Task_Resources_Index extends Minion_Task {

	protected $_options = array(
		'actions' => FALSE,
		'uris' => FALSE,
	);

	protected function _execute(array $options)
	{
		$resources_string = '';

		$resources = isset($options[1]) ? explode(',', $options[1]) : Resource::all();

		foreach ($resources as $resource)
		{
			if (is_numeric($resource))
				continue;

			if (is_string($resource))
			{
				$resource = Resource::get($resource);
			}

			if ( ! $resource->parent())
			{
				$resources_string .= $this->_resource($resource);
			}
		}
		Minion_CLI::write($resources_string);
	}

	private function _resource(Resource $resource)
	{
		$resource_string = "\n".Minion_CLI::color(' '.$resource->name(), 'green');

		if ($this->_options['actions'] !== FALSE)
		{
			$resource_string .= $this->_resource_actions($resource);
		}
		elseif ($this->_options['uris'] !== FALSE)
		{
			$resource_string .= $this->_resource_uris($resource);
		}

		foreach ($resource->children() as $child_resource_name)
		{
			$resource_string .= "\n".'   '.Minion_CLI::color(substr($child_resource_name, strlen($resource->name()) + 1), 'cyan');

			if ($this->_options['actions'] !== FALSE)
			{
				$resource_string .= $this->_resource_actions(Resource::get($child_resource_name), '  ');
			}
			elseif ($this->_options['uris'] !== FALSE)
			{
				$resource_string .= $this->_resource_uris(Resource::get($child_resource_name), '  ');
			}
		}

		$resource_string .= "\n";

		return $resource_string;
	}

	private function _resource_actions(Resource $resource, $indentation = '')
	{
		$actions_string = '';

		foreach ($resource->actions() as $type => $action_group)
		{
			foreach ($action_group as $action_name => $method)
			{
				$actions_string .= "\n".Minion_CLI::color('  '.$indentation.str_pad($action_name, 18, ' ', STR_PAD_LEFT), 'yellow');
				$actions_string .= ' '.$this->_method($method);
			}
		}

		return $actions_string;
	}

	private function _resource_uris(Resource $resource, $indentation = '')
	{
		$uris_string = "\n";
		foreach ($resource->routes() as $route_name)
		{
			$route = Route::get($route_name);

			$uris_string .= '   '.$indentation.$this->_method($route->method());
			$uris_string .= Minion_CLI::color(str_pad(preg_replace('/<(\w+)>/', ':$1', $route->get_uri()), 75, ' ', STR_PAD_RIGHT), 'yellow');

			$params_string = array();

			foreach ($route->defaults() as $param => $value)
			{
				$params_string []= ":$param => \"$value\"";
			}

			$uris_string .= '  {'.implode(', ', $params_string).'}';


			$uris_string .= "\n";
		}

		return $uris_string;
	}

	private function _method($method)
	{
		return Minion_CLI::color(str_pad(implode(', ', array_map('strtoupper', (array) $method)), 20, ' ', STR_PAD_RIGHT), 'purple');
	}

}