<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('public', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->public()->toHeaders())->toBe(['cache-control' => 'public'])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'public']);
});

it('with public', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->withPublic()->toHeaders())->toBe(['cache-control' => 'public'])
        ->and($builder->toHeaders())->toBeEmpty();
});

it('reset public', function () {
    $builder = (new CacheHeaderBuilder())
        ->public();
    expect($builder->toHeaders())->toBe(['cache-control' => 'public']);
    $builder->resetPublic();
    expect($builder->toHeaders())->toBe([]);
});

it('without public', function () {
    $builder = (new CacheHeaderBuilder())
        ->public();
    expect($builder->toHeaders())->toBe(['cache-control' => 'public'])
        ->and($builder->withoutPublic()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'public']);
});
