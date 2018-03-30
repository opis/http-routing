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

use Opis\Pattern\RegexBuilder;
use Opis\Routing\ClosureWrapperTrait;
use Opis\Routing\RouteCollection as BaseCollection;

/**
 * Class RouteCollection
 * @method Route createRoute(string $pattern, callable $action, string $name = null)
 */
class RouteCollection extends BaseCollection
{
    use ClosureWrapperTrait;

    /** @var    array */
    protected $placeholders = [];

    /** @var   callable[] */
    protected $bindings = [];

    /** @var    callable[] */
    protected $callbacks = [];

    /** @var    array */
    protected $defaults = [];

    /** @var  RegexBuilder|null */
    protected $domainBuilder;

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
     * Get wildcards
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
     * Add a callback
     *
     * @param   string $name
     * @param   callable $callback
     * @return  static|RouteCollection
     */
    public function callback(string $name, callable $callback): self
    {
        $this->callbacks[$name] = $callback;
        return $this;
    }

    /**
     * @return array
     */
    protected function getSerialize()
    {
        $map = [static::class, 'wrapClosures'];

        return [
            'parent' => parent::getSerialize(),
            'placeholders' => $this->placeholders,
            'callbacks' => array_map($map, $this->callbacks),
            'bindings' => array_map($map, $this->bindings),
            'defaults' => array_map($map, $this->defaults),
        ];
    }

    /**
     * @param $object
     */
    protected function setUnserialize($object)
    {
        $map = [static::class, 'unwrapClosures'];

        $this->placeholders = $object['placeholders'];
        $this->callbacks = array_map($map, $object['callbacks']);
        $this->bindings = array_map($map, $object['bindings']);
        $this->defaults = array_map($map, $object['defaults']);

        parent::setUnserialize($object['parent']);
    }
}
