<?php

class Controller_Users extends Controller {

	public function action_index()
	{
		$this->response->body($this->request->resource()->collection()->count());
	}

	public function action_create()
	{
		$this->response->body($this->request->resource()->collection()->count());
	}

	public function action_show()
	{
		$this->response->body(Resource::url($this->request->resource()->object(), array('action' => 'edit', 'source' => 'resourses', 'protocol' => TRUE)));
	}

	public function action_delete()
	{
		
	}

	public function action_edit()
	{
		
	}
}