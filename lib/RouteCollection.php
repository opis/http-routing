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
use Opis\Closure\SerializableClosure;
use Opis\Routing\Collections\RouteCollection as BaseCollection;

class RouteCollection extends BaseCollection
{
    
    protected $wildcards = array();
    
    protected $bindings = array();
    
    protected $filters = array();
    
    protected $permissions = array();
    
    protected $defaults = array();
    
    protected $errors = array();
    
    protected function checkType($value)
    {
        if(!($value instanceof Route))
        {
            throw new InvalidArgumentException('Expected \Opis\HttpRouting\Route');
        }
    }
    
    public function getWildcards()
    {
        return $this->wildcards;
    }
    
    public function getBindings()
    {
        return $this->bindings;
    }
    
    public function getFilters()
    {
        return $this->filters;
    }
    
    public function getDefaults()
    {
        return $this->defaults;
    }
    
    public function getError($code)
    {
        if(isset($this->errors[$code]))
        {
            return $this->errors[$code];
        }
        return null;
    }
    
    public function wildcard($name, $value)
    {
        $this->wildcard[$name] = $value;
        return $this;
    }
    
    public function bind($name, Closure $callback)
    {
        $this->bindings[$name] = $callback;
        return $this;
    }
    
    public function implicit($name, $value)
    {
        $this->defaults[$name] = $value;
        return $this;
    }
    
    public function notFound(Closure $callback)
    {
        $this->errors[404] = $callback;
        return $this;
    }
    
    public function accessDenied(Closure $callback)
    {
        $this->errors[403] = $callback;
        return $this;
    }
    
    public function filter($name, Closure $filter)
    {
        $this->filters[$name] = new ClosureFilter($filter);
        return $this;
    }
    
    public function offsetSet($offset, $value)
    {
        parent::offsetSet($offset, $value);
        $value->set('collection', $this);
    }
    
    public function serialize()
    {
        
        $map = function(&$value) use(&$map){
            if($value instanceof Closure)
            {
                return SerializableClosure::from($value);
            }
            elseif(is_array($value))
            {
                return array_map($map, $value);
            }
            elseif($value instanceof \stdClass)
            {
                $object = (array) $object;
                $object = array_map($map, $object);
                return (object) $object;
            }
            return $value;
        };
        
        SerializableClosure::enterContext();
        
        $object = serialize(array(
            'collection' => $this->collection,
            'wildcards' => $this->wildcards,
            'filters' => $this->filters,
            'bindings' => array_map($map, $this->bindings),
            'defaults' => array_map($map, $this->defaults),
            'errors' => array_map($map, $this->errors),
        ));
        
        SerializableClosure::exitContext();
        
        return $object;
    }
    
    public function unserialize($data)
    {
        $object = SerializableClosure::unserializeData($data);
        
        $map = function(&$value) use(&$map){
            if($value instanceof SerializableClosure)
            {
                return $value->getClosure();
            }
            elseif(is_array($value))
            {
                return array_map($map, $value);
            }
            elseif($value instanceof \stdClass)
            {
                $object = (array) $object;
                $object = array_map($map, $object);
                return (object) $object;
            }
            return $value;
        };
        
        $this->collection = $object['collection'];
        $this->wildcards = $object['wildcards'];
        $this->filters = $object['filters'];
        $this->bindings = array_map($map, $object['bindings']);
        $this->defaults = array_map($map, $object['defaults']);
        $this->errors = array_map($map, $object['errors']);
        
    }
}
