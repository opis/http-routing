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
abstract class Dispatcher implements IDispatcher
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
            return $this->getErrorResponse($context, new HttpError(404));
        }

        if (!in_array($context->method(), $route->get('method', ['GET']))) {
            return $this->getErrorResponse($context, new HttpError(405));
        }

        $error = null;
        $callbacks = $route->getCallbacks();
        $compiled = $this->compile($context, $route);

        $filter = function (string $filter, bool $bind = true) use($callbacks, $compiled, &$error){
            foreach ($compiled->getRoute()->get($filter, []) as $name){
                if(isset($callbacks[$name])){
                    $arguments = $compiled->getArguments($callbacks[$name], $bind);
                    $result = $callbacks[$name](...$arguments);
                    if(false === $result){
                        return false;
                    } elseif ($result instanceof HttpError){
                        $error = $result;
                        return false;
                    }
                }
            }
            return true;
        };

        if(!$filter('validate')){
            if($error === null){
                $error = new HttpError(404);
            }
            return $this->getErrorResponse($context, $error);
        }

        if(!$filter('access')){
            if($error === null){
                $error = new HttpError(403);
            }
            return $this->getErrorResponse($context, $error);
        }

        return $compiled->invokeAction();
    }

    public function compile(Context $context, Route $route): CompiledRoute
    {
        $cid = spl_object_hash($context);
        $rid = spl_object_hash($route);

        if(!isset($this->compiled[$cid][$rid])){
            return $this->compiled[$cid][$rid] = new CompiledRoute($context, $route, $this->getExtraVariables());
        }

        return $this->compiled[$cid][$rid];
    }

    /**
     * @param Context $context
     * @param HttpError $error
     * @return mixed
     */
    protected abstract function getErrorResponse(Context $context, HttpError $error);
}