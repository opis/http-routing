<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
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

use Opis\Http\Request;
use Opis\Routing\Path as BasePath;

class Path extends BasePath
{
    protected $request;
    
    protected $domain;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request->path());
    }
    
    public function request()
    {
        return $this->request;
    }
    
    public function domain()
    {
        if($this->domain === null)
        {
            $this->domain = new BasePath($this->request->host());
        }
        
        return $this->domain;
    }
}
