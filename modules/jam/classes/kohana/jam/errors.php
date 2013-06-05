<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Jam Errors
 *
 * @package    Jam
 * @category   Model
 * @author     Ivan Kerin
 * @copyright  (c) 2011-2012 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class Kohana_Jam_Errors implements Countable, SeekableIterator, ArrayAccess {

	public static function message($error_filename, $attribute, $error, $params)
	{
		if ($message = Kohana::message($error_filename, "{$attribute}.{$error}"))
		{

		}
		elseif ($message = Kohana::message('validators', $error))
		{

		}
		else
		{
			return $error_filename.":{$attribute}.{$error}";
		}

		return strtr($message, $params);
	}

	/**
	 * @var  Jam_Meta  The current meta object, based on the model we're returning
	 */
	protected $_meta = NULL;

	/**
	 * @var  Jam_Validated  The current class we're placing results into
	 */
	protected $_model = NULL;

	/**
	 * @var  string
	 */
	protected $_error_filename = NULL;

	private $_container = array();
	private $_current;

	/**
	 * Tracks a database result
	 *
	 * @param  mixed  $result
	 * @param  mixed  $model
	 */
	public function __construct(Jam_Validated $model, $error_filename)
	{
		$this->_model = $model;
		$this->_meta = $model->meta();
		$this->_error_filename = $error_filename;
	}

	public function as_array()
	{
		return $this->_container;
	}

	public function add($attribute, $error, array $params = array())
	{
		if ( ! isset($this->_container[$attribute]))
		{
			$this->_container[$attribute] = array();
		}

		$this->_container[$attribute][$error] = $params;

		return $this;
	}

	public function messages($attribute = NULL)
	{
		$messages = array();

		if ($attribute !== NULL AND ( ! is_array($attribute) OR $attribute))
		{
			foreach ( (array) $attribute as $attribute_item)
			{
				if (empty($this->_container[$attribute_item]))
					continue;

				foreach ($this->_container[$attribute_item] as $error => $params)
				{
					if ($attribute = $this->_meta->attribute($attribute_item))
					{
						$label = $attribute->label;
					}
					else
					{
						$label = Inflector::humanize($attribute_item);
					}

					$messages[] = Jam_Errors::message($this->_error_filename, $attribute_item, $error, Arr::merge($params, array(
						':model' => $this->_meta->model(),
						':attribute' => ucfirst($label)
					)));
				}
			}

			return $messages;
		}

		foreach ($this->_container as $attribute => $errors)
		{
			$messages[$attribute] = $this->messages($attribute);
		}

		return $messages;
	}

	public function messages_all($attribute = NULL)
	{
		if ($attribute !== NULL)
			return array(
				$attribute => $this->all_messages_for($attribute)
			);

		$messages = array();
		foreach ($this->_container as $attribute => $errors)
		{
			$messages[$attribute] = $this->all_messages_for($attribute);
		}

		return $messages;
	}

	public function all_messages_for($attribute)
	{
		
		if ($association = $this->_meta->association($attribute))
		{
			if ($association instanceof Jam_Association_Collection)
			{
				$messages = array();
				foreach ($this->_model->{$attribute} as $child)
				{
					$messages[$attribute][] = $child
						->errors()
						->messages_all();
				}
			}
			else
			{
				$messages[$attribute] = $this
					->_model
					->errors()
					->messages_all($attribute);
			}
		}
		else
		{
			$messages = $this->messages($attribute);
		}

		return $messages;
	}

	public function __toString()
	{
		$all_messages = array();
		foreach ($this->messages() as $field => $messages)
		{
			$all_messages[] = join(', ', $messages);
		}

		return join(', ', $all_messages);
	}


	public function seek($offset)
	{
		if ($this->offsetExists($offset))
		{
			$this->_current = $offset;

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function offsetSet($offset, $value)
	{
		throw new Kohana_Exception('Cannot set the errors directly, must use add() method');
	}

	public function offsetExists($offset)
	{
	 return isset($this->_container[$offset]);
	}

	public function offsetUnset($offset)
	{
		unset($this->_container[$offset]);
		if ($this->_current == $offset)
		{
			$this->rewind();
		}
	}

	public function offsetGet($offset)
	{
		return isset($this->_container[$offset]) ? $this->_container[$offset] : NULL;
	}

	public function rewind()
	{
		reset($this->_container);
		$this->_current = key($this->_container);
	}

	public function current()
	{
		return $this->offsetGet($this->_current);
	}

	public function key()
	{
		return $this->_current;
	}

	public function next()
	{
		next($this->_container);
		$this->_current = key($this->_container);
		return $this->current();
	}

	public function prev()
	{
		prev($this->_container);
		$this->_current = key($this->_container);
		return $this->current();
	}

	public function valid()
	{
		return isset($this->_container[$this->_current]);
	}

	public function count()
	{
		return count($this->_container);
	}
}
