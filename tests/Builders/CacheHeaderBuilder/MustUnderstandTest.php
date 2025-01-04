<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('must understand', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->mustUnderstand()->toHeaders())->toBe(['cache-control' => 'must-understand'])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'must-understand']);
});

it('with must understand', function () {
    $builder = new CacheHeaderBuilder();
    expect($builder->withMustUnderstand()->toHeaders())->toBe(['cache-control' => 'must-understand'])
        ->and($builder->toHeaders())->toBeEmpty();
});

it('reset must understand', function () {
    $builder = (new CacheHeaderBuilder())
        ->mustUnderstand();
    expect($builder->toHeaders())->toBe(['cache-control' => 'must-understand']);
    $builder->resetMustUnderstand();
    expect($builder->toHeaders())->toBe([]);
});

it('without must understand', function () {
    $builder = (new CacheHeaderBuilder())
        ->mustUnderstand();
    expect($builder->toHeaders())->toBe(['cache-control' => 'must-understand'])
        ->and($builder->withoutMustUnderstand()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['cache-control' => 'must-understand']);
});
