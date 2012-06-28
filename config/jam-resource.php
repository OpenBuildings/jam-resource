<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'key' => 'id',
	'type' => 'multiple', // multiple or singular
	'format' => FALSE, // set to FALSE if you don't want different formats included in the routes
	'formats' => array(
		'html',
		'json',
		'jsonp',
		'js',
		'xml',
		'rss'
	),
	'positive_integer_regex' => '[1-9][0-9]*',
	'slug_regex' => '(?:[a-z-0-9]*?-)?[1-9][0-9]*'
);
