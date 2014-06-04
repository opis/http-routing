Opis Routing
============
[![Latest Stable Version](https://poser.pugx.org/opis/http-routing/version.png)](https://packagist.org/packages/opis/http-routing)
[![Latest Unstable Version](https://poser.pugx.org/opis/http-routing/v/unstable.png)](//packagist.org/packages/opis/http-routing)
[![License](https://poser.pugx.org/opis/http-routing/license.png)](https://packagist.org/packages/opis/http-routing)

Extendable HTTP routing component
---------------------

###Installation

This library is available on [Packagist](https://packagist.org/packages/opis/http-routing) and can be installed using [Composer](http://getcomposer.org)

```json
{
    "require": {
        "opis/http-routing": "2.2.*"
    }
}
```

###Examples

```php
use \Opis\HttpRouting\Route;
use \Opis\HttpRouting\Router;
use \Opis\HttpRouting\RouteCollection;
use \Opis\HttpRouting\Path;

$collection = new RouteCollection();

$collection[] = Route::create('/{category}', function($category){
        return $category;
    })
    ->domain('{subdomain?}.localhost')
    ->where('subdomain', 'php')
    ->where('category', '[a-z]+')
    ->bind('category', function($category){
        return strtoupper($category);
    });

$collection->notFound(function($path){
   return 'Not found ' . $path->domain() . $path; 
});


$router = new Router($collection);

print $router->route(new Path('/webservice')); //> WEBSERVICE
print $router->route(new Path('/webservice', 'php.localhost')); //> WEBSERVICE
print $router->route(new Path('/webservice', 'www.localhost')); //> Not found www.localhost/webservice

//Serialization
$collection = unserialize(serialize($collection));

$router = new Router($collection);
print $router->route(new Path('/serialization', 'php.localhost')); //> SERIALIZATION
```
