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
use Opis\Routing\FilterInterface;
use Opis\Routing\Path as BasePath;
use Opis\Routing\Route as BaseRoute;
use Opis\Closure\SerializableClosure;
use Opis\Routing\Router as BaseRouter;

class CallbackFilter implements FilterInterface, Serializable
{
    protected $callback;
    protected $doBind;

    /**
     * CallbackFilter constructor.
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }


    /**
     * @param bool $value
     * @return CallbackFilter
     */
    public function setBindMode(bool $value): self
    {
        $this->doBind = (bool) $value;
        return $this;
    }

    /** @noinspection PhpDocSignatureInspection */

    /**
     * @param Path $path
     * @param Route $route
     * @param Router $router
     * @return mixed
     */
    public function pass(BasePath $path, BaseRoute $route, BaseRouter $router)
    {
        $wrapper = $router->wrapRoute($route);
        $values = $this->doBind ? $wrapper->extract() : $wrapper->bind();
        $callback = $this->callback;
        $arguments = $router->buildArguments($callback, $values, $this->doBind);
        return $callback(...$arguments);
    }

    public function serialize()
    {
        SerializableClosure::enterContext();

        $callable = $this->callback;

        if ($callable instanceof Closure) {
            $callable = SerializableClosure::from($callable);
        }

        $object = serialize($callable);

        SerializableClosure::exitContext();

        return $object;
    }

    public function unserialize($data)
    {
        $object = unserialize($data);

        if ($object instanceof SerializableClosure) {
            $object = $object->getClosure();
        }
        
        $this->callback = $object;
    }
}
