<?php

class Model_User extends Jam_Model {

	public static function initialie(Jam_Meta $meta)
	{
		$meta->fields(array(
			'id' => Jam::field('primary'),
		));
	}
}