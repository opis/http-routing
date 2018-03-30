<?php
/* ===========================================================================
 * Copyright 2013-2018 The Opis Project
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
    /** @var array */
    protected $cache = [];

    /**
     * @return array
     */
    public function getPlaceholders(): array
    {
        if (!isset($this->cache[__FUNCTION__])) {
            /** @var RouteCollection $collection */
            $collection = $this->collection;
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
        if (!isset($this->cache[__FUNCTION__])) {
            /** @var RouteCollection $collection */
            $collection = $this->collection;
            $this->cache[__FUNCTION__] = parent::getBindings() + $collection->getBindings();
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * @return callable[]
     */
    public function getCallbacks(): array
    {
        if (!isset($this->cache[__FUNCTION__])) {
            /** @var RouteCollection $collection */
            $collection = $this->collection;
            $this->cache[__FUNCTION__] = $this->get('callbacks', []) + $collection->getCallbacks();
        }
        return $this->cache[__FUNCTION__];
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
     * @param string[] ...$method
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
     * @param string[] ...$callbacks
     * @return static|Route
     */
    public function filter(string ...$callbacks): self
    {
        return $this->set('filter', $callbacks);
    }

    /**
     * @param string[] ...$callbacks
     * @return static|Route
     */
    public function guard(string ...$callbacks): self
    {
        return $this->set('guard', $callbacks);
    }

    /**
     * @param string $name
     * @param callable $callback
     * @return static|Route
     */
    public function callback(string $name, callable $callback): self
    {
        $list = $this->get('callbacks', []);
        $list[$name] = $callback;
        return $this->set('callbacks', $list);
    }
}
