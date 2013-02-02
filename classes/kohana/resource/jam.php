<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Resource_Jam class
 *
 * @package    OpenBuildings/jam-resource
 * @author     Haralan Dobrev <hdobrev@despark.com>
 * @copyright  (c) 2012 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
 class Kohana_Resource_Jam {

	/**
	 * Get the collection for a resource
	 * If the resource has a parent resource the method should return the child collection regarding the parent limitation.
	 *
	 * @static
	 * @param  Resource $resource The resource for the collection
	 * @param boolean $children should we get the children
	 * @return Jam_Builder
	 */
	public static function builder($resource)
	{
		if (($parent = $resource->parent()) AND $parent->is_multiple())
		{
			$parent_builder = Jam::query($parent->model());
			if ($parent->is_sluggable())
			{
				$parent_builder = $parent_builder->find_by_slug_insist($resource->param('parent_id'));
			}
			else
			{
				$parent_builder = $parent_builder->find_insist($resource->param('parent_id'));
			}

			return $parent_builder->builder($resource->field());
		}
		else
		{
			return Jam::query($resource->model());
		}
	}

	/**
	 * Get the object for a resource.
	 * If the resource has a parent resource the method should return the child object regarding the parent limitation.
	 *
	 * @static
	 * @param  Resource $resource The resource to get the object for
	 * @return mixed an object depending from the driver configuration; could be ORM or Jam_Object
	 */
	public static function object($resource)
	{
		$child_query = Resource_Jam::builder($resource);
		if ($resource->is_sluggable())
		{
			return $child_query->find_by_slug_insist($resource->param('id'));
		}
		else
		{
			return $child_query->key((int) $resource->param('id'))->find_insist();
		}
	}

	/**
	 * Checks if a model object is using the sluggable behavior
	 *
	 * @static
	 * @param  mixed  $object Jam_Model or ORM
	 * @return boolean TRUE if the object implements the sluggable behavior; FALSE otherwise
	 */
	public static function is_sluggable($object)
	{
		return array_key_exists('sluggable', $object->meta()->behaviors()) AND $object->slug;
	}
}
