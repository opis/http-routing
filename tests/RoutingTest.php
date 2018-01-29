<?php
/* ===========================================================================
 * Copyright 2013-2018 The Opis Project
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

use Opis\Routing\Context;
use Opis\HttpRouting\Route;
use Opis\HttpRouting\Router;
use Opis\HttpRouting\RouteCollection;
use PHPUnit\Framework\TestCase;

class RoutingTest extends TestCase
{
    /** @var  Router */
    protected $router;
    /** @var  RouteCollection */
    protected $collection;

    public function setUp()
    {
        $this->collection = new RouteCollection();
        $global = new \Opis\Routing\GlobalValues();
        $global['x'] = 'X';
        $this->router = new Router($this->collection, null, null, $global);
    }

    /**
     * @param $pattern
     * @param $action
     * @param string $method
     * @return Route
     * @throws Exception
     */
    protected function route($pattern, $action, $method = 'GET')
    {
        $route = new Route($pattern, $action);
        $route->method($method);
        $this->collection->addRoute($route);
        return $route;
    }

    /**
     * @param $path
     * @param string $domain
     * @param string $method
     * @param bool $secure
     * @return \Opis\Http\Response
     * @throws Exception
     */
    protected function exec($path, $domain = 'localhost', $method = 'GET', $secure = false)
    {
        $server = [
            'HTTP_HOST' => $domain,
            'HTTPS' => $secure ? 'on' : 'off',
        ];
        $request = \Opis\Http\Request::create($path, $method, [], [], [], $server);
        return $this->router->route(new Context($request->path(), $request));
    }

    /**
     * @throws Exception
     */
    public function testBasicRouting()
    {
        $this->route('/', function () {
            return 'OK';
        });

        $response = $this->exec('/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testNotFound1()
    {
        $this->assertEquals(404, $this->exec('/')->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testNotFound2()
    {
        $this->route('/', function () {
            return 'OK';
        });

        $this->assertEquals(404, $this->exec('/foo')->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testNotFound3()
    {
        $this->route('/', function () {
            return 404;
        });

        $this->assertEquals(404, $this->exec('/')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testParam()
    {
        $this->route('/{foo}', function ($foo) {
            return $foo;
        });

        $this->assertEquals('bar', $this->exec('/bar')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testParamConstraintSuccess()
    {
        $this->route('/{foo}', function ($foo) {
            return $foo;
        })
            ->where('foo', '[a-z]+');

        $this->assertEquals('bar', $this->exec('/bar')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testParamConstraintFail()
    {
        $this->route('/{foo}', function ($foo) {
            return $foo;
        })
            ->where('foo', '[a-z]+');

        $this->assertEquals(404, $this->exec('/123')->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testParamOptional1()
    {
        $this->route('/{foo?}', function ($foo) {
            return $foo;
        });

        $this->assertEquals('bar', $this->exec('/bar')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testParamOptional2()
    {
        $this->route('/{foo?}', function ($foo = 'bar') {
            return $foo;
        });

        $this->assertEquals('bar', $this->exec('/')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testParamOptional3()
    {
        $this->route('/{foo?}', function ($foo) {
            return $foo;
        })
            ->implicit('foo', 'bar');

        $this->assertEquals('bar', $this->exec('/')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testMultipleParams()
    {
        $this->route('/{foo}/{bar}', function ($bar, $foo) {
            return $bar . $foo;
        });

        $this->assertEquals('barfoo', $this->exec('/foo/bar')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testLocalBeforeFilterSuccess()
    {
        $this->route('/', function () {
            return 'OK';
        })
            ->callback('foo', function () {
                return true;
            })
            ->filter('foo');

        $this->assertEquals('OK', $this->exec('/')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testLocalBeforeFilterFail()
    {
        $this->route('/', function () {
            return 'OK';
        })
            ->callback('foo', function () {
                return false;
            })
            ->filter('foo');

        $this->assertEquals(404, $this->exec('/')->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testGlobalBeforeFilterSuccess()
    {
        $this->collection->callback('foo', function () {
            return true;
        });

        $this->route('/', function () {
            return 'OK';
        })
            ->filter('foo');

        $this->assertEquals('OK', $this->exec('/')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testGlobalBeforeFilterFail()
    {
        $this->collection->callback('foo', function () {
            return false;
        });

        $this->route('/', function () {
            return 'OK';
        })
            ->filter('foo');

        $this->assertEquals(404, $this->exec('/')->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testLocalFilterGlobalValuesSuccess()
    {
        $this->route('/', function () {
            return 'OK';
        })
            ->callback('foo', function ($x) {
                return $x == 'X';
            })
            ->filter('foo');

        $this->assertEquals('OK', $this->exec('/')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testLocalFilterGlobalValuesFail()
    {
        $this->route('/', function () {
            return 'OK';
        })
            ->callback('foo', function ($x) {
                return $x != 'X';
            })
            ->filter('foo');

        $this->assertEquals(404, $this->exec('/')->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testGlobalFilterGlobalValuesSuccess()
    {
        $this->collection->callback('foo', function ($x) {
            return $x == 'X';
        });

        $this->route('/', function () {
            return 'OK';
        })
            ->filter('foo');

        $this->assertEquals('OK', $this->exec('/')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testGlobalFilterGlobalValuesFail()
    {
        $this->collection->callback('foo', function ($x) {
            return $x != 'X';
        });

        $this->route('/', function () {
            return 'OK';
        })
            ->filter('foo');

        $this->assertEquals(404, $this->exec('/')->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testLocalBinding1()
    {
        $this->route('/{foo}', function ($foo) {
            return $foo;
        })
            ->bind('foo', function ($foo) {
                return strtoupper($foo);
            });

        $this->assertEquals('BAR', $this->exec('/bar')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testLocalBinding2()
    {
        $this->route('/', function ($foo) {
            return $foo;
        })
            ->bind('foo', function () {
                return 'BAR';
            });

        $this->assertEquals('BAR', $this->exec('/')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testGlobalBinding1()
    {
        $this->collection->bind('foo', function ($foo) {
            return strtoupper($foo);
        });

        $this->route('/{foo}', function ($foo) {
            return $foo;
        });

        $this->assertEquals('BAR', $this->exec('/bar')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testGlobalBinding2()
    {
        $this->collection->bind('foo', function () {
            return 'BAR';
        });

        $this->route('/', function ($foo) {
            return $foo;
        });

        $this->assertEquals('BAR', $this->exec('/')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testGlobals1()
    {
        $this->route('/', function ($x) {
            return $x;
        });

        $this->assertEquals('X', $this->exec('/')->getBody());
    }

    /**
     * @throws Exception
     */
    public function testGlobals2()
    {
        $this->route('/', function ($y) {
            return $y;
        })
            ->bind('y', function ($x) {
                return $x;
            });

        $this->assertEquals('X', $this->exec('/')->getBody());
    }
}
