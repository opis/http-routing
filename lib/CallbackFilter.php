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
use Opis\Routing\Context as BaseContext;
use Opis\Routing\FilterInterface;
use Opis\Routing\Route as BaseRoute;
use Opis\Routing\Router as BaseRouter;
use Serializable;
use Opis\Closure\SerializableClosure;

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

    /**
     * @param BaseRouter|Router $router
     * @param BaseContext|Request $context
     * @param BaseRoute|Route $route
     * @return bool
     */
    public function pass(BaseRouter $router, BaseContext $context, BaseRoute $route): bool
    {
        $values = $router->extract($context, $route);
        if($this->doBind){
            $values = $router->bind($values, $route->getBindings());
        }
        $arguments = $router->buildArguments($this->callback, $values, $this->doBind);
        $callback = $this->callback;
        return $callback(...$arguments);
    }

    /**
     * @return string
     */
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

    /**
     * @param string $data
     */
    public function unserialize($data)
    {
        $object = unserialize($data);

        if ($object instanceof SerializableClosure) {
            $object = $object->getClosure();
        }
        
        $this->callback = $object;
    }
}
