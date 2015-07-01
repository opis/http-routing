<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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

use Opis\Routing\Path;
use Opis\Routing\PathFilter;
use Opis\Routing\DispatcherResolver;
use Opis\Routing\Router as BaseRouter;
use Opis\Routing\Collections\FilterCollection;

class Router extends BaseRouter
{
    
    protected static $filterCollection;
    
    protected static $dispatcherResolver;
    
    public function __construct(RouteCollection $routes,
                                DispatcherResolver $resolver = null,
                                FilterCollection $filters = null)
    {
        if($resolver === null)
        {
            $resolver = static::dispatcherResolver();
        }
        
        if($filters === null)
        {
            $filters = static::filterCollection();
        }
        
        parent::__construct($routes, $resolver, $filters);
    }
    
    protected function findRoute(Path $path)
    {
        foreach($this->routes as $route)
        {
            $route->implicit('path', $path);
            
            if($this->pass($path, $route))
            {
                return $route;
            }
        }
        
        return null;
    }
    
    protected function passFilter($filter, Path $path, Route $route)
    {
        $filters = $route->getFilters();
        
        foreach($route->get($filter, array()) as $name)
        {
            if(isset($filters[$name]))
            {
                if($filters[$name]->setBindMode(true)->pass($path, $route) === false)
                {
                    return false;
                }
            }
        }
        
        return true;   
    }
    
    protected function raiseError($error, Path $path)
    {
        $callback = $this->routes->getError($error);
        
        if($callback !== null)
        {
            return $callback($path);
        }
        
        return null;
    }
    
    public function route(Path $path)
    {
        $route = $this->findRoute($path);
        
        if($route === null)
        {
            return $this->raiseError(404, $path);
        }
        
        if(!$this->passFilter('afterfilter', $path, $route))
        {
            return $this->raiseError(404, $path);
        }
        
        if(!$this->passFilter('accessfilter', $path, $route))
        {
            return $this->raiseError(403, $path);
        }
        
        
        $dispatcher = $this->resolver->resolve($path, $route);
        $result = $dispatcher->dispatch($path, $route);
        
        if($result instanceof HttpError)
        {
            return $this->raiseError($result->errorCode(), $path);
        }
        
        return $result;
    }
    
    protected static function dispatcherResolver()
    {
        if(static::$dispatcherResolver === null)
        {
            static::$dispatcherResolver = new DispatcherResolver();
        }
        
        return static::$dispatcherResolver;
    }
    
    protected static function filterCollection()
    {
        if(static::$filterCollection === null)
        {
            static::$filterCollection = new FilterCollection();
            static::$filterCollection[] = new PathFilter();
            static::$filterCollection[] = new RequestFilter();
            static::$filterCollection[] = new UserFilter();
        }
        
        return static::$filterCollection;
    }
}
