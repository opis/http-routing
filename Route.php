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

use Opis\Routing\Route as BaseRoute;

class Route extends BaseRoute
{
    
    protected $cache = array();
    
    public static function create($path, $action, $method = 'GET')
    {
        $route = new static($path,$action);
        return $route->method($method);
    }
    
    public function where($name, $value)
    {
        return $this->wildcard($name, $value);
    }
    
    public function domain($value)
    {
        return $this->set('domain', $value);
    }
    
    public function method($method)
    {
        if(!is_array($method))
        {
            $method = array($method);
        }
        
        $method = array_map('strtoupper', $method);
        
        return $this->set('method', $method);
    }
    
    public function secure($value = true)
    {
        return $this->set('secure', $value);
    }
    
    public function filters(array $filters)
    {
        return $this->set('filters', $filters);
    }
    
    public function permissions(array $permissions)
    {
        return $this->set('permissions', $permissions);
    }
    
    public function namedAs($value)
    {
        return $this->set('alias', $value);
    }
    
    public function getWildcards()
    {
        if(!isset($this->cache['wildcards']))
        {
            $this->cache['wildcards'] = $this->wildcards + $this->get('collection')->getWildcards();
        }
        return $this->cache['wildcards'];
    }
    
    public function getBindings()
    {
        if(!isset($this->cache['bindings']))
        {
            $this->cache['bindings'] = $this->bindings + $this->get('collection')->getBindings();
        }
        return $this->cache['bindings'];
    }
    
}
