Opis HTTP Routing
============
[![Build Status](https://travis-ci.org/opis/http-routing.svg?branch=master)](https://travis-ci.org/opis/http-routing)
[![Latest Stable Version](https://poser.pugx.org/opis/http-routing/version.png)](https://packagist.org/packages/opis/http-routing)
[![Latest Unstable Version](https://poser.pugx.org/opis/http-routing/v/unstable.png)](//packagist.org/packages/opis/http-routing)
[![License](https://poser.pugx.org/opis/http-routing/license.png)](https://packagist.org/packages/opis/http-routing)

HTTP Routing library
---------------------
**Opis HTTP Routing** is a library that can be used to route all types of HTTP request, providing a full
range of features, like path filters, domain filters, user defined filters, access filters, custom error
handlers for `404 Not found` and `403 Forbidden` HTTP errors and much more. 

### License

**Opis HTTP Routing** is licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0). 

### Requirements

* PHP 5.3.* or higher
* [Opis Routing](http://www.opis.io/routing) ^4.1.0

### Installation

This library is available on [Packagist](https://packagist.org/packages/opis/http-routing) and can be installed using [Composer](http://getcomposer.org).

```json
{
    "require": {
        "opis/http-routing": "^4.0.2"
    }
}
```

If you are unable to use [Composer](http://getcomposer.org) you can download the
[tar.gz](https://github.com/opis/http-routing/archive/4.0.2.tar.gz) or the [zip](https://github.com/opis/http-routing/archive/4.0.2.zip)
archive file, extract the content of the archive and include de `autoload.php` file into your project. 

```php

require_once 'path/to/http-routing-4.0.2/autoload.php';

```

### Documentation

Examples and documentation can be found at http://opis.io/http-routing .
