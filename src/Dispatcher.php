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

use Opis\Routing\{
    DispatcherTrait, IDispatcher, Router as BaseRouter
};
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface, StreamInterface
};

/**
 * @method Route findRoute(Router $router)
 */
class Dispatcher implements IDispatcher
{
    use DispatcherTrait;

    /** @var IResponseFactory */
    protected $factory;

    /**
     * Dispatcher constructor.
     * @param IResponseFactory $factory
     */
    public function __construct(IResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param BaseRouter|Router $router
     * @return mixed
     */
    public function dispatch(BaseRouter $router)
    {
        $route = $this->findRoute($router);

        if ($route === null) {
            return $this->factory
                        ->createResponse()
                        ->withStatus(404);
        }

        /** @var ServerRequestInterface $request */
        $request = $router->getContext()->data();

        if (!in_array($request->getMethod(), $route->get('method', ['GET']))) {
            return $this->factory
                        ->createResponse()
                        ->withStatus(405);
        }

        $callbacks = $route->getCallbacks();
        $invoker = $router->resolveInvoker($route);

        foreach ($route->get('guard', []) as $name) {
            if (isset($callbacks[$name])) {
                $callback = $callbacks[$name];
                $arguments = $invoker->getArgumentResolver()->resolve($callback);
                if (false === $callback(...$arguments)) {
                    return $this->factory
                                ->createResponse()
                                ->withStatus(404);
                }
            }
        }

        $result = $invoker->invokeAction();

        if (!$result instanceof ResponseInterface) {

            if (!$result instanceof StreamInterface) {
                if (!is_string($result)) {
                    try {
                        $result = (string)$result;
                    } catch (\Error $error) {
                        $result = $error->getMessage();
                    } catch (\Exception $exception) {
                        $result = $exception->getMessage();
                    }
                }
                $stream = $this->factory->createStream('php://memory', 'rw');
                $stream->write($result);
                $result = $stream;
            }

            $result = $this->factory
                            ->createResponse()
                            ->withStatus(200)
                            ->withHeader('Content-Type', 'text/html')
                            ->withBody($result);
        }

        return $result;
    }
}