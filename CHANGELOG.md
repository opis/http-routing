CHANGELOG
-------------
### v4.0.2, 2016.03.20

* Fixed a bug in `Route::domainCompiler`  method

### v4.0.1, 2016.01.19

* Fixed a bug in `Opis\HttpRouting\CallbackFilter`

### v4.0.0, 2016.01.16

* Removed `branch-alias` property from `composer.json` file
* Updated `opis/routing` library dependency to version `^4.1.0`
* Modified classes to reflect changes in `opis/routing` library
* Fixed CS
* Added tests

### v3.0.0, 2015.07.31

* Updated `opis/routing` library dependency to version `3.0.*`
* `preFilter`, `postFilter` and `accessFilter` methods were removed from `Opis\HttpRouting\Route` class
* `before`, `after` and `access` methods were added to `Opis\HttpRouting\Route` class
* `ClosureFilter` class was removed and it was replaced with `CallbackFilter` class
* Added `HttpError` class

### v2.5.0, 2015.03.20

* Added support for late binding
* Updated `opis/routing` library dependency to version `2.5.*`

### v2.4.2, 2014.11.25

* Added autoload file

### v2.4.1, 2014.11.11

* Bugfix in `Opis\HttpRouting\Route` class

### v2.4.0, 2014.10.23

* Updated `opis/routing` library dependency to version `2.4.*`

### v2.3.0, 2014.06.11

* Updated `opis/routing` library dependency to version `2.3.*`
* Updated `Opis\HttpRouting\Route` class to reflect changes that were made in `opis/routing`.

### v2.2.2, 2014.06.08

* Modified serialize method to be compatible with `opis/routing:2.2.1`

### v2.2.1, 2014.06.05

* Fixed a bug in `Opis\HttpRouting\RouteCollection`

### v2.2.0, 2014.06.03

* Started changelog
* Added `accessDenied` method to `Opis\HttpRouting\RouteCollection`
* Removed `useFilters` method from `Opis\HttpRouting\Route`
* Added `preFilter`, `postFilter` and `accessFilter` methods to `Opis\HttpRouting\Route` class
* Added `Opis\HttpRouting\ClosureFilter` class
* Modified `Opis\HttpRouting\UserFilter` class
* Changed filter order in `Opis\HttpRouting\Router`
* Updated `opis/routing` dependency to version `2.2.*`
