[![Build](https://github.com/hareland/multi-cache-remember/actions/workflows/pest.yml/badge.svg)](https://github.com/hareland/multi-cache-remember/actions/workflows/pest.yml)
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
   which will be called only if the key is not found in the cache. This is similar to the `Cache::remember()` method.

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
> To set the default TTL, you can set this in `config/services.php` on the key `multi_remember.default_ttl` -> You will have to set it in seconds.

#### Simple (No custom TTL)
```php
<?php

use Illuminate\Support\Facades\Cache;

$results = Cache::rememberMulti([
    'user:1' => fn ()=> \App\Models\User::findOrFail(1),
    'user:2' => fn ()=> \App\Models\User::findOrFail(2),
    'meta:11'=> fn ()=> \App\Models\Meta::findOrFail(11),
], 5);// 5 seconds is now the TTL for all the items.
```

#### Custom TTL on some keys
```php
<?php
use Illuminate\Support\Facades\Cache;

$results = Cache::rememberMulti([
    'dashboard.stats.top:user:1' => [fn()=>\App\Models\Stats::findFor(request()->user()), 60 * 15],
    'dashboard.stats.sales:org:3' => [fn()=>\App\Models\StatsForOrf::findFor(request()->user()->currentOrg), 60 * 5],
    'dashboard.stats.overview:org:3' => fn()=> \App\Models\OverviewStats::findFor(request()->user()->currentOrg),
], 60); // 60 seconds is the default TTL for any keys that does not have a custom one.
```