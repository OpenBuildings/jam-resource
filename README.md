Resource Jam module for Kohana 3.2
==============================

Resources act as a bridge between routes, models and requests.

The `jam-resource` module works with the mighty [Jam ORM for Kohana 3.2](//github.com/openbuildings/jam)

Main Features
-------------

 * Define resources in your bootstrap and routes for them would automatically be created
 * Generate urls from model objects or collections
 * Nest resources
 * Access the model object, the model collection or the parent object for the current request
 * Supports slugs with the sluggable behavior in Jam ORM
 * Restrict routes (and actions) to certain HTTP methods
 * Easily build a RESTful API

Usage:
------

**Defining resources**

This will generate 7 routes

```
<?php 
// Define the users resource
Resource::set('users');
?>
```

 * GET  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; **/users** &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - action index   - retrieve a list of users
 * GET  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; **/users/new** &nbsp; - action new     - form to create a new user
 * POST &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; **/users** &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - action create  - create a new user
 * GET  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  **/users/1** &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - action show    - retrieve info for a single user
 * GET  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  **/users/edit** &nbsp; - action edit    - form to edit a user
 * PUT  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  **/users/1** &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	 - action update  - update an existing user
 * DELETE **/users/1** &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - action destroy - destroy a user resource


When you visit `/users` the generated routes would open `Controller_Users::action_index()`.

From there you would be able to access a Jam_Collections for the user model with:

`$this->request->resource()->collection()`

You could also access a Jam_Builder with:

`$this->request->resource()->builder()`

---

When you visit `/users/1` the routes would open `Controller_Users::action_show()`.

From there you could access the specified user model with:

`$this->request->resource()->object()`

There is no need to check if it is laoded. If there is no user model with the specified id
`Jam_Exception_NotFound` would be thrown.

---

You could also generate the resourceful URLs for a specific model.

```
<?php
$user = Jam::factory('user', 1);

// /users/1
Resource::url($user);

// /users
Resource::url('users');

// /users/1/edit
Resource::url('users', array('action' => 'edit'));

// /users
Resource::url('users', array('action' => 'create'));
?>
```

### Configuration

There is a global configuration in `config/jam-resource.php`.

Some of it could be overriden when defining a resource.

The most important options are the resource-specific ones. They are passed in an array
as a second argument to `Resource::set()`.

**Here are the default options**:

```
<?php
Resource::set('users', array(

	'model' => 'user',
	'controller' => 'users',
	'path' => 'users'
	'sluggable' => FALSE,
	'singular' => FALSE,
));
?>
```

 * **model** - the name of the model for the resource; default: the singular form of the resource name
 * **controller** - the name of the controller for the routes; default: the plural form of the resource name
 * **path** - the string used in urls instead of the name; default - the name of the resource
 * **sluggable** - boolean indicating whether this resource uses slugs in the URLs
 * **singular** - boolean indicating whether this is a singular resource; More on singular resource below

**Generate urls from objects**

You are probably used to Route::url where you specify the name of the route and route params to generate a url. This is much easier with resources. You can use Resource::url with objects, strings and more to generate a correct url.

In most cases you will have loaded objects or collection queries which you would want to generate urls to. Here is how:

``` php
<?php
// Generate an object url
Resource::url($image); // /images/5

// Generate a slug url
Resource::url($user); // /users/some-user-123

// Generate url for child resource
Resource::url($user, 'images'); // /users/some-user-123/images

// If you have $images collection
Resource::url($user, $images); // /users/some-user-123/images

// Specify a route param
Resource::url('images', array('action' => 'new'));

// Object url for child resource
Resource::url($folder, $document); // /folders/some-folder-1234/docs/my-document-5
?>
```

**Get current objects and collections**

In the controllers corresponding to a resource you can easily get the model object, model collection or the parent model object.

``` php
<?php
// Get the resource corresponding to the route from the current request
Resource::current();

// Or
Request::current()->resource();

// Or if you are in a controller
$this->request->resource();

// Get the collection query for the current request
$this->request->resource()->collection();

// Load and get the object for the current request
$this->request->resource()->object();

// Load and get the parent object for the current request
$this->request->resource()->parent_object();
?>
```

Singular resources
------------------

**TODO: explain what singular resources are and how they should be used**

Jam driver
-------

**TODO: explain sluggable implementation here**

Formats
-------

**TODO: explain formats here**