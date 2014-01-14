<?php

namespace Opis\HttpRouting;


use Opis\Routing\DispatcherCollection;
use Opis\Routing\DispatcherInterface;
use Opis\Routing\DispatcherResolver as BaseResolver;

class DispatcherResolver extends BaseResolver
{
    
    protected $collection;
    
    protected $defaultDispatcher;
    
    public function __construct()
    {
        $this->collection = new DispatcherCollection();
        $this->defaultDispatcher = new Dispatcher();
    }
    
    public function register($name, DispatcherInterface $dispatcher)
    {
        $this->collection[$name] = $dispatcher;
        return $this;
    }
    
    public function resolve(Path $router, Route $route)
    {
        $dispatcher = $route->get('dispatcher');
        
        if($dispatcher !== null && isset($this->collection[$dispatcher]))
        {
            return $this->collection[$dispatcher];
        }
        
        return $this->defaultDispatcher;
    }
}