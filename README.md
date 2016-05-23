[<img src="https://cdn.rawgit.com/lutsen/lagan/master/lagan-logo.svg" width="100" alt="Lagan">](https://github.com/lutsen/lagan)

**Any content,  
with a backend**

Lagan lets you create flexible content objects with a simple class, and manage them with a web interface.



Why Lagan?
==========

- Lagan tries to be as simple as possible (but not simpler)
- Configuration and editing are separated
- All configuration is done by code, so developers are in control there
- Content can be edited with a web interface, so editors can do their thing
- Lagan is built on proven open-source PHP libraries
- Content models consist of a simple combination of arrays
- Content models can be any combination of properties
- It is easy to add new property types
- Lagan has built-in input validation
- Thanks to RedBean, content models can be easily modified during development and even production
- Create Twig front-end templates to display your content the way you want

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



Install Lagan
=============

Install all-but-one dependencies using [Composer](https://getcomposer.org/).  
Install RedBean by downloading it from the RedBean website: http://redbeanphp.com  
Add the RedBean *rb.php* file to the vendor directory.

Rename *config_example.php* to *config.php* and add your database and path info.

In the project root, create a folder called *cache* for the Twig cache.

Lagan uses [Slim HTTP Basic Authentication middleware](http://www.appelsiini.net/projects/slim-basic-auth) to authenticate users for the admin interface. Make sure to change the password in *index.php*, and use HTTPS to login securely.



Use Lagan
=========


Content models
--------------

After installing Lagan, you can begin adding your content models. This is where the "magic" of Lagan happens. Each type of content has it's own model. I added 2 example models, *Hoverkraft.php* and *Crew.php*. If you open them you will see they have a type, a description, an aray with different content properties and an array with validation rules.

You can add your own content models by just adding class files like this to the *models/lagan* directory. Lagan will automatically create and update database tables for them. Nice!  
[> More about the content model structure](#structure-of-a-lagan-model)



Routes
------

In the directory *routes* you can add your public routes to the *public.php* file. You can add your own route files as well. The routes are automatically included in your Lagan app.

In the routes you can use the Lagan model CRUD methods to read and manipulate your data.  
[> More about the content model methods](#methods-of-a-lagan-model)



Templates
---------

Lagan uses [Twig](http://twig.sensiolabs.org/) as its template engine. You can add your templates to the *templates/public* directory and add them to your routes to use them in your app.



Structure of a Lagan model
==========================

All Lagan content models extend the *Lagan* main model. They contain a type, a description, an aray with different content properties and an (optional) array with validation rules.

### Type ###

`$this->type` is the type of the model. It is the same as the modelname in lowercase, and defines the name of the RedBean beans and the name of the table in the database.


### Description ###

`$this->description` is the description of the model. It is displayed in the admin interface. It explains the function of the content model to the user.


### Properties ###

`$this->properties` are the properties of the model. They are an array defining the different content data-fields of the model. Each property is an array with at least the following keys:

- *name*: The name of the property. Also the name of the corresponding RedBean property. Contains only alphanumeric characters, should not contain spaces.
- *description*: The form-field label of the property in the admin interface.
- *type*: The type of data of the property. This defines which property type controller to use. More information under "Property types".
- *input*: The template to use in the admin interface. Templates are located in the *public/property-templates* directory.

There can be other optional keys, for example the *directory* key for the *image_select* property input type.


### Rules: validation ###

`$this->rules` are the rules of the model. They are an array of validation rules. For the validation we use [Valitron](https://github.com/vlucas/valitron), so the available validation rules are the same.

The key of each array in the `$this->rules` array is the name of the validation rule. The values in the value array of each rule are the names of the properties to apply the rule to.



Methods of a Lagan model
========================

All Lagan content models extend the *Lagan* main model. Doing so they inherit it's methods.  
Lagan offers the CRUD methods: *Create*, *Read*, *Update* and *Delete*.  
Lagan uses RedBean to manipulate data in the database. Redbean returns data from the database as objects called beans.


### Create ###

`create($data)` creates a RedBean bean in the database, based on the corresponding Lagan content model, and returns it. The *$data* variable is an array with at least the required properties. The array can be your HTML form POST data.


### Read ###

`read($id)` reads a bean based on the corresponding Lagan model from the database and returns it. The *$id* variable is the id of the Lagan model bean.


### Update ###

`update($data, $id)` updates a bean based on the corresponding Lagan model from the database and returns it. The *$data* variable is an array with at least the required properties. The array can be your HTML form POST data. The *$id* variable is the id of the Lagan model bean.


### Delete ###

`delete($id)` deletes a bean based on the corresponding Lagan model from the database. The *$id* variable is the id of the Lagan model bean.



Extend Lagan
============

You can extend Lagan by adding your own property types to it. All Lagan property controllers are separate dependencies. You can include them to your Lagan app with Composer. To edit properties in the Lagan web interface you need a property template. You can add new property templates to Lagan with Composer too. Check out the *composer.json* file to see which properties and templates are included.



Property type controllers
-------------------------

Each property type controller is a dependency, added with Composer. This way new property types can be developed seperate from the Lagan project code. These are the property types now installed by Composer when installing Lagan:

- fileselect
- manytomany
- manytoone
- onetomany
- position
- slug
- string
- upload


### Property type controller methods ###

A property type controller can contain a *set*, *read*, *delete* and *options* method. All methods are optional.
The *set* method is executed each time a property with this type is set.
The *read* method is executed each time a property with this type is read.  
Note: For performance reasons, the read method is only executed for reading a single bean. Because of this, only use it when it is really necessary. Otherwise, try to store the value to be returned when reading the bean directly in the database whenever possible.  
The *delete* method is executed each time a an entry with a property with this type is deleted.
The *options* method returns all the optional values this property can have.
In *Hoverkraft.php* for example, the *setPosition* and *deletePosition* method are used to check and update the position of other hoverkraft entries if the position of one hoverkraft update is changed.  
The *options* method returns all possible values for this property.


Property input templates
------------------------

To edit a property in the backend web interface it needs a template. Each property template is also a dependency, added wth Composer. They are put in the *public/property-templates* directory.

Currently these templates are available:

- fileselect
- manytoone
- onetomany
- static
- text
- textarea
- upload

The properties of the property and the content bean are available in the template. To get the property name for example, use this Twig syntax: `{{ property.name }}`. To get the content of the specific property, use `{{ bean[property.name] }}`.



Lagan project structure
=======================

An overview of the directories of a Lagan app and their contents.


#### cache (directory) ####

You need to create this directory in the project root to hold the Twig template engine cache files. If updates in your templates are not showing; remember to clear the cache directory.


#### models (directory) ####

Contains the main Lagan model.


#### models/lagan (directory) ####

Contains all the different Lagan content models.


#### public (directory) ####

Contains the *index.php* and *.htaccess* file. The *index.php* file contains the autoloader, includes the route files, and includes some other files and settings.

*The "public" directory is the directory holding your public web pages on your webserver. It's name can vary on different hosting providers and -environments. Other common names are "html", "private-html", "www" or "web". Put the files of the public directory in this public directory on your webserver.*


#### public/property-templates (directory) ####

Created by [Composer](https://getcomposer.org/). Here Composer will add all the templates needed to edit a property in the backend web interface.


#### routes (directory) ####

Contains the different route files. Each route file is automatically loaded, and contains the routes for your project. Routes are built with [Slim](http://www.slimframework.com/). Data is retrieved using Lagan models, or by using [RedBean](http://redbeanphp.com/) directly. You can add your own route files here, or add them to an existing route file.


#### templates (directory) ####

This directory contains the template files (except the property templates). The subdirectory *admin* contains all the template files for the admin environment.  
Bonus feature: the subdirectory *static* contains the template files for static pages and a 404 page. Static pages display if the route name matches their name, and no other route for this name exists. Convenient!


#### vendor (directory) ####

Created by [Composer](https://getcomposer.org/) when installing the project dependencies. Remember to manually add the RedBean rb.php file to it.


#### config_example.php (file) ####

This is an example of the *config.php* file. The *config.php* file is needed for a Lagan project to work. Rename the *config_example.php* to *config.php* and add the necessary details.



To do
-----

- Check types in PHPDocumentor inline code documentation
- Test and then add "$ php composer.phar create-project lutsen/lagan [my-app-name]" to install documentation
- Add Hoverkraft features example to public templates



Nice to have
------------

- Project homepage
- Unit testing
- Design the editor
- Adding search options and/or routes
- Allow properties to be submitted independently. Now, the Lagan set method checks all the properties. It would be more flexible if it only checked the properties that are submitted.
- JSON API
- Adding extended user login and rights management stuff
- Drag-n-drop interface for the position of entries
- Add a logger: https://github.com/Flynsarmy/Slim-Monolog
- Replace "object" with "entry" in cases where entry is clearer (in property repo's)


Editor:

- Models in left side menu: Go to entries overview or add new one
- Home: latest edited entries


Submit properties independently:

- Required properties should only be checked on creation of the entry
- Only validate properties that are submitted :-p



Food for thought
----------------

"Fancy" features in the backend (like a file upload progress indicator) should never affect other elements of the form, because they can have their own "fancy" features. So to make those features possible, each entry property should be able to be submitted individually. How can this be combined with validation required properties in an entry?
A more advanced solution would be to allways submit forms via Javascript, and have each property "hook in" to this submit function.


Lagan is a project of [Lútsen Stellingwerff](http://lutsen.land/) from [HoverKraft](http://www.hoverkraft.nl/), and started as the backend for [Cloud 9](https://www.cloud9.world/).