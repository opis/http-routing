<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
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
use Opis\Routing\Collections\FilterCollection;
use Opis\Routing\Contracts\PathInterface;
use Opis\Routing\Contracts\DispatcherResolverInterface;
use Opis\Routing\Router as BaseRouter;

class Router extends BaseRouter
{
    
    protected static $filterCollection;
    
    protected static $dispatcherResolver;
    
    public function __construct(RouteCollection $routes,
                                DispatcherResolverInterface $resolver = null,
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
    
    public function route(PathInterface $path)
    {
        $result = parent::route($path);
        
        if($result === null)
        {
            $callback = $this->routes->getError(404);
            
            if($callback !== null)
            {
                $result = $callback($path);
            }
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
            static::$filterCollection[] = new RequestFilter();
            static::$filterCollection[] = new PathFilter();
            static::$filterCollection[] = new UserFilter();
        }
        
        return static::$filterCollection;
    }
}
