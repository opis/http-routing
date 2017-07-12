<?php
/* ===========================================================================
 * Copyright 2013-2017 The Opis Project
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

use Opis\Closure\SerializableClosure;
use Opis\Routing\ClosureWrapperTrait;
use Opis\Routing\Compiler;
use Opis\Routing\Route as BaseRoute;
use Opis\Routing\RouteCollection as BaseCollection;

/**
 * Class RouteCollection
 * @package Opis\HttpRouting
 */
class RouteCollection extends BaseCollection
{
    use ClosureWrapperTrait;

    /** @var    array */
    protected $wildcards = [];

    /** @var    callable[] */
    protected $bindings = [];

    /** @var    callable[] */
    protected $callbacks = [];

    /** @var    array */
    protected $defaults = [];

    /** @var    array */
    protected $errors = [];

    /** @var array */
    protected $middleware = [];

    /** @var  Compiler|null */
    protected $domainCompiler;


    /**
     * Get wildcards
     * 
     * @return  array
     */
    public function getWildcards(): array
    {
        return $this->wildcards;
    }

    /**
     * Get bindings 
     * 
     * @return  callable[]
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * Get filters
     * 
     * @return  callable[]
     */
    public function getCallbacks(): array
    {
        return $this->callbacks;
    }

    /**
     * Get default values
     * 
     * @return  array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * Get registered middleware
     *
     * @return callable[]
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * @return Compiler
     */
    public function getDomainCompiler(): Compiler
    {
        if($this->domainCompiler === null){
            $this->domainCompiler = new Compiler([
                Compiler::TAG_SEPARATOR => '.',
                Compiler::CAPTURE_MODE => Compiler::CAPTURE_RIGHT | Compiler::CAPTURE_TRAIL
            ]);
        }
        return $this->domainCompiler;
    }

    /**
     * Get error
     * 
     * @param   int $code
     * 
     * @return  callable|null
     */
    public function getError(int $code)
    {
        if (isset($this->errors[$code])) {
            return $this->errors[$code];
        }
        return null;
    }

    /**
     * Set a wildcard
     *
     * @param   string $name
     * @param   mixed $value
     * @return $this|RouteCollection
     */
    public function wildcard(string $name, $value): self
    {
        $this->wildcards[$name] = $value;
        return $this;
    }

    /**
     * Binding
     *
     * @param   string $name
     * @param   callable $callback
     * @return $this|RouteCollection
     */
    public function bind(string $name, callable $callback): self
    {
        $this->bindings[$name] = $callback;
        return $this;
    }

    /**
     * Set a default value
     *
     * @param   string $name
     * @param   mixed $value
     * @return $this|RouteCollection
     */
    public function implicit(string $name, $value): self
    {
        $this->defaults[$name] = $value;
        return $this;
    }

    /**
     * Set error callback
     *
     * @param   callable $callback
     * @return $this|RouteCollection
     */
    public function notFound(callable $callback): self
    {
        $this->errors[404] = $callback;
        return $this;
    }

    /**
     * Set error callback
     *
     * @param   callable $callback
     * @return $this|RouteCollection
     */
    public function accessDenied(callable $callback): self
    {
        $this->errors[403] = $callback;
        return $this;
    }

    /**
     * Add a callback
     *
     * @param   string $name
     * @param   callable $callback
     * @return $this|RouteCollection
     */
    public function callback(string $name, callable $callback): self
    {
        $this->callbacks[$name] = $callback;
        return $this;
    }

    /**
     * Add a middleware
     *
     * @param string $name
     * @param callable $callback
     * @return RouteCollection
     */
    public function middleware(string $name, callable $callback): self
    {
        $this->middleware[$name] = $callback;
        return $this;
    }

    /**
     * Serialize
     * 
     * @return  string
     */
    public function serialize()
    {
        SerializableClosure::enterContext();

        $map = [static::class, 'wrapClosures'];

        $object = serialize([
            'parent' => $this->getSerialize(),
            'wildcards' => $this->wildcards,
            'filters' => $this->callbacks,
            'bindings' => array_map($map, $this->bindings),
            'defaults' => array_map($map, $this->defaults),
            'errors' => array_map($map, $this->errors),
            'middleware' => array_map($map, $this->middleware),
        ]);

        SerializableClosure::exitContext();

        return $object;
    }

    /**
     * Unserialize
     * 
     * @param   string  $data
     */
    public function unserialize($data)
    {
        $object = unserialize($data);
        $map = [static::class, 'unwrapClosures'];

        $this->setUnserialize($object['parent']);

        $this->wildcards = $object['wildcards'];
        $this->callbacks = $object['filters'];
        $this->bindings = array_map($map, $object['bindings']);
        $this->defaults = array_map($map, $object['defaults']);
        $this->errors = array_map($map, $object['errors']);
        $this->middleware = array_map($map, $object['middleware']);
    }
}
