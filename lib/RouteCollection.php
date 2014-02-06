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
    
    public function bind($name, callable $callback)
    {
        $this->bindings[$name] = $callback;
        return $this;
    }
    
    public function implicit($name, $value)
    {
        $this->defaults[$name] = $value;
        return $this;
    }
    
    public function notFound(callable $callback)
    {
        $this->errors[404] = $callback;
        return $this;
    }
    
    public function filter($name, callable $filter)
    {
        $this->filters[$name] = $filter;
        return $this;
    }
    
    public function offsetSet($offset, $value)
    {
        $value->set('collection', $this);
        parent::offsetSet($offset, $value);
    }
    
    public function serialize()
    {
        return serialize(array(
            'collection' => $this->collection,
            'bindings' => $this->bindings,
            'wildcards' => $this->wildcards,
            'filters' => $this->filters,
            'defaults' => $this->defaults,
            'errors' => $this->errors,
        ));
    }
    
    public function unserialize($data)
    {
        $object = unserialize($data);
        
        foreach($object as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}