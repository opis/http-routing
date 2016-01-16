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

use Opis\Routing\Callback;
use Opis\Routing\Path as BasePath;
use Opis\Routing\Route as BaseRoute;
use Opis\Routing\Router as BaseRouter;
use Opis\Routing\Dispatcher as BaseDispatcher;

class Dispatcher extends BaseDispatcher
{

    public function dispatch(BaseRouter $router, BasePath $path, BaseRoute $route)
    {
        $values = array();
        $domain = $route->compileDomain();
        $specials = $router->getSpecialValues();

        if ($domain !== null) {
            $domainPath = $path->domain();
            $bindings = $domain->bind($domainPath, $specials);
            $names = $domain->names($domainPath);
            $values = array_intersect_key($bindings, array_flip($names));
        }

        $values += $route->compile()->bind($path, $specials);

        $callback = new Callback($route->getAction());
        $arguments = $callback->getArguments($values, $specials);

        return $callback->invoke($arguments);
    }
}
