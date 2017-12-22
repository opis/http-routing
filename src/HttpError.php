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

class HttpError
{
    /** @var int */
    protected $code;

    /** @var array */
    protected $headers;

    /** @var string|null */
    protected $body;

    /**
     * HttpError constructor.
     * @param int $code
     * @param array $headers
     * @param string $body
     */
    public function __construct(int $code, array $headers = [], string $body = null)
    {
        $this->code = $code;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return string|null
     */
    public function getBody()
    {
        return $this->body;
    }
}