<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('shared max age', function (CacheHeaderBuilder $builder, array $expectedHeaders) {
    expect($builder->toHeaders())->toBe($expectedHeaders);
    $builder->sharedMaxAge(days: 3);
    expect($builder->toHeaders())->toBe(['cache-control' => 's-maxage=259200']);
})->with([
    '3600 sec' => [
        (new CacheHeaderBuilder())
            ->sharedMaxAge(3600),
        ['cache-control' => 's-maxage=3600'],
    ],
    '30 sec' => [
        (new CacheHeaderBuilder())
            ->sharedMaxAge(30),
        ['cache-control' => 's-maxage=30'],
    ],
    '40 sec named arg' => [
        (new CacheHeaderBuilder())
            ->sharedMaxAge(seconds: 40),
        ['cache-control' => 's-maxage=40'],
    ],
    '5 minutes named arg' => [
        (new CacheHeaderBuilder())
            ->sharedMaxAge(minutes: 5),
        ['cache-control' => 's-maxage=300'],
    ],
    '2 hours named arg' => [
        (new CacheHeaderBuilder())
            ->sharedMaxAge(hours: 2),
        ['cache-control' => 's-maxage=7200'],
    ],
    '1 day named arg' => [
        (new CacheHeaderBuilder())
            ->sharedMaxAge(seconds: 86400),
        ['cache-control' => 's-maxage=86400'],
    ],
    '2 week named arg' => [
        (new CacheHeaderBuilder())
            ->sharedMaxAge(weeks: 2),
        ['cache-control' => 's-maxage=1209600'],
    ],
    '1 month named arg' => [
        (new CacheHeaderBuilder())
            ->sharedMaxAge(months: 1),
        ['cache-control' => 's-maxage=2592000'],
    ],
    '1 year named arg' => [
        (new CacheHeaderBuilder())
            ->sharedMaxAge(years: 1),
        ['cache-control' => 's-maxage=31536000'],
    ],
    '2 hours 30 minutes' => [
        (new CacheHeaderBuilder())
            ->sharedMaxAge(minutes: 30, hours: 2),
        ['cache-control' => 's-maxage=9000'],
    ],
]);

it('with shared max age', function (CacheHeaderBuilder $builder, array $expectedHeaders) {
    expect($builder->toHeaders())->toBe($expectedHeaders);
    $builderNew = $builder->withSharedMaxAge(60);
    expect($builderNew->toHeaders())->toBe(['cache-control' => 's-maxage=60'])
        ->and($builder->toHeaders())->toBe($expectedHeaders);
})->with([
    '3600 sec' => [
        (new CacheHeaderBuilder())
            ->withSharedMaxAge(3600),
        ['cache-control' => 's-maxage=3600'],
    ],
    '30 sec' => [
        (new CacheHeaderBuilder())
            ->withSharedMaxAge(30),
        ['cache-control' => 's-maxage=30'],
    ],
    '40 sec named arg' => [
        (new CacheHeaderBuilder())
            ->withSharedMaxAge(seconds: 40),
        ['cache-control' => 's-maxage=40'],
    ],
    '5 minutes named arg' => [
        (new CacheHeaderBuilder())
            ->withSharedMaxAge(minutes: 5),
        ['cache-control' => 's-maxage=300'],
    ],
    '2 hours named arg' => [
        (new CacheHeaderBuilder())
            ->withSharedMaxAge(hours: 2),
        ['cache-control' => 's-maxage=7200'],
    ],
    '1 day named arg' => [
        (new CacheHeaderBuilder())
            ->withSharedMaxAge(seconds: 86400),
        ['cache-control' => 's-maxage=86400'],
    ],
    '2 week named arg' => [
        (new CacheHeaderBuilder())
            ->withSharedMaxAge(weeks: 2),
        ['cache-control' => 's-maxage=1209600'],
    ],
    '1 month named arg' => [
        (new CacheHeaderBuilder())
            ->withSharedMaxAge(months: 1),
        ['cache-control' => 's-maxage=2592000'],
    ],
    '1 year named arg' => [
        (new CacheHeaderBuilder())
            ->withSharedMaxAge(years: 1),
        ['cache-control' => 's-maxage=31536000'],
    ],
    '2 hours 30 minutes' => [
        (new CacheHeaderBuilder())
            ->withSharedMaxAge(minutes: 30, hours: 2),
        ['cache-control' => 's-maxage=9000'],
    ],
]);

it('reset shared max age', function () {
    $builder = (new CacheHeaderBuilder())
        ->sharedMaxAge(3600);
    expect($builder->toHeaders())->toBe(['cache-control' => 's-maxage=3600']);
    $builder->resetSharedMaxAge();
    expect($builder->toHeaders())->toBe([]);
});

it('without shared max age', function () {
    $builder = (new CacheHeaderBuilder())
        ->sharedMaxAge(3600);
    expect($builder->toHeaders())->toBe(['cache-control' => 's-maxage=3600'])
        ->and($builder->withoutSharedMaxAge()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['cache-control' => 's-maxage=3600']);
});
