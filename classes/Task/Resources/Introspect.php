<?php

class Task_Resources_Introspect extends Minion_Task {

	protected $_options = array(
		'method' => FALSE
	);

	protected function _execute(array $params)
	{
		if ( ! isset($params[1]))
		{
			Minion_CLI::write('Specify a URL to introspect!', 'red');
			return;
		}

		$method = empty($params['method']) ? Request::GET : $params['method'];
		$processed_uri = Route::introspect($params[1], $method);

		$route = $processed_uri['route'];
		$route_params = $processed_uri['params'];

		$uri = $route->get_uri();

		$action = Minion_CLI::color($route_params['controller'], 'green')
		.'#'
		.Minion_CLI::color($route_params['action'], 'yellow');

		unset($route_params['controller'], $route_params['action']);

		$params_string = array();

		foreach ($route_params as $param => $value)
		{
			$params_string []= "$param => $value";
		}

		$params_string = '  '.Minion_CLI::color('{'.implode(', ', $params_string).'}', 'cyan');

		Minion_CLI::write($action.$params_string.' '.Minion_CLI::color(strtoupper($method), 'purple'));

		$resource = $route->resource();

		if ($resource)
		{
			if ($resource->parent())
			{
				$this->_resource($resource->parent(), array('id' => $route_params['parent_id']), TRUE);
			}
			$this->_resource($resource, $route_params);
		}
	}

	private function _resource(Resource $resource, array $route_params, $is_parent = FALSE)
	{
		$resource->param($route_params);

		$output = '';

		if (isset($route_params['id']))
		{
			$output = (string) $resource->object();
		}
		elseif ($resource->option('singular'))
		{
			if ($model = $resource->option('model'))
			{
				$output = 'Singular model: '.Minion_CLI::color(Jam::class_name($model), 'light_red');
			}
		}
		else
		{
			$output = 'Collection of: '.Minion_CLI::color(Jam::class_name($resource->option('model')), 'light_red');
		}

		if ($is_parent)
		{
			$output = 'Parent: '.$output;
		}

		Minion_CLI::write($output);
	}
}