<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('max age', function (CacheHeaderBuilder $builder, array $expectedHeaders) {
    expect($builder->toHeaders())->toBe($expectedHeaders);
    $builder->maxAge(days: 3);
    expect($builder->toHeaders())->toBe(['cache-control' => 'max-age=259200']);
})->with([
    '3600 sec' => [
        (new CacheHeaderBuilder())
            ->maxAge(3600),
        ['cache-control' => 'max-age=3600'],
    ],
    '30 sec' => [
        (new CacheHeaderBuilder())
            ->maxAge(30),
        ['cache-control' => 'max-age=30'],
    ],
    '40 sec named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(seconds: 40),
        ['cache-control' => 'max-age=40'],
    ],
    '5 minutes named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(minutes: 5),
        ['cache-control' => 'max-age=300'],
    ],
    '2 hours named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(hours: 2),
        ['cache-control' => 'max-age=7200'],
    ],
    '1 day named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(seconds: 86400),
        ['cache-control' => 'max-age=86400'],
    ],
    '2 week named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(weeks: 2),
        ['cache-control' => 'max-age=1209600'],
    ],
    '1 month named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(months: 1),
        ['cache-control' => 'max-age=2592000'],
    ],
    '1 year named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(years: 1),
        ['cache-control' => 'max-age=31536000'],
    ],
    '2 hours 30 minutes' => [
        (new CacheHeaderBuilder())
            ->maxAge(minutes: 30, hours: 2),
        ['cache-control' => 'max-age=9000'],
    ],
]);

it('with max age', function (CacheHeaderBuilder $builder, array $expectedHeaders) {
    expect($builder->toHeaders())->toBe($expectedHeaders);
    $builderNew = $builder->withMaxAge(60);
    expect($builderNew->toHeaders())->toBe(['cache-control' => 'max-age=60'])
        ->and($builder->toHeaders())->toBe($expectedHeaders);
})->with([
    '3600 sec' => [
        (new CacheHeaderBuilder())
            ->maxAge(3600),
        ['cache-control' => 'max-age=3600'],
    ],
    '30 sec' => [
        (new CacheHeaderBuilder())
            ->maxAge(30),
        ['cache-control' => 'max-age=30'],
    ],
    '40 sec named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(seconds: 40),
        ['cache-control' => 'max-age=40'],
    ],
    '5 minutes named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(minutes: 5),
        ['cache-control' => 'max-age=300'],
    ],
    '2 hours named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(hours: 2),
        ['cache-control' => 'max-age=7200'],
    ],
    '1 day named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(seconds: 86400),
        ['cache-control' => 'max-age=86400'],
    ],
    '2 week named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(weeks: 2),
        ['cache-control' => 'max-age=1209600'],
    ],
    '1 month named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(months: 1),
        ['cache-control' => 'max-age=2592000'],
    ],
    '1 year named arg' => [
        (new CacheHeaderBuilder())
            ->maxAge(years: 1),
        ['cache-control' => 'max-age=31536000'],
    ],
    '2 hours 30 minutes' => [
        (new CacheHeaderBuilder())
            ->maxAge(minutes: 30, hours: 2),
        ['cache-control' => 'max-age=9000'],
    ],
]);

it('reset max age', function () {
    $builder = (new CacheHeaderBuilder())
        ->maxAge(3600);
    expect($builder->toHeaders())->not()->toBeEmpty();
    $builder->resetMaxAge();
    expect($builder->toHeaders())->toBeEmpty();
});

it('without max age', function () {
    $builder = (new CacheHeaderBuilder())
        ->maxAge(3600);
    expect($builder->withoutMaxAge()->toHeaders())->toBeEmpty()
        ->and($builder->toHeaders())->not()->toBeEmpty();
});
