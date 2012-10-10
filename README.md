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

Configuration
-------------

Global configuration options available in the config file:

 * **key** - `id` or `slug` - define if the resource uses slugs or not. More options could be defined in the future. Default: `id`
 * **driver** - the name of the driver to be used. Available drivers: orm, jam
 * **default_type** - default type for resources: `singular` or `multiple`. Default: `multiple`
 * **format** - the default format to be used. Use FALSE if you don't want formats added to routes. Default: html
 * **formats** - array of available formats. Default: `array('html', 'json', 'jsnop', 'xml', 'js', 'rss')`
 * **common_actions** - common actions which every resource has for its routes

Default:

``` php
<?php 
array(
	'object' => array(
		'edit',
		'delete'
	),
	'collection' => array(
		'new'
	)
)
?>
```

 * **default_actions** - the default actions for the routes.

Default:

``` php
<?php
array(
	'object' => 'show',
	'collection' => 'index'
)
?>
```

 * **positive_integer_regex** - the regex used for ids. Default: `[1-9][0-9]*`
 * **slug_regex** - the regex used for slugs. Default: `(?:[a-z][a-z-0-9]*?-)?[1-9][0-9]*`

Public API
----------

Here is a complete list of the public methods and properties of the resource class and the related classes:

### Resource class ###

This is the main class for the module and you will need only its public API in most cases while using the module.

#### Properties ####

 * **$cache** - boolean indicating whether the resources are cached or not

#### Static methods ####

 * **config($path = NULL)** - get resource configuration option using dot notation
 * **get($resource_name)** - get a single resource by name; throws a Resource_Notfound_Exception if not found
 * **all()** - get all defined resources in an array; the keys are the names of the resources
 * **url(...)** - generate an url for the resource
 * **current()** - get the resource for the route of the current request
 * **set($name, array $options = array(), $parent = NULL)** - create new resource with the given options
 * **cache($save = FALSE)** - cache the resources
 * **driver()** - get and cache an instance of the Resource_Driver class depending on the driver specified in the config

#### Non-static methods ####

 * **__construct($name, array $options = array(), $parent = NULL)** - constructor for the Resource class
 * **collection()** - get the collection query corresponding to the resource
 * **object()** - get the model object for the resource; throws a driver-specific exception if not loaded
 * **parent_object()** - get the model object for the parent resource for the current request
 * **get_collection()** - get and cache an instance of the Resource_Collection class
 * **clear_collection()** - clear the cached instance of the Resource_Collection class
 * **param($param = NULL)** - get or set route params into the resource
 * **option($option_name = NULL, $default = NULL)** - get one or all options for the resource
 * **is_sluggable()** - check if the resource is set to use slugs instead of ids
 * **children($child_name = NULL)** - get an array of the child resources of the resource
 * **name()** - get the name of the resource
 * **model()** - get the model name of the resource
 * **key()** - get the key of the resource
 * **type()** - get the type of the resource
 * **parent()** - get the parent resource of the resource
 * **field()** - get the field of the resource
 * **path_string()** - get path_string of the resource
 * **route_name()** - get the route_name option of the resource
 * **controller()** - get the name of the controller for the resource
 * **object_route()** - get the object route object for the resource
 * **collection_route()** - get the collection object route object for the resource

### Resource_Route class ###

The Resource_Route class extends Kohana_Route and the Route class extends the Resource_Route. It allows the resource module to attach resources to corresponding routes.

#### Static methods ####

 * **set($name, $uri_callback = NULL, $regex = NULL, $resource = NULL)** - set the route with a resource attached

#### Non-static methods ####

 * **__contstruct($uri = NULL, $regex = NULL, $resource = NULL)** - constructor for the Resource_Route class
 * **resource()** - get the resource associated with the route