Lagan
=====

Lagan lets you easily create flexible content objects with a simple class, and manage them with a web interface.

### Why Lagan? ###
It's easy to use, easy to build with and easy to extend:
- Create content objects with any combination if input types
- Content objects consist of a simple combination of arrays
- Built-in input validation
- It's simple to add new input types
- It comes with an admin web interface to manage your content
- Create front-end templates to display your content the way you want
- Database fields can be easily modified during development

Lagan is built with my favourite PHP libraries:
- [Slim framework](http://www.slimframework.com/)
- [RedBean ORM](http://redbeanphp.com/)
- [Twig template engine](http://twig.sensiolabs.org/)
- [Valitron validation library](https://github.com/vlucas/valitron)



Requirements
------------

- PHP 5.5 or newer
- A database. I use MySQL in this repo for now, but others should work as well. [Check out the RedBean documentation for that](http://redbeanphp.com/index.php?p=/connection).
- An Apache webserver if you want to use the .htaccess URL rewriting. But other webservers should work as well; [check out the Slim documentation for that](http://www.slimframework.com/docs/start/web-servers.html).
- [PDO plus driver for your database](http://php.net/manual/en/book.pdo.php) (Usually installed)
- [Multibyte String Support](http://php.net/manual/en/book.mbstring.php) (Usually installed too)



Installation
------------

Install all-but-one dependencies using [Composer](https://getcomposer.org/).  
Install RedBean by downloading it from the RedBean website: http://redbeanphp.com  
Add the RedBean *rb.php* file to the vendor directory.

Rename *config_example.php* to *config.php* and add your database and path info.

In the project root, create a folder called *cache* for the Twig cache.

Lagan uses [Slim HTTP Basic Authentication middleware](http://www.appelsiini.net/projects/slim-basic-auth) to authenticate users for the admin interface. Make sure to change the password in *index.php*, and use HTTPS to login securely.



Lagan project structure
-----------------------

#### cache (directory) ####

You need to create this directory in the project root to hold the Twig template engine cache files. If updates in your templates are not showing; remember to clear the cache directory.


#### properties (directory) ####

This directory contains all the [property types](#property-types).


#### models (directory) ####

Contains the main Lagan model and all the different [Lagan content models](#create-a-lagan-model).


#### public (directory) ####

Contains the *index.php* and *.htaccess* file. The *index.php* file contains the autoloader, includes the route files, and includes some other files and settings.

*The "public" directory is the directory holding your public web pages on your webserver. It's name can vary on different hosting providers and -environments. Other common names are "html", "private-html", "www" or "web". Put the files of the public directory in this public directory on your webserver.*


#### routes (directory) ####

Contains the different route files. Each route file is automaticly loaded, and contains the routes for your project. Routes are built with [Slim](http://www.slimframework.com/). Data is retrieved using Lagan models, or by using [RedBean](http://redbeanphp.com/) directly. You can add your own route files here, or add them to an existing route file.


#### templates (directory) ####

This directory contains the template files. The subdirectory *admin* contains all the template files for the admin environment.  
Bonus feature: the subdirectory *static* contains the template files for static pages and a 404 page. Static pages display if the route name matches their name, and no other route for this name exists. Convenient!


#### vendor (directory) ####

Created by [Composer](https://getcomposer.org/) when installing the project dependencies. Remember to manually add the RedBean rb.php file to it.


#### config_example.php (file) ####

This is an example of the *config.php* file. The *config.php* file is needed for a Lagan project to work. Rename the *config_example.php* to *config.php* and add the necessary details.



Create a Lagan model
--------------------

The "magic" of Lagan is in the Lagan models, located in the *model* directory. Each type of content has it's own model. I added two example models, *LaganHoverKraft.php* and *LaganCrew.php*.

All content models extend the *Lagan.php* class. Each model has a type, description and properties. These are defined in the `__construct` function of the model. Optional are the validation rules (also defined in `__construct`), and property methods.

All Lagan model names should start with Lagan. So *Lagan[Modelname].php*


### Type ###

`$this->type` is the type of the model. It is the same as the modelname in lowercase, and defines the name of the RedBean beans and the name of the table in the database.


### Description ###

`$this->description` is the description of the model. It is displayed in the admin interface. It explains the function of the content model to the user.


### Properties ###

`$this->properties` are the properties of the model. They are an array defining the different content data-fields of the model. Each property is an array with at least the following keys:

- *name*: The name of the property. Also the name of the corresponding RedBean property. Contains only alphanumeric characters, should not contain spaces.
- *description*: The form-field label of the property in the admin interface.
- *type*: The type of data of the property. This defines which property type controller and template to use. More information under "Property types".

There can be other optional keys, for example the *directory* key for the *image_select* property input type.


### Rules: validation ###

`$this->rules` are the rules of the model. They are an array of validation rules. For the validation we use [Valitron](https://github.com/vlucas/valitron), so the available validation rules are the same.

The key of each array in the `$this->rules` array is the name of the validation rule. The values in the value array of each rule are the names of the properties to apply the rule to.



Methods of a Lagan model
------------------------

All Lagan content models extend the *Lagan* main model. Doing so they inherit it's methods.  
Lagan offers the CRUD methods: *Create*, *Read*, *Update* and *Delete*.  
Lagan uses RedBean to manimpulate data in the database. Redbean returns data from the database as objects called beans.


### Create ###

`create($data)` creates a RedBean bean in the database, based on the corresponding Lagan content model, and returns it. The *$data* variable is an array with at least the required properties. The array can be your HTML form POST data.


### Read ###

`read($id)` reads a bean based on the corresponding Lagan model from the database and returns it. The *$id* variable is the id of the Lagan model bean.


### Update ###

`update($data, $id)` updates a bean based on the corresponding Lagan model from the database and returns it. The *$data* variable is an array with at least the required properties. The array can be your HTML form POST data. The *$id* variable is the id of the Lagan model bean.


### Delete ###

`delete($id)` deletes a bean based on the corresponding Lagan model from the database. The *$id* variable is the id of the Lagan model bean.



Property types
--------------

Each property type has it's own directory in the directory *properties*. In this directory can be an property type controller and an property type template.


### Property type controller ###

An property type controller can contain a *set*, *read*, *delete* and *options* method.  
The *set* method is executed each time a property with this type is set.
The *read* method is executed each time a property with this type is read.  
The *delete* method is executed each time a an object with a property with this type is deleted.  
In the *LaganHoverkraft.php* for example, the *setPosition* and *deletePosition* method are used to check and update the position of other hoverkraft objects if the position of one hoverkraft update is changed.  
The *options* method returns all possible values for this property.



To do
-----

- Extend Lagan read method to:
-- Search by any unique property
-- Return related beans
- Add different types of relations



Nice to have
------------

- Adding search options and/or routes
- Add a logger: https://github.com/Flynsarmy/Slim-Monolog
- Unit testing
- Drag-n-drop interface for the position of objects
- Adding extended user login and rights management stuff



Lagan is a project of [Lútsen Stellingwerff](http://lutsen.land/) from [HoverKraft](http://www.hoverkraft.nl/), and started as the backend for [Cloud 9](https://www.cloud9.world/).