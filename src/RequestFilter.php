<?php
/* ===========================================================================
 * Copyright 2013-2016 The Opis Project
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

use Opis\Routing\Context as BaseContext;
use Opis\Routing\IFilter;
use Opis\Routing\Route as BaseRoute;
use Opis\Routing\Router as BaseRouter;

class RequestFilter implements IFilter
{
    /**
     * @param BaseRouter|BaseRouter $router
     * @param BaseContext|Context $context
     * @param BaseRoute|Route $route
     * @return bool
     */
    public function pass(BaseRouter $router, BaseContext $context, BaseRoute $route): bool
    {
        //match secure
        if (null !== $secure = $route->get('secure')) {
            if ($secure !== $context->isSecure()) {
                return false;
            }
        }

        //match method
        if (!in_array($context->method(), $route->get('method', ['GET']))) {
            return false;
        }

        // match domain
        if(null !== $domain = $route->get('domain')){
            $regex = $router->getRouteCollection()->getDomainCompiler()->getRegex($domain, $route->getWildcards());
            return preg_match($regex, $context->domain());
        }

        return true;
    }
}
