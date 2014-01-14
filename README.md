##Opis Http Routing Component##

Experimental

```php
use \Opis\Http\Request;
use \Opis\HttpRouting\Route;
use \Opis\HttpRouting\Router;
use \Opis\HttpRouting\RouteCollection;
use \Opis\HttpRouting\Path;

$request = Request::create('/hello/opis');
$path = new Path($request);

$collection = new RouteCollection();

$collection[] = Route::create('/hello/{user}', function($user){
        return $user;
    })
    ->where('user', '[a-z]+')
    ->bind('user', function($value){
        return strtoupper($value);
    });

$router = new Router($collection);

$router->route($path);
```

Output

```
OPIS
```