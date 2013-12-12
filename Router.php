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

use Opis\Routing\Router as AbstractRouter;
use Opis\Routing\Compiler;
use Opis\Http\Request;
use Opis\Http\Error\NotFound as NotFoundError;

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
    
    public function execute()
    {
        $result = parent::execute();
        
        $response = $this->request->response();
        
        if($result === null)
        {
            $result = new NotFoundError('<h1>Page not found</h1>');
        }
        
        $response->body($result);
        $response->send();
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
