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

namespace Opis\HttpRouting;

use Closure;
use Serializable;
use Opis\Routing\Callback;
use Opis\Routing\Path as BasePath;
use Opis\Routing\Route as BaseRoute;
use Opis\Closure\SerializableClosure;
use Opis\Routing\Router as BaseRouter;
use Opis\Routing\CallableExpectedException;
use Opis\Routing\Collections\DispatcherCollection;
use Opis\Routing\DispatcherResolver as BaseResolver;

class DispatcherResolver extends BaseResolver implements Serializable
{
    protected $collection = array();
    protected $constructors = array();

    public function __construct()
    {
        $this->collection = new DispatcherCollection();

        $this->register('default', function() {
            return new \Opis\HttpRouting\Dispatcher();
        });
    }

    public function register($name, $callback)
    {
        if (!is_callable($callback)) {
            throw new CallableExpectedException();
        }

        $this->constructors[$name] = $callback;
        unset($this->collection[$name]);
        return $this;
    }

    public function resolve(BaseRouter $router, BasePath $path, BaseRoute $route)
    {
        $dispatcher = $route->get('dispatcher', 'default');

        if (!isset($this->constructors[$dispatcher])) {
            $dispatcher = 'default';
        }

        if (!isset($this->collection[$dispatcher])) {
            $constructor = new Callback($this->constructors[$dispatcher]);
            $this->collection[$dispatcher] = $constructor->invoke();
        }

        return $this->collection[$dispatcher];
    }

    public function serialize()
    {

        SerializableClosure::enterContext();

        $object = serialize(array_map(function($value) {

                if ($value instanceof Closure) {
                    return SerializableClosure::from($value);
                }

                return $value;
            }, $this->constructors));

        SerializableClosure::exitContext();

        return $object;
    }

    public function unserialize($data)
    {
        $object = unserialize($data);
        $this->collection = new DispatcherCollection();
        $this->constructors = array_map(function($value) {
            if ($value instanceof SerializableClosure) {
                return $value->getClosure();
            }
            return $value;
        }, $object);
    }
}
