<?php

namespace Hareland\MultiCacheRemember;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class MultiCacheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the macro.
     * @return void
     */
    public function boot()
    {
        Cache::macro('rememberMulti', function (array $keysAndCallbacks, $defaultTtl = null) {
            if (empty($keysAndCallbacks)) {
                return [];
            }

            $keys = $callbacks = $ttls = [];

            // Iterate over the input array and populate the separate arrays.
            foreach ($keysAndCallbacks as $key => $value) {
                if (!is_string($key) || (!is_array($value) && !is_callable($value))) {
                    throw new \InvalidArgumentException('Key must be a string and value must be an array or a callable.');
                }

                $keys[$key] = $key;
                $callbacks[$key] = fn() => value(is_array($value) ? $value[0] : $value);
                $ttls[$key] = is_array($value) ? $value[1] ?? $defaultTtl : $defaultTtl;
            }

            // Get hits from cache.
            $values = array_filter(array_map(
                fn($item) => false === $item ? null : $item,
                Cache::many(array_keys($keys)),
            ));

            $missingKeys = array_diff_key($keys, array_flip($values));

            if (!empty($missingKeys)) {
                $newValues = [];
                foreach ($missingKeys as $key => $index) {
                    if (isset($callbacks[$key])) {
                        $newValues[$key] = $callbacks[$key]();
                        Cache::put(
                            $key,
                            $newValues[$key],
                            $ttls[$key] ?? $defaultTtl
                        );
                    }
                }

                // Merge the new values with the ones that were found in the cache
                $values = array_merge($values, $newValues);
            }

            return $values;
        });

    }
}