CHANGELOG
-------------
### Opis HTTP Routing 2.2.2, 2014.06.08

* Modified serialize method to be compatible with `opis/routing:2.2.1`

### Opis HTTP Routing 2.2.1, 2014.06.05

* Fixed a bug in `Opis\HttpRouting\RouteCollection`

### Opis HTTP Routing 2.2.0, 2014.06.03

* Started changelog
* Added `accessDenied` method to `Opis\HttpRouting\RouteCollection`
* Removed `useFilters` method from `Opis\HttpRouting\Route`
* Added `preFilter`, `postFilter` and `accessFilter` methods to `Opis\HttpRouting\Route` class
* Added `Opis\HttpRouting\ClosureFilter` class
* Modified `Opis\HttpRouting\UserFilter` class
* Changed filter order in `Opis\HttpRouting\Router`
* Updated `opis/routing` dependency to version `2.2.*`
