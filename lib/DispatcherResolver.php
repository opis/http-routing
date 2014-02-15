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
use Serializable;
use Opis\Closure\SerializableClosure;
use Opis\Routing\Collections\DispatcherCollection;
use Opis\Routing\Contracts\DispatcherInterface;
use Opis\Routing\Contracts\PathInterface;
use Opis\Routing\Contracts\RouteInterface;
use Opis\Routing\DispatcherResolver as BaseResolver;

class DispatcherResolver extends BaseResolver implements Serializable
{
    
    protected $collection = array();
    
    protected $constructors = array();
    
    public function __construct()
    {
        $this->collection = new DispatcherCollection();
        $this->register('default', function(){
           return new Dispatcher();
        });
    }
    
    public function register($name, Closure $callback)
    {
        $this->constructors[$name] = $callback;
        unset($this->collection[$name]);
        return $this;
    }
    
    public function resolve(PathInterface $router, RouteInterface $route)
    {
        $dispatcher = $route->get('dispatcher', 'default');
        
        if(!isset($this->collection[$dispatcher]))
        {
            $constructor = $this->constructors[$dispatcher];
            $this->collection[$dispatcher] = $constructor();
        }
        
        return $this->collection[$dispatcher];
    }
    
    public function serialize()
    {
        
        SerializableClosure::enterContext();
        
        $object = serialize(array_map(function($value){
            return SerializableClosure::from($value);
        }, $this->constructors));
        
        SerializableClosure::exitContext();
        
        return $object;
    }
    
    public function unserialize($data)
    {
        $object = unserialize($data);
        $this->collection = new DispatcherCollection();
        $this->constructors = array_map(function($value){ return $value->getClosure(); }, $object);
    }
}