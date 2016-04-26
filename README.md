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



Installation
------------

Install all-but-one dependencies using Composer.
Imstall RedBean by downloading it from the Redbean website: http://redbeanphp.com
Add the Redbean rb.php file to the vendor directory.

Rename config_example.php to config.php and add your database and path info.

Lagan uses [Slim HTTP Basic Authentication middleware](http://www.appelsiini.net/projects/slim-basic-auth) to authenticate users for the admin interface. Make sure to change the password in index.php, and use HTTPS to login securely.



Create a Lagan model
--------------------

All Lagan model names should start with Lagan. So Lagan[Modelname].php



To do
-----

- Get app URI from app instead of defining them in config.php
- Move LaganHoverkraft property methods to main Lagan class?



Nice to haves
-------------

- Message "There are no [beantype]" in beans.html template if a beantype is empty
- Add a logger: https://github.com/Flynsarmy/Slim-Monolog
- Unit testing
- Drag-n-drop interface for the position of objects



Lagan is a project of [Lútsen Stellingwerff](http://lutsen.land/) from [HoverKraft](http://www.hoverkraft.nl/), and started as the backend for [Cloud 9](https://www.cloud9.world/).