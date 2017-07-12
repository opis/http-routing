<?php
/* ===========================================================================
 * Copyright 2013-2017 The Opis Project
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

use Opis\Routing\{
    Context as BaseContext, DispatcherTrait, IDispatcher, Router as BaseRouter
};

/**
 * Class Dispatcher
 * @package Opis\HttpRouting
 *
 * @property Route $route
 * @property Context $context
 * @property Router $router
 * @method Route findRoute()
 */
class Dispatcher implements IDispatcher
{
    use DispatcherTrait;

    /** @var array */
    protected $compiled = [];

    /**
     * @param BaseRouter|Router $router
     * @param BaseContext|Context $context
     * @return mixed
     */
    public function dispatch(BaseRouter $router, BaseContext $context)
    {
        $this->router = $router;
        $this->context = $context;

        $route = $this->findRoute();

        if($route === null){
            return $this->raiseError(404, $context);
        }

        $collection = $route->getRouteCollection();
        $callbacks = $route->getCallbacks();
        $compiled = $this->compile($context, $route);

        if(!$this->passUserFilter('validate', $callbacks, $compiled, true)){
            return $this->raiseError(404, $context);
        }

        if(!$this->passUserFilter('access', $callbacks, $compiled, true)){
            return $this->raiseError(403, $context);
        }

        $content = $compiled->invokeAction();

        if($content instanceof HttpError){
            return $this->raiseError($content->errorCode(), $context);
        }

        if(null !== $middleware = $route->get('middleware')){
            /** @var callable $callback */
            $callback = $collection->getMiddleware()[$middleware] ?? null;
            if($callback !== null){
                $content = $callback($content, $route, $context, $router);
                if($content instanceof HttpError){
                    return $this->raiseError($content->errorCode(), $context);
                }
            }
        }

        return $content;
    }

    public function compile(Context $context, Route $route): CompiledRoute
    {
        $cid = spl_object_hash($context);
        $rid = spl_object_hash($route);

        if(!isset($this->compiled[$cid][$rid])){
            return $this->compiled[$cid][$rid] = new CompiledRoute($context, $route, $this->getSpecialValues());
        }

        return $this->compiled[$cid][$rid];
    }

    /**
     * @param int $code
     * @param Context $context
     * @return null
     */
    protected function raiseError(int $code, Context $context)
    {
        $callback = $this->router->getRouteCollection()->getError($code);

        if($callback !== null){
            return $callback($context);
        }

        return null;
    }

    /**
     * @param string $filter
     * @param callable[] $callbacks
     * @param CompiledRoute $compiled
     * @param bool $bind
     * @return bool
     */
    protected function passUserFilter(string $filter, array $callbacks, CompiledRoute $compiled, bool $bind): bool
    {
        foreach ($compiled->getRoute()->get($filter, []) as $name){
            if(isset($callbacks[$name])){
                $arguments = $compiled->getArguments($callbacks[$name], $bind);
                if(false === $callbacks[$name](...$arguments)){
                    return false;
                }
            }
        }

        return true;
    }

}