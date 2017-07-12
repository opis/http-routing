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

use Opis\Routing\Route as BaseRoute;

/**
 * Class Route
 * @package Opis\HttpRouting
 *
 * @method RouteCollection getRouteCollection()
 */
class Route extends BaseRoute
{
    /** @var array  */
    protected $cache = [];

    /**
     * @return array
     */
    public function getWildcards(): array
    {
        if(!isset($this->cache[__FUNCTION__])){
            /** @var RouteCollection $collection */
            $collection = $this->collection;
            $this->cache[__FUNCTION__] = parent::getWildcards() + $collection->getWildcards();
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        if(!isset($this->cache[__FUNCTION__])){
            /** @var RouteCollection $collection */
            $collection = $this->collection;
            $this->cache[__FUNCTION__] = parent::getDefaults() + $collection->getDefaults();
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * @return callable[]
     */
    public function getBindings(): array
    {
        if(!isset($this->cache[__FUNCTION__])){
            /** @var RouteCollection $collection */
            $collection = $this->collection;
            $this->cache[__FUNCTION__] = parent::getBindings() + $collection->getBindings();
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        if(!isset($this->cache[__FUNCTION__])){
            /** @var RouteCollection $collection */
            $collection = $this->collection;
            $this->cache[__FUNCTION__] = $this->get('filters', []) + $collection->getFilters();
        }
        return $this->cache[__FUNCTION__];
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
        $errors = $this->get('http_errors', []);
        if (isset($errors[$code])) {
            return $errors[$code];
        }
        /** @var RouteCollection $collection */
        $collection = $this->collection;
        return $collection->getError($code);
    }

    /**
     * @param string $value
     * @return Route
     */
    public function domain(string $value): self
    {
        return $this->set('domain', $value);
    }

    /**
     * @param string|array $method
     * @return Route
     */
    public function method($method): self
    {
        if (!is_array($method)) {
            $method = [$method];
        }

        $method = array_map('strtoupper', $method);

        return $this->set('method', $method);
    }

    /**
     * @param bool $value
     * @return Route
     */
    public function secure(bool $value = true): self
    {
        return $this->set('secure', $value);
    }

    /**
     * @param string|array $filters
     * @return Route
     */
    public function before($filters): self
    {
        if (!is_array($filters)) {
            $filters = [(string) $filters];
        }

        return $this->set('before', $filters);
    }

    /**
     * @param string|array $filters
     * @return Route
     */
    public function after($filters): self
    {
        if (!is_array($filters)) {
            $filters = [(string) $filters];
        }

        return $this->set('after', $filters);
    }

    /**
     * @param string|array $filters
     * @return Route
     */
    public function access($filters): self
    {
        if (!is_array($filters)) {
            $filters = [(string) $filters];
        }

        return $this->set('access', $filters);
    }

    /**
     * @param string $name
     * @param callable $filter
     * @return Route
     */
    public function filter(string $name, callable $filter): self
    {
        $filters = $this->get('filters', []);
        $filters[$name] = $filter;
        return $this->set('filters', $filters);
    }

    /**
     * @param string $name
     * @return Route
     */
    public function middleware(string $name): self
    {
        return $this->set('middleware', $name);
    }
}
