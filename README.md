Lagan
=====

Lagan lets you easily create flexible content objects with a simple class, and manage them with a web interface. Lagan object properties are easy to validate and property types are simple to extend.
Lagan can be uses as a CMS for content that requires any combination of custom data fields.
Because Lagan is built with RedBean ORM, it is very flexible and database fields can be easily modified during development.

Lagan is built with:
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

Install all-but-one dependencies using Composer.  
Install RedBean by downloading it from the RedBean website: http://redbeanphp.com  
Add the RedBean *rb.php* file to the vendor directory.

Rename *config_example.php* to *config.php* and add your database and path info.

In the project root, create a folder called *cache* for the Twig cache.

Lagan uses [Slim HTTP Basic Authentication middleware](http://www.appelsiini.net/projects/slim-basic-auth) to authenticate users for the admin interface. Make sure to change the password in *index.php*, and use HTTPS to login securely.



Create a Lagan model
--------------------

The "magic" of Lagan is in the Lagan models. Each type of content has it's own model. I added two example models, *LaganHoverKraft.php* and *LaganCrew.php*.

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
- *input*: The type of data to input. This defines which input type controller and template to use. More information under "Input types".

There can be other optional keys, for example the *directory* key for the *image_select* property input type.


### Rules: validation ###

`$this->rules` are the rules of the model. They are an array of validation rules. For the validation we use [Valitron](https://github.com/vlucas/valitron), so the available validation rules are the same.

The key of each array in the `$this->rules` array is the name of the validation rule. The values in the value array of each rule are the names of the properties to apply the rule to.



Input types
-----------

Each property input type has it's own directory in the directory *input*. In this directory can be an input type controller and an input type template.


### Input type controller ###

An input type controller can contain a *set*, *delete* and *options* method.  
The *set* method is executed each time a property with this input type is set.  
The *delete* method is executed each time a an object with a property with this input type is deleted.  
In the *LaganHoverkraft.php* for example, the *setPosition* and *deletePosition* method are used to check and update the position of other hoverkraft objects if the position of one hoverkraft update is changed.  
The *options* method returns all possible values for this property.



Lagan project structure
-----------------------

(Coming soon)


To do
-----

- Extend the documentation
- Remove admin and setup from index.php
- Add example templates and route for public part of app
- Get app URI from app instead of defining them in config.php
- Add different types of relations
- Add populate methods to each property input type class instead of th Lagan.php



Nice to have
------------

- Message "There are no [beantype]" in beans.html template if a beantype is empty
- Add a logger: https://github.com/Flynsarmy/Slim-Monolog
- Unit testing
- Drag-n-drop interface for the position of objects
- Create seperate repositories for the slug and position controller?



Lagan is a project of [Lútsen Stellingwerff](http://lutsen.land/) from [HoverKraft](http://www.hoverkraft.nl/), and started as the backend for [Cloud 9](https://www.cloud9.world/).