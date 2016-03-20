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

use Opis\Routing\Pattern;
use Opis\Routing\Compiler;
use Opis\Routing\CompiledExpression;
use Opis\Routing\Route as BaseRoute;

class Route extends BaseRoute
{
    protected $compiledDomain;
    protected $cache = array();

    public function __construct($pattern, $action)
    {
        parent::__construct(new Pattern($pattern), $action);
    }

    public function compileDomain()
    {
        if ($this->compiledDomain === null) {
            $domain = $this->get('domain');

            if ($domain !== null) {
                $this->compiledDomain = new CompiledExpression($this->domainCompiler(), $domain, null, $this->getWildcards(), $this->getDefaults(), $this->getBindings());
            }
        }

        return $this->compiledDomain;
    }

    public function where($name, $value)
    {
        return $this->wildcard($name, $value);
    }

    public function domain($value)
    {
        return $this->set('domain', new Pattern($value));
    }

    public function method($method)
    {
        if (!is_array($method)) {
            $method = array($method);
        }

        $method = array_map('strtoupper', $method);

        return $this->set('method', $method);
    }

    public function secure($value = true)
    {
        return $this->set('secure', $value);
    }

    public function before($filters)
    {
        if (!is_array($filters)) {
            $filters = array($filters);
        }

        return $this->set('before', $filters);
    }

    public function after($filters)
    {
        if (!is_array($filters)) {
            $filters = array($filters);
        }

        return $this->set('after', $filters);
    }

    public function access($filters)
    {
        if (!is_array($filters)) {
            $filters = array($filters);
        }

        return $this->set('access', $filters);
    }

    public function filter($name, $filter)
    {
        $filters = $this->get('filters', array());
        $filters[$name] = new CallbackFilter($filter);
        return $this->set('filters', $filters);
    }

    public function dispatcher($name)
    {
        return $this->set('dispatcher', $name);
    }

    public function getWildcards()
    {
        if (!isset($this->cache['wildcards'])) {
            $this->cache['wildcards'] = $this->wildcards + $this->get('#collection')->getWildcards();
        }
        return $this->cache['wildcards'];
    }

    public function getBindings()
    {
        if (!isset($this->cache['bindings'])) {
            $this->cache['bindings'] = $this->bindings + $this->get('#collection')->getBindings();
        }
        return $this->cache['bindings'];
    }

    public function getDefaults()
    {
        if (!isset($this->cache['defaults'])) {
            $this->cache['defaults'] = $this->defaults + $this->get('#collection')->getDefaults();
        }
        return $this->cache['defaults'];
    }

    public function getFilters()
    {
        if (!isset($this->cache['filters'])) {
            $this->cache['filters'] = $this->get('filters', array()) + $this->get('#collection')->getFilters();
        }
        return $this->cache['filters'];
    }

    public function domainCompiler()
    {
        static $compiler;

        if ($compiler === null) {
            $compiler = new Compiler('{', '}', '.', '?', (Compiler::CAPTURE_RIGHT | Compiler::CAPTURE_TRAIL), '`', 'u', '[a-zA-Z0-9\-]+');
        }

        return $compiler;
    }

    public static function create($pattern, $action, $method = 'GET')
    {
        $route = new static($pattern, $action);
        return $route->method($method);
    }
}
