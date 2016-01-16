<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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
use Opis\Routing\Router;
use Opis\Routing\Binding;
use Opis\Routing\Callback;
use Opis\Routing\FilterInterface;
use Opis\Routing\Path as BasePath;
use Opis\Routing\Route as BaseRoute;
use Opis\Closure\SerializableClosure;

class CallbackFilter implements FilterInterface, Serializable
{
    protected $values;
    protected $callback;
    protected $callable;
    protected $doBind;

    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new CallableExpectedException();
        }

        $this->callable = $callback;
    }

    protected function getCallback()
    {
        if ($this->callback === null) {
            $this->callback = new Callback($this->callable);
        }

        return $this->callback;
    }

    public function setBindMode($value)
    {
        $this->doBind = (bool) $value;
        return $this;
    }

    public function pass(Router $router, BasePath $path, BaseRoute $route)
    {
        if ($this->doBind) {
            $values = $route->compile()->bind($path);
        } else {
            $values = $route->compile()->extract($path);
        }
        
        $callback = $this->getCallback();
        $specials = $router->getSpecialValues();
        $arguments = $callback->getArguments($values, $specials, $this->doBind);

        return $callback->invoke($arguments);
    }

    public function serialize()
    {
        SerializableClosure::enterContext();

        $callable = $this->callable;

        if ($callable instanceof Closure) {
            $callable = SerializableClosure::from($callable);
        }

        $object = serialize(array(
            'params' => $this->params,
            'callable' => $callable,
        ));

        SerializableClosure::exitContext();

        return $object;
    }

    public function unserialize($data)
    {
        $object = SerializableClosure::unserializeData($data);

        if ($object['callable'] instanceof SerializableClosure) {
            $object['callable'] = $object['callable']->getClosure();
        }

        $this->params = $object['params'];
        $this->callable = $object['callable'];
    }
}
