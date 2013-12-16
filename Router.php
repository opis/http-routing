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

use Opis\Routing\Router as BaseRouter;
use Opis\Routing\Compiler;
use Opis\Routing\FilterCollection;
use Opis\Http\Request;
use Opis\Http\Error\NotFound as NotFoundError;
use Opis\HttpRouting\Filters\PathFilter;
use Opis\HttpRouting\Filters\FiltersFilter;
use Opis\HttpRouting\Filters\RequestFilter;

class Router extends BaseRouter
{
    
    protected $request;
    
    protected $compiler;
    
    protected static $filterCollection;
    
    protected static $dispatcherResolver;
    
    public function __construct(Request $request, RouteCollection $routes)
    {
        $this->request = $request;
        parent::__construct(static::dispatcherResolver(), static::filterCollection(), $routes);
    }
    
    public static function dispatcherResolver()
    {
        if(static::$dispatcherResolver === null)
        {
            static::$dispatcherResolver = new DispatcherResolver();
        }
        
        return static::$dispatcherResolver;
    }
    
    public static function filterCollection()
    {
        if(static::$filterCollection === null)
        {
            static::$filterCollection = new FilterCollection();
            static::$filterCollection[] = new RequestFilter();
            static::$filterCollection[] = new PathFilter();
            static::$filterCollection[] = new FiltersFilter();
        }
        
        return static::$filterCollection;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function route()
    {
        $result = parent::route();
        
        $response = $this->request->response();
        
        if($result === null)
        {
            $result = new NotFoundError('<h2>Page not found</h2>');
        }
        
        $response->body($result);
        $response->send();
    }
}