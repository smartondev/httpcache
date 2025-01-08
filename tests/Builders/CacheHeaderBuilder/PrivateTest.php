<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('private', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->private()->toHeaders())->toBe(['cache-control' => 'private'])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'private']);
});

it('with private', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->withPrivate()->toHeaders())->toBe(['cache-control' => 'private'])
        ->and($builder->toHeaders())->toBeEmpty();
});

it('reset private', function () {
    $builder = (new CacheHeaderBuilder())
        ->private();
    expect($builder->toHeaders())->toBe(['cache-control' => 'private']);
    $builder->resetPrivate();
    expect($builder->toHeaders())->toBe([]);
});

it('without private', function () {
    $builder = (new CacheHeaderBuilder())
        ->private();
    expect($builder->toHeaders())->toBe(['cache-control' => 'private'])
        ->and($builder->withoutPrivate()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'private']);
});
