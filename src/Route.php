<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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
 * @method RouteCollection getRouteCollection()
 */
class Route extends BaseRoute
{
    /** @var array */
    private $cache = [];

    /**
     * @return array
     */
    public function getPlaceholders(): array
    {
        if (!isset($this->cache[__FUNCTION__])) {
            /** @var RouteCollection $collection */
            $collection = $this->getRouteCollection();
            $this->cache[__FUNCTION__] = parent::getPlaceholders() + $collection->getPlaceholders();
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        if (!isset($this->cache[__FUNCTION__])) {
            /** @var RouteCollection $collection */
            $collection = $this->getRouteCollection();
            $this->cache[__FUNCTION__] = parent::getDefaults() + $collection->getDefaults();
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * @return callable[]
     */
    public function getBindings(): array
    {
        if (!isset($this->cache[__FUNCTION__])) {
            /** @var RouteCollection $collection */
            $collection = $this->getRouteCollection();
            $this->cache[__FUNCTION__] = parent::getBindings() + $collection->getBindings();
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * @return array
     */
    public function getLocalPlaceholders(): array
    {
        return parent::getPlaceholders();
    }

    /**
     * @return array
     */
    public function getLocalDefaults(): array
    {
        return parent::getDefaults();
    }

    /**
     * @return array
     */
    public function getLocalBindings(): array
    {
        return parent::getBindings();
    }

    /**
     * @param string $value
     * @return static|Route
     */
    public function domain(string $value): self
    {
        return $this->set('domain', $value);
    }

    /**
     * @param string ...$method
     * @return static|Route
     */
    public function method(string ...$method): self
    {
        if (empty($method)) {
            $method[] = 'GET';
        }

        $method = array_map('strtoupper', $method);

        return $this->set('method', $method);
    }

    /**
     * @param bool $value
     * @return static|Route
     */
    public function secure(bool $value = true): self
    {
        return $this->set('secure', $value);
    }

    /**
     * @param string $name
     * @param callable|null $callback
     * @return static|Route
     */
    public function filter(string $name, callable $callback = null): self
    {
        $filters = $this->get('filters', []);
        $filters[$name] = $callback;
        return $this->set('filters', $filters);
    }

    /**
     * @param string $name
     * @param callable|null $callback
     * @return static|Route
     */
    public function guard(string $name, callable $callback = null): self
    {
        $guards = $this->get('guards', []);
        $guards[$name] = $callback;
        return $this->set('guards', $guards);
    }
}
