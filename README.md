##Opis Http Routing##

Experimental

```php
use \Opis\Http\Request;
use \Opis\HttpRouting\Route;
use \Opis\HttpRouting\Router;
use \Opis\HttpRouting\RouteCollection;

$request = Request::create('/hello/opis');

$collection = new RouteCollection();

$collection->pattern('subdomain', '(forum|blog)');

$collection[] = Route::create('/hello/{user}', function($user){
        print $user;
    })
    ->where('user', '[a-z]+')
    ->bind('user', function($value){
        return strtoupper($value);
    })
    ->domain('{subdomain?}.localhost');

$router = new Router($request, $collection);

$router->execute();
```

Output

```
OPIS
```