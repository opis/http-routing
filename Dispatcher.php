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

use Closure;
use RuntimeException;
use Opis\Routing\Route as BaseRoute;
use Opis\Routing\DispatcherInterface;

class Dispatcher implements DispatcherInterface
{

    protected $request;
    
    protected $compiler;
    
    protected $collection;
    
    public function __construct(Router $router)
    {
        $this->request = $router->getRequest();
        $this->compiler = $router->getCompiler();
        $this->collection = $router->getCollection();
    }
    
    public function dispatch(BaseRoute $route)
    {
        $filters = $this->collection->getFilters();
        
        foreach($route->get('filters', array()) as $filter)
        {
            if(isset($filters[$filter]))
            {
                $result = $filters[$filter]($route, $this->request);
                if($result !== null && $result !== true)
                {
                    return $result;
                }
            }
        }
        
        
        if($route->get('domain') === null)
        {
            $pattern = $route->getPath();
            $target = $this->request->path();
            $expr = $route->get('compiled-path');
        }
        else
        {
            $pattern = $route->get('domain', '') . $route->getPath();
            $target = $this->request->host() . $this->request->path();
            $expr = $route->get('compiled-domain') . $route->get('compiled-path');
        }
        
        $placeholders = $route->getWildcards() + $this->collection->getWildcards();
        $bindings = $route->getBindings() + $this->collection->getBindings();
        
        $expr = $this->compiler->delimit($expr);
        
        $names = $this->compiler->names($pattern);
        
        $values = $this->compiler->values($expr, $target);
        $values = $this->compiler->extract($names, $values, $route->getDefaults());
        
        $arguments = $this->compiler->bind($values, $bindings);
        
        $action = $route->getAction();
        
        if(!is_callable($action))
        {
            throw new RuntimeException('Route action is not callable');
        }
        
        return call_user_func_array($action, $arguments);
    }
}