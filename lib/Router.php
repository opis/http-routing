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

use Opis\Routing\PathFilter;
use Opis\Routing\Path as BasePath;
use Opis\Routing\Router as BaseRouter;
use Opis\Routing\Collections\FilterCollection;

class Router extends BaseRouter
{
    public function __construct(
        RouteCollection $routes,
        DispatcherResolver $resolver = null, 
        FilterCollection $filters = null,
        array $specials = array()
    ) {
        if ($resolver === null) {
            $resolver = new DispatcherResolver();
        }
        
        if ($filters === null) {
            $filters = new FilterCollection();
            $filters[] = new PathFilter();
            $filters[] = new RequestFilter();
            $filters[] = new UserFilter();
        }
        
        parent::__construct($routes, $resolver, $filters, $specials);
    }


    protected function findRoute(Path $path)
    {
        foreach ($this->routes as $route) {
            $this->specials['self'] = $route;

            if ($this->pass($path, $route)) {
                return $route;
            }
        }
        
        return null;
    }

    protected function passFilter($filter, Path $path, Route $route)
    {
        $filters = $route->getFilters();

        foreach ($route->get($filter, array()) as $name) {
            if (isset($filters[$name])) {
                if ($filters[$name]->setBindMode(true)->pass($this, $path, $route) === false) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function raiseError($error, Path $path)
    {
        $callback = $this->routes->getError($error);

        if ($callback !== null) {
            return $callback($path);
        }
        
        return null;
    }

    public function route(BasePath $path)
    {
        $this->specials += array(
            'path' => $path,
            'self' => null,
        );
        
        $route = $this->findRoute($path);

        if ($route === null) {
            return $this->raiseError(404, $path);
        }

        if (!$this->passFilter('after', $path, $route)) {
            return $this->raiseError(404, $path);
        }

        if (!$this->passFilter('access', $path, $route)) {
            return $this->raiseError(403, $path);
        }


        $dispatcher = $this->resolver->resolve($this, $path, $route);
        $result = $dispatcher->dispatch($this, $path, $route);

        if ($result instanceof HttpError) {
            return $this->raiseError($result->errorCode(), $path);
        }

        return $result;
    }
}
