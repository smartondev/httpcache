<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('no transform', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->noTransform()->toHeaders())->toBe(['cache-control' => 'no-transform'])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'no-transform']);
});

it('with no transform', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->withNoTransform()->toHeaders())->toBe(['cache-control' => 'no-transform'])
        ->and($builder->toHeaders())->toBeEmpty();
});

it('reset no transform', function () {
    $builder = (new CacheHeaderBuilder())
        ->noTransform();
    expect($builder->toHeaders())->toBe(['cache-control' => 'no-transform']);
    $builder->resetNoTransform();
    expect($builder->toHeaders())->toBe([]);
});

it('without no transform', function () {
    $builder = (new CacheHeaderBuilder())
        ->noTransform();
    expect($builder->toHeaders())->toBe(['cache-control' => 'no-transform'])
        ->and($builder->withoutNoTransform()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'no-transform']);
});
