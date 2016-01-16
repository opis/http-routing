<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2016 Marius Sarca
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */
use Opis\HttpRouting\Path;
use Opis\HttpRouting\Route;
use Opis\HttpRouting\Router;
use Opis\HttpRouting\HttpError;
use Opis\HttpRouting\RouteCollection;

class RoutingTest extends PHPUnit_Framework_TestCase
{
    protected $router, $collection;

    public function setUp()
    {
        $this->collection = new RouteCollection();
        $this->router = new Router($this->collection, null, null, array('x' => 'X'));
        $this->collection->notFound(function() {
            return 404;
        });
        $this->collection->accessDenied(function() {
            return 403;
        });
    }

    protected function route($pattern, $action, $method = 'GET')
    {
        $route = Route::create($pattern, $action, $method);
        $this->collection[] = $route;
        return $route;
    }

    protected function exec($path, $domain = 'localhost', $method = 'GET', $secure = false)
    {
        return $this->router->route(new Path($path, $domain, $method, $secure));
    }

    public function testBasicRouting()
    {
        $this->route('/', function() {
            return 'OK';
        });

        $this->assertEquals('OK', $this->exec('/'));
    }

    public function testNotFound1()
    {
        $this->assertEquals(404, $this->exec('/'));
    }

    public function testNotFound2()
    {
        $this->route('/', function() {
            return 'OK';
        });

        $this->assertEquals(404, $this->exec('/foo'));
    }

    public function testNotFound3()
    {
        $this->route('/', function() {
            return HttpError::pageNotFound();
        });

        $this->assertEquals(404, $this->exec('/'));
    }

    public function testParam()
    {
        $this->route('/{foo}', function($foo) {
            return $foo;
        });

        $this->assertEquals('bar', $this->exec('/bar'));
    }

    public function testParamConstraintSuccess()
    {
        $this->route('/{foo}', function($foo) {
                return $foo;
            })
            ->where('foo', '[a-z]+');

        $this->assertEquals('bar', $this->exec('/bar'));
    }

    public function testParamConstraintFail()
    {
        $this->route('/{foo}', function($foo) {
                return $foo;
            })
            ->where('foo', '[a-z]+');

        $this->assertEquals(404, $this->exec('/123'));
    }

    public function testParamOptional1()
    {
        $this->route('/{foo?}', function($foo) {
            return $foo;
        });

        $this->assertEquals('bar', $this->exec('/bar'));
    }

    public function testParamOptional2()
    {
        $this->route('/{foo?}', function($foo = 'bar') {
            return $foo;
        });

        $this->assertEquals('bar', $this->exec('/'));
    }

    public function testParamOptional3()
    {
        $this->route('/{foo?}', function($foo) {
                return $foo;
            })
            ->implicit('foo', 'bar');

        $this->assertEquals('bar', $this->exec('/'));
    }

    public function testMultipleParams()
    {
        $this->route('/{foo}/{bar}', function($bar, $foo) {
            return $bar . $foo;
        });

        $this->assertEquals('barfoo', $this->exec('/foo/bar'));
    }

    public function testLocalBeforeFilterSuccess()
    {
        $this->route('/', function() {
                return 'OK';
            })
            ->filter('foo', function() {
                return true;
            })
            ->before('foo');

        $this->assertEquals('OK', $this->exec('/'));
    }

    public function testLocalBeforeFilterFail()
    {
        $this->route('/', function() {
                return 'OK';
            })
            ->filter('foo', function() {
                return false;
            })
            ->before('foo');

        $this->assertEquals(404, $this->exec('/'));
    }

    public function testGlobalBeforeFilterSuccess()
    {
        $this->collection->filter('foo', function() {
            return true;
        });

        $this->route('/', function() {
                return 'OK';
            })
            ->before('foo');

        $this->assertEquals('OK', $this->exec('/'));
    }

    public function testGlobalBeforeFilterFail()
    {
        $this->collection->filter('foo', function() {
            return false;
        });

        $this->route('/', function() {
                return 'OK';
            })
            ->before('foo');

        $this->assertEquals(404, $this->exec('/'));
    }

    public function testLocalBinding1()
    {
        $this->route('/{foo}', function($foo) {
                return $foo;
            })
            ->bind('foo', function($foo) {
                return strtoupper($foo);
            });

        $this->assertEquals('BAR', $this->exec('/bar'));
    }

    public function testLocalBinding2()
    {
        $this->route('/', function($foo) {
                return $foo;
            })
            ->bind('foo', function() {
                return 'BAR';
            });

        $this->assertEquals('BAR', $this->exec('/'));
    }

    public function testGlobalBinding1()
    {
        $this->collection->bind('foo', function($foo) {
            return strtoupper($foo);
        });

        $this->route('/{foo}', function($foo) {
            return $foo;
        });

        $this->assertEquals('BAR', $this->exec('/bar'));
    }

    public function testGlobalBinding2()
    {
        $this->collection->bind('foo', function() {
            return 'BAR';
        });

        $this->route('/', function($foo) {
            return $foo;
        });

        $this->assertEquals('BAR', $this->exec('/'));
    }

    public function testSpecials1()
    {
        $this->route('/', function($x) {
            return $x;
        });

        $this->assertEquals('X', $this->exec('/'));
    }

    public function testSpecials2()
    {
        $this->route('/', function($y) {
            return $y;
        })
        ->bind('y', function($x){
           return $x; 
        });

        $this->assertEquals('X', $this->exec('/'));
    }
}
