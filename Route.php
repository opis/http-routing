<?php

namespace Opis\HttpRouting;

use Opis\Routing\Route as BaseRoute;

class Route extends BaseRoute
{
    
    public static function create($path, $action, $method = 'GET')
    {
        $route = new static($path,$action);
        return $route->method($method);
    }
    
    public function where($name, $value)
    {
        return $this->match($name, $value);
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
    
    public function namedAs($value)
    {
        return $this->set('alias', $value);
    }
    
}
