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

use Closure;
use Opis\Routing\Contracts\PathInterface;
use Opis\Routing\Contracts\RouteInterface;
use Opis\Routing\Dispatcher as BaseDispatcher;

class Dispatcher extends BaseDispatcher
{    
    
    public function dispatch(PathInterface $path, RouteInterface $route)
    {        
        
        $values = array();
        
        $domain = $route->compileDomain();
        
        if($domain !== null)
        {
            $values += $domain->bind($path->domain());
        }
        
        $values += $route->compile()->bind($path);
        
        print_r($values); die;
        return $this->invokeAction($route->getAction(), $values);
        
    }
}