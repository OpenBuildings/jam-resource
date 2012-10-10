<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Resource_HTTP_Exception_405 class
 *
 * @package    OpenBuildings/jam-resource
 * @author     Haralan Dobrev
 * @copyright  (c) 2012 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Resource_HTTP_Exception_405 extends Kohana_HTTP_Exception_405 {

	public $allow;

	public function __construct($message, $variables, $allow = array(), $code = 405)
	{
		$this->allow = $allow;
		parent::__construct($message, $variables, $code);
	}
}
