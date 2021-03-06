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

use ArrayAccess;
use Opis\Routing\{
    Context, IDispatcher, RouteInvoker as BaseRouteInvoker, Router as BaseRouter, FilterCollection, Route as BaseRoute
};

/**
 * @method RouteCollection getRouteCollection()
 * @method Dispatcher getDispatcher()
 */
class Router extends BaseRouter
{
    /**
     * Router constructor.
     * @param RouteCollection $routes
     * @param IDispatcher $dispatcher
     * @param FilterCollection|null $filters
     * @param ArrayAccess $global
     */
    public function __construct(
        RouteCollection $routes,
        IDispatcher $dispatcher = null,
        FilterCollection $filters = null,
        ArrayAccess $global = null
    ) {

        if ($dispatcher === null) {
            $dispatcher = new Dispatcher();
        }

        if ($filters === null) {
            $filters = new FilterCollection();
            $filters->addFilter(new RequestFilter())
                ->addFilter(new UserFilter());
        }

        parent::__construct($routes, $dispatcher, $filters, $global);
    }

    /**
     * @inheritDoc
     */
    protected function createInvoker(BaseRoute $route, Context $context): BaseRouteInvoker
    {
        return new RouteInvoker($route, $context, $this->getGlobalValues());
    }
}
