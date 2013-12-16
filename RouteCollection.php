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
use Opis\Routing\RouteCollection as BaseCollection;

class RouteCollection extends BaseCollection
{
    
    protected $wildcards = array();
    
    protected $bindings = array();
    
    protected $filters = array();
    
    protected $permissions = array();
    
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
    
    public function getPermissions()
    {
        return $this->permissions;
    }
    
    public function wildcard($name, $value)
    {
        $this->wildcard[$name] = $value;
        return $this;
    }
    
    public function bind($name, Closure $value)
    {
        $this->bindings[$name] = $value;
        return $this;
    }
    
    public function filter($name, Closure $filter)
    {
        $this->filters[$name] = $filter;
        return $this;
    }
    
    public function permission($name, Closure $permission)
    {
        $this->permissions[$name] = $permission;
        return $this;
    }
    
    public function offsetSet($offset, $value)
    {
        if(!($value instanceof Route))
        {
            throw new InvalidArgumentException('Expected \Opis\HttpRouting\Route');
        }
        $offset = $value->get('alias', $offset);
        $value->set('collection', $this);
        parent::offsetSet($offset, $value);
    }
}