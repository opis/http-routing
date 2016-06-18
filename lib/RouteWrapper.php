<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2016 Marius Sarca
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


use Opis\Routing\Binding;

class RouteWrapper
{
    protected $path;
    protected $route;
    protected $router;
    protected $collection;
    protected $compiler;
    protected $domainCompiler;

    protected $defaults;
    protected $wildcards;
    protected $bindings;
    protected $names;
    protected $values;
    protected $regex;
    protected $domainRegex;
    protected $extracted;
    protected $bound;

    public function __construct(Path $path, Route $route, Router $router)
    {
        $this->path = $path;
        $this->route = $route;
        $this->router = $router;
        $this->collection = $router->getRouteCollection();
        $this->regex = $this->collection->getRegex($route->getID());
        $this->compiler = $this->collection->getCompiler();
        $this->domainCompiler = $this->collection->getDomainCompiler();
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        if($this->defaults === null){
            $this->defaults = $this->route->getDefaults() + $this->collection->getDefaults();
        }
        return $this->defaults;
    }

    /**
     * @return array
     */
    public function getWildcards(): array
    {
        if($this->wildcards === null){
            $this->wildcards = $this->route->getWildcards() + $this->collection->getWildcards();
        }
        return $this->wildcards;
    }

    /**
     * @return array|\callable[]
     */
    public function getBindings(): array
    {
        if($this->bindings == null){
            $this->bindings = $this->route->getBindings() + $this->collection->getBindings();
        }
        return $this->bindings;
    }

    /**
     * @return array
     */
    public function getNames(): array
    {
        if($this->names === null){
            $this->names = array();
            if(null !== $domain = $this->route->get('domain')){
                $this->names += $this->domainCompiler->getNames($domain);
            }
            $this->names += $this->compiler->getNames($this->route->getPattern());
        }
        return $this->names;
    }

    /**
     * @return bool|string
     */
    public function getDomainRegex()
    {
        if($this->domainRegex === null){
            if(null !== $domain = $this->route->get('domain')){
                $this->domainRegex = $this->domainCompiler->getRegex($domain, $this->getWildcards());
            } else {
                $this->domainRegex = false;
            }
        }
        return $this->domainRegex;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        if($this->values === null){
            $this->values = array();
            if(false !== $domainRegex = $this->getDomainRegex()){
                $this->values += $this->domainCompiler->getValues($domainRegex, (string) $this->path);
            }
            $this->values += $this->compiler->getValues($this->regex, $this->path);
        }
        return $this->values;
    }

    /**
     * @param bool $specials
     * @return array
     */
    public function extract(bool $specials = true): array
    {
        if($this->extracted === null){
            $names = $this->getNames();
            $values = $this->getValues();
            $this->extracted = array_intersect_key($values, array_flip($names)) + $this->getDefaults();
            if($specials){
                $this->extracted += $this->router->getSpecialValues();
            }
        }
        $this->extracted;
    }

    /**
     * @return Binding[]
     */
    public function bind(): array
    {
        if($this->bound === null){
            $this->bound = $this->router->bind($this->extract() , $this->getBindings());
        }
        $this->bound;
    }

}