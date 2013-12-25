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
use Opis\Routing\Router as BaseRouter;
use Opis\Routing\DispatcherInterface;
use Opis\Routing\Compiler;
use Opis\Http\ResponseContainerInterface;
use Opis\Http\Error\AccessDenied as AccessDeniedError;

class Dispatcher implements DispatcherInterface
{    
    protected $compiler;
    
    public function __construct()
    {
        $this->compiler = new Compiler();
    }
    
    public function dispatch(BaseRouter $router, BaseRoute $route)
    {        
        $permissions = $router->getRouteCollection()->getPermissions();
        
        $request = $router->getRequest();
        
        foreach($route->get('permissions', array()) as $permission)
        {
            if(isset($permissions[$permission]))
            {
                $result = $permissions[$permission]($request, $route);
                
                if($result instanceof ResponseContainerInterface)
                {
                    return $result;
                }
                elseif($result !== null && $result !== true)
                {
                    return new AccessDeniedError($result);
                }
            }
        }
        
        if($route->get('domain') === null)
        {
            $pattern = $route->getPath();
            $target = $request->path();
            $expr = $route->get('compiled-path');
        }
        else
        {
            $pattern = $route->get('domain', '') . $route->getPath();
            $target = $request->host() . $request->path();
            $expr = $route->get('compiled-domain') . $route->get('compiled-path');
        }
        
        $placeholders = $route->getWildcards();
        $bindings = $route->getBindings();
        
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