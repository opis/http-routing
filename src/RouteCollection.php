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

use Opis\Pattern\RegexBuilder;
use Opis\Routing\{ClosureTrait, RouteCollection as BaseCollection};

/**
 * @method Route createRoute(string $pattern, callable $action, string $name = null)
 */
class RouteCollection extends BaseCollection
{
    use ClosureTrait;

    /** @var array */
    private $placeholders = [];

    /** @var callable[] */
    private $bindings = [];

    /** @var callable[] */
    private $callbacks = [];

    /** @var array */
    private $defaults = [];

    /** @var RegexBuilder|null */
    private $domainBuilder;

    /** @var callable[] */
    private $guards = [];

    /** @var callable[] */
    private $filters = [];

    /**
     * @inheritDoc
     */
    public function __construct(callable $factory = null, RegexBuilder $builder = null, string $sortKey = null, bool $sortDescending = true)
    {
        $factory = $factory ?? function (
                RouteCollection $collection,
                string $id,
                string $pattern,
                callable $action,
                string $name = null
            ) {
                return new Route($collection, $id, $pattern, $action, $name);
            };
        parent::__construct($factory, $builder, $sortKey, $sortDescending);
    }

    /**
     * Get placeholders
     *
     * @return  array
     */
    public function getPlaceholders(): array
    {
        return $this->placeholders;
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
     * Get filters
     *
     * @return callable[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Get guards
     *
     * @return callable[]
     */
    public function getGuards(): array
    {
        return $this->guards;
    }

    /**
     * @return RegexBuilder
     */
    public function getDomainBuilder(): RegexBuilder
    {
        if ($this->domainBuilder === null) {
            $this->domainBuilder = new RegexBuilder([
                RegexBuilder::SEPARATOR_SYMBOL => '.',
                RegexBuilder::CAPTURE_MODE => RegexBuilder::CAPTURE_RIGHT,
            ]);
        }
        return $this->domainBuilder;
    }

    /**
     * Add a placeholder
     *
     * @param string $name
     * @param $value
     * @return static|RouteCollection
     */
    public function placeholder(string $name, $value): self
    {
        $this->placeholders[$name] = $value;
        return $this;
    }

    /**
     * Binding
     *
     * @param   string $name
     * @param   callable $callback
     * @return  static|RouteCollection
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
     * @return  static|RouteCollection
     */
    public function implicit(string $name, $value): self
    {
        $this->defaults[$name] = $value;
        return $this;
    }

    /**
     * Add global filter
     *
     * @param string $name
     * @param callable $callback
     * @return static|RouteCollection
     */
    public function filter(string $name, callable $callback): self
    {
        $this->filters[$name] = $callback;
        return $this;
    }

    /**
     * Add global guard
     *
     * @param string $name
     * @param callable $callback
     * @return static|RouteCollection
     */
    public function guard(string $name, callable $callback): self
    {
        $this->guards[$name] = $callback;
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getSerializableData(): array
    {
        return [
            'parent' => parent::getSerializableData(),
            'placeholders' => $this->placeholders,
            'filters' => $this->wrapClosures($this->filters),
            'guards' => $this->wrapClosures($this->guards),
            'bindings' => $this->wrapClosures($this->bindings),
            'defaults' => $this->wrapClosures($this->defaults),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setUnserializedData(array $data)
    {
        $this->placeholders = $data['placeholders'];
        $this->filters = $this->unwrapClosures($data['filters']);
        $this->guards = $this->unwrapClosures($data['guards']);
        $this->bindings = $this->unwrapClosures($data['bindings']);
        $this->defaults = $this->unwrapClosures($data['defaults']);

        parent::setUnserializedData($data['parent']);
    }
}
