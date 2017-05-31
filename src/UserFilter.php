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
    IFilter,
    Context as BaseContext,
    Route as BaseRoute,
    Router as BaseRouter
};

class UserFilter implements IFilter
{
    /**
     * @param BaseRouter|Router $router
     * @param BaseContext|Context $context
     * @param BaseRoute|Route $route
     * @return bool
     */
    public function pass(BaseRouter $router, BaseContext $context, BaseRoute $route): bool
    {
        /** @var callable[] $filters */
        $filters = $route->getFilters();
        $compiled = $router->getDispatcher()->compile($context, $route);

        foreach ($route->get('before', []) as $name) {
            if (isset($filters[$name])) {
                $callback = $filters[$name];
                $arguments = $compiled->getArguments($callback, false);
                if(false === $callback(...$arguments)){
                    return false;
                }
            }
        }

        return true;
    }
}