<?php

namespace Opis\HttpRouting;

use Opis\Routing\DispatcherResolverInterface;
use Opis\Routing\DispatcherCollection;
use Opis\Routing\DispatcherInterface;
use Opis\Routing\Router as BaseRouter;
use Opis\Routing\Route as BaseRoute;

class DispatcherResolver implements DispatcherResolverInterface
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
    
    public function resolve(BaseRouter $router, BaseRoute $route)
    {
        $dispatcher = $route->get('dispatcher');
        
        if($dispatcher !== null && isset($this->collection[$dispatcher]))
        {
            return $this->collection[$dispatcher];
        }
        
        return $this->defaultDispatcher;
    }
}