## ⚠️ WARNING: This is the 5.x branch of this bundle, which is still in development. You probably want to check out the [4.x branch](https://github.com/EasyCorp/EasyAdminBundle/tree/4.x)

-----

EasyAdmin
=========

EasyAdmin is a fast, beautiful and modern admin generator for Symfony applications.

![EasyAdmin, a fast, beautiful and modern admin generator for Symfony applications](/doc/images/easyadmin-promo.jpg)

Installation
------------

EasyAdmin 5 requires PHP 8.2.0 or higher and Symfony 6.4 or higher. Run the
following command to install it in your application:

```
$ composer require easycorp/easyadmin-bundle
```

Documentation
-------------

  * Read [EasyAdmin Docs][1] on the official Symfony website
  * Check out the [EasyAdmin video tutorial][2] on SymfonyCasts

Versions
--------

| Repository Branch | EasyAdmin Version | Symfony Compatibility  | PHP Compatibility | Status               | Docs
| ----------------- | ----------------- | ---------------------- | ----------------- | -------------------- | ---
| `5.x`             | `5.x`             | `6.4`, `7.x` and `8.x` | `8.2.0` or higher | New features and bug fixes | [Read Docs](https://symfony.com/bundles/EasyAdminBundle/4.x/index.html)
| `4.x`             | `4.x`             | `5.4`, `6.x` and `7.x` | `8.0.2` or higher | Bug fixes only; no new features | [Read Docs](https://symfony.com/bundles/EasyAdminBundle/4.x/index.html)
| `3.x`             | `3.x`             | `4.4`, and `5.x`       | `7.2.5` or higher | No longer maintained | [Read Docs](https://symfony.com/bundles/EasyAdminBundle/3.x/index.html)
| `2.x`             | `2.x`             | `4.x`, and `5.x`       | `7.1.3` or higher | No longer maintained | [Read Docs](https://symfony.com/bundles/EasyAdminBundle/2.x/index.html)
| `1.x`             | `1.x`             | `2.x`, `3.x` and `4.x` | `5.3.0` or higher | No longer maintained | -

Demo Application
----------------

[easyadmin-demo](https://github.com/EasyCorp/easyadmin-demo) is a complete
Symfony application that showcases EasyAdmin features. It's based on the
[Symfony Demo](https://github.com/symfony/demo) project.

Dev Environment
---------------

EasyAdmin ships a DDEV environment, which allows you to run EasyAdmin in a Symfony Framework project
providing example entities and CRUD Controllers. 

**Requirements:**

  * [Docker](https://www.docker.com/get-started/)
  * [DDEV](https://ddev.com/get-started/)

**Using the environment:**

  * Checkout the EasyAdmin git repository and switch in the project directory
  * Perform `ddev setup` which starts and provisions the web container
  * EasyAdmin is available under the URL: https://easy-admin-bundle.ddev.site
  * To (re-)build frontend assets perform `ddev build-assets`
  * To run unit tests perform `ddev run-tests`

License
-------

This software is published under the [MIT License](LICENSE.md)

[1]: https://symfony.com/doc/4.x/bundles/EasyAdminBundle/index.html
[2]: https://symfonycasts.com/screencast/easyadminbundle
