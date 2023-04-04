[![Build](https://github.com/hareland/multi-cache-remember/actions/workflows/pest.yml/badge.svg)](https://github.com/hareland/multi-cache-remember/actions/workflows/pest.yml)
## Another laravel package :)
> **NOTE**: This package is currently WIP, Pull Requests are appreciated!
### What does this package solve?

> The rememberMulti macro for the Laravel Cache facade provides several advantages over the traditional method of
> looking up multiple cache keys one by one. These advantages include:

1. Reduced number of cache lookups: With the traditional method, each cache key is looked up individually, even if some
   of them are being retrieved in quick succession. With the rememberMulti macro, all the requested keys are retrieved
   in a single call to Cache::many(), which can significantly reduce the number of cache lookups and improve
   performance.

2. Customizable cache expiration: The rememberMulti macro allows you to specify an expiration time for all the cached
   values at once, which can simplify the cache management process and make it easier to ensure that your cached data is
   up-to-date.

3. Callbacks for missing keys: The rememberMulti macro allows you to specify a callback function for each cache key,
   which will be called only if the key is not found in the cache. This can be useful for retrieving data from a
   database or external API, and can help you avoid unnecessary queries for data that is already in the cache.

### Install

```bash
composer require hareland/multi-cache-remember
```

### Register ServiceProvider (if you need to)

```php
// in config/app.php
'providers' => [
    \Hareland\MultiCacheRemember\MultiCacheServiceProvider::class,
]
```

### Usage

```php
<?php

use Illuminate\Support\Facades\Cache;

$results = Cache::rememberMulti([
    'user:1' => fn ()=> \App\Models\User::findOrFail(1),
    'user:2' => fn ()=> \App\Models\User::findOrFail(2),
    'meta:11'=> fn ()=> \App\Models\Meta::findOrFail(11),
], 5);
```