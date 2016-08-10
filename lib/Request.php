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

/**
 * Class Path
 * @package Opis\HttpRouting
 */
class Request extends BasePath
{
    /** @var  string */
    protected $path;

    /** @var BasePath */
    protected $domain;

    /** @var string  */
    protected $method;

    /** @var bool  */
    protected $secure;

    /** @var null|mixed */
    protected $request;

    /**
     * Path constructor.
     * @param string $path
     * @param string $domain
     * @param string $method
     * @param bool $secure
     * @param mixed|null $request
     */
    public function __construct(string $path,
                                string $domain = 'localhost',
                                string $method = 'GET',
                                bool $secure = false,
                                string $request = null)
    {
        $this->method = $method;
        $this->domain = $domain;
        $this->method = strtoupper($method);
        $this->secure = $secure;
        $this->request = $request;
        parent::__construct($path);
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function domain(): string
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @return mixed|null
     */
    public function request()
    {
        return $this->request;
    }
}
