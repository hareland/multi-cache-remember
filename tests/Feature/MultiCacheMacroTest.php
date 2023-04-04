<?php

use Illuminate\Support\Facades\Cache;

it('throws exception if only values are passed.', function () {
    $this->expectException(InvalidArgumentException::class);
    Cache::rememberMulti([
        'foo',
        'baz'
    ], 30);

});

it('retrieves and stores missing values with default TTL', function () {
    $result = Cache::rememberMulti([
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
    $result = Cache::rememberMulti([
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
    $result = Cache::rememberMulti([], 30);

    expect($result)->toBe([]);
});