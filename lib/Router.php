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

use Opis\Routing\Path;
use Opis\Routing\Route as BaseRoute;
use Opis\Routing\Router as BaseRouter;
use Opis\Routing\FilterCollection;

/** @noinspection PhpMissingParentCallCommonInspection */
/**
 * Class Router
 * @package Opis\HttpRouting
 *
 * @method RouteCollection getRouteCollection()
 * @method Route findRoute(Request $path)
 * @property Request $currentPath
 */
class Router extends BaseRouter
{
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

        parent::__construct($routes, $resolver, $filters, $specials);
    }

    /**
     * @return array
     */
    public function getSpecialValues()
    {
        /** @var Request $request */
        $request = $this->currentPath;

        return $this->specials + [
            'path' => $request->path(),
            'request' => $request->request(),
            'route' => $this->currentRoute
        ];
    }

    /**
     * @param Path $path
     * @return false|mixed
     */
    public function route(Path $path)
    {
        /** @var Request $path */
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
     * @param Path $path
     * @param BaseRoute $route
     * @return array
     */
    public function extract(Path $path, BaseRoute $route): array
    {
        /** @var Request $path */
        /** @var Route $route */

        $names = [];
        if(null !== $domain = $route->get('domain')){
            $names += $this->getRouteCollection()->getDomainCompiler()->getNames($domain);
        }
        $names += $this->getCompiler()->getNames($route->getPattern());
        $regex = $this->getRouteCollection()->getRegex($route->getID());
        $values = $this->getCompiler()->getValues($regex, (string) $path);

        return array_intersect_key($values, array_flip($names)) + $route->getDefaults();
    }

    /**
     * @param int $error
     * @param Request $path
     * @return false|mixed
     */
    protected function raiseError(int $error, Request $path)
    {
        $callback = $this->getRouteCollection()->getError($error);

        if ($callback !== null) {
            return $callback($path);
        }

        return false;
    }

    /**
     * @param $filter
     * @param Request $path
     * @param Route $route
     * @return bool
     */
    protected function passFilter($filter, Request $path, Route $route)
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
