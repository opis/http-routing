<?php

namespace Opis\HttpRouting;


use Opis\Routing\DispatcherCollection;
use Opis\Routing\DispatcherInterface;
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
    
    public function resolve(Path $router, Route $route)
    {
        $dispatcher = $route->get('dispatcher', 'default');
        
        if(isset($this->collection[$dispatcher]))
        {
            return $this->collection[$dispatcher];
        }
        
        return $this->collection['default'];
    }
}