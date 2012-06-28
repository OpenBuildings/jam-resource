<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Resource extends Resource {

	public static function clear_resources()
	{
		Resource::$_resources = array();
	}

	public static function clear_format_regex()
	{
		Resource::$_format_regex = NULL;
	}
}
