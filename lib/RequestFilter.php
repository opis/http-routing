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

use Opis\Routing\FilterInterface;
use Opis\Routing\Path as BasePath;
use Opis\Routing\Route as BaseRoute;
use Opis\Routing\Router as BaseRouter;

class RequestFilter implements FilterInterface
{

    public function pass(BaseRouter $router, BasePath $path, BaseRoute $route)
    {
        //match secure
        if (null !== $secure = $route->get('secure')) {
            if ($secure !== $path->isSecure()) {
                return false;
            }
        }
        
        //match method
        if (!in_array($path->method(), $route->get('method', array('GET')))) {
            return false;
        }

        //match domain
        if (null !== $domain = $route->compileDomain()) {
            return $domain->match($path->domain());
        }
        
        return true;
    }
}
