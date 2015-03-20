<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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
use ReflectionFunction;
use Serializable;
use Opis\Closure\SerializableClosure;
use Opis\Routing\Contracts\RouteInterface;
use Opis\Routing\Contracts\FilterInterface;
use Opis\Routing\Contracts\PathInterface;

class ClosureFilter implements FilterInterface, Serializable
{
    
    protected $params;
    
    protected $callback;
    
    protected $closure;
    
    protected $doBind;
    
    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }
    
    protected function getParams()
    {
        if($this->params === null)
        {
            $this->params = array();
            
            foreach($this->getCallback()->getParameters() as $param)
            {
                $name = $param->getName();
                $value = null;
                
                if($param->isOptional())
                {
                    $value = $param->getDefaultValue();
                }
                
                $this->params[$name] = $value;
            }
        }
        
        return $this->params;
    }
    
    protected function getCallback()
    {
        if($this->callback === null)
        {
            $this->callback = new ReflectionFunction($this->closure);
        }
        
        return $this->callback;
    }
    
    public function setBindMode($value)
    {
        $this->doBind = (bool) $value;
        return $this;
    }
   
    public function pass(PathInterface $path, RouteInterface $route)
    {
        if($this->doBind)
        {
            $values = $route->compile()->bind($path);
        }
        else
        {
            $values = $route->compile()->extract($path);
        }
        
        $arguments = array();
        
        foreach($this->getParams() as $key => $value)
        {
            if(isset($values[$key]))
            {
                if($this->doBind)
                {
                    $value = $values[$key]->value();
                }
                else
                {
                    $value = $values[$key];
                }
            }
            
            $arguments[] = $value;
        }
        
        return $this->getCallback()->invokeArgs($arguments);
    }
    
    public function serialize()
    {
        SerializableClosure::enterContext();
        
        $object = serialize(array(
            'params' => $this->params,
            'closure' => SerializableClosure::from($this->closure),
        ));
        
        SerializableClosure::exitContext();
        
        return $object;
    }
    
    public function unserialize($data)
    {
        $object = SerializableClosure::unserializeData($data);
        $this->params = $object['params'];
        $this->closure = $object['closure']->getClosure();
    }
    
}
