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

use Opis\Routing\RouteInvoker as BaseRouteInvoker;

/**
 * @property Route $route
 * @method Route getRoute()
 */
class RouteInvoker extends BaseRouteInvoker
{
    public function getNames(): array
    {
        if ($this->names === null) {
            $names = [];
            if (null !== $domain = $this->route->get('domain')) {
                /** @var RouteCollection $collection */
                $collection = $this->route->getRouteCollection();
                $names += $collection->getDomainBuilder()->getNames($domain);
            }
            $names += parent::getNames();
            $this->names = $names;
        }

        return $this->names;
    }
}