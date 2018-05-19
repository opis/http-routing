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

use Opis\Routing\{
    IFilter,
    Route as BaseRoute,
    Router as BaseRouter
};
use Psr\Http\Message\RequestInterface;

class RequestFilter implements IFilter
{
    /**
     * @param BaseRouter|BaseRouter $router
     * @param BaseRoute|Route $route
     * @return bool
     */
    public function filter(BaseRouter $router, BaseRoute $route): bool
    {
        /** @var RequestInterface $request */
        $request = $router->getContext()->data();

        if (!in_array($request->getMethod(), $route->get('method', ['GET']))) {
            return false;
        }

        if (null !== $secure = $route->get('secure')) {
            if ($secure && $request->getUri()->getScheme() !== 'https') {
                return false;
            }
        }

        if (null !== $domain = $route->get('domain')) {
            $regex = $route->getRouteCollection()->getDomainBuilder()->getRegex($domain, $route->getPlaceholders());
            if(!preg_match($regex, $request->getUri()->getHost())) {
                return false;
            }
        }

        return true;
    }
}
