<?php

use Illuminate\Support\Facades\Cache;

it('gets existing items using only values as keys', function () {
    Cache::put('foo', 'bar');
    $result = Cache::rememberMany([
        'foo',
        'baz'
    ], 30);

    expect($result)->toBe([
        'foo' => 'bar',
    ]);
});

it('retrieves and stores missing values with default TTL', function () {
    $result = Cache::rememberMany([
        'foo' => fn() => 'bar',
        'baz' => fn() => 'qux'
    ], 30);

    expect($result)->toBe([
        'foo' => 'bar',
        'baz' => 'qux'
    ]);

    expect(Cache::get('foo'))->toBe('bar');
    expect(Cache::get('baz'))->toBe('qux');
});

it('retrieves and stores missing values with custom TTL', function () {
    $result = Cache::rememberMany([
        'foo' => [fn() => 'bar', 30],
        'baz' => [fn() => 'qux', 3]
    ], 60);

    expect($result)->toBe([
        'foo' => 'bar',
        'baz' => 'qux'
    ]);

    expect(Cache::get('foo'))->toBe('bar');
    expect(Cache::get('baz'))->toBe('qux');

    sleep(5);

    expect(Cache::get('baz'))->toBeNull();
});

it('returns empty array if empty array is passed as keys', function () {
    $result = Cache::rememberMany([], 30);

    expect($result)->toBe([]);
});
