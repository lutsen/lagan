<p align="center">
  <img src="https://cdn.rawgit.com/lutsen/lagan/master/lagan-logo.svg" width="100" alt="Lagan">
</p>
<p align="center"><b>Any content, with a backend</b></p>
<p align="center">Lagan lets you create flexible content objects with a simple class,<br />and manage them with a web interface that is 'automagically' created.</p>

Why Lagan?
----------

- Content models are easily created and modified
- Content models consist of a simple combination of arrays
- Content models can be any combination of properties
- Configuration and editing are separated
- All configuration is done by code, so developers are in control there
- Content can be edited with a web interface, so editors can do their thing
- Lagan is built on proven open-source PHP libraries
- It is easy to extend with new content property types
- Create Twig front-end templates to display your content the way you want

Lagan is built with my favourite PHP libraries:
- [Slim framework](http://www.slimframework.com/)
- [RedBean ORM](http://redbeanphp.com/)
- [Twig template engine](http://twig.sensiolabs.org/)



Requirements
------------

- PHP 5.5 or newer
- A database. I use MySQL in this repo for now, but others should work as well. [Check out the RedBean documentation for that](http://redbeanphp.com/index.php?p=/connection).
- An Apache webserver if you want to use the .htaccess URL rewriting. But other webservers should work as well; [check out the Slim documentation for that](http://www.slimframework.com/docs/start/web-servers.html).
- [PDO plus driver for your database](http://php.net/manual/en/book.pdo.php) (Usually installed)
- [Multibyte String Support](http://php.net/manual/en/book.mbstring.php) (Usually installed too)



Install Lagan
=============

Install Lagan and its dependencies with [Composer](https://getcomposer.org/) with this command: `$ php composer.phar create-project lagan/lagan [project-name]`  
(Replace [project-name] with the desired directory name for your new project)

The Composer script creates the *cache* directory, *config.php* file and RedBean *rb.php* file for you.

Update *config.php* with:
- your database settings
- your server paths
- the admin user(s) and their password(s)

Lagan uses [Slim HTTP Basic Authentication middleware](http://www.appelsiini.net/projects/slim-basic-auth) to authenticate users for the admin interface. Make sure to change the password in *config.php*, and use HTTPS to login securely.



Use Lagan
=========


Content models
--------------

After installing Lagan, you can begin adding your content models. This is where the "magic" of Lagan happens. Each type of content has it's own model. I added 3 example models, *Crew.php*, *Feature.php* and *Hoverkraft.php*. If you open them you will see they have a type, a description and an aray with different content properties.

You can add your own content models by just adding class files like this to the *models/lagan* directory. Lagan will automatically create and update database tables for them. Nice!  
[> More about the content model structure](#structure-of-a-lagan-model)



Web interface
-------------
You can enter the Lagan web interface by going to the */admin* directory on the webserver where you installed Lagan. Here you can log in with the username and password you added in the *config.php* file. Now you can add or edit content objects based on the Lagan models.



Routes
------

In the directory *routes* you can add your public routes to the *public.php* file. You can add your own route files as well. The routes are automatically included in your Lagan app.

In the routes you can use the Lagan model CRUD methods to read and manipulate your data.  
[> More about the content model methods](#methods-of-a-lagan-model)



Templates
---------

Lagan uses [Twig](http://twig.sensiolabs.org/) as its template engine. You can add your templates to the *templates/public* directory and add them to your routes to use them in your app.



Structure of a Lagan model
--------------------------

All Lagan content models extend the *Lagan* main model. They contain a type, a description and an aray with different content properties.  
The *Lagan* main model is part of the [Lagan Core](https://packagist.org/packages/lagan/core) repository.

A simple Lagan model looks like this:

```php
namespace Lagan\Model;

class Book extends \Lagan\Lagan {

  function __construct() {
    $this->type = 'book';
    
    $this->description = 'These objects contain information about a book.';

    $this->properties = [
      [
        'name' => 'title',
        'description' => 'The book title',
        'type' => '\Lagan\Property\Str',
        'input' => 'text'
      ]
    ];
  }

}
```

### Type ###

`$this->type` is the type of the model. It is the same as the modelname in lowercase, and defines the name of the RedBean beans and the name of the table in the database.


### Description ###

`$this->description` is the description of the model. It is displayed in the admin interface. It explains the function of the content model to the user.


### Properties ###

`$this->properties` are the properties of the model. They are an array defining the different content data-fields of the model. Each content model should always have a string type property named title.

Each property is an array with the following keys:

- *name*: Required. The name of the property. Also the name of the corresponding RedBean property. Contains only alphanumeric characters, should not contain spaces.
- *description*: Required. The form-field label of the property in the admin interface.
- *required*: Optional. Set to true if the property is required.
- *searchable*: Optional. Set to true if the property has to be searchable with the Search controller.
- *type*: Required. The type of data of the property. This defines which property type controller to use. More information under ["Property types"](#property-type-controllers).
- *input*: Required. The template to use in the admin interface. Templates are located in the *public/property-templates* directory.

There can be other optional keys, for example the *directory* key for the *image_select* property input type.

A properties array with more keys might look like this:

```php
$this->properties = [
  [
    'name' => 'title',
    'description' => 'The book title',
    'required' => true,
    'searchable' => true,
    'type' => '\Lagan\Property\Str',
    'input' => 'text',
    'validate' => 'minlength(3)'
  ]
];
```


Methods of a Lagan model
------------------------

All Lagan content models extend the *Lagan* main model. Doing so they inherit it's methods.  
Lagan offers the CRUD methods: *Create*, *Read*, *Update* and *Delete*.

Lagan uses [RedBean](http://redbeanphp.com/) to manipulate data in the database. Redbean returns data from the database as objects called [beans](http://redbeanphp.com/crud/).


### Create ###

`create($data)` creates a RedBean bean in the database, based on the corresponding Lagan content model, and returns it. The *$data* variable is an array with at least the required properties. The array can be your HTML form POST data.

```php
$book = new \Lagan\Model\Book;
$bean = $book->create($data);
```


### Read ###

`read($id)` reads a bean based on the corresponding Lagan model from the database and returns it. The *$id* variable is the id of the Lagan model bean.

Read a Book model bean with id 1:
```php
$bean = $book->read(1);
```

Read all Book model beans:
```php
$beans = $book->read();
```

### Update ###

`update($data, $id)` updates a bean based on the corresponding Lagan model from the database and returns it. The *$data* variable is an array with at least the required properties. The array can be your HTML form POST data. The *$id* variable is the id of the Lagan model bean.

Update the Book model bean with id 1:
```php
$bean = $book->update($data, 1);
```


### Delete ###

`delete($id)` deletes a bean based on the corresponding Lagan model from the database. The *$id* variable is the id of the Lagan model bean.

Delete the Book model bean with id 1:
```php
$bean = $book->delete(1);
```



Searching entries of a Lagan model
----------------------------------

Each Lagan content model can be searched using the Search controller. The search controller is part of the [Lagan Core](https://packagist.org/packages/lagan/core) repository.  
Start by setting up the search controller in a route like this: `$search = new \Lagan\Search('book');`  
The search model now can use the GET request parameters to perform a search: `$search->find( $request->getParams() )`  
It can only search properties that are set to be [searchable](#properties).

```php
$search = new \Lagan\Search('book');
$result = $search->find( $request->getParams() );
```

Search has the following options:

- From: *min
- To: *max
- Contains: *has
- Equal to: *is
- Sort: sort by property. `asc` sorts ascending and `desc` sorts descending
- Limit: limit number of results
- Offset: where to start returning results

Offset only works if limit is defined too.

Some query structure examples:  
`path/to/search?*has=[search string]`: Searches all searchable properties of a model  
`path/to/search?[property]*has=[search string]`: Searches single [property] of a model  
`path/to/search?[property]*min=[number]`: Searches all model with a minimum [number] value of [property]  
`path/to/search?[property]*has=[search string]&sort=[property]*asc`:  Searches single [property] of a model and sorts the result ascending  


That's it! Now you know everything you need to know to start using Lagan.  
Want to extend Lagan? Read on!



Extend Lagan
============

You can extend Lagan by adding your own property types to it. All Lagan property controllers are separate dependencies. You can include them to your Lagan app with Composer. To edit properties in the Lagan web interface you need a property template. You can add new property templates to Lagan with Composer too. Check out the *composer.json* file to see which properties and templates are included.



Property type controllers
-------------------------

Each property type controller is a dependency, added with Composer. This way new property types can be developed seperate from the Lagan project code. These are the property types now installed by Composer when installing Lagan:

- **File select**: [\Lagan\Property\Fileselect](https://packagist.org/packages/lagan/property-fileselect)  
  Lets the user select a file from a directory

- **Many to many**: [\Lagan\Property\Manytomany](https://packagist.org/packages/lagan/property-manytomany)  
  Define a many-to-many relation between two content entries

- **Many to one**: [\Lagan\Property\Manytoone](https://packagist.org/packages/lagan/property-manytoone)  
  Define a may-to-one relation between two content objects

- **One to many**: [\Lagan\Property\Onetomany](https://packagist.org/packages/lagan/property-onetomany)  
  Define a one-to-many relation between two content objects

- **Position**: [\Lagan\Property\Position](https://packagist.org/packages/lagan/property-position)  
  Define the order of content objects of the same type.

- **Slug**: [\Lagan\Property\Slug](https://packagist.org/packages/lagan/property-slug)  
  Creates a slug from a string, and checks if it's unique

- **String**: [\Lagan\Property\Str](https://packagist.org/packages/lagan/property-string)  
  Input and validate a string

- **Upload**: [\Lagan\Property\Upload](https://packagist.org/packages/lagan/property-upload)  
  Lets the user upload a file


### Property type controller methods ###

A property type controller can contain a *set*, *read*, *delete* and *options* method. All methods are optional.

- The **set** method is executed each time a property with this type is set.
- The **read** method is executed each time a property with this type is read.  
  Note: For performance reasons, the read method is only executed for reading a single bean. Related beans are not returned.  
- The **delete** method is executed each time a an entry with a property with this type is deleted. 
- The **options** method returns all possible values for this property.


Property input templates
------------------------

To edit a property in the backend web interface it needs a template. Each property template is also a dependency, added with Composer. They are put in the *public/property-templates* directory, so outside the vendor directory. This is done using a [Composer plugin](https://packagist.org/packages/lagan/template-installer-plugin). By placing them outside the Vendor directory they can contain stuff like Javascript or images.

Currently these templates are available:

- **[fileselect](https://packagist.org/packages/lagan/template-fileselect)**  
  Template to edit Lagan fileselect properties.

- **[manytoone](https://packagist.org/packages/lagan/template-manytoone)**  
  Template to edit Lagan many-to-one properties.

- **[tomany](https://packagist.org/packages/lagan/template-tomany)**  
  Template to edit Lagan one-to-many and many-to-many properties.

- **[text](https://packagist.org/packages/lagan/template-text)**  
  Template for Lagan properties that require text input

- **[textarea](https://packagist.org/packages/lagan/template-textarea)**  
  Textarea template for Lagan properties that require multiple lines of text input.

- **[upload](https://packagist.org/packages/lagan/template-upload)**  
  Template for Lagan upload properties.


The properties of the property and the content bean are available in the template. To get the property name for example, use this Twig syntax: `{{ property.name }}`. To get the content of the specific property, use `{{ bean[property.name] }}`.


JSON API
--------

The [Lagan JSON API route repository](https://github.com/lutsen/Lagan-JSON-API-route) contains a route file to add a JSON API to your Lagan project. To install, add the *api.php* file to the *routes* directory. To protect the */api/write* route, add it to the Slim HTTP Basic Authentication middleware setup in the index.php file:
`'path' => ['/admin', '/api/write']`.



Lagan project structure
=======================

An overview of the directories of a Lagan app and their contents.


#### cache (directory) ####

The Composer script creates this directory in the project root to hold the Twig template engine cache files. If updates in your templates are not showing; remember to clear the cache directory.


#### models/lagan (directory) ####

Contains all the different Lagan content models. They are in a seperate *lagan* directory so you can add your own models to the main *model* directory.


#### public (directory) ####

Contains the *index.php* and *.htaccess* file. The *index.php* file contains the autoloader, includes the route files, and includes some other files and settings.

*The "public" directory is the directory holding your public web pages on your webserver. It's name can vary on different hosting providers and -environments. Other common names are "html", "private-html", "www" or "web". Put the files of the "public" directory in this public directory on your webserver.*


#### public/property-templates (directory) ####

Created by [Composer](https://getcomposer.org/). Here Composer will add all the templates needed to edit a property in the backend web interface.


#### routes (directory) ####

Contains the different route files. Each route file is automatically loaded, and contains the routes for your project. Routes are built with [Slim](http://www.slimframework.com/). Data is retrieved using Lagan models, or by using [RedBean](http://redbeanphp.com/) directly. You can add your own route files here, or add them to an existing route file.  
This directory also contains *functions.php* which contains some route helper functions used in multiple route files.


#### templates (directory) ####

This directory contains the template files (except the property templates). The subdirectory *admin* contains all the template files for the admin environment.  
Bonus feature: the subdirectory *static* contains the template files for static pages and a 404 page. Static pages display if the route name matches their name, and no other route for this name exists. Convenient!


#### vendor (directory) ####

Created by [Composer](https://getcomposer.org/) when installing the project dependencies.


#### config.php (file) ####

The Composer script renames the *config_example.php* file to *config.php*. The *config.php* file is needed for a Lagan project to work. Remember to add the necessary details.


Where does the name Lagan come from, and how do you pronounce it?
-----------------------------------------------------------------

[River Lagan](https://en.wikipedia.org/wiki/River_Lagan) is a river that runs through [Belfast](https://en.wikipedia.org/wiki/Belfast). I lived in Belfast when I created Lagan.  
Lagan is pronounced /'laeg=n/ with stress on first syllable, /ae/ as in "cat" and /=/ as in the schwah or neutral "e" sound in English. (Eg "letter" = /'let=(r)/.)


Bugs
----

- Position property is -1 if first item has a position > 0


To do
-----

- Throw error if model does not have a title property
- Create auto generate option for properties. This forces a value for a property, also if it is not submitted on creation. Like a slug or a UID for example.


Nice to have
------------

- More unit tests
- Finetune search in admin
- Sort items in admin list of position property exists
- A [tree](http://www.redbeanphp.com/index.php?p=/trees) structutre relationship between objects
- Adding extended user login and rights management stuff
- Full featured install script to create working config file



Lagan is a project of [Lútsen Stellingwerff](http://lutsen.land/) from [HoverKraft](http://www.hoverkraft.nl/), and started as the backend for [Cloud 9](https://www.cloud9.world/).