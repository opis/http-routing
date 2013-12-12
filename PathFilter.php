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

use Opis\Routing\FilterInterface;
use Opis\Routing\Route as BaseRoute;

class PathFilter implements FilterInterface
{
    protected $request;
    
    protected $compiler;
    
    protected $collection;
    
    public function __construct(Router $router)
    {
        $this->request = $router->getRequest();
        $this->compiler = $router->getCompiler();
        $this->collection = $router->getCollection();
    }
    
    public function match(BaseRoute $route)
    {
        $pattern = $this->compiler->compile($route->getPath(), $route->getWildcards() + $this->collection->getWildcards());
        $route->set('compiled-path', $pattern);
        return preg_match($this->compiler->delimit($pattern), $this->request->path());
    }
    
}