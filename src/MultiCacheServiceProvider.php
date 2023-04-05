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
        Cache::macro('rememberMulti', function (array $keysAndCallbacks, $defaultTtl = null): array {
            if (empty($keysAndCallbacks)) {
                return [];
            }

            if (null === $defaultTtl) {
                $defaultTtl = config('services.multi_remember.default_ttl', 120);
            }

            $cacheManyFilter = fn($item) => false === $item ? false : $item;

            $keys = $callbacks = $ttls = [];

            foreach ($keysAndCallbacks as $key => &$value) {
                if (!is_string($key)) {
                    //This likely means this is simply a lookup,
                    // so we just return the values from cache for simplicity (using the same filtering logic)...
                    return array_filter(Cache::many(
                        array_values($keysAndCallbacks)),
                        $cacheManyFilter,
                    );
                }

                if (is_callable($value)) {
                    $value = [$value, $defaultTtl];
                } elseif (!is_array($value) || !is_callable($value[0])) {
                    throw new \InvalidArgumentException(
                        "\$keysAndCallbacks[$key] must be an array or a callable."
                    );
                }

                list($callbacks[$key], $ttls[$key]) = $value;
                $keys[] = $key;
            }
            unset($value); // unset reference

            // Get hits from cache.
            $values = array_filter(
                Cache::many($keys),
                $cacheManyFilter
            );

            $missingKeys = array_diff_key(
                array_flip($keys),
                $values,
            );

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

            return array_replace(array_flip(array_keys($keysAndCallbacks)), $values);
        });

    }
}