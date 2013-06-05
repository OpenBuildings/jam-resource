<?php

class Model_User extends Jam_Model {

	public static function initialize(Jam_Meta $meta)
	{
		$meta->fields(array(
			'id' => Jam::field('primary'),
			'username' => Jam::field('string'),
			'first_name' => Jam::field('string'),
			'last_name' => Jam::field('string'),
		));
	}

	public function name()
	{
		return implode(' ', array($this->first_name, $this->last_name));
	}
}