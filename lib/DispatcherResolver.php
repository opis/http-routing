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


use Opis\Routing\Collections\DispatcherCollection;
use Opis\Routing\Contracts\DispatcherInterface;
use Opis\Routing\Contracts\PathInterface;
use Opis\Routing\Contracts\RouteInterface;
use Opis\Routing\DispatcherResolver as BaseResolver;

class DispatcherResolver extends BaseResolver
{
    
    protected $collection;
    
    public function __construct()
    {
        $this->collection = new DispatcherCollection();
        $this->collection['default'] = new Dispatcher();
    }
    
    public function register($name, DispatcherInterface $dispatcher)
    {
        $this->collection[$name] = $dispatcher;
        return $this;
    }
    
    public function resolve(PathInterface $router, RouteInterface $route)
    {
        $dispatcher = $route->get('dispatcher', 'default');
        
        if(isset($this->collection[$dispatcher]))
        {
            return $this->collection[$dispatcher];
        }
        
        return $this->collection['default'];
    }
}