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

namespace Opis\HttpRouting;

use Opis\Http\Request;
use Opis\Http\Response;
use Opis\Routing\{
    DispatcherTrait, IDispatcher, Router as BaseRouter
};

/**
 * @method Route findRoute(Router $router)
 */
class Dispatcher implements IDispatcher
{
    use DispatcherTrait;

    /**
     * @param BaseRouter|Router $router
     * @return mixed
     */
    public function dispatch(BaseRouter $router)
    {
        $route = $this->findRoute($router);

        if ($route === null) {
            return (new Response("Not Found"))->setStatusCode(404);
        }

        /** @var Request $request */
        $request = $router->getContext()->data();

        if (!in_array($request->method(), $route->get('method', ['GET']))) {
            return (new Response("Method Not Allowed"))->setStatusCode(405);
        }

        $callbacks = $route->getCallbacks();
        $invoker = $router->resolveInvoker($route);

        foreach ($route->get('guard', []) as $name) {
            if (isset($callbacks[$name])) {
                $callback = $callbacks[$name];
                $arguments = $invoker->getArgumentResolver()->resolve($callback);
                if (false === $callback(...$arguments)) {
                    return (new Response("Not Found"))->setStatusCode(404);
                }
            }
        }

        $result = $invoker->invokeAction();

        if (!$result instanceof Response) {
            $result = new Response($result);
        }

        return $result;
    }
}