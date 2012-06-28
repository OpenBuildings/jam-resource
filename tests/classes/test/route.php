<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Route extends Resource_Route {

	public static function clear_routes()
	{
		Test_Route::$_routes = array();
	}
	
}
