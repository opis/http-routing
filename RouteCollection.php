<?php

namespace Opis\HttpRouting;

use Closure;
use Opis\Routing\RouteCollection as BaseCollection;

class RouteCollection extends BaseCollection
{
    
    protected $patterns = array();
    
    protected $bindings = array();
    
    public function pattern($name, $pattern)
    {
        $this->patterns[$name] = $pattern;
        return $this;
    }
    
    public function bind($name, Closure $value)
    {
        $this->bindings[$name] = $value;
        return $this;
    }
    
    public function offsetSet($offset, $value)
    {
        $this->check($value);
        $offset = $value->get('alias');
        parent::offsetSet($offset, $value);
        $value->set('wildcards', $this->patterns);
        $value->set('bindings', $this->bindings);
    }
}