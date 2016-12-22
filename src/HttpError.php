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

/**
 * Class HttpError
 * @package Opis\HttpRouting
 */
class HttpError
{
    /** @var int  */
    protected $errorCode;

    /**
     * HttpError constructor.
     * @param int $error
     */
    public function __construct(int $error)
    {
        $this->errorCode = $error;
    }

    /**
     * @return int
     */
    public function errorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @return HttpError
     */
    public static function pageNotFound(): self
    {
        return new static(404);
    }

    /**
     * @return HttpError
     */
    public static function accessDenied(): self
    {
        return new static(403);
    }
}
