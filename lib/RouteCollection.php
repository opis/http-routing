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

use Closure;
use Opis\Closure\SerializableClosure;
use Opis\Routing\CallableExpectedException;
use Opis\Routing\Collections\RouteCollection as BaseCollection;

class RouteCollection extends BaseCollection
{
    /** @var    array */
    protected $wildcards = array();

    /** @var    array */
    protected $bindings = array();

    /** @var    array */
    protected $filters = array();

    /** @var    array */
    protected $defaults = array();

    /** @var    array */
    protected $errors = array();

    /**
     * Check type
     * 
     * @param   \Opis\HttpRouting\Route $value
     * 
     * @throws  InvalidArgumentException
     */
    protected function checkType($value)
    {
        if (!($value instanceof Route)) {
            throw new InvalidArgumentException('Expected \Opis\HttpRouting\Route');
        }
    }

    /**
     * Get wildcards
     * 
     * @return  array
     */
    public function getWildcards()
    {
        return $this->wildcards;
    }

    /**
     * Get bindings 
     * 
     * @return  array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Get filters
     * 
     * @return  filters
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Get default values
     * 
     * @return  array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Get error
     * 
     * @param   int $code
     * 
     * @return  callable|null
     */
    public function getError($code)
    {
        if (isset($this->errors[$code])) {
            return $this->errors[$code];
        }
        return null;
    }

    /**
     * Set a wildcard
     * 
     * @param   string  $name
     * @param   mixed   $value
     * 
     * @return  $this
     */
    public function wildcard($name, $value)
    {
        $this->wildcards[$name] = $value;
        return $this;
    }

    /**
     * Binding
     * 
     * @param   string      $name
     * @param   callable    $callback
     * 
     * @return  $this
     * 
     * @throws CallableExpectedException
     */
    public function bind($name, $callback)
    {
        if (!is_callable($callback)) {
            throw new CallableExpectedException();
        }

        $this->bindings[$name] = $callback;
        return $this;
    }

    /**
     * Set a default value
     * 
     * @param   string  $name
     * @param   mixed   $value
     * 
     * @return  $this
     */
    public function implicit($name, $value)
    {
        $this->defaults[$name] = $value;
        return $this;
    }

    /**
     * Set error callback
     * 
     * @param   callable    $callback
     * 
     * @return  $this
     * 
     * @throws  CallableExpectedException
     */
    public function notFound($callback)
    {
        if (!is_callable($callback)) {
            throw new CallableExpectedException();
        }

        $this->errors[404] = $callback;
        return $this;
    }

    /**
     * Set error callback
     * 
     * @param   callable    $callback
     * 
     * @return  $this
     * 
     * @throws  CallableExpectedException
     */
    public function accessDenied($callback)
    {
        if (!is_callable($callback)) {
            throw new CallableExpectedException();
        }

        $this->errors[403] = $callback;
        return $this;
    }

    /**
     * Add a filter
     * 
     * @param   string      $name
     * @param   callable    $filter
     * 
     * @return  $this
     */
    public function filter($name, $filter)
    {
        $this->filters[$name] = new CallbackFilter($filter);
        return $this;
    }

    /**
     * 
     * @param   mixed   $offset
     * @param   mixed   $value
     */
    public function offsetSet($offset, $value)
    {
        parent::offsetSet($offset, $value);
        $value->set('#collection', $this);
    }

    /**
     * Serialize
     * 
     * @return  string
     */
    public function serialize()
    {
        SerializableClosure::enterContext();

        $map = array($this, 'wrapClosures');

        $object = serialize(array(
            'collection' => $this->collection,
            'wildcards' => $this->wildcards,
            'filters' => $this->filters,
            'bindings' => array_map($map, $this->bindings),
            'defaults' => array_map($map, $this->defaults),
            'errors' => array_map($map, $this->errors),
        ));

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
        $object = SerializableClosure::unserializeData($data);
        $map = array($this, 'unwrapClosures');

        $this->collection = $object['collection'];
        $this->wildcards = $object['wildcards'];
        $this->filters = $object['filters'];
        $this->bindings = array_map($map, $object['bindings']);
        $this->defaults = array_map($map, $object['defaults']);
        $this->errors = array_map($map, $object['errors']);
    }

    /**
     * Wrap all closures
     * 
     * @param   mixed   $value
     * 
     * @return  mixed
     */
    protected function wrapClosures(&$value)
    {
        if ($value instanceof Closure) {
            return SerializableClosure::from($value);
        } elseif (is_array($value)) {
            return array_map(array($this, __FUNCTION__), $value);
        } elseif ($value instanceof \stdClass) {
            $object = (array) $value;
            $object = array_map(array($this, __FUNCTION__), $object);
            return (object) $object;
        }
        return $value;
    }

    /**
     * Unwrap closures
     * 
     * @param   mixed   $value
     * 
     * @return  mixed
     */
    protected function unwrapClosures(&$value)
    {
        if ($value instanceof SerializableClosure) {
            return $value->getClosure();
        } elseif (is_array($value)) {
            return array_map(array($this, __FUNCTION__), $value);
        } elseif ($value instanceof \stdClass) {
            $object = (array) $value;
            $object = array_map(array($this, __FUNCTION__), $object);
            return (object) $object;
        }
        return $value;
    }
}
