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

use Opis\Http\Request;
use Opis\Routing\PathFilter;
use Opis\Routing\Collections\FilterCollection;
use Opis\Routing\Contracts\PathInterface;
use Opis\Routing\Router as BaseRouter;
use Opis\Http\Error\NotFound as NotFoundError;

class Router extends BaseRouter
{
    
    protected static $filterCollection;
    
    protected static $dispatcherResolver;
    
    public function __construct(RouteCollection $routes)
    {
        parent::__construct($routes, static::dispatcherResolver(), static::filterCollection());
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
    
    public function route(PathInterface $path)
    {
        $result = parent::route($path);
        
        $response = $path->request()->response();
        
        if($result === null)
        {
            $result = new NotFoundError('<h2>Page not found</h2>');
        }
        
        $response->body($result);
        $response->send();
    }
}