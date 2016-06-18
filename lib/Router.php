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

use SplObjectStorage;
use Opis\Routing\Path as BasePath;
use Opis\Routing\Router as BaseRouter;
use Opis\Routing\FilterCollection;

/** @noinspection PhpMissingParentCallCommonInspection */
/**
 * Class Router
 * @package Opis\HttpRouting
 *
 * @method RouteCollection getRouteCollection()
 * @method Route findRoute(Path $path)
 */
class Router extends BaseRouter
{
    /** @var SplObjectStorage */
    protected $storage;

    /**
     * Router constructor.
     * @param RouteCollection $routes
     * @param DispatcherResolver|null $resolver
     * @param FilterCollection|null $filters
     * @param array $specials
     */
    public function __construct(RouteCollection $routes, DispatcherResolver $resolver = null, FilterCollection $filters = null, array $specials = [])
    {
        if ($resolver === null) {
            $resolver = new DispatcherResolver();
        }

        if ($filters === null) {
            $filters = new FilterCollection();
            $filters->addFilter(new RequestFilter())
                    ->addFilter(new UserFilter());
        }

        $this->storage = new SplObjectStorage();
        parent::__construct($routes, $resolver, $filters, $specials);
    }

    /** @noinspection PhpDocSignatureInspection */
    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * @param Path $path
     * @return mixed|false
     */
    public function route(BasePath $path)
    {
        $route = $this->findRoute($path);
        
        if ($route === false) {
            return $this->raiseError(404, $path);
        }

        if (!$this->passFilter('after', $path, $route)) {
            return $this->raiseError(404, $path);
        }

        if (!$this->passFilter('access', $path, $route)) {
            return $this->raiseError(403, $path);
        }

        $dispatcher = $this->resolver->resolve($path, $route, $this);
        $result = $dispatcher->dispatch($path, $route, $this);

        if ($result instanceof HttpError) {
            return $this->raiseError($result->errorCode(), $path);
        }

        return $result;
    }

    /**
     * @param Route $route
     * @param Path|null $path
     * @return RouteWrapper
     */
    public function wrapRoute(Route $route, Path $path = null): RouteWrapper
    {
        if(!isset($this->storage[$route])){
            if($path === null){
                $path = $this->currentPath;
            }
            return $this->storage[$route] = new RouteWrapper($path, $route, $this);
        }
        return $this->storage[$route];
    }

    /**
     * @param int $error
     * @param Path $path
     * @return false|mixed
     */
    protected function raiseError(int $error, Path $path)
    {
        $callback = $this->getRouteCollection()->getError($error);

        if ($callback !== null) {
            return $callback($path);
        }

        return false;
    }

    /**
     * @param $filter
     * @param Path $path
     * @param Route $route
     * @return bool
     */
    protected function passFilter($filter, Path $path, Route $route)
    {
        /** @var CallbackFilter[] $filters */
        $filters = $route->get('filters', []) + $this->getRouteCollection()->getFilters();

        foreach ($route->get($filter, []) as $name) {
            if (isset($filters[$name])) {
                if ($filters[$name]->setBindMode(true)->pass($path, $route, $this) === false) {
                    return false;
                }
            }
        }

        return true;
    }

}
