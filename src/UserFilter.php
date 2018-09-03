<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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
    IFilter,
    Route as BaseRoute,
    Router as BaseRouter
};

class UserFilter implements IFilter
{

    /**
     * @param BaseRouter|Router $router
     * @param BaseRoute|Route $route
     * @return bool
     */
    public function filter(BaseRouter $router, BaseRoute $route): bool
    {
        /** @var callable[] $callbacks */
        $callbacks = $route->getCallbacks();
        $invoker = $router->resolveInvoker($route);

        foreach ($route->get('filter', []) as $name) {
            if (isset($callbacks[$name])) {
                $callback = $callbacks[$name];
                $arguments = $invoker->getArgumentResolver()->resolve($callback, false);
                if (false === $callback(...$arguments)) {
                    return false;
                }
            }
        }

        return true;
    }
}
