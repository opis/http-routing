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

use Opis\Routing\IDispatcher;
use Opis\Routing\Router as BaseRouter;
use Opis\Routing\FilterCollection;

/**
 * Class Router
 * @package Opis\HttpRouting
 *
 * @method RouteCollection getRouteCollection()
 * @method Route findRoute(Context $context)
 * @method Dispatcher getDispatcher()
 * @property Context $currentPath
 */
class Router extends BaseRouter
{
    /**
     * Router constructor.
     * @param IDispatcher $dispatcher
     * @param RouteCollection $routes
     * @param FilterCollection|null $filters
     * @param array $specials
     */
    public function __construct(
        RouteCollection $routes,
        IDispatcher $dispatcher = null,
        FilterCollection $filters = null,
        array $specials = []
    ){
        if($dispatcher === null){
            $dispatcher = new Dispatcher();
        }
        if($filters === null){
            $filters = new FilterCollection();
            $filters->addFilter(new RequestFilter())
                ->addFilter(new UserFilter());
        }
        parent::__construct($routes, $dispatcher, $filters, $specials);
    }
}
