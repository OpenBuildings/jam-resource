<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'sluggable' => FALSE,
	'singular' => FALSE, // multiple or singular
	'format' => FALSE, // default format; set to FALSE if you don't want different formats included in the routes
	'formats' => array(
		'html' => TRUE,
		'json' => TRUE,
		'jsonp' => TRUE,
		'js' => TRUE,
		'xml' => TRUE,
		'rss' => TRUE
	),
	'is_format_required' => FALSE,
	'rest_overloading' => FALSE,
	'positive_integer_regex' => '[1-9][0-9]*',
	'slug_regex' => '(?:[a-z][a-z-0-9]*?-)?[1-9][0-9]*',
);
