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

use Opis\Routing\Compiler;
use Opis\Routing\RouteCollection as BaseCollection;

/**
 * Class RouteCollection
 * @package Opis\HttpRouting
 */
class RouteCollection extends BaseCollection
{
    /** @var    array */
    protected $placeholders = [];

    /** @var   callable[] */
    protected $bindings = [];

    /** @var    callable[] */
    protected $callbacks = [];

    /** @var    array */
    protected $defaults = [];

    /** @var  Compiler|null */
    protected $domainCompiler;


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
     * @return Compiler
     */
    public function getDomainCompiler(): Compiler
    {
        if($this->domainCompiler === null){
            $this->domainCompiler = new Compiler([
                Compiler::SEGMENT_DELIMITER => '.',
                Compiler::CAPTURE_MODE => Compiler::CAPTURE_RIGHT | Compiler::CAPTURE_TRAIL
            ]);
        }
        return $this->domainCompiler;
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
        return $this->placeholder($name, $value);
    }

    /**
     * Add a placeholder
     *
     * @param string $name
     * @param $value
     * @return $this|RouteCollection
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
