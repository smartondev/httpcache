<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('immutable', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->immutable()->toHeaders())->toBe(['cache-control' => 'immutable'])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'immutable']);
});

it('with immutable', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->withImmutable()->toHeaders())->toBe(['cache-control' => 'immutable'])
        ->and($builder->toHeaders())->toBeEmpty();
});

it('reset immutable', function () {
    $builder = (new CacheHeaderBuilder())
        ->immutable();
    expect($builder->toHeaders())->toBe(['cache-control' => 'immutable']);
    $builder->resetImmutable();
    expect($builder->toHeaders())->toBe([]);
});

it('without immutable', function () {
    $builder = (new CacheHeaderBuilder())
        ->immutable();
    expect($builder->toHeaders())->toBe(['cache-control' => 'immutable'])
        ->and($builder->withoutImmutable()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'immutable']);
});
