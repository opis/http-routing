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

class RequestFilter implements FilterInterface
{
    protected $compiler;
    
    protected $request;
    
    public function __construct(Router $router)
    {
        $this->compiler = $router->getCompiler();
        $this->request = $router->getRequest();
    }
    
    public function match(BaseRoute $route)
    {
        //match secure
        if(null !== $secure = $route->get('secure'))
        {
            if($secure !== $this->request->isSecure())
            {
                return false;
            }
        }
        //match method
        if(!in_array($this->request->method(), $route->get('method', array('GET'))))
        {
            return false;
        }
        //match domain
        if(null !== $domain = $route->get('domain'))
        {
            $placeholders = $route->getWildcards() + $route->get('wildcards');
            $domain = str_replace('.', '\.', $domain);
            
            foreach($placeholders as $key => $value)
            {
                $domain = str_replace('{' . $key . '}\.', '(?P<' . $key . '>(' . $value. '))\.', $domain);
                $domain = str_replace('{' . $key . '?}\.', '((P<' . $key . '>(' . $value .')\.))?', $domain);
            }
            
            $domain = preg_replace('/\{([^?]+)\\.\}/', '(?P<$1>([a-zA-Z0-9\.\,\-_%=]+))\.', $domain);
            
            $domain = preg_replace('/\{([^?]+)\?\\.\}/', '((?P<$1>([a-zA-Z0-9\.\,\-_%=]+))\.)?', $domain);
            
            $route->set('compiled-domain', $domain);
            
            $domain = '#^' . $domain . '$#u';
            
            if(!preg_match($domain, $this->request->host()))
            {
                return false;
            }
        }
        return true;
    }
    
}