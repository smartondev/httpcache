<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('stale if error', function (CacheHeaderBuilder $builder, array $expectedHeaders) {
    expect($builder->toHeaders())->toBe($expectedHeaders);
    $builder->staleIfError(days: 3);
    expect($builder->toHeaders())->toBe(['cache-control' => 'stale-if-error=259200']);
})->with([
    '3600 sec' => [
        (new CacheHeaderBuilder())
            ->staleIfError(3600),
        ['cache-control' => 'stale-if-error=3600'],
    ],
    '30 sec' => [
        (new CacheHeaderBuilder())
            ->staleIfError(30),
        ['cache-control' => 'stale-if-error=30'],
    ],
    '40 sec named arg' => [
        (new CacheHeaderBuilder())
            ->staleIfError(seconds: 40),
        ['cache-control' => 'stale-if-error=40'],
    ],
    '5 minutes named arg' => [
        (new CacheHeaderBuilder())
            ->staleIfError(minutes: 5),
        ['cache-control' => 'stale-if-error=300'],
    ],
    '2 hours named arg' => [
        (new CacheHeaderBuilder())
            ->staleIfError(hours: 2),
        ['cache-control' => 'stale-if-error=7200'],
    ],
    '1 day named arg' => [
        (new CacheHeaderBuilder())
            ->staleIfError(seconds: 86400),
        ['cache-control' => 'stale-if-error=86400'],
    ],
    '2 week named arg' => [
        (new CacheHeaderBuilder())
            ->staleIfError(weeks: 2),
        ['cache-control' => 'stale-if-error=1209600'],
    ],
    '1 month named arg' => [
        (new CacheHeaderBuilder())
            ->staleIfError(months: 1),
        ['cache-control' => 'stale-if-error=2592000'],
    ],
    '1 year named arg' => [
        (new CacheHeaderBuilder())
            ->staleIfError(years: 1),
        ['cache-control' => 'stale-if-error=31536000'],
    ],
    '2 hours 30 minutes' => [
        (new CacheHeaderBuilder())
            ->staleIfError(minutes: 30, hours: 2),
        ['cache-control' => 'stale-if-error=9000'],
    ],
]);

it('with stale if error', function (CacheHeaderBuilder $builder, array $expectedHeaders) {
    expect($builder->toHeaders())->toBe($expectedHeaders);
    $builderNew = $builder->withStaleIfError(60);
    expect($builderNew->toHeaders())->toBe(['cache-control' => 'stale-if-error=60'])
        ->and($builder->toHeaders())->toBe($expectedHeaders);
})->with([
    '3600 sec' => [
        (new CacheHeaderBuilder())
            ->withStaleIfError(3600),
        ['cache-control' => 'stale-if-error=3600'],
    ],
    '30 sec' => [
        (new CacheHeaderBuilder())
            ->withStaleIfError(30),
        ['cache-control' => 'stale-if-error=30'],
    ],
    '40 sec named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleIfError(seconds: 40),
        ['cache-control' => 'stale-if-error=40'],
    ],
    '5 minutes named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleIfError(minutes: 5),
        ['cache-control' => 'stale-if-error=300'],
    ],
    '2 hours named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleIfError(hours: 2),
        ['cache-control' => 'stale-if-error=7200'],
    ],
    '1 day named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleIfError(seconds: 86400),
        ['cache-control' => 'stale-if-error=86400'],
    ],
    '2 week named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleIfError(weeks: 2),
        ['cache-control' => 'stale-if-error=1209600'],
    ],
    '1 month named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleIfError(months: 1),
        ['cache-control' => 'stale-if-error=2592000'],
    ],
    '1 year named arg' => [
        (new CacheHeaderBuilder())
            ->withStaleIfError(years: 1),
        ['cache-control' => 'stale-if-error=31536000'],
    ],
    '2 hours 30 minutes' => [
        (new CacheHeaderBuilder())
            ->withStaleIfError(minutes: 30, hours: 2),
        ['cache-control' => 'stale-if-error=9000'],
    ],
]);

it('reset stale if error', function () {
    $builder = (new CacheHeaderBuilder())
        ->staleIfError(3600);
    expect($builder->toHeaders())->toBe(['cache-control' => 'stale-if-error=3600']);
    $builder->resetStaleIfError();
    expect($builder->toHeaders())->toBe([]);
});

it('without stale if error', function () {
    $builder = (new CacheHeaderBuilder())
        ->staleIfError(3600);
    expect($builder->toHeaders())->toBe(['cache-control' => 'stale-if-error=3600'])
        ->and($builder->withoutStaleIfError()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'stale-if-error=3600']);
});
