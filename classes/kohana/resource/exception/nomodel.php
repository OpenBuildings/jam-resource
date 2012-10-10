<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Resource_Exception_Nomodel class
 *
 * @package    OpenBuildings/jam-resource
 * @author     Haralan Dobrev
 * @copyright  (c) 2012 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Resource_Exception_Nomodel extends Resource_Exception {

	public $model;

	public function __construct($message, $model, $fields = NULL)
	{
		$fields[':model'] = $this->model = $model;

		parent::__construct($message, $fields);
	}

}
