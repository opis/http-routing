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

namespace Opis\HttpRouting\Filters;

use Opis\Routing\FilterInterface;
use Opis\Routing\Route;
use Opis\Routing\Router;
use Opis\Routing\Compiler;

class PathFilter implements FilterInterface
{
    
    protected $compiler;
    
    public function __construct()
    {
        $this->compiler = new Compiler();
    }
    
    public function match(Router $router, Route $route)
    {
        $collection = $router->getRouteCollection();
        $request = $router->getRequest();
        $pattern = $this->compiler->compile($route->getPath(), $route->getWildcards() + $collection->getWildcards());
        
        if(preg_match($this->compiler->delimit($pattern), $request->path()))
        {
            $route->set('compiled-path', $pattern);
            return true;
        }
        
        return false;
    }
    
}