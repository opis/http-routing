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

use Opis\Routing\Dispatcher;
use Opis\Routing\Path as BasePath;
use Opis\Routing\Route as BaseRoute;
use Opis\Routing\Router as BaseRouter;
use Opis\Routing\DispatcherResolver as BaseResolver;

/**
 * Class DispatcherResolver
 * @package Opis\HttpRouting
 */
class DispatcherResolver extends BaseResolver
{
    /** @noinspection PhpDocSignatureInspection */
    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * @param Path $path
     * @param Route $route
     * @param Router $router
     * @return Dispatcher
     */
    public function resolve(BasePath $path, BaseRoute $route, BaseRouter $router): Dispatcher
    {
        $dispatcher = $route->get('dispatcher', 'default');
        $factory = $this->collection->get($dispatcher);
        return $factory();
    }
}
