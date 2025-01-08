<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('stale while revalidate', function (CacheHeaderBuilder $builder, array $expectedHeaders) {
    expect($builder->toHeaders())->toBe($expectedHeaders);
    $builder->staleWhileRevalidate(days: 3);
    expect($builder->toHeaders())->toBe(['cache-control' => 'stale-while-revalidate=259200']);
})->with([
    '3600 sec' => [
        (new CacheHeaderBuilder())
            ->staleWhileRevalidate(3600),
        ['cache-control' => 'stale-while-revalidate=3600'],
    ],
    '30 sec' => [
        (new CacheHeaderBuilder())
            ->staleWhileRevalidate(30),
        ['cache-control' => 'stale-while-revalidate=30'],
    ],
    '40 sec named arg' => [
        (new CacheHeaderBuilder())
            ->staleWhileRevalidate(seconds: 40),
        ['cache-control' => 'stale-while-revalidate=40'],
    ],
    '5 minutes named arg' => [
        (new CacheHeaderBuilder())
            ->staleWhileRevalidate(minutes: 5),
        ['cache-control' => 'stale-while-revalidate=300'],
    ],
    '2 hours named arg' => [
        (new CacheHeaderBuilder())
            ->staleWhileRevalidate(hours: 2),
        ['cache-control' => 'stale-while-revalidate=7200'],
    ],
    '1 day named arg' => [
        (new CacheHeaderBuilder())
            ->staleWhileRevalidate(seconds: 86400),
        ['cache-control' => 'stale-while-revalidate=86400'],
    ],
    '2 week named arg' => [
        (new CacheHeaderBuilder())
            ->staleWhileRevalidate(weeks: 2),
        ['cache-control' => 'stale-while-revalidate=1209600'],
    ],
    '1 month named arg' => [
        (new CacheHeaderBuilder())
            ->staleWhileRevalidate(months: 1),
        ['cache-control' => 'stale-while-revalidate=2592000'],
    ],
    '1 year named arg' => [
        (new CacheHeaderBuilder())
            ->staleWhileRevalidate(years: 1),
        ['cache-control' => 'stale-while-revalidate=31536000'],
    ],
    '2 hours 30 minutes' => [
        (new CacheHeaderBuilder())
            ->staleWhileRevalidate(minutes: 30, hours: 2),
        ['cache-control' => 'stale-while-revalidate=9000'],
    ],
]);

it('with stale while revalidate', function (CacheHeaderBuilder $builder, array $expectedHeaders) {
    expect($builder->toHeaders())->toBe($expectedHeaders);
    $builderNew = $builder->withStaleWhileRevalidate(60);
    expect($builderNew->toHeaders())->toBe(['cache-control' => 'stale-while-revalidate=60'])
        ->and($builder->toHeaders())->toBe($expectedHeaders);
})->with([
    '3600 sec' => [
        (new CacheHeaderBuilder())
            ->withStaleWhileRevalidate(3600),
        ['cache-control' => 'stale-while-revalidate=3600'],
    ],
    '30 sec' => [
        (new CacheHeaderBuilder())
            ->withStaleWhileRevalidate(30),
        ['cache-control' => 'stale-while-revalidate=30'],
    ],
    '40 sec named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleWhileRevalidate(seconds: 40),
        ['cache-control' => 'stale-while-revalidate=40'],
    ],
    '5 minutes named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleWhileRevalidate(minutes: 5),
        ['cache-control' => 'stale-while-revalidate=300'],
    ],
    '2 hours named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleWhileRevalidate(hours: 2),
        ['cache-control' => 'stale-while-revalidate=7200'],
    ],
    '1 day named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleWhileRevalidate(seconds: 86400),
        ['cache-control' => 'stale-while-revalidate=86400'],
    ],
    '2 week named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleWhileRevalidate(weeks: 2),
        ['cache-control' => 'stale-while-revalidate=1209600'],
    ],
    '1 month named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleWhileRevalidate(months: 1),
        ['cache-control' => 'stale-while-revalidate=2592000'],
    ],
    '1 year named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleWhileRevalidate(years: 1),
        ['cache-control' => 'stale-while-revalidate=31536000'],
    ],
    '2 hours 30 minutes' => [
        (new CacheHeaderBuilder())
            ->withStaleWhileRevalidate(minutes: 30, hours: 2),
        ['cache-control' => 'stale-while-revalidate=9000'],
    ],
]);

it('reset stale while revalidate', function () {
    $builder = (new CacheHeaderBuilder())
        ->staleWhileRevalidate(3600);
    expect($builder->toHeaders())->not()->toBeEmpty();
    $builder->resetStaleWhileRevalidate();
    expect($builder->toHeaders())->toBeEmpty();
});

it('without stale while revalidate', function () {
    $builder = (new CacheHeaderBuilder())
        ->staleWhileRevalidate(3600);
    expect($builder->withoutStaleWhileRevalidate()->toHeaders())->toBeEmpty()
        ->and($builder->toHeaders())->not()->toBeEmpty();
});
