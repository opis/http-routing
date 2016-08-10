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

class UserFilter extends AbstractFilter
{
    /**
     * @param Request $request
     * @param Route $route
     * @param Router $router
     * @return bool
     */
    protected function passFilter(Request $request, Route $route, Router $router): bool
    {
        /** @var CallbackFilter[] $filters */
        $filters = $route->get('filters', []) + $router->getRouteCollection()->getFilters();

        foreach ($route->get('before', []) as $name) {
            if (isset($filters[$name])) {
                if ($filters[$name]->setBindMode(false)->pass($request, $route, $router) === false) {
                    return false;
                }
            }
        }

        return true;
    }


}
