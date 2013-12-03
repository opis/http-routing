<?php

namespace Opis\HttpRouting;

use Opis\Routing\Router as AbstractRouter;
use Opis\Http\Request;

class Router extends AbstractRouter
{
    
    protected $request;
    
    protected $compiler;
    
    protected $filterList;
    
    protected $dispatcher;
    
    public function __construct(Request $request, RouteCollection $collection)
    {
        parent::__construct($collection);
        $this->request = $request;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function getCompiler()
    {
        if($this->compiler === null)
        {
            $this->compiler = new Compiler();
        }
        
        return $this->compiler;
    }
    
    protected function dispatcher()
    {
        if($this->dispatcher === null)
        {
            $this->dispatcher = new Dispatcher($this);
        }
        
        return $this->dispatcher;
    }
    
    protected function filters()
    {
        if($this->filterList === null)
        {
            $this->filterList = array(
                new PathFilter($this),
                new RequestFilter($this),
            );
        }
        
        return $this->filterList;
    }
}
