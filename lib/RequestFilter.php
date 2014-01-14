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

use Opis\Routing\Route;
use Opis\Routing\Contracts\FilterInterface;
use Opis\Routing\Contracts\PathInterface;

class RequestFilter implements FilterInterface
{
    
    public function pass(PathInterface $path, Route $route)
    {
        
        //match secure
        if(null !== $secure = $route->get('secure'))
        {
            
            if($secure !== $path->request()->isSecure())
            {
                return false;
            }
        }
        
        //match method
        if(!in_array($path->request()->method(), $route->get('method', array('GET'))))
        {
            return false;
        }
        
        //match domain
        if(null !== $domain = $route->compiledDomain())
        {
            return $domain->match($path->domain());
        }
        
        return true;
    }
    
}