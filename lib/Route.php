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

use Opis\Routing\Route as BaseRoute;

class Route extends BaseRoute
{
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
        $filters[$name] = new CallbackFilter($filter);
        return $this->set('filters', $filters);
    }

    /**
     * @param string $name
     * @return Route
     */
    public function dispatcher(string $name): self
    {
        return $this->set('dispatcher', $name);
    }

}
