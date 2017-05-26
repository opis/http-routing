<?php
/* ===========================================================================
 * Copyright 2013-2016 The Opis Project
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

use Opis\Routing\Context as BaseContext;
use Opis\Routing\IDispatcher;
use Opis\Routing\Route as BaseRoute;
use Opis\Routing\RouteCollection;
use Opis\Routing\Router as BaseRouter;
use Opis\Routing\FilterCollection;

/** @noinspection PhpMissingParentCallCommonInspection */
/**
 * Class Router
 * @package Opis\HttpRouting
 *
 * @method RouteCollection getRouteCollection()
 * @method Route findRoute(Context $context)
 * @method Dispatcher getDispatcher()
 * @property Context $currentPath
 */
class Router extends BaseRouter
{
    /**
     * Router constructor.
     * @param IDispatcher $dispatcher
     * @param RouteCollection $routes
     * @param FilterCollection|null $filters
     * @param array $specials
     */
    public function __construct(
        RouteCollection $routes,
        IDispatcher $dispatcher = null,
        FilterCollection $filters = null,
        array $specials = []
    ){
        if($dispatcher === null){
            $dispatcher = new Dispatcher();
        }

        if ($filters === null) {
            $filters = new FilterCollection();
            $filters->addFilter(new RequestFilter())
                    ->addFilter(new UserFilter());
        }

        parent::__construct($routes, $resolver, $filters, $specials);
    }

    /**
     * @param BaseContext|Context $context
     * @return false|mixed
     */
    public function route(BaseContext $context)
    {
        $route = $this->findRoute($context);

        if ($route === false) {
            return $this->raiseError(404, $context);
        }

        if (!$this->passFilter('after', $context, $route)) {
            return $this->raiseError(404, $context);
        }

        if (!$this->passFilter('access', $context, $route)) {
            return $this->raiseError(403, $context);
        }

        $dispatcher = $this->resolver->resolve($this, $context, $route);
        $result = $dispatcher->dispatch($this, $context, $route);

        if ($result instanceof HttpError) {
            return $this->raiseError($result->errorCode(), $context);
        }

        return $result;
    }

    /**
     * @param BaseContext|Context $context
     * @param BaseRoute|Route $route
     * @return array
     */
    public function extract(BaseContext $context, BaseRoute $route): array
    {
        /** @var Context $context */
        /** @var Route $route */

        $names = [];
        if(null !== $domain = $route->get('domain')){
            $names += $this->getRouteCollection()->getDomainCompiler()->getNames($domain);
        }
        $names += $this->getCompiler()->getNames($route->getPattern());
        $regex = $this->getRouteCollection()->getRegex($route->getID());
        $values = $this->getCompiler()->getValues($regex, (string) $context);

        return array_intersect_key($values, array_flip($names)) + $route->getDefaults();
    }

    /**
     * @param int $error
     * @param Context $context
     * @return false|mixed
     */
    protected function raiseError(int $error, Context $context)
    {
        $callback = $this->getRouteCollection()->getError($error);

        if ($callback !== null) {
            return $callback($context);
        }

        return false;
    }

    /**
     * @param $filter
     * @param Context $context
     * @param Route $route
     * @return bool
     */
    protected function passFilter($filter, Context $context, Route $route)
    {
        /** @var CallbackFilter[] $filters */
        $filters = $route->get('filters', []) + $this->getRouteCollection()->getFilters();

        foreach ($route->get($filter, []) as $name) {
            if (isset($filters[$name])) {
                if ($filters[$name]->setBindMode(true)->pass($this, $context, $route) === false) {
                    return false;
                }
            }
        }

        return true;
    }

}
