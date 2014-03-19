Resource Jam module for Kohana 3.2
==================================

Resources act as a bridge between routes, models and requests.

The `jam-resource` module works with the mighty [Jam ORM for Kohana 3.3](openbuildings/jam)

Main Features
-------------

 * Define resources in your bootstrap and routes for them would automatically be created
 * Generate urls from model objects or collections
 * Nest resources
 * Access the model object, the model collection or the parent object for the current request
 * Supports slugs with the sluggable behavior in Jam ORM
 * Restrict routes (and actions) to certain HTTP methods
 * Easily build a RESTful API

Defining resources
------------------

The simplest way to define a resource:

``` php
<?php
// Define the users resource
Resource::set('users');
?>
```

This would generate seven routes which would serve these purposes:

<table>
	<tr>
		<th>HTTP Verb</th>
		<th>path</th>
		<th>action</th>
		<th>used for</th>
	</tr>
	<tr>
		<td>GET</td>
		<td>/users</td>
		<td>index</td>
		<td>display a list of all users</td>
	</tr>
	<tr>
		<td>GET</td>
		<td>/users/new</td>
		<td>new</td>
		<td>return an HTML form for creating a new user</td>
	</tr>
	<tr>
		<td>POST</td>
		<td>/users</td>
		<td>create</td>
		<td>create a new user</td>
	</tr>
	<tr>
		<td>GET</td>
		<td>/users/1</td>
		<td>show</td>
		<td>display a specific user</td>
	</tr>
		<td>GET</td>
		<td>/users/1/edit</td>
		<td>edit</td>
		<td>return an HTML form for editing a user</td>
	<tr>
		<td>PUT</td>
		<td>/users/1</td>
		<td>update</td>
		<td>update a specific user</td>
	</tr>
	<tr>
		<td>DELETE</td>
		<td>/users/1</td>
		<td>destroy</td>
		<td>delete a specific user</td>
	</tr>
</table>

As you can see every action has a very specific purpose.
Something you might not be used to in the PHP world.
Everything is derived from [Ruby on Rails routing](http://guides.rubyonrails.org/routing.html).

---

You can easily limit the creation of these default routes or add more:

**Only specific routes**

``` php
<?php

Resource::set('users', array(
	'only' => array(
		'index',
		'show'
	)
));
```

**Default routes except some**

``` php
<?php

Resource::set('users', array(
	'except' => array(
		'destroy',
		'edit',
		'update'
	)
));
```

**Adding additional routes**

``` php
<?php

Resource::set('users', array(
	'with' => array(
		'picture',
		'collection' => array(
			'featured'
		)
	)
));
```

This would make accessible the following URLs (in addition to the default ones):

 * /users/1/picture
 * /users/featured

Of course you can use these options together to define those routes your application would need.

The routes which a resource would generate are separated in **member** routes and **collection** routes.
The collection routes do not have a specific id while the member routes are about a specific resource.

---

As said above the resources act as a glue between routes, models and controllers.

The model, the controller and the URI paths are derived from the resource name.

The `users` resource would guess the controller is `Controller_Users` and the model is `Model_User`.

You can easily specify these explicitly:

``` php
<?php

Resource::set('photos', array(
	'controller' => 'pictures',
	'model' => 'image'
));
```

This would still create routes to access the photos on `/photos` and `/photos/1`.
But it would use the actions in `Controller_Pictures` and the image model.

Changing the path string is achieved using the `path` option:

``` php
<?php

Resource::('users', array(
	'path' => 'people'
));
```

This would create routes for URIs like: `/people`, `/people/1` etc. while still using the users controller and user model.

Accessing resources in controllers
----------------------------------

When you visit `/users` the generated routes would open `Controller_Users::action_index()`.

From there you would be able to access a Jam_Collections for the user model with:

`$this->request->resource()->collection()`

You could also access a Jam_Builder with:

`$this->request->resource()->builder()`

---

When you visit `/users/1` the routes would open `Controller_Users::action_show()`.

From there you could access the specified user model with:

`$this->request->resource()->object()`

There is no need to check if it is loaded. If there is no user model with the specified id
`Jam_Exception_Notfound` would be thrown.


Generating URLs
---------------

You could also generate the resourceful URLs for a specific model or a collection.

Use the

``` php
<?php

// Jam_Model
$user = Jam::factory('user', 1);

// Jam_Collection
$users = Jam::query('user');

// /users/1
Resource::url($user);

// /users
Resource::url('users');

// /users
Resource::url($users);

// /users/1/edit
Resource::url($user, array('action' => 'edit'));

// /users
Resource::url('users', array('action' => 'create'));
?>
```

Child resources
---------------

**TODO: explain child resources - defining, usage and application**

Singular resources
------------------

**TODO: explain what singular resources are and how they should be used**

Sluggable
-------

You could use the `sluggable` (`TRUE`|`FALSE`) option and the `slug_regex` to set up
the routes to use slugs instead of primary keys.

**TODO: explain sluggable implementation here**

Formats
-------

**TODO: explain formats here**

LICENSE
=======

&copy; Copyright Despark Ltd. 2012

[License](//github.com/openbuildings/jam-resource/blob/master/LICENSE)
